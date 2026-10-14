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

document.querySelectorAll('[data-target-type]').forEach((select) => {
    const prefix = select.name.replace(/target_type$/, '');
    const fields = [...select.form.querySelectorAll('[data-target-field]')].filter((label) =>
        [...label.querySelectorAll('input, select')].some((input) => input.name.startsWith(prefix))
    );
    const update = () => {
        fields.forEach((label) => {
            const visible = label.dataset.targetField === select.value;
            label.hidden = !visible;
            label.querySelectorAll('input, select').forEach((input) => { input.disabled = !visible; });
        });
    };
    select.addEventListener('change', update);
    update();
});

document.querySelectorAll('[data-teacher-carousel]').forEach((carousel) => {
    const track = carousel.querySelector('.teacher-carousel-track');
    const controls = carousel.querySelector('.teacher-carousel-controls');
    const previous = carousel.querySelector('[data-teacher-prev]');
    const next = carousel.querySelector('[data-teacher-next]');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const hoverCapable = window.matchMedia('(hover: hover)');
    let visible = false;
    let touchPaused = false;
    let touchTimeout;
    let interval;

    const canScroll = () => track.scrollWidth > track.clientWidth + 2;
    const step = () => {
        const card = track.querySelector('.teacher-card');
        return card ? card.getBoundingClientRect().width + parseFloat(getComputedStyle(track).columnGap || 0) : track.clientWidth;
    };
    const move = (direction) => {
        const end = track.scrollWidth - track.clientWidth;
        const target = direction > 0
            ? (track.scrollLeft >= end - 4 ? 0 : Math.min(end, track.scrollLeft + step()))
            : (track.scrollLeft <= 4 ? end : Math.max(0, track.scrollLeft - step()));
        track.scrollTo({ left: target, behavior: reducedMotion.matches ? 'auto' : 'smooth' });
    };
    const stop = () => {
        window.clearInterval(interval);
        interval = undefined;
    };
    const start = () => {
        stop();
        if (!visible || !canScroll() || reducedMotion.matches || document.hidden || touchPaused || carousel.contains(document.activeElement) || (hoverCapable.matches && carousel.matches(':hover'))) return;
        interval = window.setInterval(() => move(1), 5000);
    };
    const update = () => {
        controls.hidden = !canScroll();
        start();
    };

    previous?.addEventListener('click', () => move(-1));
    next?.addEventListener('click', () => move(1));
    carousel.addEventListener('mouseenter', stop);
    carousel.addEventListener('mouseleave', start);
    carousel.addEventListener('focusin', stop);
    carousel.addEventListener('focusout', () => window.setTimeout(start, 0));
    carousel.addEventListener('touchstart', () => {
        touchPaused = true;
        window.clearTimeout(touchTimeout);
        stop();
    }, { passive: true });
    carousel.addEventListener('touchend', () => {
        touchTimeout = window.setTimeout(() => { touchPaused = false; start(); }, 8000);
    }, { passive: true });
    document.addEventListener('visibilitychange', start);
    reducedMotion.addEventListener('change', start);
    window.addEventListener('resize', update);

    if ('IntersectionObserver' in window) {
        new IntersectionObserver(([entry]) => {
            visible = entry.isIntersecting;
            start();
        }, { threshold: 0.2 }).observe(carousel);
    } else {
        visible = true;
    }
    update();
});
