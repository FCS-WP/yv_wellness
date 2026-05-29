/**
 * Scroll Reveal — Material-flavored entrance animations
 * JS toggles .is-visible state class; CSS handles all visuals.
 * Uses IntersectionObserver for zero-dependency, GPU-friendly reveals.
 */

const OBSERVER_OPTIONS = {
  threshold: 0.15,
  rootMargin: '0px 0px -60px 0px',
};

/**
 * Assign --stagger-index custom property to children of stagger groups.
 */
function indexStaggerChildren() {
  document.querySelectorAll('.stagger-group').forEach((group) => {
    const children = group.querySelectorAll('[data-animate-child]');
    children.forEach((child, i) => {
      child.style.setProperty('--stagger-index', i);
    });
  });
}

/**
 * IntersectionObserver callback — one-shot reveal.
 */
function handleIntersect(entries, observer) {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    }
  });
}

/**
 * Initialize scroll reveal on all [data-animate] elements.
 */
export function initScrollReveal() {
  const elements = document.querySelectorAll('[data-animate]');
  if (!elements.length) return;

  // Pre-index stagger children
  indexStaggerChildren();

  const observer = new IntersectionObserver(handleIntersect, OBSERVER_OPTIONS);

  elements.forEach((el) => {
    if (el.dataset.animate === 'immediate') {
      // Above-the-fold: reveal after first paint
      requestAnimationFrame(() => {
        el.classList.add('is-visible');
      });
    } else {
      observer.observe(el);
    }
  });
}
