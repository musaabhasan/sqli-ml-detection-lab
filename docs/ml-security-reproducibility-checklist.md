# ML Security Experiment Reproducibility Checklist

Use this checklist when extending the SQL injection detection lab with new datasets, feature extractors, classifier profiles, trained model artifacts, or evaluation reports. Security ML experiments are easy to overstate unless the dataset, preprocessing, split strategy, metrics, and operational limits are documented together.

## Experiment Header

| Field | Value |
| --- | --- |
| Experiment name |  |
| Research question |  |
| Dataset version |  |
| Feature extractor version |  |
| Classifier or detector |  |
| Runtime environment |  |
| Evaluator |  |
| Date |  |

## Dataset Provenance

| Check | Evidence | Status |
| --- | --- | --- |
| Dataset source and collection method are documented | Source note or citation |  |
| Benign and injected statement counts are recorded | Dataset summary |  |
| Duplicate and near-duplicate handling is documented | Deduplication log |  |
| Sensitive SQL samples are sanitized before sharing | Redaction review |  |
| Labeling process and reviewer role are described | Labeling protocol |  |
| Known class imbalance is quantified | Class distribution table |  |
| Dataset license or reuse constraints are recorded | License or access note |  |

## Feature Extraction Controls

| Check | Evidence | Status |
| --- | --- | --- |
| Feature definitions are versioned and mapped to implementation | Feature dictionary |  |
| Extractor handles encoding, comments, whitespace, and case normalization consistently | Unit test reference |  |
| Dangerous examples are stored as inert text, not executable SQL | Sample storage review |  |
| Feature changes trigger re-running baselines | Change note |  |
| Feature leakage risk is reviewed | Leakage review |  |
| Feature importance or interpretation is reported cautiously | Model card note |  |

## Split And Evaluation Design

| Check | Evidence | Status |
| --- | --- | --- |
| Train, validation, and test split strategy is documented | Split configuration |  |
| Random seed or deterministic split ID is recorded | Reproduction note |  |
| Similar samples are not split across train and test in a way that inflates performance | Leakage check |  |
| Metrics include false positives and false negatives, not only accuracy | Evaluation table |  |
| Threshold selection is separated from final test reporting | Threshold note |  |
| Baseline heuristic or simple classifier is reported for comparison | Baseline result |  |
| Confidence intervals or repeated runs are included where feasible | Statistical summary |  |

## Security-Specific Reporting

| Metric or evidence | Why it matters | Recorded? |
| --- | --- | --- |
| True positive rate | Shows detection of injected statements |  |
| False positive rate | Indicates operational noise for benign SQL |  |
| False negative examples | Shows bypass patterns and residual risk |  |
| False positive examples | Helps avoid blocking legitimate queries |  |
| Evasion limitations | Clarifies what syntax variants remain hard |  |
| Operational decision threshold | Links score to alert, review, or block behavior |  |
| Human review path | Prevents unsupported automated enforcement |  |

## Model Card Evidence

| Section | Required content |
| --- | --- |
| Intended use | Research, education, triage, or dashboard support |
| Not intended for | Standalone production blocking without layered controls |
| Training data | Dataset version, counts, labeling source, and caveats |
| Features | Extractor version and feature dictionary link |
| Evaluation | Metrics, split strategy, thresholds, and failure examples |
| Limitations | Dataset size, syntax coverage, evasion patterns, and transferability |
| Security controls | Safe sample handling, redaction, audit logging, and review path |

## Reproduction Package

| Artifact | Included? | Location |
| --- | --- | --- |
| Dataset summary or sanitized sample |  |  |
| Feature dictionary |  |  |
| Split configuration |  |  |
| Evaluation script or notebook |  |  |
| Result tables |  |  |
| Model card |  |  |
| Environment details |  |  |
| Known limitations |  |  |

## Claim Calibration

Before publishing or presenting results, confirm:

- Accuracy is not used as the only performance claim.
- Results are described as dataset-specific unless externally validated.
- The detector is framed as support for secure coding and review, not a replacement for prepared statements.
- False negatives are treated as residual risk, not minor errors.
- False positives are discussed as operational workload.
- Any comparison with the referenced paper explains differences in dataset, features, tools, and trained artifacts.
