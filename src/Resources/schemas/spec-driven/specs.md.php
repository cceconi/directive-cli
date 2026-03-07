<?php

/** @var string $projectName */
/** @var string $changeName */
/** @var string $projectContext */

return <<<TEMPLATE
# Specs: {$changeName}

> **Project:** {$projectName}
> **Schema:** spec-driven
> Create one spec file per capability: `specs/<capability-id>/spec.md`

---

{$projectContext}

---

## Spec template

Each spec file (`specs/<id>/spec.md`) must follow this structure:

```markdown
## ADDED|MODIFIED|REMOVED Requirements

### Requirement: <Title>
<Description> SHALL <behaviour>.

#### Scenario: <Title>
- **WHEN** <condition>
- **THEN** <expected outcome>
```

TEMPLATE;
