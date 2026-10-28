# Storefront rebuild — Mastersoft

Objective: rebuild mastersoftstore.com on Laravel 12 + Filament 5 (admin) and Blade + Livewire + Tailwind v4 (storefront), following the approved Stitch designs.
Branch: `feat/storefront-rebuild` (after baseline commit on `main`). Delivery: `ask-on-risk`. Test runner: PHPUnit (`php artisan test`).
Orchestrator: Claude (Opus). Implementation workers: Codex (via `codex:codex-rescue`) for well-specified units with disjoint edit surfaces.

## Specs

- S1 Sources of truth: `Docs/Stitch/00-brief.md`, `Docs/Stitch/02-review-notes.md`, Stitch exports in `Docs/Stitch/plantillas/01-07` (visual reference only), legacy code in `Docs/public_html_mastersoft` (read-only reference).
- S2 Ordering ends in "pedido por WhatsApp": no online payment, no customer accounts.
- S3 Products have a nullable `regular_price`; "Oferta", struck price and "Ahorro" render only when `regular_price > price`.
- S4 Admin (Filament) manages: products, categories, brands, banners, promotions, company settings, About page, legal pages, orders, claims (Libro de Reclamaciones), contact messages.
- S5 Storefront screens: Home, Tienda, Detalle, Carrito (page + drawer), Pedido, Confirmación, Nosotros, Contáctanos, Libro de Reclamaciones, Legal, 404/empty states/toasts.
- S6 Orders are persisted (with ubigeo, payment preference and notes) before opening `wa.me`; the message includes the order code.
- S7 Every form collecting personal data has a required, unchecked privacy consent checkbox.
- S8 UI copy in Spanish (Peru); code, comments and docs in English.
- S9 Do not publish claims listed under "Claims to confirm" in `02-review-notes.md`; business data comes from the admin.

## Tasks

| ID | Specs | Unit | Route | Status | Commit |
|---|---|---|---|---|---|
| T0 | S1 | Baseline: ignore `Docs/public_html_mastersoft`, commit template + docs on `main`, create feature branch | Claude inline | blocked: user approval | — |
| T1 | S3, S4, S6 | Domain: migrations, models, factories, seeders (categories, brands, products, banners, promotions, settings, about, legal pages, orders, order items, claims, contact messages, ubigeo) | Claude inline | pending | — |
| T2 | S8 | Storefront foundation: Tailwind v4 tokens, layout, header, footer, mobile action rail, WhatsApp FAB, product card, icons | Claude inline | pending | — |
| T3 | S4 | Filament resources and pages for every S4 entity (claims with status, response and business-day countdown) | Codex (parallel with T4) | pending | — |
| T4 | S3, S5 | Catalog screens: Home, Tienda (Livewire filters, sort, pagination), Detalle | Codex (parallel with T3) | pending | — |
| T5 | S2, S6, S7 | Cart service, drawer, cart page, order form, persistence, `wa.me`, confirmation | Claude inline | pending | — |
| T6 | S5, S7 | Libro de Reclamaciones, Contáctanos, Nosotros, Legal, 404/empty states | Codex | pending | — |
| T7 | all | Seam check: full test suite, review against specs, Risk lines | Claude | pending | — |

Route evidence: T3 and T4 are independent units with disjoint edit surfaces (`app/Filament/**` vs. storefront views/Livewire) and several hundred lines each, so they run in parallel (Writer trigger a). T1, T2 and T5 hold shared foundations or core business rules and stay with the orchestrator.

## Log

- L1 (2026-10-28, user): "repartamos las actividades con codex para no quedarse corto en tokens, opus seguirá siendo el orquestador principal"
- L2 (2026-10-28): The repository has no commits yet, so Codex worktrees and diff-based review have no base. `Docs/public_html_mastersoft` (legacy copy with `vendor/`, session files and possibly credentials) is not git-ignored and must never be committed. T0 waits for the user's approval of the baseline commit on `main`.
