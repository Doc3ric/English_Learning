# EnglishOS 2.0: Daily Writing Coach (Major Pivot)

This is a significant architecture change on top of the existing 11-phase app. It does NOT delete existing work — it makes the existing modules (Vocabulary, Grammar, Mistakes, Journal) auto-populate from a new central "Daily Writing Coach" loop, instead of requiring manual entry as the primary workflow.

## Core Philosophy Change
Old model: user manually adds words, manually logs mistakes, manually picks lessons — learning is organized by feature (separate folders).
New model: user does ONE daily writing activity. The AI analyzes it and automatically feeds every other module. Manual add forms still exist as a fallback/editing tool, but are no longer the primary way content enters the system.

## New Tech Requirement
This needs meaningfully more AI usage than Phase 5's simple lesson generation — each writing submission requires a single, carefully structured Groq call that returns strict JSON with: corrected text, explanations, extracted vocabulary, detected mistakes (categorized), and four scores (grammar/vocabulary/naturalness/clarity) plus a CEFR estimate. Keep this to ONE API call per submission where possible, not five separate calls, to keep it fast and stay well within Groq's free-tier rate limits.

---

## Sub-Phase 12A: Core Writing Coach Loop (build first, get this solid before anything else)

### New table: `writing_sessions`
- `prompt_topic` (string — the challenge topic given that day)
- `user_response` (long text — what the user wrote)
- `word_count` (integer)
- `ai_corrected_version` (long text)
- `ai_explanation` (long text — why corrections were made)
- `grammar_score`, `vocabulary_score`, `naturalness_score`, `clarity_score` (integers 0-100)
- `cefr_estimate` (string, e.g. "B1")
- `created_at`

### Flow
1. Home screen shows "Today's Writing Challenge" with a topic (start simple: rotate through a fixed list of prompts like "Describe what you did today," "Explain a challenge you faced," "Describe your favorite tool/app and why" — don't over-engineer topic selection yet, that's 12C)
2. User writes response in a textarea (encourage a minimum word count, e.g. 50+ words, but don't hard-block submission)
3. On submit: single Groq API call requesting strict JSON with corrected version, explanation, 4 scores, and CEFR estimate
4. Show results: corrected version side-by-side or below the original, explanation of key corrections, scores displayed clearly
5. Save everything to `writing_sessions`
6. Offer a "Rewrite it yourself" box where the user tries again incorporating the feedback (optional, not required to proceed)

**Test before moving to 12B**: write a real entry, confirm the correction is sensible, confirm scores/CEFR appear, confirm it saves.

---

## Sub-Phase 12B: Auto-Population of Existing Modules

Once 12A is confirmed working, wire the same API response to also populate:

### Journal
- Every writing coach submission IS a journal entry — insert into `journal_entries` automatically (using `user_response` as content, auto word count). The Journal module's existing list/edit/delete views now show these automatically. No separate manual journal entry needed day-to-day (manual "+ New Entry" form stays available for off-cycle writing).

### Vocabulary
- Extend the Groq prompt to also return a short list of "words the user could have used but didn't" (upgrade suggestions) plus any genuinely new/advanced words worth learning from the corrected version
- Auto-insert these into the `vocabulary` table, tagged with a new `source: 'writing_coach'` field so you can tell auto-added words from ones you added yourself
- These show up in the existing "Today's Words" flow — you still write your own example sentence to mark them learned (keeps the active-recall requirement from Phase 2)

### Mistakes
- Extend the Groq prompt to return specific mistakes found, each with: wrong_text, correct_text, reason, category (Grammar/Vocabulary/Pronunciation/Writing)
- Auto-insert into the `mistakes` table, tagged `source: 'writing_coach'`
- These appear automatically in the existing Mistakes Review mode — no manual logging needed for mistakes caught during writing (manual quick-add form stays available for things you notice outside the app, e.g. in a conversation)

**Test before moving to 12C**: submit one writing entry, then check Journal, Vocabulary, and Mistakes tabs — confirm each one now has new auto-generated entries from that single submission.

---

## Sub-Phase 12C: Weakness Tracking + Personalized Grammar

### New logic: Weakness Analysis
- Query `mistakes` grouped by `category` over the last 30 days, ranked by frequency
- Display this as a simple bar breakdown (reuse Chart.js, already in the stack) — e.g. "Past tense: 12 mistakes, Articles: 8 mistakes" — on the Stats & Goals page

### Connect to Grammar Roadmap (modify Phase 5's existing logic)
- When generating the next grammar lesson, include the top 1-2 weakness categories in the Groq prompt: "This learner frequently struggles with articles and past tense — prioritize a lesson on whichever of these they haven't already covered"
- This doesn't remove the sequential unlock system — it just makes the *choice* of which grammar topic comes next smarter

### Connect to Daily Writing Challenge Topic
- Instead of a fixed rotating list (from 12A), have the topic occasionally target a weak area directly: "Today's Challenge: write 150 words and try to avoid article mistakes" — a simple version is fine (pick the #1 weakness category and mention it in the prompt shown to the user)

**Test before moving to 12D**: log or generate enough mistakes in one category, confirm the weakness breakdown chart reflects it, and confirm the next generated grammar lesson or writing challenge references that weakness.

---

## Sub-Phase 12D: Home Dashboard Redesign

Replace the Phase 9 "Today's Mission checklist" with the new primary flow:
- **Today's Writing Challenge** card (topic + Start button) — this is now the main call-to-action, front and center
- **Yesterday's Improvement** — simple comparison of yesterday's 4 scores vs. the day before (or vs. a rolling average), shown as up/down deltas
- **Weakness of the Week** — the top mistake category from 12C, shown prominently
- Keep: current streak, level indicator, weekly goal snapshot from Phase 8 (these still work fine as-is)

**Test before moving to 12E**: confirm the dashboard actually shows real deltas (not the same static values every day) after a few days of writing sessions exist.

---

## Sub-Phase 12E: Rewrite Challenge (Optional Polish)

- After receiving corrections, offer buttons: "Make it Professional" / "Make it Sound Native" — each triggers a follow-up Groq call that rewrites the corrected version in that style
- Show all versions (original → corrected → professional → native) side by side or stacked for comparison
- This is a nice-to-have — build only after 12A-12D are solid and tested

---

## Sub-Phase 12F: AI Memory / Continuity (Optional Polish, build last)

- When generating a new writing challenge topic, include a short summary of the last 1-2 `writing_sessions` entries in the prompt (e.g., topic + one-sentence gist), so the AI can occasionally reference past context: "Yesterday you mentioned buying a new laptop — how has it been?"
- Keep this lightweight — don't try to build a full conversation memory system, just enough continuity to feel personal

---

## What Stays Exactly As-Is (Do Not Touch)
- Reading Tracker (Phase 6) — this remains fully manual, separate from the Writing Coach
- Study Timer (Phase 7) — unchanged
- Achievements (Phase 11) — unchanged, though consider adding 1-2 new achievements later for writing streaks (not required now)
- Timeline + Global Search (Phase 10) — unchanged, though the timeline will now naturally show more entries since writing sessions feed multiple tables

## Explicitly Out of Scope
- Don't remove the manual "Add Word," "Add Mistake," or "New Journal Entry" forms — keep them as a fallback, just no longer the primary daily workflow
- Don't rebuild Grammar's lesson-generation mechanism from scratch — only add weakness-awareness to the existing prompt (12C)
- Don't attempt full multi-turn conversation memory (12F) — a lightweight one-sentence callback is enough
