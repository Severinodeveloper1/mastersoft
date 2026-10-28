# Mastersoft Store — Stitch Design Brief

Redesign of the public storefront of **mastersoftstore.com**. The admin side is built with Filament and is **out of scope** for Stitch. Reference source (read-only): `Docs/public_html_mastersoft/public_html`.

## 1. Business context

- Peruvian online store for computer and office accessories, networking gear and printer supplies (access points, adapters, USB Wi-Fi, maintenance boxes, paper, sensors, isopropyl alcohol, speakers, tools).
- Legal entity: TROYA COMPANY E.I.R.L. Base: Nueva Cajamarca, Peru. Footer credit: "Distribuido por Grupo VesergenPerú".
- Currency: soles, shown as `S/.`. Prices include 18% IGV.
- Storefront language: **Spanish (Peru)**. All UI copy in the designs must be in Spanish.
- Audience: small businesses, technicians and home users buying from mobile first, often through WhatsApp.

## 2. Key decisions

| Topic | Decision |
|---|---|
| Ordering | No online payment. The cart ends in **"Enviar pedido por WhatsApp"**: a short form, then WhatsApp opens with the order prefilled. |
| Accounts | No customer login, wishlist or compare. |
| Stack target | Blade + Livewire + Tailwind CSS (Filament 5 stack). Designs must map cleanly to Tailwind utilities. |
| Brand | Keep the existing lime green identity; modernize everything else. |
| Mobile | Mobile-first. Search and WhatsApp must be reachable on mobile (the old site hid them). |

## 3. Design system

### Colors

| Token | Hex | Use |
|---|---|---|
| `primary` | `#A4CE3A` | Brand lime green: nav accents, primary buttons, active states, badges. |
| `primary-dark` | `#6E8F1E` | Text and icons on white that need brand color (lime on white fails contrast). Hover state of primary. |
| `on-primary` | `#1F2A0A` | Text on lime backgrounds. Never white text on lime. |
| `accent` | `#F92400` | Sale price, "Oferta" badge, destructive actions, alerts. Use sparingly. |
| `ink` | `#1E1E1E` | Headings and body text. |
| `muted` | `#6B6B6B` | Secondary text, captions. |
| `surface` | `#FFFFFF` | Cards, page background. |
| `surface-alt` | `#F5F5F5` | Alternate sections, product image background. |
| `footer` | `#2B2B2B` | Footer background (old site used `#484848`). |
| `whatsapp` | `#25D366` | Only for WhatsApp buttons. |

### Typography

- Headings: **Roboto** 500/700 (kept from the old site).
- Body: **Open Sans** 400/600.
- Prices: Roboto 700, tabular numbers.

### Shape and components

- Radius: 12px cards, 8px inputs, full pill for primary buttons and chips.
- Soft shadows, light borders (`#E5E5E5`). Clean, technical, trustworthy — not flashy.
- Icons: outline style (Heroicons/Lucide family).
- Product images on `surface-alt` square containers, object-fit contain.

## 4. Global layout

### Header (desktop)

1. Thin top bar: phone, "Envíos a todo el Perú", WhatsApp link.
2. Main bar: logo, wide search "Buscar producto" with live suggestions, WhatsApp contact button, cart icon with item count and `S/.` total.
3. Nav bar: "Categorías" mega-menu button, links Inicio / Tienda / Nosotros / Contáctanos.

### Header (mobile)

Logo, search icon that expands to full width, cart icon with count, burger menu with categories and pages.

### Floating WhatsApp button

Bottom-right on every page, green `#25D366`.

### Footer (4 columns, dark)

1. Logo and short company description.
2. "Enlaces": Inicio, Tienda, Nosotros, Contáctanos, Políticas de privacidad, Términos y condiciones.
3. "Contacto": address, phone, email, social icons (YouTube, Facebook, LinkedIn, Instagram). "Síguenos".
4. "Libro de Reclamaciones": official book icon + link, "RUC: 20614007584", "TROYA COMPANY E.I.R.L.".

Bottom bar: "© Mastersoft, Todos los derechos reservados." and "Distribuido por Grupo VesergenPerú".

### Product card (shared)

Square image, category label, name (max 2 lines), price `S/. 380.00`, optional sale price with struck original **only when the product really has one**, optional "Oferta" or "Agotado" badge, "Agregar al carrito" button. Hover: quick view eye icon.

## 5. Screens

| # | Screen | Prompt |
|---|---|---|
| 1 | Home | `01-prompts.md` → Home |
| 2 | Tienda (catalog with filters) | → Tienda |
| 3 | Detalle de producto | → Detalle |
| 4 | Carrito (drawer + page) | → Carrito |
| 5 | Enviar pedido por WhatsApp | → Pedido |
| 6 | Pedido enviado (confirmation) | → Confirmación |
| 7 | Nosotros | → Nosotros |
| 8 | Contáctanos | → Contacto |
| 9 | Libro de Reclamaciones | → Reclamaciones |
| 10 | Legal page (privacy / terms) | → Legal |
| 11 | 404 / empty states | → Estados vacíos |

Generate desktop (1440px) and mobile (390px) for screens 1–5; desktop is enough for the rest unless Stitch makes mobile cheap.

## 6. What NOT to carry over from the old site

These were template leftovers or fake data:

- "Nuevo" badge on every product and the fake strike-through price (`price × 1.20`).
- Hard-coded star ratings and lorem-ipsum reviews ("Reseñas" tab).
- Dead wishlist / compare icons.
- New York map on the contact page.
- Billing checkout with fake `$` prices.

## 7. Data available for designs

| Entity | Fields |
|---|---|
| Product | name, slug, category, brand, price, images (gallery), stock, unit, description, information (rich text) |
| Category / Brand | name, product count in stock |
| Banner | image (hero carousel) |
| Promotion | image, linked product |
| Company | name, RUC, logo, side banner, description, phone, address, email, social links |
| About | image, title, subtitle, description, years of experience, phone, mission, vision, values |

Do not design fields outside this list (no SKU display, ratings, reviews or specs table) unless marked as a proposal.

## 8. Open points

- Final logo file: the live site loads it from the database; we need the original asset.
- WhatsApp number for orders.
- Real copy for Nosotros (mission, vision, values) — the old site stored it in the database.
- Service strip promises (free shipping, 30-day returns, 24/7 support) must be confirmed by the business before publishing.
