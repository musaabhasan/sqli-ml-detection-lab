<?php

declare(strict_types=1);

namespace SqliLab\Repository;

use PDO;
use SqliLab\Support\Json;
use SqliLab\Support\Uuid;

final class LabRepository
{
    public function __construct(private readonly ?PDO $db)
    {
    }

    public function connected(): bool
    {
        return $this->db instanceof PDO;
    }

    public function summary(): array
    {
        if (!$this->connected()) {
            return [
                'connected' => false,
                'feature_count' => 0,
                'classifier_count' => 0,
                'sample_count' => 0,
                'detection_count' => 0,
                'average_risk_score' => null,
            ];
        }

        return [
            'connected' => true,
            'feature_count' => (int) $this->db->query('SELECT COUNT(*) FROM feature_definitions')->fetchColumn(),
            'classifier_count' => (int) $this->db->query('SELECT COUNT(*) FROM classifier_profiles')->fetchColumn(),
            'sample_count' => (int) $this->db->query('SELECT COUNT(*) FROM sample_statements')->fetchColumn(),
            'detection_count' => (int) $this->db->query('SELECT COUNT(*) FROM detection_events')->fetchColumn(),
            'average_risk_score' => $this->nullableFloat($this->db->query('SELECT AVG(risk_score) FROM detection_events')->fetchColumn()),
        ];
    }

    public function classifierProfiles(): array
    {
        if (!$this->connected()) {
            return [];
        }

        return $this->db->query('SELECT * FROM classifier_profiles ORDER BY accuracy DESC, name')->fetchAll();
    }

    public function samples(): array
    {
        if (!$this->connected()) {
            return [];
        }

        return $this->db->query('SELECT * FROM sample_statements ORDER BY id LIMIT 8')->fetchAll();
    }

    public function recentDetections(int $limit = 6): array
    {
        if (!$this->connected()) {
            return [];
        }

        $stmt = $this->db->prepare('SELECT * FROM detection_events ORDER BY created_at DESC LIMIT :limit');
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function experiments(): array
    {
        if (!$this->connected()) {
            return [];
        }

        return $this->db->query('SELECT * FROM experiments ORDER BY FIELD(status, "active", "planned", "completed"), title')->fetchAll();
    }

    public function saveDetection(string $statement, array $analysis): ?string
    {
        if (!$this->connected()) {
            return null;
        }

        $uuid = Uuid::v4();
        $stmt = $this->db->prepare(
            'INSERT INTO detection_events
            (uuid, statement_hash, statement_preview, risk_score, consensus_label, feature_json, prediction_json, created_at)
            VALUES
            (:uuid, :statement_hash, :statement_preview, :risk_score, :consensus_label, :feature_json, :prediction_json, UTC_TIMESTAMP())'
        );
        $stmt->execute([
            'uuid' => $uuid,
            'statement_hash' => hash('sha256', $statement),
            'statement_preview' => (string) $analysis['statement_preview'],
            'risk_score' => (float) $analysis['risk_score'],
            'consensus_label' => (string) $analysis['consensus_label'],
            'feature_json' => Json::encode($analysis['features']),
            'prediction_json' => Json::encode($analysis['predictions']),
        ]);

        $audit = $this->db->prepare('INSERT INTO audit_events (action, actor, payload_json, created_at) VALUES (:action, :actor, :payload_json, UTC_TIMESTAMP())');
        $audit->execute([
            'action' => 'detection.created',
            'actor' => 'portal-user',
            'payload_json' => Json::encode(['uuid' => $uuid, 'consensus_label' => $analysis['consensus_label']]),
        ]);

        return $uuid;
    }

    private function nullableFloat(mixed $value): ?float
    {
        return $value === null ? null : round((float) $value, 2);
    }
}
