# ERP sync (Vesertech)

Objective: Mastersoft pulls categories, brands and products from the Vesertech ERP every night; admin features stay intact. Branch `feat/erp-sync` (from `feat/storefront-rebuild` at `6b837cb`). Delivery: ask-on-risk. Tests: `php -c <scratch>/php-test.ini vendor/bin/phpunit`.

## Specs

- S1 Secrets: `Docs/info/` holds a live test token and the `demo` login; it must never be committed (git-ignored).
- S2 Image uploads: images uploaded in the admin (products, banners, promotions, brands, company logo, About) must be publicly visible in the store. User: "estoy registrando algunas imágenes a los productos y banners y sin embargo no se visualizan". No existing admin action is removed: "no se eliminará la subida de imágenes, no se eliminará ninguna de las acciones que ya cuenta el sistema".
- S3 Authentication: "se loguea, captura el token y lo usa para ejecutar el endpoint". `POST {base}/api/v1/auth/login`, header `X-Tenant-Id`, body `usuario`, `clave`, `aplicacion: "W"`, `dispositivo`. Base URL, tenant and credentials come from `.env` only; the token is never logged or stored.
- S4 Categories and brands: synced from `GET /api/v1/categorias?arbol=true&estado=1` and `GET /api/v1/marcas?arbol=true&estado=1`, flattening `subcategorias` / `submarcas`. Products relate by `idcategoria` / `idmarca` ("la categoría a la que debe apuntar y relacionar son las que te muestro en el archivo /info/categoria_grupo.md, los cuales son el "idcategoria"").
- S5 Products: synced from `GET /api/v1/productos?page=N&per_page=M&estado=1` (Laravel paginator: `data.data`, `data.last_page`), matched by `external_id` = `idproducto`. Name = `descripcioninterna`; price = principal price of the `principalventa` presentation ("todos los precios ya incluyen IGV"); stock = `stock_disponible`; unit = presentation `nombre`; ERP image URLs from `imagenes[].url` by `orden` ("aparentemente no vencen").
- S6 Presentations: Mastersoft products have a single presentation, "sin embargo controlemos estas excepciones por si en algún momento ocurre": with several presentations use the `principalventa` one and log a warning; a product without a usable price is skipped and logged, never published at 0.
- S7 Deactivation: full load every night; records missing from a COMPLETE load are deactivated in the store ("concuerdo con tu propuesta"). If any page fails, nothing is deactivated.
- S8 Images coexist: ERP image URLs and admin uploads are both shown; the sync never touches admin uploads. Only `https://` URLs are accepted.
- S9 Schedule: one artisan command runnable by hand, scheduled at night ("todas las noches en un determinado tiempo, posiblemente de madrugada"), never overlapping; each run logs created/updated/deactivated/skipped counts and failures.
- S10 (pending, not authorized yet) Orders to ERP as quotations: `POST /api/v1/cotizaciones` with `idcliente` from `.env` ("considero que debemos agregarlo al .env de manera que siempre tome el usuario configurado"), `idmoneda`, `idcondicionpago`, `dias`, `reservarstock: false`, `observacion`, `items[{idproducto, cantidad, preciounitario}]`.

Assumptions (to confirm): admin-written description is kept when the ERP sends null description fields; ERP images are listed before admin uploads; categories stay flat (no parent in the store); stock decimals are truncated to whole units.

## Tasks

| ID | Specs | Unit | Route | Status | Commit |
|---|---|---|---|---|---|
| T1 | S1 | Ignore `Docs/info/` | inline | pending | |
| T2 | S2 | Admin uploads on the `public` disk | inline | pending | |
| T3 | S3-S9 | ERP client, catalog sync command, schedule, image merge in the store | inline (sequential, no Writer trigger) | pending | |
| T4 | S10 | Orders to ERP quotations | blocked: awaiting authorization | pending | |

## Log

- L1 (2026-10-29, user): "sí, arrancá con los tres pasos" (1. ignore `Docs/info/`, 2. public disk fix, 3. `erp:sync-products` tested first against the sample response, live API only with explicit authorization).
- L2 (2026-10-29, user answers): "correcto, todos los precios ya incluyen IGV" / "aparentemente no vencen" / "concuerdo con tu propuesta" / "los productos que cargaremos para este proyecto de mastersoft sólo contarán con una presentación ... sin embargo controlemos estas excepciones por si en algún momento ocurre" / "la categoría a la que debe apuntar y relacionar son las que te muestro en el archivo /info/categoria_grupo.md" / "el endpoint que usaremos será el de cotización: /info/cotizacion_pedido.md".
- L3 (2026-10-29): Root cause of invisible images: `SpatieMediaLibraryFileUpload` falls back to `config('filament.default_filesystem_disk')` = `env('FILESYSTEM_DISK', 'local')`, so files land in `storage/app/private`; `public/storage` links to the empty `storage/app/public`.
