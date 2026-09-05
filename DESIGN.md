---
name: Golden Street Moving
description: Warm, gold-accented marketing site and booking flow for a local moving company
colors:
  marigold-gold: "#dbbd1a"
  midnight-navy: "#05172b"
  harbor-blue: "#0d253e"
  ink-olive: "#1d2315"
  soft-mist: "#faf9f8"
  pure-white: "#ffffff"
  near-black: "#1e1e1e"
  ink-field: "#131314"
typography:
  display:
    fontFamily: "Montserrat, sans-serif"
    fontSize: "clamp(2.2rem, 5vw, 3.2rem)"
    fontWeight: 700
    lineHeight: 1.2
  headline:
    fontFamily: "Montserrat, sans-serif"
    fontSize: "clamp(1.8rem, 4vw, 2.6rem)"
    fontWeight: 700
    lineHeight: 1.2
  title:
    fontFamily: "Montserrat, sans-serif"
    fontSize: "clamp(1.6rem, 3.5vw, 2rem)"
    fontWeight: 700
    lineHeight: 1.2
  body:
    fontFamily: "Inter, sans-serif"
    fontSize: "1rem"
    fontWeight: 400
    lineHeight: 1.6
  label:
    fontFamily: "Inter, sans-serif"
    fontSize: "0.9rem"
    fontWeight: 700
    letterSpacing: "0.02em"
rounded:
  sm: "10px"
  md: "12px"
  lg: "16px"
  xl: "18px"
  full: "9999px"
spacing:
  xs: "10px"
  sm: "24px"
  md: "30px"
  lg: "42px"
  xl: "80px"
  xxl: "110px"
components:
  button-primary:
    backgroundColor: "{colors.marigold-gold}"
    textColor: "{colors.pure-white}"
    rounded: "{rounded.sm}"
    padding: "8px 16px"
  button-primary-hover:
    backgroundColor: "{colors.midnight-navy}"
    textColor: "{colors.pure-white}"
  button-secondary:
    backgroundColor: "{colors.midnight-navy}"
    textColor: "{colors.pure-white}"
    rounded: "{rounded.sm}"
    padding: "8px 16px"
  button-secondary-hover:
    backgroundColor: "{colors.marigold-gold}"
    textColor: "{colors.pure-white}"
  card-benefit:
    backgroundColor: "{colors.pure-white}"
    rounded: "{rounded.md}"
    padding: "{spacing.sm}"
  card-service:
    backgroundColor: "{colors.pure-white}"
    rounded: "{rounded.md}"
---

# Design System: Golden Street Moving

## Overview

**Creative North Star: "The Golden Hour Move"**

The system reads moving as a hopeful transition, not a chore — the same optimism as golden-hour light. A single warm gold (Marigold Gold) carries every headline, every primary action, and every moment of confirmation, always against calm neutral ground: white, a soft off-white mist, or one of two deep navies. Nothing else competes for the eye. The voice is warm and reassuring, editorial-clean rather than loud — surfaces sit flat at rest and lift only when touched, copy leans on words like "stress-free" and "handled with care," and the gold never spreads thin enough to feel decorative rather than earned.

The system has two real, confirmed navies rather than one: Midnight Navy, the deeper of the two, grounds structural surfaces (footer, the booking card, the secondary button) and reads as the brand's "night" register. Harbor Blue, a half-step lighter, is reserved for feature moments that need their own atmosphere (the out-of-state banner, the booking section backdrop) — close enough to belong to the same family, distinct enough to signal "this section is a different beat."

**Key Characteristics:**
- One accent color (Marigold Gold) used consistently for headlines, primary actions, hover states, and active indicators — never for large fills.
- Two deep navies with distinct roles: Midnight Navy for structure, Harbor Blue for atmospheric feature sections.
- Flat-by-default surfaces; shadow appears only as a hover response, always soft and warm-tinted, never hard-edged (with one confirmed exception — see Elevation).
- Generously rounded corners (10–18px) throughout; nothing sharp-cornered except the hero's full-bleed image.
- Warm, reassuring editorial voice carried in both copy and motion (gentle AOS fade-ups, no aggressive animation).

