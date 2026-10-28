---
name: Mastersoft Storefront Engine
colors:
  surface: '#fcf9f8'
  surface-dim: '#dcd9d9'
  surface-bright: '#fcf9f8'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f6f3f2'
  surface-container: '#f0eded'
  surface-container-high: '#eae7e7'
  surface-container-highest: '#e5e2e1'
  on-surface: '#1b1b1c'
  on-surface-variant: '#444937'
  inverse-surface: '#303030'
  inverse-on-surface: '#f3f0ef'
  outline: '#747965'
  outline-variant: '#c4c9b1'
  surface-tint: '#4d6700'
  primary: '#4d6700'
  on-primary: '#ffffff'
  primary-container: '#a4ce3a'
  on-primary-container: '#3f5500'
  inverse-primary: '#abd541'
  secondary: '#4c6700'
  on-secondary: '#ffffff'
  secondary-container: '#c7ed73'
  on-secondary-container: '#4f6c00'
  tertiary: '#bb1900'
  on-tertiary: '#ffffff'
  tertiary-container: '#ffaa9a'
  on-tertiary-container: '#9c1300'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#c6f25b'
  primary-fixed-dim: '#abd541'
  on-primary-fixed: '#151f00'
  on-primary-fixed-variant: '#394d00'
  secondary-fixed: '#caf075'
  secondary-fixed-dim: '#aed45c'
  on-secondary-fixed: '#141f00'
  on-secondary-fixed-variant: '#384e00'
  tertiary-fixed: '#ffdad3'
  tertiary-fixed-dim: '#ffb4a6'
  on-tertiary-fixed: '#3f0300'
  on-tertiary-fixed-variant: '#8f1000'
  background: '#fcf9f8'
  on-background: '#1b1b1c'
  surface-variant: '#e5e2e1'
typography:
  headline-xl:
    fontFamily: Roboto Flex
    fontSize: 2.25rem
    fontWeight: '700'
    lineHeight: 2.75rem
  headline-xl-mobile:
    fontFamily: Roboto Flex
    fontSize: 1.75rem
    fontWeight: '700'
    lineHeight: 2.125rem
  headline-lg:
    fontFamily: Roboto Flex
    fontSize: 1.75rem
    fontWeight: '700'
    lineHeight: 2.25rem
  headline-lg-mobile:
    fontFamily: Roboto Flex
    fontSize: 1.375rem
    fontWeight: '700'
    lineHeight: 1.75rem
  headline-md:
    fontFamily: Roboto Flex
    fontSize: 1.25rem
    fontWeight: '500'
    lineHeight: 1.625rem
  headline-sm:
    fontFamily: Roboto Flex
    fontSize: 1rem
    fontWeight: '500'
    lineHeight: 1.375rem
  price-hero:
    fontFamily: Roboto Flex
    fontSize: 1.875rem
    fontWeight: '700'
    lineHeight: 2rem
  price-card:
    fontFamily: Roboto Flex
    fontSize: 1.25rem
    fontWeight: '700'
    lineHeight: 1.5rem
  body-lg:
    fontFamily: Open Sans
    fontSize: 1rem
    fontWeight: '400'
    lineHeight: 1.5rem
  body-md:
    fontFamily: Open Sans
    fontSize: 0.875rem
    fontWeight: '400'
    lineHeight: 1.375rem
  body-md-semibold:
    fontFamily: Open Sans
    fontSize: 0.875rem
    fontWeight: '600'
    lineHeight: 1.375rem
  body-sm:
    fontFamily: Open Sans
    fontSize: 0.75rem
    fontWeight: '400'
    lineHeight: 1.125rem
  label-md:
    fontFamily: Roboto Flex
    fontSize: 0.875rem
    fontWeight: '500'
    lineHeight: 1.125rem
  label-sm:
    fontFamily: Roboto Flex
    fontSize: 0.75rem
    fontWeight: '700'
    lineHeight: 1rem
  caption:
    fontFamily: Open Sans
    fontSize: 0.6875rem
    fontWeight: '400'
    lineHeight: 0.875rem
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-desktop: 1.5rem
  margin: 1rem
  margin-tablet: 1.5rem
  margin-desktop: 2rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 1rem
  space-lg: 1.5rem
  space-xl: 2rem
  space-2xl: 3rem
---

## Brand & Style

This design system establishes an e-commerce platform for computer hardware, corporate networking infrastructure, and printer consumables within the Peruvian technology sector. The visual language blends crisp technical authority with immediate retail accessibility.

