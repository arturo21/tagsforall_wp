=== Tags4All - WP Plugin for SEO, GEO & API Integration ===
Contributors: arturo21
Tags: seo, geo, aeo, cache, schema, google analytics, meta pixel, hubspot, performance
Requires at least: 6.5
Tested up to: 6.7
Requires PHP: 8.2
Stable tag: 2.0.0
License: GPLv2 or later

Tags4All es un plugin modular para WordPress enfocado en SEO moderno, GEO (Generative Engine Optimization), AEO (Answer Engine Optimization), gestión de caché e integración asíncrona de APIs.

== Description ==

Tags4All v2.0.0 revoluciona la gestión del posicionamiento web en WordPress integrando:

* **GEO y AEO Schema Generator**: Inyección automática de marcado Schema JSON-LD (Article, FAQPage, Person, Organization) optimizado para motores de IA como ChatGPT, Gemini y Perplexity.
* **Caché Nativa e Inspección de Conflictos**: Motor de caché estática con vaciado inteligente y sistema de alertas en WP Admin si detecta otros plugins de caché activos (WP Rocket, LiteSpeed, W3 Total Cache, etc.).
* **Optimización Core Web Vitals**: Minificación segura de HTML, habilitador de imágenes WebP, limpieza de query strings (`?ver=`) y remoción de scripts innecesarios de emojis.
* **Conexión de APIs Externas**: Rastreo asíncrono con Google Analytics 4 (Measurement Protocol), Meta Graph API, YouTube Data API v3 y HubSpot REST API v3.

== Installation ==

1. Sube los archivos del plugin al directorio `/wp-content/plugins/tagsforall/`.
2. Activa el plugin desde el menú 'Plugins' en WordPress.
3. Ve a 'Ajustes' > 'Tags4All SEO' para configurar las APIs y la caché.

== Changelog ==

= 2.0.0 =
* Refactorización completa a PHP 8.5 / PHP 8.2+ con strict types.
* Incorporación del módulo de Caché Nativa y detector de plugins de caché externos.
* Módulo Schema JSON-LD para motores generativos (GEO / AEO).
* Minificación HTML segura con aislamiento de bloques `<script>`, `<style>` y `<pre>`.
* Conectores de APIs actualizados a GA4, Meta Graph API v20+, YouTube v3 y HubSpot v3.
