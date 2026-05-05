<?php

declare(strict_types=1);

namespace SqliLab\Service;

final class SqlFeatureExtractor
{
    public function extract(string $statement): array
    {
        $sql = trim($statement);
        $lower = strtolower($sql);
        $length = strlen($sql);
        $operators = preg_match_all('/(=|<>|!=|<=|>=|\bor\b|\band\b|\blike\b)/i', $sql);

        return [
            'length_score' => min(10.0, round($length / 32, 2)),
            'quote_density' => min(10.0, substr_count($sql, "'") + substr_count($sql, '"')),
            'comment_tokens' => $this->hasCommentToken($sql) ? 10.0 : 0.0,
            'boolean_tautology' => $this->hasTautology($lower) ? 10.0 : 0.0,
            'union_select' => preg_match('/\bunion\b\s+(?:all\s+)?\bselect\b/i', $sql) === 1 ? 10.0 : 0.0,
            'stacked_query' => $this->hasStackedQuery($sql) ? 10.0 : 0.0,
            'dangerous_keyword' => preg_match('/\b(drop|truncate|alter|grant|revoke|exec|execute|xp_cmdshell|information_schema)\b/i', $sql) === 1 ? 10.0 : 0.0,
            'time_delay' => preg_match('/\b(sleep|benchmark|pg_sleep|waitfor\s+delay|dbms_pipe|dbms_lock)\b/i', $sql) === 1 ? 10.0 : 0.0,
            'encoding_probe' => preg_match('/\b(char|concat|hex|unhex|load_file|0x[0-9a-f]+)\b/i', $sql) === 1 ? 10.0 : 0.0,
            'operator_density' => min(10.0, round(($operators / max(1, str_word_count($sql))) * 35, 2)),
        ];
    }

    public function riskScore(array $features, array $definitions): float
    {
        $weighted = 0.0;
        $totalWeight = 0.0;

        foreach ($definitions as $definition) {
            $key = (string) ($definition['key'] ?? '');
            $weight = max(0.1, (float) ($definition['weight'] ?? 1));
            $value = max(0.0, min(10.0, (float) ($features[$key] ?? 0)));
            $weighted += ($value / 10.0) * 100.0 * $weight;
            $totalWeight += $weight;
        }

        return $totalWeight > 0 ? round($weighted / $totalWeight, 2) : 0.0;
    }

    private function hasCommentToken(string $sql): bool
    {
        return str_contains($sql, '--') || str_contains($sql, '#') || str_contains($sql, '/*') || str_contains($sql, '*/');
    }

    private function hasTautology(string $sql): bool
    {
        return preg_match('/\b(or|and)\b\s+[\'"]?\w+[\'"]?\s*=\s*[\'"]?\w+[\'"]?/i', $sql) === 1
            || preg_match('/[\'"]\s*=\s*[\'"]/', $sql) === 1
            || preg_match('/\b1\s*=\s*1\b/', $sql) === 1;
    }

    private function hasStackedQuery(string $sql): bool
    {
        $trimmed = trim($sql);
        if (!str_contains($trimmed, ';')) {
            return false;
        }

        return preg_match('/;\s*(select|insert|update|delete|drop|alter|exec|truncate)\b/i', $trimmed) === 1;
    }
}
