---
name: brainstorm
description: "Turn a rough idea into a shared understanding of what we want to achieve, how we will know it worked, and what it takes to get there. Investigates the codebase first, closes gaps deliberately, opens the direction up to the team on Discord before it is fixed, and produces an outcome document with no code in it. Use whenever the user wants to design, plan, scope or think through something before building it, including casual phrasings like 'I want to add X', 'how would we do Y' or 'ik wil eigenlijk dat...'. Always runs before any implementation."
argument-hint: '[what you want to build]'
disable-model-invocation: true
---

Brainstorm the following: **$ARGUMENTS**

## What this produces

A Discord discussion that other developers can contribute to while the direction
is still open, and a document stating the intended outcome, how we will know it
worked, what it must do, what it takes, and what could go wrong.

It is not an implementation plan and it is not a template to fill in.

## Hard rules

- No code, no code fences, no file paths, no FSD layer, module, store or DTO names,
  neither in the conversation nor in the document. Naming systems, capabilities,
  data and third parties is expected and encouraged.
- Research before asking. Never ask the user anything the codebase can answer.
- Every question carries your own proposed answer, so the user corrects rather
  than composes. This holds for questions to the team as well.
- There is no question limit. Ask until the gap sweep passes, then stop.
- Never publish with a gap you noticed. An open point stated out loud is fine,
  an unnoticed one is a failure of this skill.
- One brainstorm is one Discord thread. Everything about it goes in that thread,
  including later revisions. Never open a second thread for the same subject.
- Cut scope actively and say out loud what you cut and why.
- This skill ends at the published document. Implementation is a separate request.

## Step 1: Investigate

Search and read before you say anything. Work out for yourself:

- whether this already exists, fully or partially
- which existing behaviour and data this would touch
- what already depends on the current behaviour
- which conventions already cover this kind of work
- what is genuinely missing today

Report in at most five lines what you found, in plain language, plus one line
naming what you could not determine from the code.

## Step 2: Frame the problem

State in two or three sentences what you believe the outcome should be, phrased
as a result rather than a solution.

Challenge the premise here if the investigation suggests the real problem is a
different one, or that this already exists in another form. Losing the request is
cheaper than designing the wrong thing. Wait for confirmation or correction
before continuing.

## Step 3: Close the gaps

One question per message. Each question states your own answer first, so the user
only has to confirm or correct: "I'd assume this only applies to logged in users,
correct me."

Ask about intent, behaviour, constraints and consequences. Ask as many as needed.

Never ask which layer, which module, which store, which pattern, which component
or which endpoint shape. You decide those and propose them in step 4.

"Skip" is always a valid answer. If the code already answers it, you should not
have asked.

## Step 4: Propose directions

Give two or three genuinely different routes to the outcome. Different means
different consequences, not the same route renamed.

Per route: what it means in practice, what it costs, what it gives up.

Then one paragraph with your recommendation and the reasoning. Never a neutral
menu. State whether the recommended choice is easily reversible or hard to undo.

"Do not build this" and "this already exists, use it" are legitimate
recommendations and should be given when they are true.

## Step 5: Gap sweep

Before anything is shared or written, walk through this out loud and resolve or
record every hit. This is the step that keeps the document from having holes.

- who is affected besides the person asking
- what happens to the data and the users that already exist today
- what happens when it fails, is unavailable, or is used wrong
- what the first edge case is that breaks the happy path
- what has to be true outside the code: access, accounts, legal, someone else's time
- how we will know afterwards that it actually worked
- which single assumption, if wrong, invalidates the whole direction

Anything unresolved moves to Open points with an owner or a next action. Nothing
stays implicit.

## Step 6: Open it to the team

Do this while the direction can still change, not after. Ask the user for approval
to post, then create the forum thread:

THREAD_ID=$(discord-post -n "<Feature name>" "<review post>")

The review post contains, in this order: the outcome in two sentences, the routes
with their trade-offs, your recommendation and why, and at most three specific
questions. Each question states your current answer, so replying costs a
colleague ten seconds instead of ten minutes. Never ask for "thoughts".

Keep it under 2000 characters. If it does not fit, shorten the trade-offs rather
than dropping the questions, or attach the long version as a file.

Report the thread to the user and continue. Do not wait for replies inside the
session and do not treat silence as agreement: the document records that feedback
was still open at the time of writing.

When the user brings back replies later, re-enter at step 4, revise, and post the
revision into the same thread with -t "$THREAD_ID".

## Step 7: Write the document

Write to:

${TMPDIR:-/tmp}/YYYY-MM-DD-<topic>.md

Never write into the project tree. The repo stays free of planning documents.

Structure, dropping any section that would genuinely be empty:

# <Title>

## Goal

What is true once this is done that is not true now.

## Why now

The trigger or the problem. Omit when obvious.

## Success criteria

How we will know afterwards that it worked. Observable, checkable.

## What it must do

Behaviour as short statements, describable to a non-developer.

## Impact on what exists

Current behaviour, data and users that change or break.

## What we need for it

Capabilities, data, systems, access, third parties, people, decisions.
What has to exist, not how it gets built.

## Risks and failure modes

What goes wrong, how bad it is, whether it is recoverable.

## What we deliberately skip

Cut scope, each with its reason.

## Open points

Each with an owner or a next action, including team feedback not yet received.

Written in English. Length follows the subject: as long as the content requires,
never padded to look complete.

## Step 8: Review and publish

Present the document inline in the conversation. Never tell the user to open a
path. On changes, adjust and present again.

Once approved, publish it into the same thread:

discord-post -t "$THREAD_ID" "**<Title>**: <one line outcome>" "$DOC_PATH"

Only delete the scratch file after the post has demonstrably succeeded. If it
fails, keep the file, report the path, and say what failed. Never print or echo
the webhook URL.

## You are doing it wrong if

- you asked something the codebase could have answered
- you asked a question without offering your own answer first
- you posted to Discord only after the decision was already fixed
- you asked the team for "thoughts" instead of specific questions
- you opened a new thread for a revision of an existing brainstorm
- you published while something from the gap sweep was unresolved and unrecorded
- the document contains a file list, a path or a code fence
- a section reads as filler because the heading demanded content
- you presented options without a recommendation
- you accepted the stated problem without checking it is the real one
- you started writing code after publishing
