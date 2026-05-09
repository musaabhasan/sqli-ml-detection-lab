# SQLi WAF Bypass Mutation Evaluation Protocol

SQL injection models can look strong on canonical payloads and fail on equivalent variants that use encoding, comments, whitespace, keyword splitting, case changes, parameter pollution, or dialect-specific syntax. Use this protocol to evaluate model robustness against semantics-preserving SQLi mutations in an authorized, offline, non-exploitative research setting.

## Scope and Safety Rules

This protocol is for defensive model evaluation and dataset quality improvement only.

- Use sanitized payload corpora and offline test harnesses.
- Do not run mutated payloads against systems without written authorization.
- Do not include real customer queries, secrets, production database names, or live URLs in test artifacts.
- Preserve labels, mutation lineage, and reviewer confidence for reproducibility.
- Treat submitted SQL samples as sensitive security evidence.

## Objectives

- Measure whether model predictions remain stable across equivalent malicious variants.
- Detect over-reliance on brittle tokens such as exact keyword casing or fixed comment strings.
- Identify false positives caused by benign queries that contain uncommon syntax.
- Separate parser normalization weaknesses from classifier weaknesses.
- Improve reporting by showing robustness by mutation family, not only aggregate accuracy.

## Baseline Payload Record

| Field | Description |
| --- | --- |
| Payload ID | Stable identifier for the original sample |
| Label | Malicious / benign / ambiguous |
| SQL family | UNION, boolean, time-based, stacked, error-based, authentication bypass, metadata probing, benign |
| Dialect | MySQL, PostgreSQL, SQL Server, Oracle, SQLite, generic |
| Source | Public dataset, synthetic, lab-generated, reviewed sample |
| Reviewer confidence | High / medium / low |
| Sensitive data removed | Yes / no |
| Mutation allowed | Yes / no |

## Mutation Families

| Family | Defensive Test Purpose | Example Transformation Type |
| --- | --- | --- |
| Case variation | Detect brittle keyword matching | Uppercase, lowercase, mixed case |
| Whitespace variation | Detect over-reliance on spaces | Tabs, newlines, repeated spaces, removed optional spaces |
| Comment insertion | Test comment-aware normalization | Inline comments between tokens or at token boundaries |
| Encoding | Test URL, HTML, Unicode, or escaped-character normalization | Percent encoding, HTML entities, escaped quotes |
| Operator variation | Test equivalent logical expressions | Alternative comparison forms or boolean grouping |
| Keyword splitting | Test token reconstruction | Split SQL keywords with comments or whitespace |
| String literal variation | Test quote and escape handling | Single quotes, doubled quotes, escaped characters |
| Parameter context | Test query-string and form encoding effects | Reordered parameters, duplicate keys, encoded delimiters |
| Dialect variation | Test database-specific syntax | Function names, concatenation, comment style, limit syntax |
| Benign hard cases | Reduce false positives | Legitimate search, reporting, analytics, and migration queries |

Avoid publishing mutation examples that are directly reusable against live systems. Store concrete payloads in restricted test fixtures and publish aggregate findings.

## Evaluation Dataset Design

| Slice | Required Coverage | Purpose |
| --- | --- | --- |
| Canonical malicious | Original SQLi families before mutation | Baseline detection |
| Mutated malicious | At least three mutation families per SQLi family | Robustness under evasion pressure |
| Benign simple | Normal application queries | Baseline false-positive rate |
| Benign complex | Reports, analytics, migrations, nested queries | False-positive stress test |
| Ambiguous or weak-label | Samples with uncertain source or intent | Label governance and exclusion decisions |
| Dialect-specific | MySQL, PostgreSQL, SQL Server, Oracle, SQLite where relevant | Parser and feature portability |

Split rules:

- Keep all variants of one baseline payload in the same train/test group unless the experiment explicitly tests variant generalization.
- Do not place a canonical payload in training and its mutation in test when reporting headline generalization metrics.
- Split by source family, collection period, or payload lineage to reduce leakage.
- Report mutation lineage counts so reviewers can detect inflated sample sizes.

## Feature and Normalization Review

| Review Area | Question | Evidence |
| --- | --- | --- |
| Token normalization | Are comments, case, and whitespace normalized before feature extraction? | Extractor output sample |
| Encoding handling | Are URL or HTML encodings decoded in a controlled order? | Normalization trace |
| Dialect awareness | Does the extractor handle dialect-specific comments and functions? | Dialect test set |
| Benign hard cases | Do analytics and migration queries trigger injection-like features? | False-positive review |
| Mutation lineage | Can every variant be traced to a baseline payload? | Manifest |
| Label stability | Does the mutation preserve malicious or benign semantics? | Reviewer note |

## Metrics to Report

| Metric | Purpose |
| --- | --- |
| Detection rate by SQLi family | Shows which attack styles remain detectable |
| Detection rate by mutation family | Identifies brittle feature assumptions |
| False-positive rate on benign complex queries | Measures practical deployment burden |
| Prediction stability | Compares canonical payload prediction with mutated variants |
| Confidence shift | Shows whether scores degrade even when labels remain correct |
| Leakage-adjusted performance | Removes inflated results from near-duplicate variants |
| Reviewer confidence distribution | Separates strong labels from ambiguous samples |

## Evaluation Procedure

1. Select a reviewed baseline corpus with labels and SQL families.
2. Remove secrets, production identifiers, and sensitive business data.
3. Generate or curate mutation variants under controlled, documented rules.
4. Record mutation lineage, family, reviewer, and semantic-preservation decision.
5. Run the existing feature extractor on canonical and mutated samples.
6. Score samples using the same model, threshold, and extractor version.
7. Report metrics by SQLi family and mutation family.
8. Review false negatives and false positives with sample-level evidence.
9. Decide whether to adjust normalization, features, thresholds, labels, or training data.
10. Preserve manifests and aggregate findings for reproducibility.

## Finding Categories

| Finding | Meaning | Required Action |
| --- | --- | --- |
| Mutation collapse failure | Equivalent variants produce very different feature vectors | Improve normalization or add parser-aware features |
| Evasion-sensitive model | Detection drops sharply for one mutation family | Add training coverage or revise features |
| Benign syntax false positives | Complex legitimate SQL is misclassified | Add benign hard cases and review thresholds |
| Dialect blind spot | Detection relies on one database syntax family | Add dialect-aware features and test slices |
| Leakage inflation | Variants leak between train and test | Re-split by lineage and report corrected metrics |
| Label drift | Mutation changes semantics or becomes ambiguous | Exclude or relabel with reviewer evidence |

## Release Decision

| Decision | Criteria |
| --- | --- |
| Ready | Detection and false-positive rates remain acceptable across target mutation and dialect slices |
| Ready with limitations | Known weak family is documented and covered by compensating controls |
| Normalization update required | Feature extraction is brittle to encoding, comments, case, or whitespace |
| Training update required | Model needs broader malicious and benign hard-case coverage |
| Not suitable for operational use | Performance depends on canonical payloads and fails realistic variants |

## Evidence Package

Retain:

- baseline corpus manifest,
- mutation rule manifest,
- mutation lineage mapping,
- extractor version,
- model or threshold version,
- metrics by SQLi family,
- metrics by mutation family,
- false-positive and false-negative review notes,
- leakage review,
- release decision.

## Reporting Guidance

Report aggregate mutation families and defensive findings. Avoid publishing step-by-step payload strings that would materially help bypass a live control. When examples are needed for peer review, keep them in restricted fixtures and redact application-specific identifiers.
