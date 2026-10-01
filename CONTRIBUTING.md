# Contributing

- Use `pnpm` (never npm/yarn) and the Node version in `.nvmrc`.
- Prefix all global PHP functions, hooks, filters and options with `ss_`; classes live in the `SystemScripts\` namespace.
- All user-facing strings use the `systemscripts` text domain; escape all output.
- Follow `.editorconfig` and the WordPress coding standards (`composer lint`).
- Keep markup accessible (WCAG 2.2 AA): landmarks, labels, focus states, contrast.
