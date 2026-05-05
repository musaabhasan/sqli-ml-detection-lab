<?php

declare(strict_types=1);

if (PHP_SAPI === 'cli-server') {
    $assetPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $assetFile = realpath(__DIR__ . $assetPath);

    if ($assetFile !== false && str_starts_with($assetFile, __DIR__) && is_file($assetFile)) {
        return false;
    }
}

use SqliLab\Repository\LabRepository;
use SqliLab\Security\Csrf;
use SqliLab\Security\SecurityHeaders;
use SqliLab\Service\DetectorService;
use SqliLab\Service\SqlFeatureExtractor;
use SqliLab\Support\Database;
use SqliLab\Support\Json;
use SqliLab\Support\View;

require __DIR__ . '/../src/bootstrap.php';

SecurityHeaders::apply();
Csrf::start();

$config = require __DIR__ . '/../config/paper.php';
$repository = new LabRepository(Database::tryConnection());
$detector = new DetectorService(new SqlFeatureExtractor());
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($path === '/health') {
    jsonResponse(['status' => 'ok', 'service' => $config['portal']['slug']]);
}

if ($path === '/api/summary') {
    jsonResponse([
        'portal' => $config['portal'],
        'paper' => $config['paper'],
        'summary' => $repository->summary(),
        'classifiers' => $config['classifiers'],
    ]);
}

if ($path === '/api/detect' && $method === 'POST') {
    $statement = trim((string) ($_POST['statement'] ?? ''));
    if ($statement === '') {
        jsonResponse(['message' => 'A SQL statement is required.'], 422);
    }

    $analysis = $detector->analyze($statement, $config);
    jsonResponse($analysis);
}

if ($path === '/detector' && $method === 'POST') {
    handleDetectorPost($config, $repository, $detector);
}

if ($path === '/detector') {
    sendPage($config, 'Detector', renderDetector($config, $repository));
}

if ($path === '/paper') {
    sendPage($config, 'Paper Alignment', renderPaper($config, $repository));
}

sendPage($config, 'Dashboard', renderDashboard($config, $repository));

function handleDetectorPost(array $config, LabRepository $repository, DetectorService $detector): void
{
    if (!Csrf::valid($_POST['_csrf_token'] ?? null)) {
        sendPage($config, 'Session expired', '<section class="panel"><h1>Session expired</h1><p>Please refresh and try again.</p></section>', 419);
    }

    $statement = trim((string) ($_POST['statement'] ?? ''));
    if ($statement === '' || strlen($statement) > 5000) {
        sendPage($config, 'Validation error', '<section class="panel"><h1>Validation error</h1><p>Enter a SQL statement under 5,000 characters.</p></section>', 422);
    }

    $analysis = $detector->analyze($statement, $config);
    $uuid = $repository->saveDetection($statement, $analysis);
    sendPage($config, 'Detection Result', renderDetectionResult($config, $repository, $statement, $analysis, $uuid));
}

function sendPage(array $config, string $title, string $body, int $status = 200): void
{
    http_response_code($status);
    echo layout($config, $title, $body);
    exit;
}

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo Json::encode($payload);
    exit;
}

function layout(array $config, string $title, string $body): string
{
    $appTitle = View::e((string) $config['portal']['title']);
    $pageTitle = View::e($title);

    return <<<HTML
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$pageTitle} | {$appTitle}</title>
  <link rel="stylesheet" href="/assets/app.css">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="/"><span class="brand-mark">SQLi</span><span>{$appTitle}</span></a>
    <nav>
      <a href="/">Dashboard</a>
      <a href="/detector">Detector</a>
      <a href="/paper">Paper</a>
      <a href="/api/summary">API</a>
    </nav>
  </header>
  <main class="page-shell">{$body}</main>
</body>
</html>
HTML;
}

