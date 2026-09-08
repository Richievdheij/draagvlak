---
name: draagvlak-design
description: "Use when touching how the Draagvlak prototype looks: any CSS file, a token, spacing, a colour, a component, focus and hover states, or the accessibility floor. Covers where a stylesheet belongs and the rules that keep the design believable."
---

# Styling Draagvlak

Read `AGENTS.md` if you have not, and `docs/04-designsysteem.md` for the reasoning.

## The one hard rule

Every colour, size, font and radius comes from a custom property in
`public/assets/css/base/tokens.css`. A literal hex code, pixel value or font name
anywhere else is a bug, including one you are only using once.

Three layers, and you almost always want the middle one:

1. **Primitives** (`--mint-600`, `--paper-100`) build the layer below and are never used
   in a component.
2. **Semantic** (`--color-accent`, `--color-surface`, `--color-text-soft`) is what you
   write.
3. **Component**, set inside the component itself, for a size that exists nowhere else:
   `--avatar-size`, `--field-height`.

Light and dark share one definition through `light-dark()`, so a new colour is one line
with two values. A colour without a dark value is almost always a mistake.

## Where a stylesheet goes

```
base/              fonts, tokens, reset, elements, utilities
layout/            one file per file in views/layout/
components/        one file per file in views/components/
features/<name>/   everything for one feature
```

A screen loads `base/`, `layout/`, `components/` and the folder of its own feature.
Nothing has to be registered.

In doubt: used by one feature is `features/<name>/`; used by two is `components/`. Name
the file after what it styles, not after the screen it happens to be on today.

Every file opens with one or two lines saying what it styles and which template it
belongs to. BEM-style names: `.block`, `.block__element`, `.block--modifier`, `.is-open`
for a state JavaScript switches. One class per selector, so nothing accidentally
overrides anything.

## The direction, and why

Soft mint, white cards on a pale green background, generous rounding, a hairline instead
of a shadow. It looks like an app you already have on your phone.

That is a choice, not timidity. A scoring app that looked threatening would not be
believable, and then you are only testing whether people find scary interfaces scary.
The discomfort comes from the text and the numbers.

So: losing points is never red. It goes quiet and beige. Red is only for a form that is
genuinely filled in wrong. No gradients, no glass, no drop shadows.

## States

One focus indicator, and never a second signal next to it. `:focus-visible` in
`base/elements.css` draws a single outline that follows the corner the element already
has. A component may move it with `--focus-offset`, and that is all:

```css
.field__input {
    /* The ring sits on the edge here, so focusing reads as the field lighting
       up and not as a second border. */
    --focus-offset: 0;
}
```

Never remove a focus style. Never give an input a hover state: a field that reacts to
the mouse passing over it reads as a bug, not as polish.

## The accessibility floor

Not extras. Contrast at least AA. Every input has a real label, not a placeholder.
Touch targets at least 44px, which is `2.75rem` and why `--field-height` and the buttons
are that tall; make a button narrower rather than shorter. Motion is off for anybody who
asked for that, already handled in `tokens.css`. An icon that carries meaning gets text
or an `aria-label`.

## Size

One column of `--width-app`: full width on a phone, centred on a laptop. It has to read
as an app, not as a website that was stretched.

Test at 390px wide and at a normal laptop width. Both, every time.

## Formatting

`npm run format` runs Prettier over CSS with `.prettierrc`, and VS Code does it on save.
Do not hand-align declarations.
