import js from '@eslint/js'
import globals from 'globals'

export default [
    { ignores: ['dist/', 'node_modules/', 'vendor/'] },
    js.configs.recommended,
    {
        // Front-end bundle (ES modules, built by Vite).
        files: ['resources/**/*.js'],
        languageOptions: { sourceType: 'module', globals: globals.browser },
    },
    {
        // Block editor script: plain script using the WordPress globals, no build step.
        files: ['blocks/**/*.js'],
        languageOptions: {
            sourceType: 'script',
            globals: { ...globals.browser, wp: 'readonly' },
        },
    },
    {
        files: ['*.mjs'],
        languageOptions: { sourceType: 'module', globals: globals.node },
    },
]
