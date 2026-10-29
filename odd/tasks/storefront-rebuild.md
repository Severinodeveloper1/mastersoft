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
| T0 | S1 | Baseline: ignore `Docs/public_html_mastersoft`, commit template + docs on `main`, create feature branch | Claude inline | done | `93a3c66` (main) |
| T1 | S3, S4, S6 | Domain: migrations, models, factories, seeders (categories, brands, products, banners, promotions, settings, about, legal pages, orders, order items, claims, contact messages, ubigeo) | Claude inline | done (17 tests green) | `eb79250`, claim retry fix |
| T2 | S8 | Storefront foundation: Tailwind v4 tokens, layout, header, footer, mobile action rail, WhatsApp FAB, product card, icons | Claude inline | done (23 tests green, Vite build ok) | `f82ed2b` |
| T3 | S4 | Filament resources and pages for every S4 entity (claims with status, response and business-day countdown) | Codex (parallel with T4) | done (6 admin tests) | `c9e1702` |
| T4 | S3, S5 | Catalog screens: Home, Tienda (Livewire filters, sort, pagination), Detalle | Codex (parallel with T3) | done (37 tests total) | `2fab91a` |
| T5 | S2, S6, S7 | Cart service, drawer, cart page, order form, persistence, `wa.me`, confirmation | Codex (context backstop; Claude reviewed the diff) | done (13 tests) | `8148a9b` |
| T6 | S5, S7 | Libro de Reclamaciones, Contáctanos, Nosotros, Legal, 404/empty states | Codex (parallel with T5) | done | `5db057f` |
| T7 | all | Seam check: full test suite, review against specs, Risk lines | Claude + independent verifier (Risk items 4, 5) | done: no blockers | `8f135e7` verified |

Route evidence: T3 and T4 are independent units with disjoint edit surfaces (`app/Filament/**` vs. storefront views/Livewire) and several hundred lines each, so they run in parallel (Writer trigger a). T1, T2 and T5 hold shared foundations or core business rules and stay with the orchestrator.

## Log