## Colors

The palette is deliberately narrow: one gold accent, two navies, and a small warm-neutral set for text and backgrounds.

### Primary
- **Marigold Gold** (`#dbbd1a`): The system's only accent. Carries every heading (h1–h6), primary button fills, primary-button hover target color for the secondary button, active carousel dots, focus/error text in the booking form, and small accent fills (e.g. the 20%-opacity gold behind benefit-card icons, `#dbbd1a33`).

### Secondary
- **Midnight Navy** (`#05172b`): The structural dark. Used for the footer background, the booking card background, and the secondary button's resting fill (which swaps to gold on hover).

### Tertiary
- **Harbor Blue** (`#0d253e`): The atmospheric dark. Used for the out-of-state-transfers banner and the booking section's own background — sections that need to feel like a distinct beat from the structural navy above.

### Neutral
- **Pure White** (`#ffffff`): Card and container backgrounds, text on both navies, and the base canvas.
- **Soft Mist** (`#faf9f8`): The benefits section's background — a warm off-white, not stark white, used to separate one section from the next without a hard line.
- **Ink Olive** (`#1d2315`): Default body text color — a warm near-black rather than pure gray, applied via the global `<body>` and `<p>` styles.
- **Ink Field** (`#131314`): A near-black reserved specifically for booking-form input backgrounds (paired with white text and a black border) — distinct from Ink Olive; don't reuse Ink Olive for form surfaces.
- **Near Black** (`#1e1e1e`): A minor utility dark, seen forcing text color inside the Google Places autocomplete widget.

### Named Rules
**The One Accent Rule.** Marigold Gold is the only warm color in the system. It marks headings, primary actions, and state changes — never large surface fills. If a new element needs emphasis, reach for gold before reaching for a new color.

**The Two-Navy Rule.** Midnight Navy and Harbor Blue are both legitimate, both confirmed, and not interchangeable: Midnight Navy is structure (things that frame the page), Harbor Blue is a feature section's own atmosphere. Don't introduce a third near-navy — pick one of these two.

### Known inconsistency (flagged, not a pattern to repeat)
The Google Places autocomplete widget (`address-autocomplete` in `_booking.scss`) is themed with `--gmpx-color-primary: #5E17EB` — a purple with no relationship to the rest of the palette. This is a real gap in the current implementation, not a system color; new work should retheme that widget to Marigold Gold rather than treating purple as available.

## Typography

**Display/Headline/Title Font:** Montserrat (with sans-serif fallback)
**Body Font:** Inter (with sans-serif fallback)

**Character:** A confident, bold serif-adjacent grotesque (Montserrat at 700) carries every heading in gold, set against Inter's quieter, warmer body text (Ink Olive) — the pairing does the "confident but approachable" work without needing decoration.

### Hierarchy
- **Display** (700, `clamp(2.2rem, 5vw, 3.2rem)`, line-height 1.2): Hero title only (`h1`).
- **Headline** (700, `clamp(1.8rem, 4vw, 2.6rem)`, line-height 1.2): Section titles (`h2`) — benefits, services, booking, FAQ, reviews.
- **Title** (700, `clamp(1.6rem, 3.5vw, 2rem)`, line-height 1.2): Card and sub-section titles (`h3`).
- **Body** (400, 1rem/16px, line-height 1.6): Paragraph copy, in Ink Olive. A `.lead` variant (500, 1.125rem) marks the hero description and other emphasized intro paragraphs.
- **Label** (700, 0.9rem, letter-spacing 0.02em, uppercase in practice): Small structural labels — e.g. the booking modal's section headers (`section-title`, uppercase, `#475569`).

