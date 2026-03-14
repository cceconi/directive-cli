<?php

/** @var string $projectName */
/** @var string $changeName */
/** @var string $projectContext */

return <<<TEMPLATE
# Tasks: {$changeName}

> **Project:** {$projectName}
> **Schema:** spec-driven

---

{$projectContext}

---

## Group 1 — <!-- Group title -->

- [ ] 1.1 <!-- Task description -->
- [ ] 1.2 <!-- Task description -->

## Group 2 — <!-- Group title -->

- [ ] 2.1 <!-- Task description -->

## Validation

- [ ] V1 Run full test suite — 0 failures
- [ ] V2 Run static analysis — 0 errors

TEMPLATE;
