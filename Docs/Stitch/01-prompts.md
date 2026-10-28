# Stitch Prompts — Mastersoft Store

How to use:

1. Paste **Base context** first in a new Stitch project (or prepend it to each prompt).
2. Then paste one screen prompt at a time. Generate desktop, then ask for the mobile variant.
3. Keep every result in the same Stitch project so the style stays consistent.

---

## Base context

```text
Design a modern e-commerce storefront for "Mastersoft", a Peruvian store selling computer and office accessories, networking gear (access points, USB Wi-Fi adapters), cables and adapters (HDMI, VGA, USB, Ethernet), printer supplies (Epson maintenance boxes, paper) and tools.

All UI text must be in Spanish (Peru). Currency is soles, written "S/." (example: S/. 380.00).

Style: clean, technical and trustworthy, lots of white space, mobile-first.
Colors: brand lime green #A4CE3A for primary buttons, active states and accents (dark text #1F2A0A on green, never white); darker green #6E8F1E for brand-colored text on white; red-orange #F92400 only for sale prices and alerts; text #1E1E1E; secondary text #6B6B6B; light section background #F5F5F5; footer #2B2B2B; WhatsApp buttons #25D366.
Typography: Roboto for headings and prices, Open Sans for body.
Shapes: 12px card radius, pill-shaped primary buttons and chips, soft shadows, thin #E5E5E5 borders, outline icons.

Global header: thin top bar ("Envíos a todo el Perú", phone, WhatsApp); main bar with logo "Mastersoft", wide search field "Buscar producto", WhatsApp contact button, cart icon with item count badge and total "S/. 0.00"; nav bar with a "Categorías" mega-menu button and links Inicio, Tienda, Nosotros, Contáctanos.
Floating round WhatsApp button at bottom-right on every page.
Global footer (dark): column 1 logo and short description; column 2 "Enlaces" (Inicio, Tienda, Nosotros, Contáctanos, Políticas de privacidad, Términos y condiciones); column 3 "Contacto" with address, phone, email and "Síguenos" social icons (YouTube, Facebook, LinkedIn, Instagram); column 4 "Libro de Reclamaciones" with the book icon, "RUC: 20614007584" and "TROYA COMPANY E.I.R.L.". Bottom bar: "© Mastersoft, Todos los derechos reservados." and "Distribuido por Grupo VesergenPerú".

Product card: square product image on #F5F5F5, small category label, product name (max 2 lines), price, optional "Oferta" badge with struck original price, "Agregar al carrito" pill button, quick-view eye icon on hover.
```

---

## Home

```text
Screen: Home page of Mastersoft.

Sections in order:
1. Global header.
2. Hero: large banner carousel (about 75% width) with dots, next to a tall static promotional side banner (25% width). Banners show products like a Brother DCP-T730DW printer and a 27" curved gaming monitor.
3. Service strip with 4 icon items: "Devolución gratuita – Garantía de 30 días", "Envío gratis – En todos los pedidos", "Soporte 24/7 – Atención en línea", "Pedidos por WhatsApp – Rápido y sin registro".
4. "Compra por categoría": row of circular or card category shortcuts: Access Point, Adaptadores, USB Inalámbrico, Herramientas, Papel, Sensores, Alcohol Isopropílico, Parlantes, "Ver todas".
5. "Nuestras promociones": two wide promotional cards side by side, each linking to a product.
6. "Nuestros Productos": pill tabs "Destacados" and "Recién llegados", grid of 8 product cards (4 per row), "Ver toda la tienda" button. Examples: Ubiquiti PowerBeam M5 S/. 380.00, Adaptador USB a VGA, Adaptador HDMI a VGA, Adaptador USB a Ethernet.
7. "Productos más vendidos": compact horizontal cards (small image left, name and price right) in 3 columns, 6 items, e.g. Caja de mantenimiento Epson, Adaptador USB Wi-Fi D-Link.
8. WhatsApp call-to-action band: "¿No encuentras lo que buscas? Escríbenos y te ayudamos" with a green WhatsApp button.
9. Global footer.
```

