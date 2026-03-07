# Directive CLI

Directive CLI is the developer experience tool for [Directive](https://directivephp.com) projects.  
One command scaffolds a full hexagonal project structure, AI context files, and IDE prompts ready to go.

---

## Requirements

- PHP 8.4+
- Composer

---

## Installation

Install once globally on your machine:

```bash
composer global require directive/cli
```

Verify:

```bash
directive --version
```

> **Fallback**: If `directive` conflicts with an existing binary on your system, use `directive-cli` instead — it is installed automatically alongside `directive`.

---

## Commands

| Command | Description |
|---|---|
| `directive new <project-name>` | *(coming soon)* Scaffold a new Directive project |
| `directive update-ide` | *(coming soon)* Regenerate IDE context files |
| `directive upgrade` | *(coming soon)* Get AI migration instructions for a new Directive version |

---

## License

MIT — © Christian CECONI
