<?php

declare(strict_types=1);

use SqliLab\Service\DetectorService;
use SqliLab\Service\SqlFeatureExtractor;

require __DIR__ . '/../src/bootstrap.php';

$config = require __DIR__ . '/../config/paper.php';

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$extractor = new SqlFeatureExtractor();
$detector = new DetectorService($extractor);

assertTrue($config['paper']['doi'] === '10.1109/ICECTA48151.2019.8959617', 'Paper DOI must be configured accurately.');
assertTrue(count($config['classifiers']) === 5, 'The top five classifier profiles should be represented.');
assertTrue(count($config['feature_definitions']) >= 10, 'Feature definitions should be comprehensive enough for the lab.');

$benign = 'SELECT id, name, email FROM users WHERE id = ?';
$benignFeatures = $extractor->extract($benign);
assertTrue($benignFeatures['boolean_tautology'] === 0.0, 'Benign statement should not trigger tautology.');
assertTrue($benignFeatures['union_select'] === 0.0, 'Benign statement should not trigger UNION pattern.');

$tautology = "' OR '1'='1' --";
$tautologyFeatures = $extractor->extract($tautology);
assertTrue($tautologyFeatures['boolean_tautology'] === 10.0, 'Injected tautology should be detected.');
assertTrue($tautologyFeatures['comment_tokens'] === 10.0, 'SQL comment token should be detected.');

$union = '1 UNION SELECT username, password FROM users --';
$unionFeatures = $extractor->extract($union);
assertTrue($unionFeatures['union_select'] === 10.0, 'UNION SELECT attack should be detected.');

$stacked = '1; DROP TABLE users; --';
$stackedFeatures = $extractor->extract($stacked);
assertTrue($stackedFeatures['stacked_query'] === 10.0, 'Stacked query should be detected.');
assertTrue($stackedFeatures['dangerous_keyword'] === 10.0, 'Dangerous keyword should be detected.');

$maliciousAnalysis = $detector->analyze($tautology, $config);
assertTrue($maliciousAnalysis['consensus_label'] === 'injected', 'Tautology payload should produce injected consensus.');
assertTrue(count($maliciousAnalysis['predictions']) === 5, 'Detector should return five classifier profile predictions.');

$benignAnalysis = $detector->analyze($benign, $config);
assertTrue(in_array($benignAnalysis['consensus_label'], ['benign', 'review'], true), 'Benign statement should not produce strong injected consensus.');

echo 'test-suite-ok' . PHP_EOL;
