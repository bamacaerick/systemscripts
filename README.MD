# SystemScripts

A base WordPress theme for all SystemScripts projects. Classic PHP theme with `theme.json`, built with
[Tailwind CSS](https://tailwindcss.com) v4 and [Vite](https://vite.dev).

Planned: native custom blocks, schema.org JSON-LD graph, SEO, and WCAG 2.2 AA accessibility.
No paid plugins required.

## Requirements

- WordPress 6.5+, PHP 8.1+
- Node.js 24 LTS (see `.nvmrc`) and [pnpm](https://pnpm.io) 10+

## Getting started

```bash
pnpm install
pnpm dev     # Vite dev server (writes a `hot` file so PHP loads assets from it)
pnpm build   # production build into dist/
```

The theme folder should be named `systemscripts`; the built asset URLs are derived from the folder name.
Copy `.env.example` to `.env` to change the dev server port or URL.

## Conventions

- Slug and text domain: `systemscripts`
- PHP namespace: `SystemScripts\` (autoloaded from `src/`)
- Function, hook and filter prefix: `ss_`

## Credits

Originally forked from [TailPress](https://tailpress.io) by Jeffrey van Rossum (MIT).

## Authors

SystemScripts Team — [bamacaerick](https://www.linkedin.com/in/bamacaerick/)

## License

MIT. See [LICENSE](LICENSE).
