<?php

declare(strict_types=1);

namespace SqliLab\Service;

final class DetectorService
{
    public function __construct(private readonly SqlFeatureExtractor $extractor)
    {
    }

    public function analyze(string $statement, array $config): array
    {
        $definitions = $config['feature_definitions'] ?? [];
        $classifiers = $config['classifiers'] ?? [];
        $features = $this->extractor->extract($statement);
        $riskScore = $this->extractor->riskScore($features, $definitions);
        $predictions = [];

        foreach ($classifiers as $classifier) {
            $adjusted = round($riskScore * (float) ($classifier['sensitivity'] ?? 1), 2);
            $label = $adjusted >= (float) ($classifier['threshold'] ?? 45) ? 'injected' : 'benign';
            $predictions[] = [
                'classifier' => (string) $classifier['name'],
                'label' => $label,
                'score' => $adjusted,
                'threshold' => (float) $classifier['threshold'],
                'reported_accuracy' => (float) $classifier['accuracy'],
            ];
        }

        $injectedVotes = count(array_filter($predictions, fn (array $prediction): bool => $prediction['label'] === 'injected'));
        $consensus = match (true) {
            $injectedVotes >= 3 => 'injected',
            $injectedVotes <= 1 => 'benign',
            default => 'review',
        };

        return [
            'statement_preview' => substr(trim($statement), 0, 500),
            'risk_score' => $riskScore,
            'consensus_label' => $consensus,
            'injected_votes' => $injectedVotes,
            'total_votes' => count($predictions),
            'features' => $features,
            'predictions' => $predictions,
        ];
    }
}
