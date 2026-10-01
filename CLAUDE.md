# SystemScripts theme

Classic WordPress base theme: Tailwind v4 + Vite, native blocks, JSON-LD schema, SEO, accessibility.

- Slug/text domain `systemscripts`, namespace `SystemScripts\` (`src/`), prefix `ss_`. English only.
- No paid-plugin dependencies. ACF is not used.
- pnpm only. Node per `.nvmrc`. `pnpm dev` / `pnpm build` / `pnpm lint`.
- Author: SystemScripts Team (bamacaerick). Original fork: TailPress (MIT), keep its notice in LICENSE.
- Escape all output; no hardcoded demo content in templates.

## Gotchas learned the hard way

- **theme.json holds tokens only.** Its `styles` output is unlayered CSS and beats Tailwind utilities
  (a link color there overrides `text-white` on buttons). Element defaults live in `resources/css/base.css`.
- **Layer order beats specificity.** Order is base < components < utilities. `custom.css` and `components.css`
  are in the `components` layer. Keep content selectors inside `:where()`: `:is()` takes the highest argument's
  specificity and will beat `.ss-button`.
- **Never put `>` in a class on block output** (for example `[&>*]:mt-0`). `wpautop` runs on post content after
  blocks render and splits the tag at the `>`, breaking the markup. Use plain utilities on the children instead.
- Block text values are escaped; textareas go through `Blocks::paragraphs()`. Heading tags go through `tag_escape()`.
- `__()` must not run before `init` (WP 6.7+ notice). Keep translated labels out of code that runs on load.
- Single-character text nodes (like a text arrow) are skipped by contrast checkers: use SVG icons.
- `ss` is a short prefix; PHPCS `PrefixAllGlobals` is therefore off. Keep new code namespaced.

## Verification

`pnpm lint`, then run `pnpm a11y` against a local site. Check keyboard use, 320px reflow, and view the page.
Static checks do not catch layout bugs.
