/**
 * Mobile Menu — toggles .is-open on the overlay container and handles
 * accessibility (focus trap, ARIA state, ESC + outside-click dismiss).
 */

const SELECTORS = {
  hamburger: '.site-header__hamburger',
  mobileMenu: '.site-header__mobile-menu',
  overlay: '.site-header__mobile-menu-overlay',
  closeBtn: '.site-header__mobile-close',
  panel: '.site-header__mobile-menu-panel',
};

const FOCUSABLE =
  'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])';

export function initMobileMenu() {
  const hamburger = document.querySelector(SELECTORS.hamburger);
  const mobileMenu = document.querySelector(SELECTORS.mobileMenu);
  const overlay = document.querySelector(SELECTORS.overlay);
  const closeBtn = document.querySelector(SELECTORS.closeBtn);
  const panel = document.querySelector(SELECTORS.panel);

  if (!hamburger || !mobileMenu || !panel) return;

  let lastFocused = null;

  function getFocusable() {
    return Array.from(panel.querySelectorAll(FOCUSABLE)).filter(
      (el) => !el.hasAttribute('disabled') && el.offsetParent !== null
    );
  }

  function open() {
    lastFocused = document.activeElement;
    mobileMenu.classList.add('is-open');
    mobileMenu.setAttribute('aria-hidden', 'false');
    hamburger.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';

    // Move focus to the close button for an immediate, predictable trap.
    requestAnimationFrame(() => {
      if (closeBtn) closeBtn.focus();
    });
  }

  function close() {
    if (!mobileMenu.classList.contains('is-open')) return;
    mobileMenu.classList.remove('is-open');
    mobileMenu.setAttribute('aria-hidden', 'true');
    hamburger.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';

    if (lastFocused && typeof lastFocused.focus === 'function') {
      lastFocused.focus();
    } else {
      hamburger.focus();
    }
  }

  function handleKeydown(e) {
    if (!mobileMenu.classList.contains('is-open')) return;

    if (e.key === 'Escape') {
      e.preventDefault();
      close();
      return;
    }

    if (e.key === 'Tab') {
      const focusables = getFocusable();
      if (focusables.length === 0) {
        e.preventDefault();
        return;
      }
      const first = focusables[0];
      const last = focusables[focusables.length - 1];
      const active = document.activeElement;

      if (e.shiftKey && active === first) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && active === last) {
        e.preventDefault();
        first.focus();
      }
    }
  }

  hamburger.addEventListener('click', open);
  if (closeBtn) closeBtn.addEventListener('click', close);
  if (overlay) overlay.addEventListener('click', close);

  document.addEventListener('keydown', handleKeydown);

  // Close on nav link activation so route changes feel snappy.
  panel.querySelectorAll('.site-header__mobile-nav a').forEach((link) => {
    link.addEventListener('click', close);
  });
}