### Named Rules
**The Gold Headline Rule.** Every heading (`h1`–`h6`) is Marigold Gold by default, set in Montserrat 700. This is a deliberate, site-wide default — don't override heading color per-section without a strong reason; it's the single strongest visual signature of the system.

## Layout

The page is a stack of full-width sections, each with its own background color (alternating white / Soft Mist / Harbor Blue) to create rhythm without dividing lines. Content is constrained by a centered `.container` (1290px max width, or 1068px in its `--small` variant), with horizontal padding that collapses to 10px below the 980px breakpoint.

Section vertical rhythm is generous and consistent: 80px top/bottom padding for standard sections (benefits, services, FAQ, reviews), stepping up to 110px for the booking section specifically — the page's primary conversion moment gets the most breathing room. Card grids use a 30px gap; content-to-heading spacing inside a section is 42px.

Breakpoints follow a named scale (`mobile` 320px → `desktop` 980px → `large` 1025px → `wide` 1281px → `desktopLg` 1920px); most responsive collapses (nav to hidden, multi-column grids to one column, form fields to full width) happen at the `desktop`/`large` boundary (980–1025px), not at conventional 768px.

## Elevation & Depth

Surfaces are flat at rest; elevation is a *response* to hover, never a resting state, and every shadow (with one exception) is soft, diffuse, and low-opacity black rather than hard-edged.

### Shadow Vocabulary
- **Resting card** (`box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05)`): The barely-there default for benefit and service cards.
- **Hover-lift** (`box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)`): What resting cards animate to on hover — a 350ms shadow transition, no transform.
- **Ambient panel** (`box-shadow: 0 16px 50px rgba(0,0,0,.06)`): Large soft glow under the FAQ accordion container.
- **Review card** (`box-shadow: 0 10px 30px rgba(0,0,0,.06)`): Slightly tighter ambient shadow for testimonial cards.
- **Modal** (`box-shadow: 0 10px 40px rgba(0,0,0,.15)`): The deepest shadow in the system, reserved for the booking status modal — depth signals it's floating above the page, not part of the flow.

### Named Rules
**The Hover-Lift Rule.** Cards are flat at rest and gain a soft, diffuse shadow only on hover (350ms ease). A card that shows its shadow before interaction is off-system.

### Confirmed exception
The fixed header uses `box-shadow: 0 0 10px #000` — a solid, undiffused black shadow, sharper and heavier than anything else in the system. This is a known one-off (kept because the header is a persistent-chrome element, not a content surface) — don't copy it into new components; new hover/elevation states should follow the soft ambient shadows above instead.

## Shapes

Corners are generous and rounded almost everywhere; nothing in the marketing page uses a sharp corner except the hero's full-bleed photograph and the booking table's status chips.

- **sm (10px):** Buttons — the tightest radius in the system.
- **md (12px):** Cards (benefit, service) and the booking card.
- **lg (16px):** The booking status modal and review cards.
- **xl (18px):** The FAQ accordion container — the softest, most pillow-like radius, matching its role as a calm resting surface.
- **full (circle):** Icon badges (benefit-card icons, review avatars), the carousel's prev/next arrows, and active carousel dots (which stretch from a circle to a 16px pill when active).

Borders are used sparingly and only where a hard edge earns its place: a 1px black border on booking-form inputs (on an otherwise dark, borderless page), a hairline top border between FAQ items (`rgba(0,0,0,.08)`), and a hairline top border above the footer's bottom bar (`rgba(255,255,255,.1)`).

## Components

### Buttons
- **Shape:** 10px rounded corners (`{rounded.sm}`).
- **Primary:** Marigold Gold background, white text, Montserrat 700, `8px 16px` padding. Hover swaps the fill to Midnight Navy (350ms ease) — never a tint or opacity change, a full color swap.
- **Secondary:** The exact inverse — Midnight Navy background at rest, swapping to Marigold Gold on hover. Primary and secondary are a deliberate mirrored pair.
- **Disabled:** Flat gray (`#ccc`) background, `not-allowed` cursor — no gold, no navy.