### Personality & Tone
- **Technical Competence:** Clean lines, deliberate structure, and high legibility communicate reliability and authenticity for corporate IT procurement and home-office consumers.
- **Commercial Vitality:** Punchy lime green accents drive momentum, while vivid red tags isolate promotional urgency. 
- **Accessibility & Contrast:** Strictly enforces WCAG AA compliance; bright lime (#A4CE3A) is paired exclusively with dark olive text (#1F2A0A), avoiding low-contrast white text on lime surfaces.

### Design Movement
- **Modern Utilitarian E-Commerce:** Clean structural borders, bright high-key white backdrops, dedicated neutral product display wells (`#F5F5F5`), and purposeful color accents. The UI avoids decorative blurs in favor of instant visual parsing, quick technical comparison, and streamlined transactional workflows.

## Colors

The palette balances vivid brand cues with utilitarian retail contrast.

### Palette Architecture
- **Primary Lime (`#A4CE3A`):** Used for high-impact visual anchors, hero banners, primary action fills, category indicator pips, and active filter selections.
- **Primary Dark (`#6E8F1E`):** Serves as accessible text on pure white, brand icons, and rich interactive hover states where lime lacks text contrast.
- **On-Primary Deep Olive (`#1F2A0A`):** Text and iconography resting on top of `#A4CE3A` backgrounds. White text is strictly prohibited on lime surfaces.
- **Accent / Alert (`#F92400`):** Reserved for "Oferta" discount chips, strikethrough sale prices, inventory shortages ("Últimas unidades"), and error notifications.
- **Ink Primary (`#1E1E1E`):** Heading titles, product names, core descriptions, and tabular pricing integers.
- **Text Muted (`#6B6B6B`):** Technical SKUs, tax notes ("Incluye IGV"), breadcrumbs, and inactive metadata.
- **Surfaces (`#FFFFFF` & `#F5F5F5`):** Crisp pure white foundations paired with cool neutral frames for isolated product photo canvases and alternating specification table rows.
- **Footer Dark (`#2B2B2B`):** Grounded institutional foundation containing business identifiers (TROYA COMPANY E.I.R.L., RUC), institutional certifications, and legal disclaimers.
- **WhatsApp (`#25D366`):** Dedicated conversion color for direct operator assistance and instantaneous checkout assistance.
- **Structural Border (`#E5E5E5`):** Low-contrast dividers and card perimeters for high layout density without visual friction.

## Typography

The type scale pairs technical, geometric headlines and tabular pricing metrics (Roboto Flex) with highly legible humanist body text (Open Sans) optimized for prolonged scanning of product specifications.

### Rules & Formatting
- **Currency & Commercial Numerals:** All Peruvian Soles prices (`S/. 00.00`) must enforce tabular numbers (`font-variant-numeric: tabular-nums; font-feature-settings: 'tnum' 1;`).
- **Product Titles:** Set in `body-md-semibold` or `headline-sm` with a 2-line vertical truncation (`line-clamp-2`) in grid listings to guarantee card alignment across varying title lengths.
- **SKUs and Part Numbers:** Set in uppercase `caption` using `#6B6B6B` with tracking (`letter-spacing: 0.05em;`) to visually decouple identifiers from descriptive copy.

## Layout & Spacing

Layouts follow a fluid, responsive 12-column grid system pinned to a maximum container width of `1280px` (`max-w-7xl`).

### Screen Form Factors
- **Mobile (< 640px):** 4-column layout. Margin `1rem`, gutter `1rem`. Product catalogs render in 2-column compact grids (`grid-cols-2 gap-3`).
- **Tablet (640px - 1024px):** 8-column layout. Margin `1.5rem`, gutter `1.25rem`. Product listings render in 3-column configurations (`grid-cols-3 gap-4`).
- **Desktop (≥ 1024px):** 12-column layout. Margin `2rem`, gutter `1.5rem`. Search and catalog viewports render with a 3-column sticky faceted filter sidebar (`col-span-3`) paired with a 9-column item canvas (`col-span-9`, rendering 3 or 4 product cards per row).

### Sticky Shell Ergonomics
- **Search Header:** Fixed header housing brand identity, real-time Livewire search query input, and quick cart access.
- **Mobile Action Rail:** Fixed bottom container on mobile viewports housing quick category drawer toggle, Livewire instant search shortcut, cart counter, and the prominent floating WhatsApp contact trigger.

## Elevation & Depth

This design system uses a clean, low-shadow aesthetic that emphasizes sharp product borders and subtle depth cues rather than heavy drop shadows.

### Elevation Hierarchy
- **Flat Surface (`elevation-0`):** `#FFFFFF` body canvas, divided only by 1px solid `#E5E5E5` lines.
- **Card Base (`elevation-1`):** Border `1px solid #E5E5E5` with ambient shadow `0 1px 3px 0 rgba(0, 0, 0, 0.05)`.
- **Interactive Card Hover (`elevation-2`):** Elevates via translation `translate-y-[-2px]`, border color transition to `#A4CE3A`, and shadow `0 6px 16px -4px rgba(110, 143, 30, 0.12), 0 2px 4px -1px rgba(0, 0, 0, 0.04)`.
- **Dropdowns & Auto-complete (`elevation-3`):** Livewire search result flyouts and navigation menus use `0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.06)` with a 1px `#E5E5E5` boundary.
- **Sticky WhatsApp FAB (`elevation-float`):** High priority circular action button anchored via `0 4px 14px 0 rgba(37, 211, 102, 0.38)`.

## Shapes

The geometric balance pairs rounded structural containers with fully pill-shaped actionable targets.

### Radius Distribution
- **Cards & Banners (`rounded-xl` / 12px):** Product cards, category tiles, checkout panels, and promotional banners.
- **Inputs & Dropdowns (`rounded-lg` / 8px):** Search fields, quantity selectors, checkout forms, and faceted filter option lists.
- **Pills (`rounded-full` / 9999px):** Primary transactional CTAs ("Agregar al carrito"), status tags ("En stock"), promotional discount tags ("Oferta"), category filter chips, and floating buttons.
- **Images:** Product canvas images are encased in standard `rounded-lg` (8px) within the inner card margins.

## Components

### Buttons
- **Primary CTA:** Background `#A4CE3A`, text `#1F2A0A` (bold), shape `rounded-full`, padding `0.625rem 1.25rem`. Hover shifts to `#93BC2F` with active scale compression (`scale-[0.98]`).
- **Secondary / Technical Action:** Background `#FFFFFF`, border `1.5px solid #6E8F1E`, text `#6E8F1E`. Hover fills with `#6E8F1E` and flips text to `#FFFFFF`.
- **WhatsApp Direct Order CTA:** Background `#25D366`, text `#FFFFFF`, icon 20px outline/solid WhatsApp glyph. Hover scales slightly with elevated shadow.
- **Destructive / Alert Action:** Background `#F92400`, text `#FFFFFF`, rounded `rounded-lg`.

### Product Cards
- Built as a vertical flex column within a `rounded-xl` border of `#E5E5E5` on `#FFFFFF`.
- **Media Frame:** Top element containing a 1:1 square `#F5F5F5` frame with `rounded-lg` padding to center isolated product hardware PNGs.
- **Badges:** Absolute-positioned top-left chip: "Oferta -20%" (Background `#F92400`, text `#FFFFFF`, `rounded-full`).
- **Meta Block:** Upper area showcases brand and SKU in `#6B6B6B` `caption`. Below it, the product title in 2-line clamp.
- **Pricing Cluster:** Strikethrough regular price in `#6B6B6B` `body-sm`, beside the current price in `#1E1E1E` (or `#F92400` if on sale) in tabular `price-card`.

### Search Input (Livewire Optimized)
- Container shaped with `rounded-lg`, background `#FFFFFF`, border `1.5px solid #E5E5E5`, focus ring `2px solid #A4CE3A` with zero offset.
- Left-aligned thin outline magnifying glass in `#6B6B6B`.
- Integrated Livewire loading spinner pulse on input keyup inside the right edge.

### Chips & Category Selectors
- Non-active: Background `#F5F5F5`, border `1px solid transparent`, text `#1E1E1E`, `rounded-full`.
- Active/Selected: Background `#A4CE3A`, text `#1F2A0A`, font-weight `600`, border `1px solid #6E8F1E`.

### Checkboxes & Radios
- Size `1.125rem x 1.125rem`, border `1.5px solid #6B6B6B`, rounded `rounded` (4px for checkboxes) or `rounded-full` (radios).
- Checked State: Background `#6E8F1E`, border color `#6E8F1E`, internal white checkmark or center pip. Focus ring in `#A4CE3A`.

### Iconography
- Consistent 1.5px stroke outline aesthetic (Lucide / Heroicons style). Never filled unless depicting active star ratings or direct social icons. Standard bounding box: 20x20px inline, 24x24px in navigation.