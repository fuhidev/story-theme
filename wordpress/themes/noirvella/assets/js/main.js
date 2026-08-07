/**
 * Vanilla, no dependencies, no build step. Everything here is progressive
 * enhancement -- with JS off, the TOC is still a full chapter list, the
 * chapter index is still complete, and the description is still readable
 * (only its clamp toggle disappears, which is why the toggle button ships
 * hidden and this file unhides it).
 */
(function () {
  'use strict';

  var MOBILE_TOC_MAX = 1023;  // must match the breakpoint in main.css
  var HISTORY_KEY = 'nvl_reading_history';
  var HISTORY_MAX = 6;
  var THEME_KEY = 'nvl_theme';

  var i18n = (window.nvlData && window.nvlData.i18n) || {};
  function t(key, fallback) { return i18n[key] || fallback; }

  document.addEventListener('DOMContentLoaded', function () {
    initThemeToggle();
    initToc();
    initNavToggle();
    initReadingHistory();
    initContinueReading();
    initChapterIndex();
    initShowMore();
  });

  /* ------------------------------------------------------------------ */
  /* Light / dark theme toggle                                           */
  /* ------------------------------------------------------------------ */
  function initThemeToggle() {
    var button = document.getElementById('nvl-theme-toggle');
    if (!button) return;

    // The inline script in header.php already set data-theme="dark" on
    // <html> before paint if that was the saved choice -- this just reads
    // that same state back so the button's label/pressed state agrees
    // with what's already on screen.
    function isDark() { return document.documentElement.getAttribute('data-theme') === 'dark'; }

    function sync() {
      var dark = isDark();
      button.setAttribute('aria-pressed', dark ? 'true' : 'false');
      button.setAttribute('aria-label', dark ? t('lightTheme', 'Switch to light theme') : t('darkTheme', 'Switch to dark theme'));
    }

    button.addEventListener('click', function () {
      var next = isDark() ? '' : 'dark';
      if (next) {
        document.documentElement.setAttribute('data-theme', next);
      } else {
        document.documentElement.removeAttribute('data-theme');
      }
      try { localStorage.setItem(THEME_KEY, next || 'light'); } catch (e) { /* private mode -- theme just won't persist */ }
      sync();
    });

    sync();
  }

  /* ------------------------------------------------------------------ */
  /* Table of contents                                                   */
  /* ------------------------------------------------------------------ */
  function initToc() {
    var toc = document.getElementById('nvl-toc');
    var head = toc ? toc.querySelector('.toc-head') : null;
    if (!toc || !head) return;

    var list = toc.querySelector('.toc-list');

    function isSidebar() { return window.innerWidth > MOBILE_TOC_MAX; }

    /**
     * Centres the reader's own chapter in the list, so a 200-entry TOC
     * opens on chapter 137 rather than back at chapter 1. Scoped to the
     * list's own scroll box, so it never moves the page itself.
     */
    function centreCurrent() {
      if (!list || !list.clientHeight) return;
      var current = list.querySelector('li.current');
      if (!current) return;
      list.scrollTop = current.offsetTop - list.clientHeight / 2 + current.offsetHeight / 2;
    }

    function setOpen(open) {
      toc.classList.toggle('open', open);
      head.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open) centreCurrent();
    }

    /**
     * As a sidebar the list is permanently expanded and the header is not
     * a control, so the button must not keep announcing itself as a
     * collapsed toggle. Re-run on resize because the same element crosses
     * the breakpoint both ways.
     */
    function syncState() {
      if (isSidebar()) {
        head.setAttribute('aria-expanded', 'true');
        head.setAttribute('aria-disabled', 'true');
        head.setAttribute('tabindex', '-1');
        centreCurrent();
      } else {
        head.removeAttribute('aria-disabled');
        head.removeAttribute('tabindex');
        head.setAttribute('aria-expanded', toc.classList.contains('open') ? 'true' : 'false');
      }
    }

    head.addEventListener('click', function () {
      // Above the breakpoint the TOC is a static sidebar that is always
      // open; toggling it there would collapse a panel with no visible
      // way to bring it back.
      if (isSidebar()) return;
      setOpen(!toc.classList.contains('open'));
    });

    document.addEventListener('click', function (e) {
      if (isSidebar()) return;
      if (!toc.contains(e.target)) setOpen(false);
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !isSidebar()) setOpen(false);
    });

    window.addEventListener('resize', syncState);
    syncState();
  }

  /* ------------------------------------------------------------------ */
  /* Mobile header                                                       */
  /* ------------------------------------------------------------------ */
  function initNavToggle() {
    var toggle = document.getElementById('nvl-nav-toggle');
    var panel = document.getElementById('nvl-nav');
    if (!toggle || !panel) return;

    toggle.addEventListener('click', function () {
      var open = panel.classList.toggle('open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  /* ------------------------------------------------------------------ */
  /* Reading history (localStorage)                                      */
  /* ------------------------------------------------------------------ */
  function readHistory() {
    try {
      var raw = window.localStorage.getItem(HISTORY_KEY);
      var parsed = raw ? JSON.parse(raw) : [];
      return Array.isArray(parsed) ? parsed : [];
    } catch (e) {
      // Private mode, disabled storage, or corrupted JSON -- the feature
      // is a convenience, so failing silently is correct here.
      return [];
    }
  }

  function writeHistory(entries) {
    try {
      window.localStorage.setItem(HISTORY_KEY, JSON.stringify(entries));
    } catch (e) { /* quota or disabled storage -- nothing to do */ }
  }

  /**
   * Records the chapter currently being read. One entry per story, so
   * reading ten chapters of one serial leaves one "continue" card
   * pointing at the latest one rather than ten stacked cards.
   */
  function initReadingHistory() {
    var wrap = document.querySelector('.chapter-wrap[data-story-id]');
    if (!wrap) return;

    var storyId = wrap.getAttribute('data-story-id');
    if (!storyId) return;

    var entry = {
      storyId: storyId,
      storyTitle: wrap.getAttribute('data-story-title') || '',
      chapterTitle: wrap.getAttribute('data-chapter-title') || '',
      chapterNumber: parseInt(wrap.getAttribute('data-chapter-number'), 10) || 0,
      url: window.location.href,
      ts: Date.now()
    };

    var history = readHistory().filter(function (item) {
      return item && item.storyId !== storyId;
    });

    history.unshift(entry);
    writeHistory(history.slice(0, HISTORY_MAX));
  }

  /**
   * Renders the homepage "Continue reading" strip. The section ships
   * hidden from PHP and is only unhidden once there is something to put
   * in it -- reading history is per-browser, so it cannot come from the
   * server without breaking page caching.
   */
  function initContinueReading() {
    var section = document.getElementById('nvl-continue');
    var track = document.getElementById('nvl-continue-track');
    if (!section || !track) return;

    function render() {
      var history = readHistory();
      track.textContent = '';

      if (!history.length) {
        section.hidden = true;
        return;
      }

      history.forEach(function (item) {
        if (!item || !item.url) return;

        var card = document.createElement('div');
        card.className = 'continue-card';

        var link = document.createElement('a');
        link.href = item.url;

        var story = document.createElement('span');
        story.className = 'continue-story';
        story.textContent = item.storyTitle || '';
        link.appendChild(story);

        var chapter = document.createElement('span');
        chapter.className = 'continue-chapter';
        chapter.textContent = item.chapterNumber
          ? t('chapter', 'Chapter %d').replace('%d', item.chapterNumber) + ' · ' + (item.chapterTitle || '')
          : (item.chapterTitle || '');
        link.appendChild(chapter);

        var resume = document.createElement('span');
        resume.className = 'continue-resume';
        resume.textContent = t('resume', 'Resume') + ' →';
        link.appendChild(resume);

        card.appendChild(link);

        var remove = document.createElement('button');
        remove.type = 'button';
        remove.className = 'continue-remove';
        remove.setAttribute('aria-label', t('remove', 'Remove from history'));
        remove.textContent = '×';
        remove.addEventListener('click', function () {
          writeHistory(readHistory().filter(function (other) {
            return other && other.storyId !== item.storyId;
          }));
          render();
        });
        card.appendChild(remove);

        track.appendChild(card);
      });

      section.hidden = false;
    }

    render();
  }

  /* ------------------------------------------------------------------ */
  /* Chapter index on the story page                                     */
  /* ------------------------------------------------------------------ */
  function initChapterIndex() {
    var list = document.getElementById('nvl-chapter-index-list');
    if (!list) return;

    var filter = document.getElementById('nvl-chapter-filter');
    var order = document.getElementById('nvl-chapter-order');
    var empty = document.querySelector('.chapter-index-empty');
    var items = Array.prototype.slice.call(list.querySelectorAll('.chapter-index-item'));

    if (filter) {
      filter.addEventListener('input', function () {
        var term = filter.value.trim().toLowerCase();
        var visible = 0;

        items.forEach(function (item) {
          // Matching the chapter number as well as the title is what makes
          // typing "42" jump straight to chapter 42, which is how readers
          // actually use this box.
          var match = !term
            || (item.getAttribute('data-title') || '').indexOf(term) !== -1
            || (item.getAttribute('data-num') || '') === term;
          item.hidden = !match;
          if (match) visible++;
        });

        if (empty) empty.hidden = visible !== 0;
      });
    }

    if (order) {
      var label = order.querySelector('.chapter-order-label');
      order.addEventListener('click', function () {
        var next = order.getAttribute('data-order') === 'asc' ? 'desc' : 'asc';
        order.setAttribute('data-order', next);
        if (label) label.textContent = next === 'asc' ? t('oldestFirst', 'Oldest first') : t('newestFirst', 'Newest first');

        // Re-append in the new order rather than rebuilding the markup, so
        // nothing about the rows (links, dates, current highlight) can
        // drift out of sync with what the server rendered.
        var sorted = items.slice().sort(function (a, b) {
          var an = parseInt(a.getAttribute('data-num'), 10) || 0;
          var bn = parseInt(b.getAttribute('data-num'), 10) || 0;
          return next === 'asc' ? an - bn : bn - an;
        });
        sorted.forEach(function (item) { list.appendChild(item); });
      });
    }
  }

  /* ------------------------------------------------------------------ */
  /* Story description clamp                                             */
  /* ------------------------------------------------------------------ */
  function initShowMore() {
    var body = document.getElementById('nvl-story-description-body');
    var toggle = document.getElementById('nvl-story-description-toggle');
    if (!body || !toggle) return;

    // A three-line synopsis doesn't need a "Show more" button, and the
    // clamp would be invisible anyway -- drop both in that case.
    if (body.scrollHeight <= body.clientHeight + 8) {
      body.classList.remove('is-clamped');
      return;
    }

    toggle.hidden = false;
    toggle.addEventListener('click', function () {
      var clamped = body.classList.toggle('is-clamped');
      toggle.setAttribute('aria-expanded', clamped ? 'false' : 'true');
      toggle.textContent = clamped ? t('showMore', 'Show more') : t('showLess', 'Show less');
    });
  }
})();
