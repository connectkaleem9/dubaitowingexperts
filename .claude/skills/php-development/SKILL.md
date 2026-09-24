---
name: php-development
description: Implement PHP features in the project's MVC structure.
---
# Php Development

Read CLAUDE.md before using this skill.

## Purpose
Secure, maintainable server code.

## Inputs
- Feature spec
- CLAUDE.md sections 4-5, 9

## Process
1. Route -> Controller -> Validator -> Model/Service -> View.
2. strict_types, typed signatures.
3. Handle errors; log, don't display.

## Rules
- SQL only in Models with bound params.
- No superglobals outside Request.
- No secrets in code.

## Output
app/, admin/, routes/

## Validation checklist
- [ ] php -l passes
- [ ] tests/run.php passes
- [ ] Security checklist items for the feature pass
