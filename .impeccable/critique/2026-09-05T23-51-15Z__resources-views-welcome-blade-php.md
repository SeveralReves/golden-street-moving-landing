---
target: landing page (welcome.blade.php)
total_score: 24
max_score: 36
na_heuristics: 7
p0_count: 2
p1_count: 2
target_identity: "file:/home/gabo/Projects/golden-street-moving-landing/resources/views/welcome.blade.php"
target_fingerprint: "sha256:12ed9213a515f34aa2c809dc28a27ffcf1407b4055c6c210aad1d662e31067de"
target_path: /home/gabo/Projects/golden-street-moving-landing/resources/views/welcome.blade.php
timestamp: 2026-09-05T23-51-15Z
slug: resources-views-welcome-blade-php
closed: true
---
Method: dual-agent (A: revisión de diseño · B: detector + evidencia de navegador)

## Puntaje de Salud del Diseño

| # | Heurística | Puntaje | Hallazgo clave |
|---|-----------|-------|-----------------|
| 1 | Visibilidad del estado del sistema | 3 | El wizard de booking ("Step 1 of 3" + barra dorada) comunica progreso con claridad |
| 2 | Coincidencia con el mundo real | 2 | El link de nav "Contact" abre el formulario de cotización, no info de contacto real |
| 3 | Control y libertad del usuario | 2 | En mobile no hay navegación alguna — imposible saltar a otra sección |
| 4 | Consistencia y estándares | 3 | El sistema de componentes es fiel a DESIGN.md; el etiquetado del nav no lo es |
| 5 | Prevención de errores | 3 | El sistema define texto de error en dorado; sin verificar en vivo |
| 6 | Reconocimiento antes que recuerdo | 2 | El nav desaparece por completo en mobile |
| 7 | Flexibilidad y eficiencia | n/a | No aplica a una landing page de conversión única |
| 8 | Diseño estético y minimalista | 3 | Limpio en general; las fotos rompen la coherencia (ver P2) |
| 9 | Recuperación de errores | 3 | Sin verificar en vivo, tokens de diseño presentes |
| 10 | Ayuda y documentación | 3 | El FAQ responde preguntas operativas reales |

**Total: 24/36** (heurística 7 marcada n/a, renormalizado sobre 9 heurísticas aplicables) → banda **Bueno** (67%).

## Veredicto de Especificidad de Diseño

**Evaluación de diseño (Assessment A):** Mixto. El copy y la arquitectura de información cargan verdad de producto real — precio por hora, ventana de 48h para reprogramar, niveles de seguro, un wizard de cotización real de varios pasos con direcciones. Pero el lenguaje visual lo traiciona: la foto del hero (un remolque utilitario bajo cielo nublado) no se lee como "The Golden Hour Move" de DESIGN.md, el banner de mudanzas interestatales muestra una caja de madera improvisada amarrada en una camioneta abierta — contradiciendo el copy adyacente de "cobertura completa, tracking en tiempo real" — y nada en la página señala que el público principal es el área de Atlanta. Quitando la paleta dorado/navy, esta podría ser la plantilla de cualquier mudancera regional.

**Escaneo determinístico (Assessment B):** 0 hallazgos en el escaneo estático de los archivos Blade. El escaneo en vivo (Puppeteer, vía `impeccable detect` contra `http://localhost:8000/`) encontró 20 hallazgos en desktop y 15 en mobile: 14/12 de `low-contrast`, 4 de `line-length`, 1 `clipped-overflow-container` (`div.booking__card` recorta un hijo posicionado), y advertencias menores de `overused-font`. La mayoría de los `low-contrast` recaen sobre elementos con `data-aos="fade-up"` — AOS los renderiza con `opacity:0` hasta que un scroll real dispara `.aos-animate`, y una sola pasada de captura automatizada no siempre dispara ese reveal. Assessment A topó independientemente con el mismo fenómeno, lo corrigió con captura por scroll incremental, y al hacerlo no encontró problemas de contraste en esos textos — la mayoría de esos hallazgos son artefactos de temporización del escaneo, no bugs reales.

**Lo que sí queda confirmado por ambos métodos, de forma independiente:** el hallazgo `#ffffff sobre #dbbd1a (1.9:1, se necesita 4.5:1)` es el color en reposo del botón `.button__primary` — no depende de AOS, aparece en ambos viewports del detector, y Assessment A lo identificó por separado como el problema P0 de contraste en los CTAs. Esa coincidencia entre las dos evaluaciones ciegas es real, no casualidad.

## Impresión General

El sistema de componentes está ejecutado con fidelidad real a DESIGN.md — botones espejados, cards con hover-lift, esquinas redondeadas consistentes — y el copy tiene sustancia operativa genuina. Pero dos problemas de alto impacto socavan exactamente los dos momentos que más importan en una landing de conversión: los CTAs (contraste ilegible) y la prueba social (reseñas fabricadas con nombres duplicados). La mayor oportunidad no es rediseñar el sistema — es cerrar la brecha entre lo que el sistema promete (calidez, confianza, "golden hour") y lo que la fotografía real y el nav mobile actualmente entregan.

## Lo que Funciona

