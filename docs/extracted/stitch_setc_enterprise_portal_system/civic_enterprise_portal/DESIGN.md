---
name: Civic Enterprise Portal
colors:
  surface: '#f8f9ff'
  surface-dim: '#cbdbf5'
  surface-bright: '#f8f9ff'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#eff4ff'
  surface-container: '#e5eeff'
  surface-container-high: '#dce9ff'
  surface-container-highest: '#d3e4fe'
  on-surface: '#0b1c30'
  on-surface-variant: '#44474d'
  inverse-surface: '#213145'
  inverse-on-surface: '#eaf1ff'
  outline: '#74777e'
  outline-variant: '#c4c6ce'
  surface-tint: '#4a5f7f'
  primary: '#001229'
  on-primary: '#ffffff'
  primary-container: '#0f2744'
  on-primary-container: '#798fb1'
  inverse-primary: '#b2c8ed'
  secondary: '#0051d5'
  on-secondary: '#ffffff'
  secondary-container: '#316bf3'
  on-secondary-container: '#fefcff'
  tertiary: '#00160c'
  on-tertiary: '#ffffff'
  tertiary-container: '#002d1d'
  on-tertiary-container: '#1fa072'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#d4e3ff'
  primary-fixed-dim: '#b2c8ed'
  on-primary-fixed: '#021c39'
  on-primary-fixed-variant: '#324866'
  secondary-fixed: '#dbe1ff'
  secondary-fixed-dim: '#b4c5ff'
  on-secondary-fixed: '#00174b'
  on-secondary-fixed-variant: '#003ea8'
  tertiary-fixed: '#85f8c4'
  tertiary-fixed-dim: '#68dba9'
  on-tertiary-fixed: '#002114'
  on-tertiary-fixed-variant: '#005137'
  background: '#f8f9ff'
  on-background: '#0b1c30'
  surface-variant: '#d3e4fe'
typography:
  headline-xl:
    fontFamily: Plus Jakarta Sans
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
  headline-xl-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
  headline-lg:
    fontFamily: Plus Jakarta Sans
    fontSize: 30px
    fontWeight: '600'
    lineHeight: 38px
  headline-lg-mobile:
    fontFamily: Plus Jakarta Sans
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  headline-md:
    fontFamily: Plus Jakarta Sans
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 28px
  headline-sm:
    fontFamily: Plus Jakarta Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '500'
    lineHeight: 20px
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
  code-sm:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '500'
    lineHeight: 18px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1.5rem
  gutter-sm: 1rem
  margin: 2rem
  margin-sm: 1rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
---

## Brand & Style

The design system embodies the intersection of institutional reliability, modern enterprise rigor, and compassionate public administration. It reframes governmental workflows—often perceived as intimidating, opaque, and bureaucratic—into straightforward, welcoming, and empowering digital services. 

The aesthetic is built upon modern civic minimalism combined with technical software precision, drawing direct influences from gov.uk's radical clarity, Vercel's clean monochromatic structure, and shadcn/ui’s component discipline:
- **Authority without friction**: Clean surfaces, architectural typography, and meticulous whitespace replace bureaucratic visual clutter.
- **Radical accessibility**: Built to meet and exceed WCAG 2.1 AA standards, ensuring clarity for all constituents regardless of device, context, or visual ability.
- **Empathetic clarity**: Humanized microcopy, unambiguous validation, and explicit step-based progressive disclosure lower cognitive strain during critical administrative tasks.

## Colors

The palette establishes an immediate foundation of trust, high functional contrast, and clear state communication:

- **Primary (`#0f2744`, Deep Navy)**: Used for primary visual anchors, authoritative text headers, navigation framing, and high-impact structural UI elements. Supported by mid-navy (`#1e3a5f`) for hover states and interactive depth.
- **Secondary (`#2563eb`, Civic Royal Blue)**: The active interactive colorway, reserved for key actions, primary links, focus indicators, and active workflow states.
- **Neutrals & Surfaces**: A disciplined scale of pure white (`#ffffff`), zinc slate (`#f8fafc`, `#f1f5f9`), and architectural divider borders (`#e2e8f0`, `#cbd5e1`). Body text rests on deep charcoal-slate (`#0f172a`), preserving an APCA/contrast ratio far beyond 7:1.
- **Status Accents**:
  - **Success (`#059669` / `#ecfdf5`)**: Conveys verified statuses, approved submissions, and safe completion.
  - **Warning (`#d97706` / `#fffbeb`)**: Designates pending reviews, actionable alerts, and intermediate states.
  - **Critical / Danger (`#e11d48` / `#fff1f2`)**: Highlights urgent blockers, validation errors, and irreversible actions.

## Typography

The type system blends the humane geometry of **Plus Jakarta Sans** for display surfaces with the utilitarian, screen-optimized neutrality of **Inter** for forms, tables, and dense data environments.

