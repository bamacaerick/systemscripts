# Changelog

All notable changes to SystemScripts are documented in this file.

## Unreleased

- Forked from TailPress 5.0.2 and renamed to SystemScripts (slug `systemscripts`, namespace `SystemScripts`, prefix `ss_`).
- Removed the `tailpress/framework` dependency; added own `Setup` and `Assets` classes and a PSR-4 autoloader.
- Switched to pnpm, Node 24 LTS, Vite 8 and Tailwind CSS 4.3.
- Vite config no longer hardcodes the theme path or dev domain.
- Removed the TailPress demo hero from `header.php`.
- Updated the release workflow (pnpm, Node from `.nvmrc`, current actions).