function renderDashboard(array $config, LabRepository $repository): string
{
    $summary = $repository->summary();
    $paper = $config['paper'];
    $classifiers = $repository->classifierProfiles() ?: $config['classifiers'];
    $experiments = $repository->experiments();
    $recent = $repository->recentDetections();
    $classifierCards = renderClassifierCards($classifiers);
    $experimentCards = renderExperiments($experiments);
    $recentCards = renderRecentDetections($recent);
    $dbStatus = $summary['connected'] ? 'MySQL connected' : 'MySQL not connected';
    $detectorCount = (string) ($summary['detection_count'] ?? 0);
    $classifierCount = (string) ($summary['classifier_count'] ?: count($config['classifiers']));
    $featureCount = (string) ($summary['feature_count'] ?: count($config['feature_definitions']));
    $paperTitle = View::e((string) $paper['title']);
    $doiUrl = View::e((string) $paper['url']);
    $tagline = View::e((string) $config['portal']['tagline']);

    return <<<HTML
<section class="hero panel">
  <div>
    <p class="eyebrow">SQL injection research portal</p>
    <h1>Machine learning inspired SQL injection detection lab.</h1>
    <p>{$tagline}</p>
    <div class="hero-actions">
      <a class="button-link" href="/detector">Analyze SQL statement</a>
      <a class="secondary-link" href="{$doiUrl}" target="_blank" rel="noopener">Open paper DOI</a>
    </div>
  </div>
  <aside class="paper-card">
    <span>Research reference</span>
    <strong>{$paperTitle}</strong>
    <small>ICECTA 2019, IEEE, DOI {$paper['doi']}</small>
  </aside>
</section>
<section class="metric-grid">
  <article><span>Paper dataset</span><strong>{$paper['dataset_size']}</strong><small>SQL statements reported</small></article>
  <article><span>Classifiers</span><strong>{$classifierCount}</strong><small>{$paper['classifiers_evaluated']} evaluated in the paper</small></article>
  <article><span>Feature signals</span><strong>{$featureCount}</strong><small>Transparent extraction model</small></article>
  <article><span>Detections</span><strong>{$detectorCount}</strong><small>{$dbStatus}</small></article>
</section>
<section class="section-head"><h2>Reported Top Classifiers</h2><a href="/paper">Citation details</a></section>
<section class="classifier-grid">{$classifierCards}</section>
<section class="split-layout">
  <div>
    <section class="section-head"><h2>Research Experiments</h2><a href="/detector">Run detector</a></section>
    <div class="stack">{$experimentCards}</div>
  </div>
  <aside class="panel">
    <h2>Recent Detections</h2>
    {$recentCards}
  </aside>
</section>
HTML;
}

function renderDetector(array $config, LabRepository $repository): string
{
    $csrf = Csrf::field();
    $samples = renderSamples($repository->samples());
    $warning = $repository->connected() ? '' : '<div class="notice warning">MySQL is not connected. Detection works, but results are not persisted.</div>';

    return <<<HTML
{$warning}
<section class="panel form-panel">
  <p class="eyebrow">Detector interface</p>
  <h1>Analyze a SQL statement</h1>
  <p class="muted">The detector extracts syntax features and compares the resulting risk score against the top classifier profiles reported in the paper.</p>
  <form method="post" action="/detector">
    {$csrf}
    <label>SQL statement <textarea required name="statement" rows="7" maxlength="5000" placeholder="Example: SELECT id, name FROM users WHERE id = ?"></textarea></label>
    <button type="submit">Analyze statement</button>
  </form>
</section>
<section class="section-head"><h2>Seed Samples</h2><span>For local demonstration</span></section>
<section class="sample-grid">{$samples}</section>
HTML;
}

function renderDetectionResult(array $config, LabRepository $repository, string $statement, array $analysis, ?string $uuid): string
{
    $label = View::e((string) $analysis['consensus_label']);
    $score = number_format((float) $analysis['risk_score'], 2);
    $statementHtml = View::e($statement);
    $uuidText = $uuid ? '<p class="muted">Saved detection ID: ' . View::e($uuid) . '</p>' : '<p class="muted">Database not connected. Result was not persisted.</p>';
    $features = '';
    foreach ($analysis['features'] as $key => $value) {
        $features .= '<article><span>' . View::e((string) $key) . '</span><strong>' . View::e((string) $value) . '</strong></article>';
    }

    $predictions = '';
    foreach ($analysis['predictions'] as $prediction) {
        $predictions .= '<article class="prediction"><strong>' . View::e((string) $prediction['classifier']) . '</strong><span>' . View::e((string) $prediction['label']) . '</span><small>Score ' . View::e((string) $prediction['score']) . ' / threshold ' . View::e((string) $prediction['threshold']) . '</small></article>';
    }

    return <<<HTML
<section class="panel result-panel">
  <p class="eyebrow">Detection result</p>
  <h1>Consensus: {$label}</h1>
  <p>Risk score: <strong>{$score}%</strong>. Votes: {$analysis['injected_votes']} of {$analysis['total_votes']} classifier profiles flagged the statement as injected.</p>
  {$uuidText}
  <pre><code>{$statementHtml}</code></pre>
</section>
<section class="section-head"><h2>Extracted Features</h2><a href="/detector">Analyze another</a></section>
<section class="feature-grid">{$features}</section>
<section class="section-head"><h2>Classifier Profile Output</h2><span>Research-aligned comparison</span></section>
<section class="classifier-grid">{$predictions}</section>
HTML;
}

