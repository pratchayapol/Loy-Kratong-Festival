const readCookie = n => decodeURIComponent((document.cookie.split('; ').find(x => x.startsWith(n + '=')) || '')
    .split('=')[1] || '');
const __timers = new Set();
const __schedule = (fn, ms) => {
    const t = setTimeout(fn, ms);
    __timers.add(t);
    return t;
};
const __clearAll = () => {
    for (const t of __timers) clearTimeout(t);
    __timers.clear();
};
window.addEventListener('beforeunload', __clearAll);

function ensureKeyframeSheet() {
    let el = document.getElementById('dyn-keyframes');
    if (!el) {
        el = document.createElement('style');
        el.id = 'dyn-keyframes';
        document.head.appendChild(el);
    }
    if (!el.sheet) {
        const tmp = document.createElement('style');
        document.head.appendChild(tmp);
        const sheet = tmp.sheet;
        document.head.removeChild(tmp);
        return sheet;
    }
    return el.sheet;
}

// วนลูปใหม่→เก่าเป็นรอบ ๆ ไม่ว่าง ไม่ซ้ำภายในรอบเดียว และแสดงใหม่ทันที
function riverScene(types, recent) {
    const WATER_TOP = 25; // เริ่มโซนน้ำ
    const WATER_BAND = 28; // ความสูงโซนน้ำ
    // เลนสูงขึ้น = ช่องไฟแนวตั้งมากขึ้น
    const LANE_COUNT = window.innerWidth < 640 ? 6 : 10;
    const LANE_HEIGHT = WATER_BAND / LANE_COUNT;

    // เวลาห่างขั้นต่ำแนวนอน
    const GAP_GLOBAL_MS = window.innerWidth < 640 ? 2200 : 2800;
    const GAP_PER_LANE_MS = window.innerWidth < 640 ? 3200 : 3800;

    // ห้ามเลนติดกันยิงพร้อมกันภายในช่วงนี้ เพื่อตัด “กลุ่มก้อน”
    const NEIGHBOR_COOLDOWN_MS = 1400;

    const DUR_INIT_MIN = 22,
        DUR_INIT_MAX = 34;
    const DUR_LOOP_MIN = 18,
        DUR_LOOP_MAX = 28;

    const MAX_ITEMS = window.innerWidth < 640 ? 32 : 80;
    const typeImg = t => types?.[t]?.img || Object.values(types || {})[0]?.img || '';

    // สถานะเลน
    const laneNextFree = Array.from({
        length: LANE_COUNT
    }, () => 0);
    const lanePhase = Array.from({
        length: LANE_COUNT
    }, (_, i) => (i % 2) ? 900 : 0); // สลับฟันปลาแบบแข็ง
    let globalNextFree = 0;

    function ensureKeyframes(name, css) {
        const sheet = ensureKeyframeSheet();
        sheet.insertRule(css, sheet.cssRules.length);
        return name;
    }

    function makeStyle(dur, delay, top, z) {
        const drift = `dr_${Math.random().toString(36).slice(2)}`;
        ensureKeyframes(drift, `@keyframes ${drift}{
      0%{left:-20%;opacity:0} 10%{opacity:1} 90%{opacity:1} 100%{left:120%;opacity:0}
    }`);
        const floatDur = rnd(3.0, 4.0);
        const swayDur = rnd(4.8, 6.2);
        const chopDur = rnd(8, 12);

        return `
      top:${top}%;
      left:-20%;
      animation:
        ${drift} ${dur}s linear ${delay}s forwards,
        floatY ${floatDur}s ease-in-out ${delay}s infinite,
        sway   ${swayDur}s  ease-in-out ${delay}s infinite,
        chop   ${chopDur}s  linear      ${rnd(0, 3)}s infinite;
    `;
    }

    function mkItem(r, init, laneIdx) {
        const dur = init ? rnd(DUR_INIT_MIN, DUR_INIT_MAX) : rnd(DUR_LOOP_MIN, DUR_LOOP_MAX);
        const delay = (lanePhase[laneIdx] || 0) / 1000; // ฟันปลาแบบคงที่ต่อเลน

        // ไม่ใส่ jitter แนวตั้ง เพื่อตัดการทับกันจริง
        const laneTop = WATER_TOP + 6 + laneIdx * LANE_HEIGHT;
        const top = laneTop;

        const z = 100 + (laneIdx % 2 ? laneIdx : (LANE_COUNT - laneIdx)); // สลับชั้นบนล่างตามเลน

        return {
            id: r.id,
            clientId: `${init ? 'srv' : 'cli'}_${r.id}_${Math.random().toString(36).slice(2)}`,
            img: typeImg(r.type),
            wish: `${r.nickname} : ${r.wish}`,
            style: makeStyle(dur, delay, top, z),
            z,
            show: false,
            paused: false,
            __life: (dur + delay) * 1000,
            __deadline: Date.now() + (dur + delay) * 1000,
            __lane: laneIdx
        };
    }

    function canUseLane(now, lane) {
        if (now < laneNextFree[lane]) return false;
        // กันเลนข้างเคียงยิงพร้อมๆ กัน
        const L = lane - 1,
            R = lane + 1;
        if (L >= 0 && now < laneNextFree[L] + NEIGHBOR_COOLDOWN_MS) return false;
        if (R < LANE_COUNT && now < laneNextFree[R] + NEIGHBOR_COOLDOWN_MS) return false;
        return now >= globalNextFree;
    }

    function pickLane(now) {
        // เลือกเลนที่ “ใช้ได้” ก่อน หากไม่มี เลือกเลนที่ว่างเร็วสุด
        let bestLane = -1;
        for (let i = 0; i < LANE_COUNT; i++) {
            if (canUseLane(now, i)) {
                bestLane = i;
                break;
            }
        }
        if (bestLane === -1) {
            let minT = Infinity,
                idx = 0;
            for (let i = 0; i < LANE_COUNT; i++) {
                const t = Math.max(laneNextFree[i], globalNextFree);
                if (t < minT) {
                    minT = t;
                    idx = i;
                }
            }
            bestLane = idx;
        }
        return bestLane;
    }

    function bookLane(now, lane) {
        laneNextFree[lane] = now + GAP_PER_LANE_MS;
        globalNextFree = now + GAP_GLOBAL_MS;
        // ขยับเฟสเล็กน้อยเพื่อกระจาย
        lanePhase[lane] = (lanePhase[lane] + 600) % 2000;
    }

    const scheduleRemoval = (vm, item, ms) => {
        if (item.__tid) {
            clearTimeout(item.__tid);
            __timers.delete(item.__tid);
            item.__tid = null;
        }
        item.__deadline = Date.now() + ms;
        item.__tid = __schedule(() => {
            const i = vm.items.findIndex(x => x.clientId === item.clientId);
            if (i > -1) vm.items.splice(i, 1);
        }, ms + 30);
    };

    const base = Array.from(new Map((recent || []).map(r => [r.id, r])).values()).sort((a, b) => b.id - a.id);

    return {
        items: [],
        order: base,
        seenInCycle: new Set(),
        idx: 0,

        pause(k) {
            if (!k || k.paused) return;
            k.paused = true;
            k.__remain = Math.max(3000, (k.__deadline || 0) - Date.now());
            if (k.__tid) {
                clearTimeout(k.__tid);
                __timers.delete(k.__tid);
                k.__tid = null;
            }
        },
        resume(k) {
            if (!k || !k.paused) return;
            k.paused = false;
            scheduleRemoval(this, k, (k.__remain || 0) + 8000);
        },

        init() {
            const initCount = Math.min(16, this.order.length);
            for (let k = 0; k < initCount; k++) this._spawnNext(true);
            const tick = () => {
                this._spawnNext(false);
                __schedule(tick, window.innerWidth < 400 ? rnd(6500, 9000) : rnd(4800, 7600));
            };
            __schedule(tick, 900);
        },

        _spawnNext(isInitial) {
            if (!this.order.length) return;
            if (this.seenInCycle.size >= this.order.length) {
                this.seenInCycle.clear();
                this.idx = 0;
            }
            let guard = 0;
            while (this.seenInCycle.has(this.order[this.idx]?.id) && guard++ < this.order.length) {
                this.idx = (this.idx + 1) % this.order.length;
            }
            const r = this.order[this.idx];
            if (!r) return;
            this.seenInCycle.add(r.id);
            this.idx = (this.idx + 1) % this.order.length;

            const now = Date.now();
            const lane = pickLane(now);
            bookLane(now, lane);

            const item = mkItem(r, isInitial, lane);
            this.items.unshift(item);
            if (this.items.length > MAX_ITEMS) this.items.splice(MAX_ITEMS);
            scheduleRemoval(this, item, item.__life);
        },

        spawnFromRecord(r) {
            this.order = [r, ...this.order.filter(x => x.id !== r.id)].sort((a, b) => b.id - a.id);
            this.seenInCycle.clear();
            this.idx = 0;

            const now = Date.now();
            const lane = pickLane(now);
            bookLane(now, lane);

            const item = mkItem(r, false, lane);
            this.items.unshift(item);
            if (this.items.length > MAX_ITEMS) this.items.splice(MAX_ITEMS);
            scheduleRemoval(this, item, item.__life);
        },

        spawnNew(p) {
            this.spawnFromRecord({
                id: p.id ?? Date.now(),
                type: p.type,
                nickname: p.nickname,
                age: p.age,
                wish: p.wish
            });
        }
    }
}

function krathongForm() {
    return {
        form: {
            type: 'banana',
            nickname: '',
            age: '',
            wish: ''
        },
        error: '',
        ok: '',
        async submit() {
            this.error = '';
            this.ok = '';
            try {
                const meta = document.querySelector('meta[name="csrf-token"]').content;
                const xsrf = readCookie('XSRF-TOKEN');
                const res = await fetch('/krathongs', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': meta,
                        'X-XSRF-TOKEN': xsrf,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(this.form)
                });
                if (!res.ok) {
                    let msg = `HTTP ${res.status}`;
                    try {
                        const j = await res.json();
                        msg = j.message || msg;
                    } catch (_) { }
                    throw new Error(msg);
                }
                const data = await res.json();
                this.ok = 'ลอยแล้ว ✨';
                const api = Alpine.$data(document.getElementById('river'));
                api?.spawnNew?.(data);
                this.form.wish = '';
                setTimeout(() => {
                    Alpine.store('ui').open = false;
                    this.ok = '';
                }, 1500);
            } catch (e) {
                this.error = e.message;
            }
        }
    }
}
