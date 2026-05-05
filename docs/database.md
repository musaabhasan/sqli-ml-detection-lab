# Database Schema

The database uses MySQL 8.0 with `utf8mb4` collation.

## Tables

- `feature_definitions`: feature keys, descriptions, and scoring weights.
- `classifier_profiles`: top classifier model cards and reported metrics.
- `sample_statements`: local benign and injected examples for demonstration.
- `detection_events`: submitted statement hash, preview, risk score, consensus label, feature JSON, and prediction JSON.
- `experiments`: research improvement backlog and dataset/model experiments.
- `audit_events`: activity trail for saved detections.

## Data Handling

Submitted SQL can contain sensitive application structure, table names, or field names. The portal stores only a hash and preview of the statement by default, plus extracted features and prediction evidence.

Before production use, define retention, redaction, role access, and export policies.
