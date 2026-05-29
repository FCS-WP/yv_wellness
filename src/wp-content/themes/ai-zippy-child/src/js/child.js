// =============================================================================
// Child theme entry — client-specific JS goes here.
// The parent theme (ai-zippy) handles core behaviors; use this file only for
// client overrides or new features that don't belong in core.
// =============================================================================

import { initScrollReveal } from './modules/scroll-reveal.js';
import { initMobileMenu } from './modules/mobile-menu.js';

document.addEventListener('DOMContentLoaded', () => {
  initScrollReveal();
  initMobileMenu();
});

console.log("[ai-zippy-child] loaded");
