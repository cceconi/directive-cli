<?php

/** @var string $projectName */
/** @var string $changeName */
/** @var string $projectContext */

return <<<TEMPLATE
# Proposal: {$changeName}

> **Project:** {$projectName}
> **Schema:** spec-driven

---

{$projectContext}

---

## Why

<!-- Describe the problem or opportunity this change addresses. -->

## What Changes

<!-- List the high-level changes: new capabilities, modified behaviour, removed concepts. -->

## Capabilities

<!-- One row per capability. Use ADDED / MODIFIED / REMOVED prefix. -->

| # | Type | Capability | Description |
|---|------|-----------|-------------|
| 1 | ADDED | | |

## Impact

<!-- Who/what is affected? Breaking changes? Migration needed? -->

TEMPLATE;
