# SystemScripts

SystemScripts is a custom, classic PHP theme for WordPress. It is the site's theme (not a child theme) and combines
PHP templates with `theme.json`, native WordPress blocks, Tailwind CSS 4 and Vite. It does not require ACF or other
paid plugins.

## Features

- **Design system:** WordPress `theme.json` presets for colors, typography, spacing, shadows and radii, shared with
  Tailwind CSS.
- **Templates:** responsive layouts for pages, posts, archives, author archives, search results and 404 pages;
  primary and footer menus; two footer widget areas.
- **Native blocks:** Hero, call to action, card grid, FAQ and testimonials, plus a landing page pattern. Block controls
  and server-rendered previews are provided without a separate JavaScript build.
- **SEO:** descriptions, canonical and robots metadata, Open Graph and X/Twitter cards, and per-post SEO fields.
  Built-in metadata is disabled when a supported SEO plugin is active to avoid duplicate output.
- **Structured data:** a JSON-LD graph for the organization/person, website, pages, breadcrumbs, articles and FAQ
  content where applicable.
- **Author profiles:** additional author details, author headers and boxes, and optional author archives.
- **Customizer settings:** organization/schema details, social profiles, SEO defaults, comment settings, and Google
  Tag Manager or Google Analytics 4 IDs.
- **Accessibility:** skip link, semantic landmarks, keyboard-friendly responsive navigation, labelled forms, visible
  focus states and responsive block alignment. Check the finished design with assistive technology and manual
  keyboard testing.
- **Security defaults:** XML-RPC and user enumeration are disabled by default and can be controlled with filters.

## Requirements

- WordPress 6.5 or later
- PHP 8.1 or later
- Node.js 24 LTS (see `.nvmrc`) and pnpm 10 or later for asset development
- Composer for PHP coding-standard checks

## Getting started

Run these commands from the theme directory:

```bash
pnpm install
pnpm dev     # Start Vite; a temporary `hot` file points WordPress to the dev server
pnpm build   # Build production assets into dist/
```

The theme folder must be named `systemscripts`; the production asset URL is derived from the folder name. Copy
`.env.example` to `.env` to configure the development server port or URL. `dist/` is generated and not committed,
so build it before deploying the theme.

## Customizing the design

Start with `theme.json` for global colors, fonts, font sizes, spacing, radii, shadows and content widths. These values
are the design-token source of truth; `resources/css/theme.css` maps them to Tailwind utilities and provides editor
and build fallbacks.

Use the files under `resources/css/` for presentation:

- `base.css` contains element defaults.
- `components.css` contains shared component styles.
- `custom.css` is the project-specific stylesheet.
- `utilities.css` contains utility-layer adjustments.
- `editor-style.css` provides styles for the block editor.

`resources/css/app.css` imports these styles. Prefer changing tokens and reusable styles before adding one-off rules.
Tailwind scans the theme templates and block PHP files for utility classes. The theme is already custom-built for this
site, so you can edit it directly; a child theme is not needed.

## WordPress settings

In **Appearance → Customize → SystemScripts**:

| Section | Settings |
| --- | --- |
| Organization & Schema | Publisher type (organization, local business or person), name, contact details, address and social profile URLs |
| SEO & Social | Default sharing image, X/Twitter handle and author archive switch |
| Features & Tracking | Site-wide comments switch, Google Tag Manager ID or Google Analytics 4 ID |

SEO fields for individual posts and pages include a title override, meta description and `noindex` option. Author
details are managed on each user's profile. Assign the primary and footer menus through WordPress menu settings; footer
widget areas are managed under **Appearance → Widgets**.

## Blocks and patterns

Each block lives in `blocks/<name>/` with a `block.json` definition and a server-side `render.php` template. The
`ss.fields` definition supplies editor controls; `blocks/editor.js` renders the controls and live preview. To add a
block, follow an existing block's structure and update its metadata and render template. Tailwind picks up classes
from `blocks/**/*.php` automatically.

The theme also registers a SystemScripts landing page pattern. Do not put a literal `>` inside a block output class
name; see `CLAUDE.md` for the reason and other implementation notes.

## Extension points

| Hook | Purpose |
| --- | --- |
| `ss_schema_graph` | Change or add nodes in the JSON-LD graph |
| `ss_schema_webpage_type` | Override the WebPage type |
| `ss_schema_article_types` | Map post types to Article types (defaults to `post` → `BlogPosting`) |
| `ss_schema_search_action` | Return `false` to remove the search action |
| `ss_seo_external_active` | Control whether built-in SEO output backs off |
| `ss_breadcrumbs` | Change the breadcrumb trail |
| `ss_show_author_box`, `ss_show_related_posts` | Toggle those template parts |
| `ss_analytics_enabled` | Return `false` to disable analytics output |
| `ss_disable_xmlrpc`, `ss_block_user_enumeration` | Control the security defaults |

Template action hooks: `ss_site_before`, `ss_header`, `ss_content_start`, `ss_content_end`, `ss_content_after` and
`ss_footer`.

## Development checks

```bash
pnpm lint:js   # ESLint
pnpm lint:php  # PHPCS
pnpm i18n      # Regenerate the translation template (requires WP-CLI)
pnpm a11y      # Run pa11y-ci against a running site
```

Before release, also check keyboard-only use, 200% zoom, 320px viewport width, contrast and the block editor. Static
checks do not replace visual and assistive-technology testing.

## Conventions

- Theme slug and text domain: `systemscripts`
- PHP namespace: `SystemScripts\` (`src/`, loaded by the theme autoloader)
- Function and hook prefix: `ss_`
- Escape output, sanitize input and keep user-facing strings translatable.

## Credits and license

SystemScripts is based on [TailPress](https://tailpress.io) by Jeffrey van Rossum. The original work is MIT-licensed;
its copyright notice is retained in [LICENSE](LICENSE). SystemScripts is also distributed under the MIT License.
