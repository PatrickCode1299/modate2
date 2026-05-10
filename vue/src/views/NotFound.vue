<template>
  <main class="page-404" role="main" aria-labelledby="page-title">
    <Header style="background-color:white;" class="header-layer" />

    <!-- Background image layer + heavy blur for frosted effect -->
    <div class="bg-layer" aria-hidden="true"></div>
    <div class="bg-overlay" aria-hidden="true"></div>

    <!-- dim vignette to increase contrast -->
    <div class="vignette" aria-hidden="true"></div>

    <!-- subtle particles (muted) -->
    <div class="particles" aria-hidden="true">
      <div
        v-for="(p, i) in particles"
        :key="i"
        class="particle"
        :style="{
          left: p.x + '%',
          top: p.y + '%',
          width: p.size + 'px',
          height: p.size + 'px',
          animationDelay: p.delay + 's',
          opacity: p.opacity
        }"
      />
    </div>

    <section class="stage" :aria-describedby="'exhibit-desc'">
      <!-- Main glass card -->
      <article
        class="glass-card"
        id="page-title"
        tabindex="-1"
        ref="mainCard"
        aria-labelledby="page-title"
      >
        <div class="header-row">
          <div class="badge">EXHIBIT • 404</div>
          <div class="meta">Interdimensional Web Wastebin</div>
        </div>

        <h1 class="title" id="page-title">404 — Lost In The Source</h1>
        <p class="lead" id="exhibit-desc">
          Welcome to the Museum of Misplaced Pages — catalogued with shame, annotated
          with sarcasm, and preserved behind glass.
        </p>

        <div class="exhibit" aria-live="polite">
          <!-- image frame -->
          <div
            class="img-frame"
            @mousemove="tilt"
            @mouseleave="resetTilt"
            ref="imgFrame"
            :aria-hidden="false"
          >
            <img
              :src="current.picture"
              :alt="current.title"
              class="exhibit-img"
              :style="imgTransform"
              loading="eager"
              draggable="false"
            />

            <div class="plaque" role="doc-subtitle">
              <div class="plaque-title">{{ current.title }}</div>
              <div class="plaque-sub">{{ current.subtitle }}</div>
            </div>
          </div>

          <!-- copy -->
          <div class="exhibit-copy">
            <h2 class="exhibit-heading">Object Description</h2>
            <p class="description">{{ current.description }}</p>

            <div class="mini-meta" aria-hidden="false">
              <div>Exhibit ID: <strong>{{ exhibitId }}</strong></div>
              <div>Found: <strong>{{ foundDate }}</strong></div>
              <div>Curator: <strong>{{ curator }}</strong></div>
            </div>

            <div class="controls" role="group" aria-label="Image controls">
              <button class="ctrl" @click="prev" aria-label="Previous exhibit" title="Previous (←)">
                ◀
              </button>
              <button class="ctrl accent" @click="randomize" aria-label="Random exhibit" title="Random (R)">
                🎲 Surprise
              </button>
              <button class="ctrl" @click="next" aria-label="Next exhibit" title="Next (→)">
                ▶
              </button>
            </div>

            <div class="action-row">
              <RouterLink to="/" class="home-btn" title="Return home">⬅ Return to homepage</RouterLink>
              <button class="ghost-btn" @click="viewAll" title="Browse exhibits">Browse other exhibits</button>
            </div>
          </div>
        </div>

        <div class="satire-grid" aria-hidden="false">
          <div class="card small">
            <h3>Possible Reasons</h3>
            <ul>
              <li><code>display:none</code> applied as protective custody.</li>
              <li>Dev wrote <em>"TODO: later™"</em> and the later never arrived.</li>
              <li>It ran away with a 500 Server Error — a tragic romance.</li>
            </ul>
          </div>

          <div class="card small">
            <h3>Lost &amp; Found</h3>
            <ol>
              <li>One missing semicolon (console evidence available)</li>
              <li>A designer's optimism</li>
              <li>~3GB of orphaned PNGs</li>
            </ol>
          </div>

          <div class="card small">
            <h3>Curator's Note</h3>
            <p class="note">
              "Preserve with coffee. Fix with refactors. Do not feed after midnight."
            </p>
          </div>
        </div>
      </article>

      <!-- Sidebar -->
      <aside class="sidebar" aria-labelledby="sidebar-title">
        <h3 id="sidebar-title">Museum Catalogue</h3>

        <div class="sidebar-section roster-wrap" aria-live="polite">
          <h4>Exhibit Roster</h4>
          <ul class="roster" role="list">
            <li
              v-for="(p, idx) in images"
              :key="idx"
              :class="{active: idx === currentIndex}"
            >
              <button class="roster-btn" @click="select(idx)" :aria-pressed="idx===currentIndex">
                <img :src="p.thumb" :alt="p.title" class="thumb" />
                <div class="roster-text">
                  <strong class="roster-title">{{ p.title }}</strong>
                  <span class="roster-sub">{{ p.subtitle }}</span>
                </div>
              </button>
            </li>
          </ul>
        </div>

        <div class="sidebar-section facts-wrap">
          <h4>Quick Facts</h4>
          <dl class="facts">
            <div><dt>Pages catalogued</dt><dd>{{ images.length }}</dd></div>
            <div><dt>Hours open</dt><dd>Always (the internet never sleeps)</dd></div>
            <div><dt>Admission</dt><dd>Free (but tears accepted)</dd></div>
          </dl>
        </div>

        <div class="sidebar-section credits">
          <h4>Museum Credits</h4>
          <p>Curated by <strong>{{ curator }}</strong></p>
          <p class="small">Powered by empathy and too much CSS.</p>
        </div>
      </aside>
    </section>

    <Footer v-if="user_mail === null" class="footer-fixed" />
  </main>
