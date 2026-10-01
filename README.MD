# SystemScripts

A base WordPress theme for SystemScripts projects. Classic PHP theme with `theme.json` design tokens, built with
[Tailwind CSS](https://tailwindcss.com) v4 and [Vite](https://vite.dev).

- **Native custom blocks** (no ACF, no plugins): hero, call to action, card grid, FAQ, testimonials
- **SEO**: meta description, canonical, robots, Open Graph / Twitter cards, per-post SEO fields
- **Structured data**: one schema.org JSON-LD `@graph` (Organization, WebSite, WebPage, BreadcrumbList,
  BlogPosting, Person, FAQPage, ...)
- **Authorship**: author profile fields, author box, `rel="author"`, `Person` schema
- **Accessibility**: built for WCAG 2.2 AA (skip link, landmarks, focus states, labelled forms, AA contrast)
- No paid plugins required. English only (translation-ready).

## Requirements

- WordPress 6.5+, PHP 8.1+
- Node.js 24 LTS (see `.nvmrc`) and [pnpm](https://pnpm.io) 10+
- Composer (only for PHP linting)

## Getting started

```bash
pnpm install
pnpm dev     # Vite dev server (writes a `hot` file so PHP loads assets from it)
pnpm build   # production build into dist/
```

The theme folder must be named `systemscripts`: built asset URLs are derived from the folder name.
Copy `.env.example` to `.env` to change the dev server port or URL. `dist/` is not committed; build it before
deploying (the release workflow does this).

## Settings

**Appearance → Customize → SystemScripts**

| Section | What it controls |
| --- | --- |
| Organization & Schema | Publisher type (Organization / Local business / Person), name, contact, address, social profile URLs |
| SEO & Social | Default sharing image, X/Twitter handle, disable author archives |
| Features & Tracking | Disable comments, Google Tag Manager or GA4 ID |

Per post/page: an **SEO** box (title override, meta description, noindex).
Per user: **Author details** on the profile screen (job title, expertise, profile links).

If Yoast, Rank Math, SEOPress, All in One SEO or The SEO Framework is active, the theme stops printing its own
meta tags, canonical and JSON-LD to avoid duplicates (filter: `ss_seo_external_active`).

## Design tokens

`theme.json` is the single source of truth for colors, fonts, font sizes, spacing, shadows and radii.
`resources/css/theme.css` maps those variables into Tailwind (`bg-primary`, `text-muted`, `rounded-lg`, ...).

The default palette is chosen for WCAG AA contrast. Check new colors before changing them:
text needs 4.5:1, UI borders and focus rings 3:1.

## Blocks

Blocks live in `blocks/<name>/` (`block.json` + `render.php`). The `ss.fields` list in `block.json` defines the
attributes and the editor controls (text, textarea, url, select, toggle, image, repeater). A shared script,
`blocks/editor.js`, builds the sidebar UI and a live server-side preview; there is no JavaScript build step for blocks.

To add a block, copy an existing folder, edit `block.json` and `render.php`. Tailwind picks up classes from
`blocks/**/*.php` automatically. Do not use a literal `>` inside a class name (see `CLAUDE.md`).

## Filters

| Filter | Purpose |
| --- | --- |
| `ss_schema_graph` | Add or change nodes in the JSON-LD graph |
| `ss_schema_webpage_type` | Override the WebPage type |
| `ss_schema_article_types` | Map post types to Article types (default: `post` → `BlogPosting`) |
| `ss_schema_search_action` | Return false to drop the site search action |
| `ss_seo_external_active` | Force the SEO back-off on or off |
| `ss_breadcrumbs` | Change the breadcrumb trail |
| `ss_show_author_box`, `ss_show_related_posts` | Toggle those template parts |
| `ss_analytics_enabled` | Return false until the visitor consents |
| `ss_disable_xmlrpc`, `ss_block_user_enumeration` | Turn off the security defaults |

Template hooks: `ss_site_before`, `ss_header`, `ss_content_start`, `ss_content_end`, `ss_content_after`, `ss_footer`.

## Quality checks

```bash
pnpm lint:js   # ESLint
pnpm lint:php  # PHPCS (security, escaping, i18n rules)
pnpm i18n      # regenerate languages/systemscripts.pot (needs WP-CLI)
pnpm a11y      # axe via pa11y-ci against a running site; edit .pa11yci.json first
```

Beyond the automated checks, test with the keyboard only (Tab, Enter, Escape), at 200% zoom and at 320px width.

## Conventions

- Slug and text domain: `systemscripts`
- PHP namespace: `SystemScripts\` (autoloaded from `src/`), function/hook/filter prefix: `ss_`
- All output escaped; all user-facing strings translatable

## Credits

Originally forked from [TailPress](https://tailpress.io) by Jeffrey van Rossum (MIT).

## Authors

SystemScripts Team: [bamacaerick](https://www.linkedin.com/in/bamacaerick/)

## License

MIT. See [LICENSE](LICENSE).
