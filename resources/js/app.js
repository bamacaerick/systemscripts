// Accessible mobile navigation: aria-expanded state, Escape and outside click to close.
function initNavigation() {
    const nav = document.getElementById('primary-navigation')
    const toggle = document.getElementById('primary-menu-toggle')

    if (!nav || !toggle) {
        return
    }

    const isOpen = () => toggle.getAttribute('aria-expanded') === 'true'

    const setOpen = (open) => {
        toggle.setAttribute('aria-expanded', String(open))
        nav.classList.toggle('max-md:hidden', !open)
    }

    toggle.addEventListener('click', () => setOpen(!isOpen()))

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen()) {
            setOpen(false)
            toggle.focus()
        }
    })

    document.addEventListener('click', (event) => {
        if (isOpen() && !nav.contains(event.target) && !toggle.contains(event.target)) {
            setOpen(false)
        }
    })
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNavigation)
} else {
    initNavigation()
}
