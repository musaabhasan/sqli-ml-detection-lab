# Dataset Leakage And Label Quality Audit

Use this audit before publishing SQL injection detection results, adding new samples, retraining models, or comparing classifier profiles. SQL security datasets are especially vulnerable to leakage because duplicate payloads, template-generated queries, source labels, filenames, and obvious token artifacts can make a model appear stronger than it is.

## Audit Header

| Field | Value |
| --- | --- |
| Dataset version |  |
| Dataset owner |  |
| Collection window |  |
| Benign sample source |  |
| Injected sample source |  |
| Feature extractor version |  |
| Split strategy |  |
| Reviewer |  |
| Review date |  |

## Leakage Risk Checks

| Leakage Source | Example | Risk | Required Evidence |
| --- | --- | --- | --- |
| Exact duplicates | Same SQL statement appears in train and test | Inflated performance | Duplicate hash report |
| Near duplicates | Same template with one changed literal | Inflated generalization | Normalized-query similarity review |
| Source artifacts | Dataset filename, folder, feed, or export marker included in features | Model learns label source | Feature dictionary review |
| Label tokens | Sample contains comments such as malicious, attack, safe, test | Direct label leakage | Raw sample scan |
| Split contamination | Generated payload families split across train and test | Family memorization | Family-aware split summary |
| Preprocessing leakage | Normalization uses full dataset statistics before split | Test information leaks into training | Pipeline order evidence |
| Threshold leakage | Threshold selected on final test set | Overfit decision boundary | Separate validation record |
| Time leakage | Future attack patterns appear in training for a historical test | Unrealistic retrospective score | Time-aware split decision |

## Label Quality Review

| Check | Evidence | Status |
| --- | --- | --- |
| Label source is documented for each sample family | Source register |  |
| Benign queries come from realistic application patterns | Benign source note |  |
| Injected queries include multiple syntax families and bypass styles | Attack family summary |  |
| Ambiguous samples have reviewer notes rather than forced labels | Ambiguity register |  |
| Samples requiring execution context are flagged as context-dependent | Context note |  |
| Multi-statement, encoded, commented, and whitespace variants are reviewed | Variant coverage note |  |
| Reviewer disagreement is recorded and resolved | Label adjudication note |  |
| Sensitive production queries are sanitized without changing detection-relevant syntax | Redaction evidence |  |

## SQL Injection Family Coverage

| Family | Present? | Notes |
| --- | --- | --- |
| Tautology and boolean-based injection |  |  |
| UNION-based injection |  |  |
| Error-based injection |  |  |
| Time-based or blind injection |  |  |
| Stacked queries |  |  |
| Comment and whitespace obfuscation |  |  |
| Encoded payloads |  |  |
| Stored procedure or database-specific syntax |  |  |
| Benign complex reporting queries |  |  |
| Benign ORM-generated queries |  |  |

## Leakage Tests To Run

| Test | Expected Output |
| --- | --- |
| Exact hash duplicate check | Count and list of duplicated normalized statements |
| Near-duplicate similarity review | Clusters with high token or AST similarity |
| Family-aware split check | Confirmation that payload families do not cross train/test boundaries where possible |
| Label token scan | Any comments, filenames, fields, or metadata that reveal labels |
| Feature-only baseline | Detect whether simple source artifacts explain labels |
| Train/test source distribution check | Source proportions by split and class |
| Threshold selection trace | Validation split used for threshold, final test held out |

## Label Decision Matrix

| Label State | Treatment | Reporting Rule |
| --- | --- | --- |
| Confirmed benign | Keep as benign | Document source and realism |
| Confirmed injected | Keep as injected | Document family and reviewer basis |
| Context-dependent | Keep only with context flag or exclude from primary metric | Report separately |
| Ambiguous | Exclude from final test or route to adjudication | Do not force into headline score |
| Synthetic template | Keep only if template family is split carefully | Report synthetic nature |
| Sensitive production query | Redact and revalidate syntax impact | Preserve detection-relevant features |

## Release Gate

| Gate | Minimum Evidence |
| --- | --- |
| Deduplication | Exact and near-duplicate review completed |
| Label provenance | Source and reviewer basis recorded |
| Split defensibility | Split avoids family, source, and threshold leakage |
| Feature leakage | Feature dictionary excludes source and label artifacts |
| Variant coverage | Attack and benign families are summarized |
| Error analysis | False positives and false negatives reviewed by SQL family |
| Claim calibration | Results described as dataset-specific unless externally validated |

## Reporting Notes

- Report dataset size with class counts, source types, and collection window.
- State whether samples are synthetic, production-derived, public-source, or mixed.
- Explain deduplication and split strategy before reporting accuracy.
- Include false positive and false negative examples by SQL family.
- Avoid claiming production readiness without validation on application-specific traffic.
- Keep the detector positioned as research and triage support, not a replacement for prepared statements and least-privilege controls.
