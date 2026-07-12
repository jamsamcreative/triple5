## Keeping PROJECT_CONTEXT.md Current (MANDATORY)

**Start here:** Read [`PROJECT_CONTEXT.md`](PROJECT_CONTEXT.md) for a high-level briefing on what this project is, its current state, recent work, and key decisions.

`PROJECT_CONTEXT.md` is a living briefing document that every new Claude session reads to understand what is going on. **You must keep it current as you work — not at the end of the session.**

**When to update:** After completing each significant piece of work (fixing a bug, adding a feature, changing architecture, resolving a major issue). If a session crashed right now, PROJECT_CONTEXT.md should reflect everything done so far.

**How to update:** Revise the existing sections — especially "Recent Focus Areas", "What is Working", and "Known Issues". Do not just append — rewrite entries so they represent the current state. Old focus areas that are no longer recent should be dropped or condensed. Keep it under ~100 lines total.

**What NOT to do:** Do not treat this as a changelog. Do not add line items for every small edit. Think of it as: "If a new Claude started right now, what would it need to know?"

---

## Keeping DESIGN_CONTEXT.md Current (when applicable)

If `DESIGN_CONTEXT.md` exists in this project, it loads automatically whenever a prompt or recent file activity involves UI/design. It is the source of truth for **design logic** — the *why* behind colors, typography, components, motion, and a11y choices — not raw token values (those live in code).

**When to update:** The moment you make a design decision. New color used? New component pattern? Rejected a layout direction? Decided that buttons are always filled, never outlined? Capture the decision *and the reasoning* immediately. Do not wait for end of session.

**How to update:** Edit the relevant section directly. Keep entries terse but always include the *why*. If you reject something, log it in "Anti-Patterns" so it doesn't get reintroduced.

**What NOT to do:** Do not duplicate token values that already live in `tailwind.config`, CSS custom properties, or token files — link to them. This file is for the reasoning, conventions, and patterns that aren't expressible as code.
