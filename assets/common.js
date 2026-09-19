/* GIS training — shared page behaviour.
   Loaded on every page after the PHP partials have rendered the topbar and pager.
   The server puts the current chapter into window.CHAPTER:
     { n: 1, modules: [{ id: "1.1", file: "m1-decision.php", title: "…" }, …], current: "1.1" | null }
   Progress is stored per chapter in localStorage under "gis-chN-progress". */

const CHAPTER = window.CHAPTER || { n: 0, modules: [], current: null };
const MODULES = CHAPTER.modules;                 // kept as globals: chapter index pages read them
const PROG_KEY = `gis-ch${CHAPTER.n}-progress`;

function getProgress() { try { return JSON.parse(localStorage.getItem(PROG_KEY) || "{}"); } catch { return {}; } }
function setDone(id, v) { try { const p = getProgress(); if (v) p[id] = true; else delete p[id]; localStorage.setItem(PROG_KEY, JSON.stringify(p)); } catch {} }

/* ---- chrome: the topbar and pager are server-rendered; here we add what only the browser knows ---- */
function buildChrome() {
  if (!MODULES.length) return;
  const prog = getProgress();
  const doneCount = MODULES.filter(m => prog[m.id]).length;
  document.querySelectorAll(".modnav a[data-module]").forEach(a => a.classList.toggle("done", !!prog[a.dataset.module]));
  const bar = document.querySelector(".progress > div");
  if (bar) bar.style.width = `${(doneCount / MODULES.length) * 100}%`;
  const box = document.getElementById("doneBox");
  if (box && CHAPTER.current) {
    box.checked = !!prog[CHAPTER.current];
    box.addEventListener("change", e => { setDone(CHAPTER.current, e.target.checked); location.reload(); });
  }
}

/* ---- quick-check quizzes ----
   <div class="quiz" data-answer="1" data-fb="…"><div class="q">…</div><div class="opts"><button class="opt">…</button>…</div><div class="fb"></div></div> */
function initQuizzes() {
  document.querySelectorAll(".quiz").forEach(q => {
    const ans = parseInt(q.dataset.answer, 10);
    const fb = q.querySelector(".fb");
    q.querySelectorAll(".opt").forEach((b, i) => {
      b.addEventListener("click", () => {
        q.querySelectorAll(".opt").forEach(x => x.classList.remove("right", "wrong"));
        const ok = i === ans;
        b.classList.add(ok ? "right" : "wrong");
        if (!ok) q.querySelectorAll(".opt")[ans].classList.add("right");
        fb.className = "fb show " + (ok ? "ok" : "no");
        fb.textContent = (ok ? "Correct. " : "Not quite. ") + (q.dataset.fb || "");
      });
    });
  });
}

/* ---- click-to-sort activity ----
   .sorter with data-items='[{"t":"…","bin":"proximity","why":"…"}]' and .bin[data-bin] elements */
function initSorters() {
  document.querySelectorAll(".sorter").forEach(s => {
    const items = JSON.parse(s.dataset.items);
    const wrap = s.querySelector(".items");
    let active = null;
    items.forEach((it, i) => {
      const b = document.createElement("button"); b.className = "item"; b.textContent = it.t; b.dataset.i = i;
      b.addEventListener("click", () => { if (b.classList.contains("placed")) return; wrap.querySelectorAll(".item").forEach(x => x.classList.remove("active")); b.classList.add("active"); active = i; });
      wrap.appendChild(b);
    });
    s.querySelectorAll(".bin").forEach(bin => {
      bin.addEventListener("click", () => {
        if (active == null) return;
        const it = items[active];
        const ok = it.bin === bin.dataset.bin;
        const d = document.createElement("div"); d.className = "placed-item " + (ok ? "ok" : "bad");
        d.textContent = (ok ? "✓ " : "✗ ") + it.t + (ok ? "" : ` → belongs in “${it.bin}”`) + (it.why ? " — " + it.why : "");
        bin.appendChild(d);
        const btn = wrap.querySelector(`.item[data-i="${active}"]`); btn.classList.remove("active"); btn.classList.add("placed");
        active = null;
      });
    });
  });
}

/* ---- tabs: .tabs holds the buttons; the .tabpanel siblings follow in the same parent ---- */
function initTabs() {
  document.querySelectorAll(".tabs").forEach(tabs => {
    const btns = tabs.querySelectorAll("button");
    let panels = tabs.parentElement.querySelectorAll(":scope > .tabpanel");
    if (!panels.length) panels = tabs.parentElement.querySelectorAll(".tabpanel");
    btns.forEach((b, i) => b.addEventListener("click", () => { btns.forEach(x => x.setAttribute("aria-selected", "false")); panels.forEach(p => p.classList.remove("show")); b.setAttribute("aria-selected", "true"); if (panels[i]) panels[i].classList.add("show"); }));
    if (btns[0]) btns[0].click();
  });
}

/* ---- copy buttons: either a .copywrap around a <pre>, or a button with data-copy="<id of source>" ---- */
function initCopy() {
  document.querySelectorAll(".copywrap").forEach(w => {
    const b = document.createElement("button"); b.className = "btn small"; b.textContent = "Copy";
    b.addEventListener("click", async () => { try { await navigator.clipboard.writeText(w.querySelector("pre").textContent); b.textContent = "Copied ✓"; setTimeout(() => b.textContent = "Copy", 1500); } catch { b.textContent = "Select & copy manually"; } });
    w.prepend(b);
  });
  document.querySelectorAll("[data-copy]").forEach(b => b.addEventListener("click", () => {
    const src = document.getElementById(b.dataset.copy); if (!src) return;
    navigator.clipboard?.writeText(src.textContent).then(() => { const t = b.textContent; b.textContent = "Copied ✓"; setTimeout(() => b.textContent = t, 1500); }).catch(() => { b.textContent = "Select and copy manually"; });
  }));
}

/* ---- saved notes (labs): any input with data-save="name" is remembered in this browser ---- */
function initNotes() {
  document.querySelectorAll("[data-save]").forEach(el => {
    const key = `gis-ch${CHAPTER.n}-` + el.dataset.save;
    const save = () => { try { localStorage.setItem(key, el.type === "checkbox" ? (el.checked ? "1" : "0") : el.value); } catch {} };
    try { const v = localStorage.getItem(key); if (v != null) { if (el.type === "checkbox") el.checked = v === "1"; else el.value = v; } } catch {}
    el.addEventListener("input", save);
    el.addEventListener("change", save);
  });
}

if (typeof document !== "undefined") document.addEventListener("DOMContentLoaded", () => {
  buildChrome(); initQuizzes(); initSorters(); initTabs(); initCopy(); initNotes();
  if (window.pageInit) window.pageInit();
});
