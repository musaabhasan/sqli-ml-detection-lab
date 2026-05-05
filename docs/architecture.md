# Architecture

SQLi ML Detection Lab uses a small PHP service-and-repository architecture.

## Layers

- `public/index.php`: routing, rendering, CSRF validation, JSON endpoints, and detector submission handling.
- `src/Service/SqlFeatureExtractor.php`: statement feature extraction.
- `src/Service/DetectorService.php`: classifier-profile comparison and consensus labeling.
- `src/Repository/LabRepository.php`: database persistence through PDO prepared statements.
- `config/paper.php`: paper metadata, feature definitions, and top classifier profiles.
- `database/migrations` and `database/seeders`: repeatable MySQL setup.

## Main Routes

- `/`: dashboard with paper metrics, model cards, experiments, and recent detections.
- `/detector`: SQL statement detector interface.
- `/paper`: paper citation, workflow alignment, and feature definitions.
- `/health`: liveness endpoint.
- `/api/summary`: JSON summary for reporting integrations.
- `/api/detect`: POST endpoint for detector integrations.

## Detection Flow

1. A SQL statement is submitted.
2. The feature extractor calculates weighted syntax features.
3. The detector calculates an aggregate risk score.
4. The score is compared against the top classifier profiles represented from the paper.
5. A consensus label is produced.
6. If MySQL is connected, features, predictions, and a statement hash are stored as a detection event.

## Design Principle

The project keeps detector logic transparent and explainable so that the research concept can be reviewed, extended, and replaced with trained model artifacts later.