Mobile follow-up:

```text
Now the mobile version (390px) of the Home page: compact header with logo, search icon, cart with count and burger menu; hero carousel full width (hide side banner or stack it below); service strip as a horizontal scroll; categories as a horizontal scroll of chips; product grid 2 per row; footer columns stacked; floating WhatsApp button.
```

---

## Tienda

```text
Screen: Store catalog page "Tienda".

1. Global header.
2. Compact page title "Tienda" with breadcrumb "Inicio / Tienda".
3. Two-column layout:
   - Left sidebar (25%): search field "Buscar en la tienda"; "Categorías" list with product counts in small green badges (single selection); "Marcas" list with counts (e.g. Ubiquiti, TP-Link, D-Link, Epson, Brother); "Precio" range with min and max inputs; "Limpiar filtros" text button; one promotional banner at the bottom.
   - Main area (75%): toolbar with result count "48 productos", active filter chips with an x to remove, sort select "Ordenar por: Relevancia / Menor precio / Mayor precio / Nombre A-Z", grid/list view toggle. Product grid 3 per row, 12 cards. Pagination at the bottom.
4. Show one product card in "Agotado" state (greyed image, disabled button) — design only, out-of-stock products are hidden by default.
5. Global footer.
```

Mobile follow-up:

```text
Mobile version of Tienda: sticky bar under the header with "Filtrar" and "Ordenar" buttons; filters open in a bottom sheet with the same groups and an "Aplicar filtros" button; product grid 2 per row; "Cargar más" button instead of numbered pagination.
```

---

## Detalle

```text
Screen: Product detail page.

1. Global header, breadcrumb "Inicio / Tienda / Access Point / Ubiquiti PowerBeam M5".
2. Two-column top area:
   - Left: image gallery with a large main image and a row of thumbnails, zoom on hover.
   - Right: category label "Access Point"; product name "Ubiquiti PowerBeam M5 PBE-M5-400"; brand "Marca: Ubiquiti"; price "S/. 380.00" large (optional struck "S/. 456.00" and "Oferta" badge); stock "Disponible: 12 en stock" with a green dot; short description paragraph; quantity selector (− 1 +); primary button "Agregar al carrito" and a secondary green outline button "Consultar por WhatsApp"; small trust row: "Envío a todo el Perú", "Garantía", "Pedido por WhatsApp".
3. Tabs: "Descripción" and "Información técnica" (rich text with bullet lists). No reviews tab.
4. "También te puede interesar": carousel of 4–6 product cards from the same category.
5. Global footer.
```

Mobile follow-up:

```text
Mobile version of the product detail: swipeable gallery with dots, info stacked below, sticky bottom bar with price, quantity and "Agregar al carrito", tabs as accordions.
```

---

## Carrito

```text
Screen: Shopping cart, two parts.

Part A — cart drawer that slides in from the right after "Agregar al carrito": title "Tu carrito (3)", list of items with thumbnail, name, unit price, quantity stepper and remove icon; subtotal; buttons "Ver carrito" (outline) and "Enviar pedido por WhatsApp" (green WhatsApp button).

Part B — full cart page "Carrito de compras":
- Left: table/list of items with image, name, category, unit price, quantity stepper, line total and remove icon; "Vaciar carrito" text button; "Seguir comprando" link.
- Right sticky summary card "Resumen del pedido": Subtotal (sin IGV), IGV (18%), Total in large type, note "Los precios incluyen IGV", primary green WhatsApp button "Enviar pedido por WhatsApp", small text "Confirmaremos stock, envío y forma de pago por WhatsApp."
- Empty state: illustration of an empty cart, "Tu carrito está vacío", button "Ir a la tienda".
```

---

## Pedido