- **Wizard de booking**: "Step 1 of 3" + barra de progreso dorada dentro de una card Midnight Navy reduce genuinamente la intimidación de un formulario largo.
- **Fidelidad del sistema de componentes**: el par de botones dorado↔navy espejados, las cards con elevación en hover, y el radio de esquina consistente se ejecutan igual en cada sección, según DESIGN.md.
- **Contenido del FAQ**: responde preguntas operativas concretas (precio por hora, cancelación de 48h, niveles de seguro) en vez de relleno genérico.

## Problemas Prioritarios

**[P0] Reseñas fabricadas + datos estructurados falsos.**
Por qué importa: `section-reviews.blade.php` muestra "John Doe", "Jane Smith", fotos de stock y una URL falsa de Google — con "John Doe" duplicado como la 5ª entrada. El `AggregateRating` de JSON-LD se calcula sobre estos datos falsos y se envía a buscadores. PRODUCT.md ya marcó esto como algo que no debe tratarse como real.
Fix: ocultar la sección (o mostrar un estado honesto "reseñas reales próximamente") y quitar el JSON-LD hasta tener datos reales.
Comando sugerido: /impeccable harden

**[P0] Los botones CTA fallan el contraste.**
Por qué importa: `.button__primary` usa texto blanco sobre dorado (#dbbd1a) — contraste ≈1.9:1 contra el mínimo AA de 4.5:1. Afecta a todos los CTAs de la página (Book your date now, 3× Request this service, Continue, Contact Us, Book Your Move Now) — todo el mecanismo de conversión. Confirmado de forma independiente por ambas evaluaciones.
Fix: cambiar el texto a Ink Olive o Midnight Navy sobre el fondo dorado.
Comando sugerido: /impeccable harden

**[P1] No hay navegación alguna en mobile.**
Por qué importa: `header__ul` es `display:none` por debajo de 1025px, sin botón de menú hamburguesa en `header.blade.php`. En mobile solo queda el logo y un CTA — sin forma de saltar a Servicios, FAQ o Reviews.
Fix: agregar un toggle de menú mobile.
Comando sugerido: /impeccable layout

**[P1] Etiquetas de navegación mal alineadas/duplicadas.**
Por qué importa: "Books" (typo) y "Contact" apuntan ambos a `/#booking`; "Home" apunta al `/#` mal formado. Un visitante que hace clic en "Contact" esperando teléfono/email termina en el formulario de cotización.
Fix: renombrar "Books" → "Booking", dar a "Contact" un destino real.
Comando sugerido: /impeccable clarify

**[P2] La fotografía contradice la promesa de marca.**
Por qué importa: el hero (remolque bajo cielo nublado), la card de "Residential Moving" (parece un garage/bodega) y el banner de mudanzas interestatales (camioneta abierta con caja de madera amarrada) contradicen el posicionamiento de "cuidado, asegurado, calidez dorada".
Fix: nueva sesión fotográfica en luz cálida/golden-hour con equipo y camión reales.
Comando sugerido: /impeccable adapt

## Alertas por Persona

**Jordan (primerizo confundido):** hace clic en "Contact" esperando un número de teléfono y cae en el formulario de cotización; en mobile no encuentra "FAQ" ni "Testimonials" sin scrollear todo, porque no existe nav.

**Riley (probador metódico):** detecta la reseña duplicada de "John Doe" (texto y fecha idénticos, dos veces); marca el texto de los CTAs como difícil de leer sobre el dorado; hace clic en "Books" y "Contact" y descubre que llevan al mismo destino.

**Casey (usuario mobile distraído):** no tiene navegación en mobile más allá del botón CTA; la imagen más grande en su scroll rápido es la foto poco profesional de la camioneta de mudanza interestatal, justo después del FAQ.

## Observaciones Menores

- `layouts/default.blade.php` carga la fuente Bunny "Figtree" pero nada la usa (Montserrat/Inter cargan aparte vía `_font.scss`) — petición de red muerta.
- El reveal de AOS no tiene guard de `prefers-reduced-motion` ni fallback `<noscript>` — el contenido es invisible sin JS + un scroll real.
- `.section__reviews` define `border-radius: 18px` en una sección full-bleed — invisible en la práctica, CSS muerto.
- El detector marcó `div.booking__card` recortando un hijo posicionado (`clipped-overflow-container`) en ambos viewports — vale la pena verificarlo en vivo (podría ser el dropdown del datepicker); si recorta un elemento interactivo sería más serio de lo que parece. Sugerido: una pasada rápida de /impeccable audit.
- El detector marcó `overused-font` (Inter ~70% del texto) y párrafos de ~107 caracteres/línea (se recomienda <80) — no crítico, pero vale la pena en una pasada de /impeccable typeset.
- Sin errores de consola ni requests rotos detectados en ningún viewport (verificado por Assessment B) — la base técnica está sana.

## Preguntas para Considerar

- ¿Qué pasaría si la sección de reviews simplemente dijera "Reseñas reales próximamente — reserva con confianza hoy" en vez de mostrar datos falsos?
- ¿Cuánto cambiaría el veredicto de especificidad con solo rehacer la fotografía del hero y del banner en luz cálida real, sin tocar el layout?
- ¿Qué tanto de las alertas de Casey se resolverían solo con colapsar el header a un botón de menú + CTA persistente en mobile?