</template>

<script setup>
/* Full interactive logic with accessible controls + particles + tilt */
import { ref, reactive, computed, onMounted } from 'vue';
import Header from '../component/Header.vue';
import Footer from '../component/Footer.vue';

let user_mail = localStorage.getItem('USER_MAIL') || null;

/* ---------- Exhibit data ---------- */
/* Adjust asset paths if your files are elsewhere */
const images = [
  {
    picture: new URL('@/assets/spidey.jfif', import.meta.url).href,
    thumb: new URL('@/assets/spidey.jfif', import.meta.url).href,
    title: 'Spider-Man Contemplates Web Design',
    subtitle: 'A weary hero and his CSS',
    description:
      "After fighting bugs and IE, Spider-Man took up responsive design. He tried to center a div and accidentally centered the entire universe. The page wandered off politely.",
  },
  {
    picture: new URL('@/assets/milk.jfif', import.meta.url).href,
    thumb: new URL('@/assets/milk.jfif', import.meta.url).href,
    title: 'A Noble Carton of Milk',
    subtitle: 'Spoiled dreams, refrigerated.',
    description:
      "This page expired the same week someone said 'we'll refactor later'. A dignified end amongst cereal and sticky notes.",
  },
  {
    picture: new URL('@/assets/cat_new.jpg', import.meta.url).href,
    thumb: new URL('@/assets/cat_new.jpg', import.meta.url).href,
    title: 'Sir Whiskerton, PhD (Backend)',
    subtitle: 'Debugging out loud',
    description:
      "Sir Whiskerton ran `rm -rf /` while trying to clear cache. He insists it was a misunderstanding. The missing page sympathizes.",
  },
  {
    picture: new URL('@/assets/404cat.jfif', import.meta.url).href,
    thumb: new URL('@/assets/404cat.jfif', import.meta.url).href,
    title: '404 Programming Cat',
    subtitle: 'Position: absolute; left: -9999px;',
    description:
      "A cunning creature of style who hides production pages behind `left: -9999px;`. Great at hide and seek; terrible at SEO.",
  },
];

/* ---------- reactive state ---------- */
const currentIndex = ref(0);
const current = computed(() => images[currentIndex.value]);
const exhibitId = ref(`404-${Math.floor(Math.random() * 9000 + 1000)}`);
const foundDate = new Date().toLocaleDateString();
const curator = 'Dr. Syntax';