```text
Screen: "Enviar pedido por WhatsApp" — short order form, no payment.

Layout: two columns.
Left — form "Tus datos":
- Toggle "Persona" / "Empresa". Persona: Nombre, Apellidos, DNI (optional). Empresa: RUC, Razón social.
- Celular / WhatsApp with country code selector defaulting to Peru +51.
- Email (optional).
- "Entrega": radio cards "Recojo en tienda" and "Envío a domicilio". If envío: Departamento, Provincia, Distrito (cascading selects) and Dirección.
- "Forma de pago preferida": chips "Transferencia bancaria", "Pago contra entrega".
- "Notas del pedido (opcional)" textarea.
Right — sticky "Tu pedido" card: compact item list with quantities and line totals, Subtotal, IGV, Total, big green button "Enviar pedido por WhatsApp" with WhatsApp icon, helper text "Se abrirá WhatsApp con el detalle de tu pedido para que lo confirmes."

Mobile: single column, order summary collapsible at the top, sticky bottom button.
```

---

## Confirmación

```text
Screen: Order sent confirmation.
Centered card: green check icon, title "¡Pedido enviado!", text "Te escribiremos por WhatsApp para confirmar stock, envío y pago.", order code "Pedido #MS-000123", compact summary of items and total, buttons "Abrir WhatsApp" (green, in case the app did not open) and "Seguir comprando" (outline).
```

---

## Nosotros

```text
Screen: "Nosotros" (About us).
1. Global header, page title "Nosotros".
2. Split section: image of the store or team on the left; on the right a kicker "Quiénes somos", title, two paragraphs, a stat tile "8 Años de experiencia", and a phone callout "¿Tienes alguna pregunta?" with the phone number.
3. Three cards with icons: "Misión", "Visión", "Valores".
4. Brands we work with: row of grey logos (Ubiquiti, TP-Link, D-Link, Epson, Brother).
5. WhatsApp call-to-action band and global footer.
```

---

## Contacto

```text
Screen: "Contáctanos".
1. Global header, page title "Contáctanos", subtitle "¡Estamos aquí para ti! ¿Cómo podemos ayudarte?".
2. Four info tiles: Dirección, Teléfono / WhatsApp, Email, Horario de atención.
3. Two columns: left form "Envía tu mensaje" (kicker "Vamos a conectarnos") with Nombre, Email, Teléfono, Asunto, Mensaje (500 characters max counter) and button "Enviar mensaje"; right an embedded map of Nueva Cajamarca, San Martín, Perú.
4. Global footer.
```

---

## Reclamaciones

```text
Screen: "Libro de Reclamaciones" (official Peruvian consumer complaint book, INDECOPI).
Header block: official book icon, title "Libro de Reclamaciones", company "TROYA COMPANY E.I.R.L. – RUC 20614007584", date, auto claim number "Hoja N.° 000123".
Form in numbered sections (cards):
1. "Datos del consumidor": Tipo de documento (DNI, CE, Pasaporte), Número de documento, Nombres, Apellido paterno, Apellido materno, Fecha de nacimiento, Email, Teléfono (+51 selector), Departamento, Provincia, Distrito, Dirección. Checkbox "Soy menor de edad" revealing "Datos del padre, madre o apoderado".
2. "Identificación del bien contratado": radio "Producto" / "Servicio", Orden de compra, Monto reclamado (S/.), Descripción.
3. "Detalle de la reclamación": radio cards "Reclamo" (disconformidad con el producto o servicio) and "Queja" (malestar con la atención), Detalle textarea, Pedido del consumidor textarea.
4. Consent checkbox and legal note: "La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante el INDECOPI." Button "Enviar reclamo".
Success state: "Reclamo registrado – Hoja N.° 000123. Te enviamos una copia a tu email."
```

---

## Legal

```text
Screen: legal content page, reused for "Políticas de privacidad" and "Términos y condiciones".
Compact header band with title and "Última actualización: 28 de octubre de 2026"; centered readable column (max 760px) with headings, paragraphs and lists; sticky table of contents on the left on desktop; global header and footer.
```

---

## Estados vacíos

```text
Screens: utility states in the same style.
1. 404: "Página no encontrada", search field and button "Volver al inicio".
2. Search with no results: "No encontramos productos para 'cable usb c'", suggestions of categories and a WhatsApp help button.
3. Toast notifications: "Producto agregado al carrito" (success, with "Ver carrito" link) and "Stock insuficiente" (error).
```
