# Changelog

All notable changes to SystemScripts are documented in this file.

## Unreleased

### Identity and tooling
- Forked from TailPress 5.0.2 and renamed to SystemScripts (slug `systemscripts`, namespace `SystemScripts`, prefix `ss_`).
- Removed the `tailpress/framework` dependency; own `Setup` and `Assets` classes and a PSR-4 autoloader.
- pnpm, Node 24 LTS, Vite 8, Tailwind CSS 4.3. Vite config no longer hardcodes the theme path or dev domain.
- ESLint, PHPCS (security/i18n rules), CI workflow, release workflow on current actions.
- Translation template `languages/systemscripts.pot`.

### Design system
- `theme.json` is the single source of truth for tokens, mapped into Tailwind.
- New WCAG AA palette (the TailPress primary and secondary failed contrast at 3.76:1 and 2.14:1).
- Grid based `alignwide` / `alignfull` replaces the 100vw and fixed-pixel hacks.

### Templates and accessibility
- New: page, author, full-layout 404, loop/archive-header/content-none/related-posts parts, footer menu and widget areas.
- Skip link, `wp_body_open`, accessible mobile menu (aria-expanded, Escape, outside click, no-JS fallback),
  labelled `type="search"` form, real comment form labels and autocomplete, accessible pagination.
- Exactly one `<h1>` per view. Removed the TailPress demo hero and inline microdata.

### Blocks, SEO, schema, authorship
- Native blocks without ACF: hero, call to action, card grid, FAQ, testimonials, and a landing page pattern.
- Meta description, canonical, robots, Open Graph, Twitter cards, per-post SEO fields. Backs off for SEO plugins.
- JSON-LD `@graph` with stable ids, including FAQPage from the FAQ block.
- Author profile fields, author box and author archive, published/updated dates, optional author archive switch.
- Customizer settings for organization data, social profiles, tracking IDs and feature switches.
- Security defaults: XML-RPC off, user enumeration blocked, cleaned `<head>`.
