import { defineConfig, loadEnv } from 'vite'
import tailwindcss from '@tailwindcss/vite'
import fs from 'node:fs'
import path from 'node:path'

const root = import.meta.dirname
const themeSlug = path.basename(root)
const hotFile = path.join(root, 'hot')

// Writes a `hot` file while the dev server runs so PHP knows to load assets from it.
const hotFilePlugin = () => ({
    name: 'ss-hot-file',
    configureServer(server) {
        server.httpServer?.once('listening', () => {
            const { https } = server.config.server
            const { port } = server.httpServer.address()
            const url = server.config.server.origin ?? `${https ? 'https' : 'http'}://localhost:${port}`
            fs.writeFileSync(hotFile, url)
        })

        const cleanup = () => fs.existsSync(hotFile) && fs.rmSync(hotFile)
        server.httpServer?.once('close', cleanup)
        process.on('exit', cleanup)
        process.on('SIGINT', () => process.exit())
        process.on('SIGTERM', () => process.exit())
    },
})

export default defineConfig(({ command, mode }) => {
    const env = loadEnv(mode, root, '')
    const isBuild = command === 'build'

    return {
        base: isBuild ? `/wp-content/themes/${themeSlug}/dist/` : '/',
        server: {
            port: Number(env.VITE_PORT) || 3000,
            cors: true,
            origin: env.VITE_DEV_SERVER_URL || undefined,
        },
        build: {
            manifest: true,
            outDir: 'dist',
            emptyOutDir: true,
            rollupOptions: {
                input: [
                    'resources/js/app.js',
                    'resources/css/app.css',
                    'resources/css/editor-style.css',
                ],
            },
        },
        plugins: [
            tailwindcss(),
            hotFilePlugin(),
        ],
    }
})