/* tilt / parallax */
const imgTransform = ref({});
const imgFrame = ref(null);
function tilt(e) {
  const el = imgFrame.value;
  if (!el) return;
  const rect = el.getBoundingClientRect();
  const x = (e.clientX - rect.left) / rect.width - 0.5; // -0.5 .. 0.5
  const y = (e.clientY - rect.top) / rect.height - 0.5;
  const rotateY = x * 10; // degrees
  const rotateX = -y * 6;
  imgTransform.value = {
    transform: `rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateZ(8px)`,
  };
}
function resetTilt() {
  imgTransform.value = { transform: `translateZ(0) rotateX(0) rotateY(0)` };
}

/* controls */
function prev() {
  currentIndex.value = (currentIndex.value - 1 + images.length) % images.length;
  focusCard();
}
function next() {
  currentIndex.value = (currentIndex.value + 1) % images.length;
  focusCard();
}
function randomize() {
  let newIndex = Math.floor(Math.random() * images.length);
  if (newIndex === currentIndex.value) newIndex = (newIndex + 1) % images.length;
  currentIndex.value = newIndex;
  focusCard();
}
function select(i) {
  currentIndex.value = i;
  focusCard();
}
function viewAll() {
  randomize();
  // non-blocking message (developers can wire a toast)
  console.info('Browsing: now shuffled.');
}

/* keyboard shortcuts: left, right, r */
function onKey(e) {
  if (e.key === 'ArrowLeft') prev();
  if (e.key === 'ArrowRight') next();
  if (e.key.toLowerCase() === 'r') randomize();
}

/* focus card for accessibility after action */
const mainCard = ref(null);
function focusCard() {
  if (mainCard.value) {
    mainCard.value.focus({ preventScroll: true });
  }
}

/* particles (muted) */
const particles = reactive([]);
onMounted(() => {
  // generate particles with restrained opacity so content stays readable
  for (let i = 0; i < 14; i++) {
    particles.push({
      x: Math.random() * 100,
      y: Math.random() * 100,
      size: Math.floor(Math.random() * 18) + 6,
      delay: Math.random() * 6,
      opacity: 0.03 + Math.random() * 0.08,
    });
  }
  // random initial exhibit
  currentIndex.value = Math.floor(Math.random() * images.length);

  // keyboard listeners
  window.addEventListener('keydown', onKey);
});
/* cleanup would be added if component unmount handled here */
</script>

<style scoped>
/* ---------- Base variables ---------- */
:root {
  --bg-img: url('/src/assets/bg_blur.jpg'); /* fallback - keep file in assets */
  --glass-bg: rgba(255,255,255,0.06);
  --glass-border: rgba(255,255,255,0.08);
  --accent: 96 165 250; /* rgb */
}

/* Page base */
.page-404 {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
  color: #e6eefc;
  position: relative;
  overflow: hidden;
  background: #071023; /* fallback dark base */
}

/* Header layer keeps header above visuals */
.header-layer { z-index: 60; position: relative; }

/* Background: use a photo + heavy blur behind card */
.bg-layer {
  position: absolute;
  inset: 0;
  background-image: var(--bg-img);
  background-size: cover;
  background-position: center;
  filter: blur(16px) saturate(110%) contrast(90%);
  transform: scale(1.06);
  z-index: 1;
  will-change: transform;
}
.bg-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(180deg, rgba(5,10,20,0.6), rgba(7,10,20,0.68));
  z-index: 2;
}

/* Vignette for better contrast near edges */
.vignette {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background: radial-gradient(60% 50% at 50% 40%, rgba(255,255,255,0.02), rgba(0,0,0,0.45));
  mix-blend-mode: multiply;
  z-index: 3;
}

