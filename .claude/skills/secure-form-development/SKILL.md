---
name: secure-form-development
description: Build public or admin forms that are safe and accessible.
---
# Secure Form Development

Read CLAUDE.md before using this skill.

## Purpose
Collect input without spam, injection or accessibility problems.

## Inputs
- Field list
- Validation rules

## Process
1. CSRF token + honeypot + min-time trap + rate limit.
2. Server-side Validator.
3. Label every field; aria-describedby errors.
4. PRG pattern (redirect after POST).
5. Fire form_submit event on success page.

## Rules
- Never trust client validation.
- Re-display input escaped.
- Store, then notify (email) - storage must not depend on mail.

## Output
Form partial + controller action + model insert

## Validation checklist
- [ ] Missing/invalid CSRF -> 419
- [ ] Honeypot filled -> silently dropped
- [ ] XSS payload renders inert
- [ ] Keyboard-only completion works