- L1 (2026-10-28, user): "repartamos las actividades con codex para no quedarse corto en tokens, opus seguirá siendo el orquestador principal"
- L2 (2026-10-28): The repository has no commits yet, so Codex worktrees and diff-based review have no base. `Docs/public_html_mastersoft` (legacy copy with `vendor/`, session files and possibly credentials) is not git-ignored and must never be committed. T0 waits for the user's approval of the baseline commit on `main`.
- L3 (2026-10-28, user): "crea el commit inicial, la carpeta de Docs\public_html_mastersoft es sólo referencial no se debe subir al repo"
- L4 (2026-10-28): T0 done. `/Docs/public_html_mastersoft/` added to `.gitignore`; baseline `93a3c66` on `main` (188 files, legacy folder untracked); branch `feat/storefront-rebuild` created. `.env.example` could not be inspected (local permission rule blocks `.env*` reads); it is the stock template file.
- L5 (2026-10-28): Local PHP (WinGet) runs without php.ini, so PHPUnit lacks mbstring. Tests run with a scratch ini outside the repo: `php -c <scratch>/php-test.ini vendor/bin/phpunit` (extensions: mbstring, openssl, pdo_sqlite, sqlite3, fileinfo, intl, gd, zip, curl, pdo_mysql, exif). The user's PHP install was not modified. `docker-compose.yml` still names the stack `tira-inmobiliaria` (template leftover).
- L6 (2026-10-28): T1 done. RED observed (11 errors, classes missing) then GREEN. Decisions: enums with Spanish labels (Filament `HasLabel`); order code `MS-000123` from id; claim code `000123-2026` per-year correlative with bounded retry (`Claim::file`) on unique collisions; claim due date = 15 business days (Mon–Fri, holidays not modeled); offer math in integer cents (no bcmath dependency); `ContentSeeder` runs in `DatabaseSeeder`; `DemoCatalogSeeder` is manual and local-only. Risk: item 4 (claim correlative under concurrent filings), mitigated by the unique index plus retry and pinned by a collision test.
- L7 (2026-10-28): T2 done. Claude owns `routes/web.php`: every page route exists now and points to placeholder controllers in `App\Http\Controllers\Store` that T4/T5/T6 fill without touching routes. Legacy URLs (`/store`, `/about`, `/contact`, `/cart`, `/libro-reclamaciones`, `/product/{slug}`) 301-redirect. Order confirmation route is signed (no guessable `MS-000123` URLs). Icons: Heroicons via blade-heroicons (already installed with Filament) plus inline WhatsApp/social SVGs. `tests/TestCase` calls `withoutVite()`.
- L8 (2026-10-28): T3 first Codex attempt failed: its sandbox cannot reach Packagist; no repo changes. Claude installed `filament/spatie-laravel-media-library-plugin` v5.6.7 from the host (Risk: item 5, new dependency). Composer reports 33 security advisories in 10 existing template packages: out of scope, to raise with the user. T3 relaunched without network needs; T4 launched in parallel.
- L9 (2026-10-28): T3 and T4 integrated (37 tests green, Vite build ok, pages checked in a browser against a scratch SQLite with DemoCatalogSeeder). Orchestrator fixes: product gallery reserved a thumbnail column with a single image; brand-styled Livewire pagination (`components.store.pagination`); best sellers as 3-column grid on desktop. Super admin passes all policies (Shield `define_via_gate` + `intercept_gate: before`); other roles need permissions assigned in Shield. A stale `public/hot` (Vite dev pointer from 2026-08-10) was moved to the scratchpad. Local preview: run PHP built-in server from `public/` with the scratch ini (`artisan serve` drops the ini). Follow-up: Codex Blade views are written as long single lines; reformat for readability in T7.
- L10 (2026-10-28): T5 moved to Codex by the long-session backstop (orchestrator context is large); T5 and T6 run in parallel with disjoint surfaces.
- L11 (2026-10-28): T5 and T6 integrated. Seam check: 58 tests / 350 assertions green, Vite build ok; local HTTP check: all pages 200, empty /pedido redirects to /carrito, custom 404, unsigned confirmation not reachable; no-JS add-to-cart flow verified (2 x S/. 380.00 → total 760.00 = 644.07 + IGV 115.93). Orchestrator review of OrderForm::submit: DB prices re-read under lockForUpdate inside a transaction, stock check, item snapshots, cart cleared after commit, signed redirect. Fixed my T2 layout test to seed ContentSeeder (legal pages now 404 when their row is missing, as specified). Independent verifier launched for Risk items 4 (claim numbering, checkout locking) and 5 (media plugin dependency).
- L12 (2026-10-28): T7 independent verifier on 8f135e7: no blockers; S1–S9 pass (S5 and S9 with advisories); full suite 58/350 OK; 10 probes OK (forged price ignored, confirmation 403 without/with tampered signature, rejected submits write nothing and keep the cart, transaction rollback on item failure, Claim::file retries only on unique collisions and gives up after 3, map iframe allowlist, unescaped HTML only from admin fields). Dependency compatible (plugin and filament both v5.6.7). Advisories: (1) stock is locked but never decremented, so two orders can take the last unit — product decision; (2) claim/contact emails sent after save: a mail failure shows an error after the record exists; (3) retry budget 3 x 50 ms; (4) contact Asunto options hardcoded, no FAQ; (5) home service strip mentions "Recojo en tienda", close to a claim to confirm; (6) "Oferta -0%" possible under 0.5% discount; (7) phpunit.xml lacks APP_KEY, clean clones fail 38 tests; (8) Blade single-line formatting; (9) lockForUpdate is a no-op on SQLite, real concurrency only on MySQL. Verifier scratch removed.
- L13 (2026-10-29): `ab9ca30` fixed About and Contact rendering without admin content (page header, empty state, approved Contact design; test added).
- L14 (2026-10-29, user): "sí, descontá el stock al confirmar el pedido"
- L15 (2026-10-29): Advisory 1 resolved. `OrderForm::submit` decrements each product's stock by the ordered quantity inside the same transaction, on the rows already locked with `lockForUpdate`; rejected submits leave stock untouched. RED observed (stock stayed 4, expected 2), then GREEN; full suite 62 tests / 373 assertions OK. Cancelling an order in the admin does not restore stock (not requested). Risk: item 4 (concurrent checkouts; serialized by the row lock on MySQL, no-op on SQLite).
