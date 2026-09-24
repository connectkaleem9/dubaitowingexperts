---
name: image-optimization
description: Process and serve images efficiently.
---
# Image Optimization

Read CLAUDE.md before using this skill.

## Purpose
Fast, SEO-friendly images.

## Inputs
- Uploaded or source images

## Process
1. Validate MIME via finfo.
2. Re-encode with GD to WebP (fallback JPEG) at 1600/800/400 widths.
3. Descriptive slug filenames.
4. Store width/height; output srcset + sizes.
5. Lazy-load below the fold.

## Rules
- Max upload 8 MB.
- Strip EXIF (privacy: location).
- Alt text required in admin.

## Output
Uploader service output + media rows

## Validation checklist
- [ ] WebP served
- [ ] Dimensions set (no CLS)
- [ ] Alt text present