- **Headlines (Plus Jakarta Sans)**: Used for page headers, modal titles, and section greetings. The geometric curves project a friendly, approachable civic demeanor while retaining an executive posture.
- **Body & Data (Inter)**: Delivers exceptional optical performance across complex form layouts, inline badges, and high-density citizen dashboards.
- **Vertical Rhythm**: All line-heights align directly to an 4px/8px basework grid to ensure balanced multi-column alignments across responsive reflows.

## Layout & Spacing

The layout is structured around an adaptive 12-column responsive fluid grid inside a bounded max-width canvas (`1280px` for general enterprise flows; `800px` for multi-step transactional forms):

- **Breakpoints**: Mobile (`< 640px`, 4 columns), Tablet (`640px – 1024px`, 8 columns), Desktop (`≥ 1024px`, 12 columns).
- **Rhythm**: Component spacing adheres rigorously to an 8px modular scale. Form fields and related micro-copy rely on compact vertical spacing (`space-xs` to `space-sm`), while administrative panels and card groupings leverage generous structural breathing room (`space-lg` to `space-xl`).
- **Form Layout**: In procedural citizen filing forms, single-column field sequences are enforced on mobile and restricted to two columns on desktop to preserve linear cognitive flow.

## Elevation & Depth

Visual hierarchy is maintained through subtle tonal layering combined with low-contrast borders and feather-weight ambient shadows:

- **Base Layer (Ground)**: Zinc-tinted slate (`#f8fafc`) sets an unglamorous, eye-resting background.
- **Level 1 (Cards & Data Sections)**: Pure white (`#ffffff`) background bounded by a sharp `1px` border (`#e2e8f0`) and an ambient shadow: `0 1px 3px 0 rgba(15, 23, 42, 0.04), 0 1px 2px -1px rgba(15, 23, 42, 0.02)`.
- **Level 2 (Hover States, Floating Menus & Dropdowns)**: Elevated with crisp borders and an expanded ambient shadow: `0 10px 15px -3px rgba(15, 23, 42, 0.06), 0 4px 6px -4px rgba(15, 23, 42, 0.03)`.
- **Level 3 (Modals & Confirmation Drawers)**: Heavy backdrop blur (`backdrop-blur-sm` over `rgba(15, 39, 68, 0.4)`) paired with `0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.04)`.

## Shapes

The design system implements a refined `rounded-xl` aesthetic for cards, panels, and modal dialogs, balanced by standardized `rounded-lg` radiuses for atomic inputs and buttons. This balance introduces soft, approachable geometry without losing organizational structure.

- **Primary Cards & Modals**: `rounded-xl` (12px to 16px radius) creates comfortable, friendly enclosures for complex procedures.
- **Controls & Inputs**: `rounded-lg` (8px radius) preserves crisp alignment along form margins and data rows.
- **Badges & Status Chips**: Pill-shaped (`rounded-full`) for immediate perceptual distinction against rectangular form fields.

## Components

### Buttons
- **Primary**: Deep Navy background (`#0f2744`) or Civic Royal Blue (`#2563eb`), white label, `rounded-lg`, height `40px` (or `48px` on mobile), font weight `500`. On focus: `2px` offset outline in Royal Blue.
- **Secondary / Outlined**: White surface, slate border (`#e2e8f0`), navy text. Hover introduces slate background tint (`#f1f5f9`).
- **Destructive**: Rose background (`#e11d48`), white label, strictly paired with explicit confirmation modals.

### Form Inputs & Selects
- Framed in `1px` border (`#cbd5e1`), white background, `rounded-lg`, `40px` height.
- Labels are persistent, high-contrast (`#0f172a`), placed above inputs, supplemented by optional empathetic helper text (`#64748b`).
- Focus state features a sharp, zero-latency 2px ring in Civic Blue (`#2563eb`).

### Status Badges & Chips
- Formatted as full pills (`rounded-full`) with a soft background tint and dark text matching the respective semantic tone:
  - **Verificado / Completado**: Emerald tint (`#ecfdf5`), deep green label (`#065f46`), dot indicator (`#10b981`).
  - **En Revisión / Pendiente**: Amber tint (`#fffbeb`), dark amber label (`#92400e`).
  - **Acción Requerida**: Crimson tint (`#fff1f2`), deep crimson label (`#9f1239`).

### Steppers & Progress Indicators
- Linear, numbered, horizontal timeline with completed steps flagged by check icons in Emerald Green, current step highlighted in Civic Blue with high-contrast text, and upcoming steps presented in subdued slate outlines.

### Cards & Data Containers
- White background (`#ffffff`), `rounded-xl`, subtle border (`#e2e8f0`). Header, body, and footer are segmented by delicate 1px internal dividers or distinct background tints for batch action toolbars.