/* particles - subtle */
.particles { position: absolute; inset: 0; z-index: 4; pointer-events: none; }
.particle {
  position: absolute;
  border-radius: 999px;
  background: radial-gradient(circle, rgba(255,255,255,0.95), rgba(255,255,255,0.02));
  filter: blur(6px);
  transform: translate3d(0,0,0);
  animation: floatParticle 14s linear infinite;
  mix-blend-mode: screen;
  will-change: transform, opacity;
}
@keyframes floatParticle {
  0% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-12px) scale(1.03); }
  100% { transform: translateY(0) scale(1); }
}

/* Stage layout */
.stage {
  display: grid;
  grid-template-columns: 1fr 340px;
  gap: 28px;
  align-items: start;
  width: min(1180px, 96%);
  margin: 48px auto;
  position: relative;
  z-index: 10; /* above bg and particles */
}

/* Glass card */
.glass-card {
  background: linear-gradient(180deg, rgba(255,255,255,0.03), rgba(255,255,255,0.02));
  border: 1px solid rgba(255,255,255,0.06);
  backdrop-filter: blur(10px) saturate(120%);
  border-radius: 16px;
  padding: 26px;
  box-shadow: 0 30px 60px rgba(3,7,18,0.6);
  color: #e6eefc;
  outline: none;
}
.glass-card:focus {
  box-shadow: 0 30px 80px rgba(3,7,18,0.7), 0 0 0 4px rgba(96,165,250,0.08);
}

/* header row */
.header-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 12px;
  margin-bottom: 8px;
}
.badge {
  background: rgba(var(--accent),0.10);
  color: rgb(var(--accent));
  padding: 6px 10px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 0.82rem;
  letter-spacing: 0.5px;
}
.meta {
  font-size: 0.82rem;
  opacity: 0.95;
}

/* titles */
.title {
  font-size: 2rem;
  margin: 8px 0 8px;
  color: #fff;
  font-weight: 800;
  line-height: 1.06;
}
.lead { margin: 0 0 16px; color: rgba(230,238,252,0.92); font-size: 1rem; }

/* exhibit grid */
.exhibit {
  display: grid;
  grid-template-columns: 340px 1fr;
  gap: 20px;
  align-items: start;
}

/* image frame */
.img-frame {
  width: 340px;
  height: 340px;
  border-radius: 14px;
  overflow: hidden;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  transform-style: preserve-3d;
  transition: transform 0.22s ease;
  background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
  border: 1px solid rgba(255,255,255,0.02);
  box-shadow: 0 18px 40px rgba(2,6,23,0.45);
}
.exhibit-img {
  width: 92%;
  height: 92%;
  object-fit: cover;
  border-radius: 10px;
  transition: transform 380ms cubic-bezier(.2,.9,.3,1), box-shadow 380ms;
  transform-style: preserve-3d;
  will-change: transform;
  user-select: none;
  pointer-events: none;
  box-shadow: 0 12px 30px rgba(2,6,23,0.55);
  animation: imgFloat 6s ease-in-out infinite;
}
@keyframes imgFloat {
  0% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
  100% { transform: translateY(0); }
}

/* plaque */
.plaque {
  position: absolute;
  left: 12px;
  bottom: 12px;
  padding: 10px 12px;
  background: rgba(2,6,23,0.56);
  border-radius: 8px;
  backdrop-filter: blur(6px);
  color: #fff;
  font-weight: 700;
  font-size: 0.88rem;
  box-shadow: 0 8px 18px rgba(0,0,0,0.45);
}
.plaque-sub { font-weight: 500; font-size: 0.75rem; opacity: 0.92; margin-top: 4px; }