function renderPaper(array $config, LabRepository $repository): string
{
    $paper = $config['paper'];
    $authors = View::e(implode(', ', $paper['authors']));
    $title = View::e((string) $paper['title']);
    $venue = View::e((string) $paper['venue']);
    $doi = View::e((string) $paper['doi']);
    $url = View::e((string) $paper['url']);
    $featureCards = '';
    foreach ($config['feature_definitions'] as $feature) {
        $featureCards .= '<article class="panel dimension-card"><span>' . View::e((string) $feature['key']) . '</span><h3>' . View::e((string) $feature['label']) . '</h3><p>' . View::e((string) $feature['description']) . '</p></article>';
    }

    return <<<HTML
<section class="panel paper-detail">
  <p class="eyebrow">Paper alignment</p>
  <h1>{$title}</h1>
  <p><strong>Authors:</strong> {$authors}</p>
  <p><strong>Venue:</strong> {$venue}, {$paper['publisher']}, {$paper['year']}</p>
  <p><strong>DOI:</strong> <a href="{$url}" target="_blank" rel="noopener">{$doi}</a></p>
  <p>The portal follows the paper's research path: collect SQL statements, extract features, compare classifiers, select the strongest models, and provide a practical detector interface.</p>
</section>
<section class="metric-grid">
  <article><span>Dataset</span><strong>{$paper['dataset_size']}</strong><small>SQL statements</small></article>
  <article><span>Classifiers</span><strong>{$paper['classifiers_evaluated']}</strong><small>Compared in the paper</small></article>
  <article><span>Best accuracy</span><strong>{$paper['reported_best_accuracy']}%</strong><small>Reported result</small></article>
  <article><span>Portal model</span><strong>5</strong><small>Top classifier profiles</small></article>
</section>
<section class="section-head"><h2>Feature Extraction Signals</h2><span>Transparent detector basis</span></section>
<section class="dimension-grid">{$featureCards}</section>
HTML;
}

function renderClassifierCards(array $classifiers): string
{
    $html = '';
    foreach ($classifiers as $classifier) {
        $name = View::e((string) ($classifier['name'] ?? 'Classifier'));
        $accuracy = View::e((string) ($classifier['accuracy'] ?? $classifier['accuracy'] ?? ''));
        $tp = View::e((string) ($classifier['tp_rate'] ?? ''));
        $tn = View::e((string) ($classifier['tn_rate'] ?? ''));
        $html .= "<article class=\"panel classifier-card\"><span>Accuracy {$accuracy}%</span><h3>{$name}</h3><p>TP rate {$tp}% / TN rate {$tn}%</p></article>";
    }

    return $html;
}

function renderExperiments(array $experiments): string
{
    if ($experiments === []) {
        return '<article class="initiative"><strong>Connect MySQL to view seeded experiments.</strong><span>Docker Compose loads the schema and seed data automatically.</span></article>';
    }

    $html = '';
    foreach ($experiments as $experiment) {
        $title = View::e((string) $experiment['title']);
        $objective = View::e((string) $experiment['objective']);
        $status = View::e((string) $experiment['status']);
        $html .= "<article class=\"initiative\"><div><strong>{$title}</strong><span>{$objective}</span></div><span class=\"badge\">{$status}</span></article>";
    }

    return $html;
}

function renderRecentDetections(array $detections): string
{
    if ($detections === []) {
        return '<p class="muted">No detection events are stored yet.</p>';
    }

    $html = '<div class="recent-list">';
    foreach ($detections as $row) {
        $label = View::e((string) $row['consensus_label']);
        $score = number_format((float) $row['risk_score'], 2);
        $preview = View::e((string) $row['statement_preview']);
        $html .= "<div><strong>{$label} / {$score}%</strong><span>{$preview}</span></div>";
    }

    return $html . '</div>';
}

function renderSamples(array $samples): string
{
    if ($samples === []) {
        return '<article class="panel"><p class="muted">Connect MySQL to view seeded SQL samples.</p></article>';
    }

    $html = '';
    foreach ($samples as $sample) {
        $label = View::e((string) $sample['label']);
        $statement = View::e((string) $sample['statement_text']);
        $html .= "<article class=\"panel sample-card\"><span>{$label}</span><pre><code>{$statement}</code></pre></article>";
    }

    return $html;
}
