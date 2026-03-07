<?php

/** @var string $projectName */
/** @var string $changeName */
/** @var string $projectContext */

return <<<TEMPLATE
# Design: {$changeName}

> **Project:** {$projectName}
> **Schema:** spec-driven

---

{$projectContext}

---

## Context

<!-- Brief description of the current state of the codebase relevant to this change. -->

## Goals / Non-Goals

**Goals:**
-

**Non-Goals:**
-

## Decisions

### D1 — <!-- Decision title -->

<!-- Rationale and alternatives considered. -->

## Risks / Trade-offs

- **[Risk]** <!-- Description → Mitigation -->

TEMPLATE;
