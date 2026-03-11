# Directive CLI

Directive is a PHP framework for AI-assisted development — a structured way to build software with a language model as your co-developer, working from specs rather than vibes.

---

## Why PHP

I love PHP. Not despite its reputation — because of what it actually is today: a fast, expressive, modern language that runs most of the web and keeps evolving. Directive is my bet that PHP has a strong future in the AI-assisted development era, and a way to help make that true.

## Why embed AI workflow in the framework

Integrating the workflow directly into the framework is the shortest path from "I want to use AI to write code" to "it actually works reliably in my project." No separate toolchain to configure, no conventions to invent — just install and start. And nothing stops anyone from porting this model to any other language or framework they love.

---

## How it works

Directive follows a spec-driven development cycle:

1. **Set project context** — describe your domain, stack, and conventions once; the agent reuses this in every change.
2. **Brainstorm** *(optional)* — explore ideas conversationally, then break them into scoped, sequenced changes.
3. **Create a change** — structured artifacts (proposal → design → specs → tasks) guide the agent through planning before any code is written.
4. **Implement** — the agent works through tasks one by one, writing code against the specs, running tests and static analysis as it goes.
5. **Verify & archive** — validate that implementation matches specs, sync to the main spec repository, and archive the change.

---

## Slash Commands

Slash commands are invoked in your IDE chat (e.g. GitHub Copilot, Cursor). They work hand-in-hand with the CLI — the agent calls `directive` commands internally and orchestrates the workflow on your behalf.

| Command | Description |
|---|---|
| `/dtsx-project` | Define project domain, users, and bounded contexts |
| `/dtsx-stack <tech>` | Add or update technical stack rules for the agent |
| `/dtsx-discuss` | Capture and structure intent conversationally (brainstorm) |
| `/dtsx-evaluate [discussion]` | Break a discussion into ordered, scoped changes |
| `/dtsx-kickoff [discussion]` | Create all changes from a brainstorm in one shot |
| `/dtsx-new <name>` | Create a change and draft the proposal |
| `/dtsx-continue [<change>]` | Write the next artifact (design → specs → tasks) |
| `/dtsx-propose <name>` | Fast-track: create a change with all artifacts in one pass |
| `/dtsx-apply [<change>]` | Implement tasks (code + tests) |
| `/dtsx-verify [<change>]` | Check implementation against specs and design |
| `/dtsx-reflect [<change>]` | Update artifacts to match implementation reality |
| `/dtsx-sync [<change>]` | Merge delta specs into main specs, keep change active |
| `/dtsx-archive [<change>]` | Sync specs and archive a completed change |

---

## CLI Commands

| Command | Description |
|---|---|
| `directive change:new <name>` | Create a new change directory with scaffolded artifacts |
| `directive change:list` | List active changes and their status |
| `directive change:status <name>` | Show artifact completion status for a change |
| `directive change:instructions <artifact> --change <name>` | Get template and rules for an artifact |
| `directive change:archive <name>` | Move a change to the archive |

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

## Acknowledgements

Directive's spec-driven workflow is inspired by [OpenSpec](https://github.com/openspec-dev/openspec), an excellent tool for AI-assisted development built around structured artifact workflows. Directive is an independent implementation — not a fork — adapted for the PHP ecosystem and licensed under MIT, as OpenSpec is.

---

## License

MIT
