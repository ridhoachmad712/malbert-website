document.querySelectorAll('.menu-toggle').forEach((button) => {
    const menu = document.getElementById(button.getAttribute('aria-controls'));
    if (!menu) return;

    const closeMenu = () => {
        button.setAttribute('aria-expanded', 'false');
        menu.classList.remove('open');
        menu.querySelectorAll('details[open]').forEach((details) => { details.open = false; });
    };

    button.addEventListener('click', () => {
        const expanded = button.getAttribute('aria-expanded') === 'true';
        button.setAttribute('aria-expanded', String(!expanded));
        menu.classList.toggle('open', !expanded);
        if (expanded) closeMenu();
    });

    menu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', closeMenu);
    });

    document.addEventListener('keydown', (event) => {
        const expandedDetails = menu.querySelector('details[open]');
        if (event.key === 'Escape' && (menu.classList.contains('open') || expandedDetails)) {
            const wasOpen = menu.classList.contains('open');
            closeMenu();
            if (wasOpen) {
                button.focus();
            } else {
                expandedDetails?.querySelector('summary')?.focus();
            }
        }
    });

    document.addEventListener('click', (event) => {
        if (!button.contains(event.target) && !menu.contains(event.target)) {
            closeMenu();
        }
    });
});
