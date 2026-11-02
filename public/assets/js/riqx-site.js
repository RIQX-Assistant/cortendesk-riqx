/* RIQX: the public /privacy and /Help pages. Same behaviour as riqx.one: the theme follows the sun
   in Tehran unless chosen by hand for the browser session, stars twinkle, sections reveal on scroll. */
(function () {
  var root = document.documentElement, RAD = Math.PI / 180, LAT = 35.69, LNG = 51.39;
  root.classList.add('js');

  function sun(t) {
    var off = 3.5 * 36e5;
    t = Math.floor((t + off) / 864e5) * 864e5 + 12 * 36e5 - off;
    var n = Math.ceil(t / 864e5 + 2440587.5 - 2451545 + 0.0008);
    var js = n - LNG / 360;
    var M = (357.5291 + 0.98560028 * js) % 360;
    var C = 1.9148 * Math.sin(M * RAD) + 0.02 * Math.sin(2 * M * RAD) + 0.0003 * Math.sin(3 * M * RAD);
    var L = (M + C + 180 + 102.9372) % 360;
    var jt = 2451545 + js + 0.0053 * Math.sin(M * RAD) - 0.0069 * Math.sin(2 * L * RAD);
    var dec = Math.asin(Math.sin(L * RAD) * Math.sin(23.4397 * RAD));
    var w = Math.acos((Math.sin(-0.833 * RAD) - Math.sin(LAT * RAD) * Math.sin(dec)) / (Math.cos(LAT * RAD) * Math.cos(dec))) / RAD;
    var ms = function (j) { return (j - 2440587.5) * 864e5; };
    return [ms(jt - w / 360), ms(jt + w / 360)];
  }
  function auto(now) { var s = sun(now); return now >= s[0] && now < s[1] ? 'light' : 'dark'; }
  function manual() { var m = document.cookie.match(/(?:^|;\s*)rq_theme=(light|dark)/); return m ? m[1] : null; }
  function store(mode) {
    var tail = '; path=/; SameSite=Lax' + (location.protocol === 'https:' ? '; Secure' : '');
    document.cookie = mode === 'light' || mode === 'dark' ? 'rq_theme=' + mode + tail : 'rq_theme=; Max-Age=0' + tail;
  }
  function apply() {
    var theme = manual() || auto(Date.now());
    root.setAttribute('data-theme', theme);
    var meta = document.querySelector('meta[name=theme-color]');
    if (meta) meta.content = theme === 'light' ? '#eef3fb' : '#050b18';
  }
  apply();

  document.addEventListener('DOMContentLoaded', function () {
    var reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

    var sw = document.querySelector('.theme-switch');
    function showTheme() {
      var mode = manual() || 'auto', dark = root.getAttribute('data-theme') !== 'light';
      var toggle = sw.querySelector('.ts-toggle');
      sw.dataset.mode = mode;
      toggle.setAttribute('aria-checked', String(dark));
      toggle.setAttribute('aria-label', dark ? sw.dataset.dark : sw.dataset.light);
      sw.querySelector('.ts-auto').setAttribute('aria-pressed', String(mode === 'auto'));
    }
    function setTheme(mode) { store(mode); apply(); showTheme(); }
    sw.querySelector('.ts-toggle').addEventListener('click', function () {
      setTheme(root.getAttribute('data-theme') === 'light' ? 'dark' : 'light');
    });
    sw.querySelector('.ts-auto').addEventListener('click', function () { setTheme('auto'); });
    showTheme();
    setInterval(function () { apply(); showTheme(); }, 60000);

    // Stars: a slow twinkle over the backdrop, drawn about 12 times a second.
    var canvas = document.querySelector('.sky');
    if (canvas && !reduceMotion) {
      var ctx = canvas.getContext('2d'), stars = [], last = 0;
      var size = function () {
        canvas.width = innerWidth; canvas.height = innerHeight;
        stars = [];
        for (var i = 0, n = Math.round(innerWidth * innerHeight / 14000); i < n; i++) {
          stars.push({ x: Math.random() * innerWidth, y: Math.random() * innerHeight, r: .5 + Math.random() * 1.1, p: Math.random() * 6.283, s: .4 + Math.random() * 1.1 });
        }
      };
      var draw = function (now) {
        requestAnimationFrame(draw);
        if (document.hidden || now - last < 80) return;
        last = now;
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#cfe2ff';
        for (var i = 0; i < stars.length; i++) {
          var st = stars[i];
          ctx.globalAlpha = .25 + .55 * (.5 + .5 * Math.sin(st.p + now / 1000 * st.s));
          ctx.beginPath(); ctx.arc(st.x, st.y, st.r, 0, 6.283); ctx.fill();
        }
      };
      addEventListener('resize', size);
      size();
      requestAnimationFrame(draw);
    }

    var toTop = document.querySelector('.to-top'), raf = 0;
    var onScroll = function () {
      raf = 0;
      var max = root.scrollHeight - innerHeight;
      toTop.classList.toggle('show', scrollY > 500);
      toTop.style.setProperty('--p', max > 0 ? (scrollY / max).toFixed(3) : 0);
    };
    addEventListener('scroll', function () { if (!raf) raf = requestAnimationFrame(onScroll); }, { passive: true });
    onScroll();
    toTop.addEventListener('click', function (e) {
      e.preventDefault();
      scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { en.target.classList.add('in'); io.unobserve(en.target); }
      });
    }, { rootMargin: '0px 0px -8% 0px' });
    // What is already on screen never waits for the observer: the text must not depend on it.
    document.querySelectorAll('.reveal').forEach(function (el) {
      if (el.getBoundingClientRect().top < innerHeight) el.classList.add('in');
      else io.observe(el);
    });

    // The table of contents marks the section being read.
    var links = document.querySelectorAll('.toc a'), byId = {};
    links.forEach(function (a) { byId[a.getAttribute('href').slice(1)] = a; });
    var spy = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        links.forEach(function (a) { a.classList.remove('on'); });
        if (byId[en.target.id]) byId[en.target.id].classList.add('on');
      });
    }, { rootMargin: '-20% 0px -70% 0px' });
    document.querySelectorAll('.doc-card').forEach(function (el) { spy.observe(el); });
  });
})();
