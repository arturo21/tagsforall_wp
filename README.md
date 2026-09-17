# 🚀 Tags4All v2.0.0 — WordPress Plugin for SEO, GEO, AEO & API Integrations

[![WordPress Version](https://img.shields.io/badge/WordPress-6.5%2B-blue.svg?logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-8.2%20--%208.5-777BB4.svg?logo=php&logoColor=white)](https://php.net)
[![License](https://img.shields.io/badge/License-GPLv2%20%2F%20MIT-green.svg)](LICENSE)
[![SEO & GEO Ready](https://img.shields.io/badge/SEO%20%26%20GEO-AI--Optimized-orange.svg)](#-novedades-seo-geo--aeo)
[![Core Web Vitals](https://img.shields.io/badge/Core%20Web%20Vitals-Optimized-brightgreen.svg)](#-rendimiento--core-web-vitals)

> **Tags4All** es un plugin integral para WordPress diseñado para llevar la optimización web al siguiente nivel: desde el posicionamiento SEO tradicional hasta la optimización para motores generativos de Inteligencia Artificial (**GEO**) y de respuesta (**AEO**), combinando integración de APIs externas, rendimiento de carga extremo (*Core Web Vitals*) y un módulo nativo de caché de página con detección inteligente de conflictos.

---

## 📌 Tabla de Contenidos
- [✨ Novedades en la Versión 2.0.0](#-novedades-en-la-versión-200)
- [⚡ Módulo de Caché Nativo & Detección de Conflictos](#-módulo-de-caché-nativo--detección-de-conflictos)
- [🤖 Optimización SEO, GEO & AEO](#-optimización-seo-geo--aeo)
- [🚀 Rendimiento & Core Web Vitals](#-rendimiento--core-web-vitals)
- [🔌 Integración con APIs Externas](#-integración-con-apis-externas)
- [🛡️ Arquitectura PHP 8.5 & Seguridad](#️-arquitectura-php-85--seguridad)
- [📁 Estructura del Proyecto](#-estructura-del-proyecto)
- [📥 Instalación y Despliegue](#-instalación-y-despliegue)
- [🤝 Contribuciones y Soporte](#-contribuciones-y-soporte)

---

## ✨ Novedades en la Versión 2.0.0

La versión **2.0.0** representa una reconstrucción total del plugin para adaptarse al ecosistema de búsqueda y desarrollo moderno:

| Área | Característica | Beneficio Principal |
| :--- | :--- | :--- |
| **Arquitectura** | Refactorización a **PHP 8.5** con tipado estricto | Mayor velocidad de ejecución, código limpio y manejo robusto de excepciones. |
| **Caché** | Motor estático nativo + Detector de plugins | Evita conflictos de doble caché y acelera la entrega de páginas. |
| **SEO IA** | Marcado **Schema JSON-LD** dinámico (GEO/AEO) | Indexación por ChatGPT, Gemini, Perplexity y resúmenes de IA (*AI Overviews*). |
| **EEAT 2.0** | Integración de esquemas `Person` y `Organization` | Refuerza la autoridad de marca, firma del autor y confiabilidad del sitio. |
| **APIs** | Soporte para GA4, Meta Graph v20+, YouTube v3 y HubSpot | Seguimiento asíncrono sin ralentizar la carga del frontend. |
| **WPO** | Minificación segura HTML + WebP + Clean URLs | Puntuaciones máximas en Google PageSpeed, GTMetrix y Pingdom. |

---

## ⚡ Módulo de Caché Nativo & Detección de Conflictos

Tags4All incluye su propio sistema de almacenamiento en caché de página estática para reducir las consultas a la base de datos MySQL y la carga en el servidor.

### 🛡️ Detección Automática de Conflictos
Si se detecta un plugin de caché de terceros activo (*WP Rocket*, *LiteSpeed Cache*, *W3 Total Cache*, *WP Super Cache*, *WP Fastest Cache*, *Autoptimize*, *SG Speed Optimizer*), el plugin muestra una alerta clara en el panel de **WP Admin**:

```text
⚠️ Advertencia de Rendimiento - Tags4All:
Se ha detectado que los siguientes plugins de caché están activos: WP Rocket.
Tags4All ya incluye un módulo integrado de caché de página y optimización de assets.
Te recomendamos desactivar o desinstalar los otros plugins de caché para evitar conflictos.
```

### 🧹 Funcionalidades del Motor de Caché
- **Caché de Página Estática**: Almacenamiento en `/wp-content/cache/tagsforall/`.
- **Exclusiones Inteligentes**: Omite usuarios autenticados, peticiones `POST` y páginas del carrito/checkout.
- **Purgado Automático**: Invalida la caché al guardar entradas (`save_post`), aprobar comentarios o cambiar configuraciones.
- **Acceso Rápido**: Botón **"🧹 Limpiar Caché Tags4All"** integrado en la barra de herramientas del admin de WordPress.

---

## 🤖 Optimización SEO, GEO & AEO

El plugin evoluciona del SEO tradicional al **GEO** (*Generative Engine Optimization*) y **AEO** (*Answer Engine Optimization*), preparando tu contenido para la era de la Inteligencia Artificial:

- 📄 **Esquemas JSON-LD Automáticos**: Inyecta estructuras `Article`, `FAQPage`, `HowTo` y `Product` respetando el formato nativo sin entidades HTML corruptas.
- 📹 **Vídeo-First Metadata**: Integración con YouTube Data API v3 para inyectar automáticamente esquemas `VideoObject` con transcripciones y marcadores temporales.
- 👤 **EEAT 2.0 (Experience, Expertise, Authoritativeness, Trustworthiness)**: Inclusión de metadatos de autoría (`Person`) vinculados a biografías y perfiles profesionales, junto con la información corporativa (`Organization`).
- 🏷️ **Gestión de Metadatos Social Media**: Generación dinámica de etiquetas Open Graph (`og:title`, `og:description`, `og:image`) y Twitter Cards con protección contra fallos de comillas (`esc_attr()`).

---

## 🚀 Rendimiento & Core Web Vitals

Diseñado específicamente para obtener luz verde en **Google PageSpeed Insights**, **GTMetrix** y **Pingdom**:

- 🗜️ **Minificación Segura de HTML**: Comprime el código eliminando comentarios innecesarios y espacios adyacentes a etiquetas, **protegiendo e isolando automáticamente bloques `<script>`, `<style>` y `<pre>`**.
- 🖼️ **Soporte WebP Nativo**: Añade soporte para el tipo MIME `image/webp` en la biblioteca de medios de WordPress.
- 🧹 **Query String Remover**: Elimina parámetros como `?ver=x.x.x` en archivos CSS y JS para permitir un almacenamiento en caché perfecto en CDNs.
- 🚫 **Emoji Junk Cleaner**: Elimina los scripts y estilos por defecto de emojis de WordPress que sobrecargan el `<head>`.
- ⚡ **Inyección Asíncrona**: Carga los scripts de seguimiento externos con atributos `async` y `defer`.

---

## 🔌 Integración con APIs Externas

Conecta tu sitio web con las principales plataformas del mercado de manera optimizada y centralizada:

```
                  ┌────────────────────────┐
                  │      Tags4All v2       │
                  └───────────┬────────────┘
                              │
     ┌────────────────────────┼────────────────────────┐
     ▼                        ▼                        ▼
┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│  Google GA4  │      │ Meta Graph / │      │ YouTube v3   │
│  Measurement │      │    Pixel     │      │ Data API     │
└──────────────┘      └──────────────┘      └──────────────┘
```

- **Google Analytics 4**: Integración mediante Measurement Protocol API / Tag Manager sin scripts bloqueantes.
- **Meta Graph API & Pixel**: Inyección optimizada del píxel de Facebook y sincronización de eventos Open Graph.
- **YouTube Data API v3**: Extracción automática de metadatos de vídeo para esquemas ricos.
- **HubSpot REST API v3**: Seguimiento de formularios e interacciones de usuario en tiempo real.

---

## 🛡️ Arquitectura PHP 8.5 & Seguridad

El código ha sido refactorizado cumpliendo los estándares más estrictos de desarrollo en WordPress:

- **PHP 8.5 Modern Syntax**: Tipado estricto (`declare(strict_types=1);`), gestión estructurada de excepciones, Enumeraciones y Atributos.
- **WordPress Code Reference**: Uso exclusivo de hooks oficiales de la Plugin API, `wp_remote_get()`, `wp_remote_post()` y endpoints de la **WP REST API**.
- **Protección CSRF**: Todos los formularios del área `/admin` validan tokens de seguridad mediante `wp_verify_nonce()`.
- **Protección XSS**: Sanitización de entradas con `sanitize_text_field()` y escapado en frontend con `esc_attr()` y `esc_html()`.
- **Desinstalación Limpia (`uninstall.php`)**: Borra automáticamente la configuración de `wp_options` y purga las carpetas de caché al eliminar el plugin.

---

## 📁 Estructura del Proyecto

```text
wp-content/plugins/tagsforall/
├── tagsforall.php                     # Archivo principal e inicialización del plugin
├── uninstall.php                      # Script de desinstalación y limpieza total
├── readme.txt                         # Especificación oficial para WordPress.org
├── README.md                          # Documentación visual para GitHub
├── admin/
│   └── class-tagsforall-admin.php     # Panel de administración, pestañas y Nonces
└── includes/
    ├── class-tagsforall-cache.php     # Motor de caché nativo y detector de conflictos
    ├── class-tagsforall-seo-geo.php   # Marcado JSON-LD (GEO/AEO) y metadatos HTML
    ├── class-tagsforall-performance.php # Minificador HTML, WebP y Core Web Vitals
    └── class-tagsforall-api-integrations.php # Conectores asíncronos para GA4, Meta, YT y HubSpot
```

---

## 📥 Instalación y Despliegue

### Instalación Manual
1. Crea la carpeta `tagsforall` dentro del directorio `wp-content/plugins/` de tu instalación de WordPress.
2. Copia las carpetas `admin/` e `includes/` con sus respectivos archivos manteniendo la estructura indicada arriba.
3. Copia `tagsforall.php`, `uninstall.php` y `readme.txt` en la raíz de `wp-content/plugins/tagsforall/`.
4. Ve al panel de WordPress **Plugins > Plugins instalados** y haz clic en **Activar**.

### Despliegue mediante CLI (Bash)
```bash
# Crear estructura de carpetas
mkdir -p wp-content/plugins/tagsforall/admin
mkdir -p wp-content/plugins/tagsforall/includes

# Verificar permisos (Unix)
chmod 755 wp-content/plugins/tagsforall/
```

---

## 🤝 Contribuciones y Soporte

¿Te gusta el proyecto o deseas colaborar?

- 👤 **Autor**: Arturo Vásquez ([@arturo21](https://github.com/arturo21))
- 📜 **Licencia**: GPLv2 / MIT
- 💖 **Donaciones & Colaboración**: Puedes apoyar el desarrollo continuo a través de [PayPal](https://www.paypal.com/paypalme/avsolucionesweb).

---

<p align="center">
  <sub>Desarrollado con ❤️ para impulsar el posicionamiento web del futuro.</sub>
</p>