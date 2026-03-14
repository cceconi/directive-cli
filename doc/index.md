---
title: "Directive — Slash Commands Reference"
description: "Complete reference for all Directive slash commands invoked in IDE chat."
nav_order: 2
---

# Slash Commands Reference

Slash commands are invoked in IDE chat (GitHub Copilot, Cursor, Claude Code, Antigravity). The agent orchestrates the workflow on your behalf: it asks questions, reads and writes files, and calls `directive change:*` CLI commands internally.

→ [Context commands](slash-commands-context.md) — `/dtsx-project`, `/dtsx-stack`, `/dtsx-discuss`, `/dtsx-evaluate`, `/dtsx-kickoff`

→ [Change lifecycle commands](slash-commands-lifecycle.md) — `/dtsx-new`, `/dtsx-continue`, `/dtsx-propose`, `/dtsx-apply`, `/dtsx-verify`, `/dtsx-reflect`, `/dtsx-learn`, `/dtsx-sync`, `/dtsx-archive`, `/dtsx-commit`

---

## All Commands

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
| `/dtsx-learn [<change>]` | Capitalise technical decisions into spec context files |
| `/dtsx-sync [<change>]` | Merge delta specs into main specs, keep change active |
| `/dtsx-archive [<change>]` | Sync specs and archive a completed change |
| `/dtsx-commit [<change>]` | Generate an intermediate conventional commit *(manual mode only)* |

---

## Two-mode interaction model

- **Slash commands** — invoke the IDE agent. The agent orchestrates: asks questions, reads/writes files, calls CLI commands internally.
- **CLI commands** — call `directive <cmd>` in the terminal. Operate on the filesystem without agent involvement. Provide data and instructions that the agent consumes.
