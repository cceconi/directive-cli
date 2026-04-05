---
title: "Slash Commands — Context"
description: "Reference for /dtsx-project, /dtsx-stack, /dtsx-discuss, /dtsx-evaluate, /dtsx-kickoff — commands that define and refine project context."
group: context
commands: [dtsx-project, dtsx-stack, dtsx-discuss, dtsx-evaluate, dtsx-kickoff]
nav_order: 10
---

# Slash Commands — Context

These commands define and enrich project context. They have no CLI equivalent: enriching `directive-spec/context/common.yaml` requires conversational judgment — the agent asks questions, interprets answers, and writes directly.

---

### `/dtsx-project`

**Description** — Defines the project's functional domain, users, and bounded contexts. Initialises or revises `directive-spec/context/common.yaml`.

**Input** — No argument. The agent starts the conversation.

**Preconditions** — None. Can be invoked at any time, including on an existing project to revise context.

**Workflow**
1. The agent asks questions about the business domain, user types, and bounded contexts
2. The agent clarifies non-functional constraints (security, performance, scalability) if relevant
3. The agent writes the result to `directive-spec/context/common.yaml` under the `domain`, `users`, and `contexts` keys

**CLI invoked** — None. The agent writes directly.

**Guardrails**
- If `common.yaml` already exists, the agent proposes updates while preserving unmodified existing keys
- The agent asks for confirmation before overwriting existing content

---

### `/dtsx-stack <tech>`

**Description** — Adds or updates stack rules for a technical component. The agent analyses the technology and writes concrete implementation rules to a dedicated `directive-spec/context/<tech>.yaml` file, then adds a reference to it in `directive-spec/context/common.yaml`.

**Input** — `<tech>` (optional): component or technology name (e.g. `postgresql`, `redis`, `symfony`). If absent, the agent asks which stack to analyse.

**Preconditions** — None. Usable at any point, including mid-project when adding a new technology.

**Workflow**
1. The agent identifies the technology (from the argument or via a question)
2. The agent analyses the stack: versions, conventions, recommended patterns, anti-patterns to avoid
3. The agent writes rules to `directive-spec/context/<tech>.yaml` (e.g. `php.yaml`, `postgresql.yaml`)
4. The agent adds or updates a reference to that file in `directive-spec/context/common.yaml`
5. These rules are reused in every change to guide implementation

**CLI invoked** — None. The agent writes directly.

**Guardrails**
- Never overwrite existing rules without presenting them to the user first
- If the technology is unknown or ambiguous, ask for clarification before writing

---

### `/dtsx-discuss`

**Description** — Conversational intent capture mode. The agent listens, structures, and reformulates what the user wants to build — without breaking things down into changes yet. Produces a structured `discussion.md`.

**Input** — No argument. The agent engages in open conversation.

**Preconditions** — None. Typically used at the start of a project or before a series of changes.

**Workflow**
1. The agent opens a conversation about intent (features, constraints, priorities)
2. The agent may ask clarifying questions and explore alternatives
3. The agent structures the exchange into a `discussion.md` at `directive-spec/brainstorm/<slug>/discussion.md`
4. The discussion can be resumed and enriched with `/dtsx-discuss` in the same session

**CLI invoked** — None. The agent writes directly.

**Guardrails**
- Do not break down into changes or create technical artifacts during this phase
- If the user already wants to implement, suggest `/dtsx-new` or `/dtsx-propose`

---

### `/dtsx-evaluate [discussion]`

**Description** — Analyses an existing discussion and breaks it down into an ordered, sequenced list of changes — each with a name, description, and dependencies.

**Input** — `[discussion]` (optional): slug of the discussion to evaluate (e.g. `2024-01-auth-rework`). If absent, the agent lists available discussions.

**Preconditions** — A `discussion.md` must exist in `directive-spec/brainstorm/`. Can also be invoked if the user describes verbally what they want evaluated.

**Workflow**
1. The agent reads the relevant `discussion.md`
2. The agent identifies functional entities, technical dependencies, and logical delivery order
3. The agent proposes an ordered list of changes with kebab-case name, description, and dependencies
4. The user can adjust the list before confirming
5. The agent writes the final list to `directive-spec/brainstorm/<slug>/changes-list.md`

**CLI invoked** — None. The agent writes directly.

**Guardrails**
- Present the list to the user before writing it
- If the discussion is too vague to break down, start a new `/dtsx-discuss` cycle

---

### `/dtsx-kickoff [discussion]`

**Description** — Creates all changes from an evaluated list in one pass, invoking `directive change:new` for each.

**Input** — `[discussion]` (optional): slug of the discussion / change list to create. If absent, the agent lists available `changes-list.md` files.

**Preconditions** — An evaluated `changes-list.md` must exist in `directive-spec/brainstorm/<slug>/`. Alternatively, the agent can create changes from a verbally provided list.

**Workflow**
1. The agent reads the relevant `changes-list.md`
2. The agent presents the list of changes to be created and asks for confirmation
3. For each change in order: `directive change:new <name>`
4. The agent displays a summary of created changes

**CLI invoked**
- `directive change:new <name>` (× N changes)

**Guardrails**
- Always ask for confirmation before creating the changes
- If a change with that name already exists, report it and ask how to proceed (skip / rename)
- Do not generate artifacts (proposal, design, etc.) — that is the role of `/dtsx-new` or `/dtsx-propose`