/* copy column */
.exhibit-copy { padding: 6px 0; color: rgba(230,238,252,0.94); }
.exhibit-heading { margin: 0 0 6px; font-size: 1.05rem; color: #e6eefc; }
.description { margin: 0 0 12px; line-height: 1.5; color: rgba(230,238,252,0.93); }

/* mini meta */
.mini-meta { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 8px; color: rgba(255,255,255,0.9); }
.mini-meta div { font-size: 0.88rem; }

/* controls */
.controls { margin-top: 14px; display: flex; gap: 10px; align-items: center; }
.ctrl {
  border: none;
  padding: 10px 12px;
  background: rgba(255,255,255,0.03);
  color: #fff;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 800;
  transition: transform 0.12s ease, background 0.12s;
}
.ctrl:hover { transform: translateY(-3px); background: rgba(255,255,255,0.05); }
.ctrl:focus { outline: 2px solid rgba(96,165,250,0.12); outline-offset: 2px; }
.ctrl.accent {
  background: linear-gradient(90deg, rgba(var(--accent),0.12), rgba(255,255,255,0.02));
  color: rgb(var(--accent));
  box-shadow: 0 10px 28px rgba(10,20,60,0.18);
}

/* action row */
.action-row { display:flex; gap:12px; margin-top:16px; align-items:center; }
.home-btn {
  background: linear-gradient(90deg, rgba(255,255,255,0.98), rgba(245,245,255,0.96));
  color: #08203a;
  padding: 10px 14px;
  border-radius: 999px;
  font-weight: 700;
  text-decoration: none;
}
.ghost-btn {
  background: transparent;
  border: 1px solid rgba(255,255,255,0.06);
  color: rgba(255,255,255,0.95);
  padding: 10px 12px;
  border-radius: 10px;
  cursor: pointer;
}

/* satire grid */
.satire-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-top: 20px; }
.card {
  background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
  padding: 12px;
  border-radius: 12px;
  border: 1px solid rgba(255,255,255,0.02);
}
.card.small h3 { margin: 0 0 6px; color: rgba(255,255,255,0.95); }
.card.small ul, .card.small ol { margin: 0; padding-left: 18px; color: rgba(255,255,255,0.86); }
.note { font-style: italic; color: rgba(255,255,255,0.86); }

/* sidebar */
.sidebar {
  position: sticky;
  top: 28px;
  height: fit-content;
  background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));
  padding: 18px;
  border-radius: 12px;
  border: 1px solid rgba(255,255,255,0.03);
  color: rgba(255,255,255,0.95);
}
.sidebar h3 { margin: 0 0 10px; font-size: 1.05rem; color: #fff; }
.sidebar-section { margin-bottom: 14px; }

/* roster */
.roster { list-style: none; margin: 0; padding: 0; display: grid; gap: 8px; max-height: 260px; overflow: auto; }
.roster-btn {
  display: flex;
  gap: 12px;
  align-items: center;
  width: 100%;
  background: transparent;
  border: none;
  color: rgba(255,255,255,0.95);
  text-align: left;
  padding: 8px;
  border-radius: 8px;
  cursor: pointer;
}
.roster-btn:hover { background: rgba(255,255,255,0.02); }
.thumb { width: 48px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.03); }
.roster-text { display:flex; flex-direction:column; }
.roster-title { font-size: 0.95rem; }
.roster-sub { font-size: 0.78rem; opacity: 0.86; }

/* facts */
.facts { display: grid; gap: 8px; color: rgba(255,255,255,0.9); }
.facts dt { font-weight: 700; font-size: 0.9rem; }
.facts dd { margin: 0; font-size: 0.9rem; color: rgba(255,255,255,0.86); }

/* credits */
.credits .small { font-size: 0.85rem; opacity: 0.85; }

/* footer fix */
.footer-fixed { position: fixed; bottom: 0; width: 100%; z-index: 60; }

/* responsiveness */
@media (max-width: 1000px) {
  .stage { grid-template-columns: 1fr; gap: 18px; width: min(980px, 96%); margin-top: 30px; }
  .exhibit { grid-template-columns: 1fr; }
  .img-frame { margin: 0 auto; width: 92%; height: 340px; }
  .sidebar { position: static; }
  .satire-grid { grid-template-columns: 1fr; }
  .action-row { flex-direction: column; align-items: stretch; }
}

@media (max-width: 520px) {
  .title { font-size: 1.5rem; }
  .lead { font-size: 0.95rem; }
  .glass-card { padding: 16px; border-radius: 12px; }
}
</style>