### Cards
- **Benefit cards:** White background, 12px corners, centered content, a circular gold-tinted icon badge (`#dbbd1a33` background) above a bold title and centered gray-olive description. Flat at rest, hover-lift shadow on interaction.
- **Service cards:** White background, 12px corners, a top image (250px, object-cover) that scales to 110% on hover while the card's shadow lifts — the image zoom and shadow lift fire together as one hover gesture. Content below the image left-aligns; the CTA button pins to the card's bottom edge via `margin-top: auto`.
- **Review cards:** White background, 16px corners, soft ambient shadow always-on (not hover-triggered, since this card lives inside a carousel) — avatar, name, Google-gold (`#fbbc04`) star rating, and quoted text in a compact grid.

### Inputs / Fields (booking form)
- **Style:** Dark theme, distinct from the rest of the (light) page — `#131314` (Ink Field) background, 1px solid black border, white text, 10px corner radius, 40px height.
- **Focus:** Border lightens to `#ccc`, no glow or outline.
- **Errors:** Marigold Gold text, 12px, directly beneath the field.
- **Labels:** White, 600 weight, 1rem — set against the dark card, not the page.

### Navigation (header)
- Fixed top bar, white background, 80px tall, logo left / links center / primary CTA button right. Links are bold with a gold hover color. Integrates Headroom.js: on desktop, once scrolled past the top, the header becomes pointer-events-transparent except for the nav itself, and the logo fades to invisible — a "ghost header" state that keeps navigation reachable without the chrome competing for attention. Scrolling down further slides the whole header off-screen (`translateY(-150%)`); scrolling up restores it.

### FAQ Accordion
- Native `<details>`/`<summary>`, wrapped in one large white rounded (18px) panel with the Ambient Panel shadow. Items are separated by hairline top borders, not individual card shadows. The chevron icon rotates 180° when a question opens.

### Reviews Carousel
- Slick-powered card carousel. Circular white prev/next arrow buttons (soft shadow, subtle dark tint on hover) and pill-style dot indicators — inactive dots are small gray circles; the active dot elongates to a 16px gold pill.

### Status Chips (dashboard only)
- Small uppercase pill labels (`10px` radius-ish pill, `5px 10px` padding) that use functional, non-brand colors keyed to lead status: pending=red, in_review=blue, quoted=aqua, closed=green, cancelled=gray/faded. These intentionally sit outside the gold/navy palette — they're a status-signaling system for internal staff, not a brand surface, and shouldn't be reharmonized to gold/navy.

## Do's and Don'ts

### Do:
- **Do** treat Marigold Gold as the system's only accent — for headlines, primary actions, hover states, and active/selected indicators.
- **Do** keep cards and panels flat at rest; only add shadow as a hover response, and keep it soft and warm-tinted (see Elevation's Shadow Vocabulary).
- **Do** use the primary/secondary button pair as mirrored opposites (gold↔navy swap on hover) rather than inventing a third button treatment.
- **Do** pick Midnight Navy for structural chrome (footer, cards, secondary actions) and Harbor Blue for a feature section's own atmosphere — they are not interchangeable, but both are real, confirmed tokens.
- **Do** keep corners generously rounded (10–18px); a sharp-cornered new component would be off-system.

### Don't:
- **Don't** introduce a new accent color alongside gold — if something needs to stand out, make it gold, larger, or bolder, not a new hue.
- **Don't** copy the header's hard `0 0 10px #000` shadow into new components; it's a confirmed one-off for persistent chrome, not the system's shadow language.
- **Don't** theme new form widgets in the stray purple (`#5E17EB`) currently leaking from the Google Places autocomplete override — that's a known gap, not an available brand color.
- **Don't** reharmonize the dashboard's functional status-chip colors (red/blue/aqua/green/gray) into gold/navy — they're a distinct, non-brand signaling system for internal staff.
