# Stitch Review Notes

Decisions taken while reviewing Stitch exports. Implementation follows `00-brief.md` plus these notes; Stitch HTML is visual reference only.

## Global rules for implementation

| Topic | Rule |
|---|---|
| Tokens | Do not copy Stitch's Tailwind config. Its `primary` is `#4d6700`; our brand lime `#A4CE3A` is `primary`, per `00-brief.md`. |
| Header | One responsive Blade partial. **No user/account icon** (Stitch keeps adding it). |
| Footer | One responsive partial built from `00-brief.md` §4. Stitch footers differ per screen and per breakpoint; ignore them. |
| Icons | Lucide/Heroicons outline via Blade Icons, not Material Symbols. |
| Mobile WhatsApp | Bottom action rail already has WhatsApp; hide the floating button on mobile to avoid duplicates. |
| Data | All business data (phone, email, address, schedule, socials) comes from the admin. Stitch values are placeholders. |
| Images | Stitch images are hotlinked to googleusercontent and will expire. Placeholders only. |
| Screenshots | Stitch `screen.png` exports are broken (28 bytes). Render `code.html` instead. |
| No SKU | Stitch keeps showing "SKU". The product model has none; do not render it (also not in WhatsApp prefill). |
| No wishlist | Ignore heart/favorite icons. Share icon (Web Share API) is allowed. |
| Sale price | **Decided (2026-10-28): include from day one.** Nullable `regular_price` (compare-at) on products, edited manually in the admin. "Oferta", struck price and "Ahorro" render only when it is set and greater than `price`; otherwise the card shows the normal price only. |
| No coupons | There is no coupon system. Remove any "Cupón" field and the fake "TECNO10" feedback. RUC/razón social belong to the order form, not the cart. |
| Payment methods | Do not list Yape, Plin, BCP, BBVA or "link de pago" until the business confirms them. Legacy offered only "Transferencia bancaria" and "Pago contra entrega". |
| Shipping cost | Show "Por coordinar" without an amount (no "S/. 0.00"). |
| Low stock | "Últimas N unidades" (mobile cart) is allowed: derived from `stock` under a configurable threshold. Other per-item lines ("Stock verificado Lima / Regiones", "Alta compatibilidad…") are invented. |
| Overlaps | Hide the floating WhatsApp button while the cart drawer is open and on mobile. Money columns use `whitespace-nowrap`. Never let the floating button cover a primary CTA. |
| Inputs | Every input has a visible background or border (Stitch's desktop order form leaves some fields as bare placeholders). |
| Rich text | Product "Descripción" and "Información técnica" are admin rich text. Stitch's highlight cards, topology image and spec blocks are just example content, not structured fields. |

## Claims to confirm with the business before publishing

- "Envío gratis", "Devolución gratuita / 30 días", "Soporte 24/7".
- "Despacho Nacional Olva & Shalom Express".
- "Factura Electrónica — Boleta o Factura SUNAT".
- "Stock en tiempo real".
- "Garantía local 12 meses", "100% Original", "Nuevo / Sellado".
- Couriers "Olva Courier / Shalom / Marvisur", "despacho express hoy".
- "Emisión de Boleta o Factura a solicitud", "Recojo en tienda (sin costo adicional)" and the store pickup address.
- "Distribuidor autorizado" of any brand, and the brand logos shown on Nosotros.
- Store address, opening hours ("Domingos: Cerrado") and FAQ answers.
- Privacy policy and terms: real texts from the business or a lawyer, never Stitch's.
- "Órgano competente: INDECOPI – Sede Regional San Martín" and the claims email address.
- "Confirmamos en menos de 5 minutos", "entrega inmediata", "Venta segura".
- Never publish "Garantía Real con SUNAT" (SUNAT does not guarantee products).
- Never publish "Pasarela 100% protegida con encriptación TLS 256-bit": there is no payment gateway.
- Opening hours "Lun - Sáb: 8:30am - 7:30pm".

## Screens

| # | Screen | Folder | Status | Notes |
|---|---|---|---|---|
| 1 | Home | `plantillas/01` | Approved | Pending items covered by global rules. |
| 2 | Tienda | `plantillas/02` | Approved | Categories: single select (radio). Brands: multi select (checkbox), backend must accept several brands. Price range filter and sort are new vs. legacy. Mobile: "Cargar más" instead of pagination. Mobile footer shows an invented Lima address; ignore. |
| 3 | Detalle de producto | `plantillas/03` | Approved | Desktop: gallery with thumbnails and zoom, tabs "Descripción Detallada" / "Información Técnica", no reviews. Mobile: swipe gallery, accordions, sticky bottom bar (price, quantity, "Agregar"). Related cards may show stock. Drop the "Rango: 25+ Km PtP" image overlay (no such field). |
| 4 | Carrito (page + drawer) | `plantillas/04` | Approved | Page: item table, "Vaciar carrito", "Seguir comprando", summary (Subtotal sin IGV, IGV 18%, Envío por coordinar, Total). Drawer: item list, "Ver Carrito Detallado", WhatsApp CTA; add a quantity stepper (Stitch shows static "Cant: 1"). Mobile: stacked item cards, progress "Carrito • Coordinación • Envío", sticky total bar. The cart CTA navigates to the order form (screen 5); it does not open WhatsApp directly. The "Ver Drawer Lateral" button is a Stitch demo toggle; ignore. |
| 5 | Enviar pedido por WhatsApp | `plantillas/05` | Approved | Sections: 1 datos (Persona natural: Nombres, Apellidos, DNI opcional; Empresa: RUC, Razón Social, Persona de contacto; Celular +51; Correo opcional), 2 entrega (Envío a domicilio o agencia with Departamento/Provincia/Distrito, Dirección o agencia, Referencia; Recojo en tienda hides address fields), 3 pago preferido, 4 notas. Summary card with "Por coordinar" shipping. **Add a required consent checkbox** linking to Políticas de privacidad (Ley 29733, personal data). Mobile has no footer (focused checkout): keep at least a minimal footer with the Libro de Reclamaciones link. Validation: DNI 8 digits, RUC 11 digits, Peru mobile 9 digits. Persist the order (with ubigeo, payment preference and notes, which legacy dropped) before opening `wa.me`, and include the order code in the message. Ubigeo data can come from legacy `public/js/ubigeo.js`. |
| 6 | Confirmación de pedido | `plantillas/06` | Approved with fixes | Keep: check icon, order code with date, "Abrir WhatsApp" fallback, 3 next steps, items, delivery and payment preference, totals, "Seguir comprando", print. Fix: the main heading must be the largest text and read **"¡Pedido registrado!"** (the customer still has to send the WhatsApp message, so "enviado" is inaccurate); drop "REGISTRO EXITOSO EN SISTEMA" and "Ticket en espera de validación técnica"; remove bank names after "Transferencia bancaria"; remove the empty space in the totals card and keep the total on one line; drop the bottom trust row. Same minimal-footer rule as the order form. |
| 7 | Libro de Reclamaciones | `plantillas/06` | Approved | Matches the legal structure: provider data, correlative "Hoja N.° 000123-2026" with date, 1 consumer (Tipo/Número de documento, Nombres, Apellido paterno/materno, Teléfono, Correo, ubigeo, Dirección, minor → apoderado), 2 bien contratado (Producto/Servicio, N° pedido, Monto, Descripción), 3 Reclamo/Queja with definitions, Detalle, Pedido del consumidor, 4 legal notice "La formulación del reclamo no impide…", privacy consent (Ley 29733), 15 días hábiles. Fixes: Provincia/Distrito as cascading selects like the order form; the email-notification checkbox must not be pre-checked; drop "copia digital certificada" (just "copia") and "v2.4 • Sistema Digital". Fix legacy bug: the amount was never sent. Admin side (Filament): claims resource with status, provider response ("acciones adoptadas"), response date and business-days countdown. |
| 8 | Nosotros | `plantillas/07` | Approved (layout) | Maps to the legacy About fields: image, kicker/title, description, years of experience, phone callout, Misión / Visión / Valores, plus a brands row (from the Brand table, logos only for brands the business really distributes). Drop: "Desde 2018", "+3,400 paquetes / 98.6% entregas puntuales", "Comprobado", "Distribuidor Autorizado", "menos de 10 minutos", "Stock permanente en almacén". Do not use the AI store photo (garbled "MASTERSET PERU" sign). Fix the overlapping floating cards on the image. |
| 9 | Contáctanos | `plantillas/07` | Approved (layout) | Info tiles (Dirección, Teléfono/WhatsApp, Correo, Horario), form (Nombre, Correo, Celular +51, Asunto select, Mensaje 500 chars, unchecked privacy consent), map, FAQ. Persist messages in the DB and notify by email (legacy only emailed). Asunto options and FAQ entries are admin content. Map: Google Maps embed of the real store location. Drop "Datos protegidos bajo cifrado seguro", "Envíos garantizados por agencias certificadas" and all FAQ answers Stitch wrote. |
| 10 | Legal | `plantillas/07` | Approved (layout only) | Tabs Políticas de Privacidad / Términos y Condiciones, sticky table of contents, "Última actualización", readable column. **All legal text Stitch wrote is invented and must never be published** (TLS, banks, Yape/Plin, Lima dispatch, response times). Real texts come from the business or its lawyer and are edited in the admin (legacy used Markdown models). Drop "Documento Oficial Regulado" badge; "Descargar en PDF" is out of scope for now. |
| 11 | Estados vacíos | `plantillas/07` | Approved | 404 with search and links, search without results with suggested categories and WhatsApp CTA, toasts (added to cart with "Ver carrito", insufficient stock with "Ajustar cantidad", copied order code). Stitch rendered it as a showcase page: ignore the "Plantillas de Sistema", "Modo vista previa UI", "Integración Livewire" and version chrome. Replace "ERROR DE ENRUTAMIENTO WEB" with plain language and drop "almacén central". |

**Stitch phase complete (2026-10-28): all 11 screens reviewed.**
