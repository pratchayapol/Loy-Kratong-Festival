<script>
    // โหลดและแสดงกราฟ Ping Monitoring
    const STATUS_SLUG = "loykratong";
    const MONITOR_ID = "34";
    const ENDPOINT = `/kuma/heartbeat/${STATUS_SLUG}`;
    const Y_MIN = 0, Y_MAX = 5000;
    const TZ = 'Asia/Bangkok';
    const POLL_MS = 10_000;

    let pingChart;
    let pollTimer = null;
    let inflight = null;

    function toUTCDate(t) {
        if (typeof t === 'number') {
            const ms = (t < 2e10 ? t * 1000 : t);
            return new Date(ms);
        }
        if (typeof t === 'string') {
            return new Date(t.replace(' ', 'T') + 'Z');
        }
        return new Date(t);
    }

    // สร้างหรืออัปเดตกราฟ
    function upsertChart(series) {
        if (window.innerWidth < 640) return; // skip on small screens
        const xmin = series[0].x.getTime();
        const xmax = series[series.length - 1].x.getTime();
        const ctx = document.getElementById('pingChart');
        if (!ctx) return;

        if (!pingChart) {
            pingChart = new Chart(ctx, {
                type: 'line',
                data: { datasets: [{ label: 'Ping', data: series, pointRadius: 0, spanGaps: false }] },
                options: {
                    responsive: true, maintainAspectRatio: false, parsing: false, animation: false, normalized: true,
                    datasets: { line: { tension: 0, cubicInterpolationMode: 'monotone' } },
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        x: {
                            type: 'time', bounds: 'data', min: xmin, max: xmax,
                            title: { display: true, text: 'เวลา' },
                            ticks: {
                                source: 'data',
                                callback: (v) => new Date(v).toLocaleString('th-TH', {
                                    timeZone: TZ, hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit'
                                })
                            },
                            time: {
                                displayFormats: {
                                    millisecond: 'HH:mm:ss', second: 'HH:mm:ss', minute: 'HH:mm', hour: 'HH:mm', day: 'dd/MM HH:mm'
                                },
                                tooltipFormat: 'HH:mm:ss'
                            }
                        },
                        y: { min: Y_MIN, max: Y_MAX, ticks: { precision: 0 }, title: { display: true, text: 'ms' } }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                title: (items) => {
                                    const ts = items?.[0]?.parsed?.x;
                                    return new Date(ts).toLocaleString('th-TH', {
                                        timeZone: TZ, hour12: false, year: 'numeric', month: '2-digit', day: '2-digit',
                                        hour: '2-digit', minute: '2-digit', second: '2-digit'
                                    }) + ' (UTC+7)';
                                },
                                label: (ctx) => `Ping: ${ctx.parsed.y} ms`
                            }
                        }
                    }
                }
            });
        } else {
            pingChart.data.datasets[0].data = series;
            pingChart.options.scales.x.min = xmin;
            pingChart.options.scales.x.max = xmax;
            pingChart.update('none');
        }
    }

    // โหลดข้อมูล Ping แบบละเอียด
    async function loadPingExact() {
        const errEl = document.getElementById('pingErr');
        if (errEl) errEl.textContent = '';
        inflight?.abort?.();
        const controller = new AbortController();
        inflight = controller;
        try {
            const res = await fetch(ENDPOINT, { credentials: 'same-origin', signal: controller.signal });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            const raw = data.heartbeatList?.[MONITOR_ID] ?? [];
            const series = raw
                .filter(h => Number.isFinite(h.ping) && h.ping > 0 && h.ping < 60000 && h.time)
                .map(h => ({ x: toUTCDate(h.time), y: Math.min(h.ping, Y_MAX) }))
                .sort((a, b) => a.x - b.x);
            if (series.length === 0) throw new Error('no data');
            upsertChart(series);
        } catch (e) {
            if (e.name !== 'AbortError' && errEl) {
                errEl.textContent = `โหลดกราฟไม่สำเร็จ: ${e.message}`;
                console.error(e);
            }
        } finally {
            if (inflight === controller) inflight = null;
        }
    }

    function startPolling() {
        if (pollTimer) return;
        pollTimer = setInterval(() => { if (!document.hidden) loadPingExact(); }, POLL_MS);
    }
    function stopPolling() { clearInterval(pollTimer); pollTimer = null; }

    document.addEventListener('visibilitychange', () => { if (!document.hidden) loadPingExact(); });
    document.addEventListener('DOMContentLoaded', () => { loadPingExact(); startPolling(); });

    document.addEventListener('alpine:init', () => {
        let once = false;
        Alpine.effect(() => {
            const open = Alpine.store('ui')?.aboutOpen;
            if (open && !once) {
                once = true;
                setTimeout(() => { loadPingExact(); try { pingChart?.resize(); } catch {} }, 150);
            }
            if (!open) once = false;
        });
    });

    // สร้างหิ่งห้อยบินบนผิวน้ำ
    function fireflies() {
        const COUNT = window.innerWidth < 640 ? 18 : 36;
        const DUR_MIN = 8, DUR_MAX = 16;
        const SIZE_MIN = 2, SIZE_MAX = 3.5;
        const BAND_TOP = 6;
        const BAND_HEIGHT = 30;
        const sheet = ensureKeyframeSheet();

        const pathKF = () => {
            const name = 'ff_' + Math.random().toString(36).slice(2);
            const step = () => ({ x: rnd(-30, 30), y: rnd(-18, 18) });
            const p1 = step(), p2 = step(), p3 = step(), p4 = step();
            const css = `@keyframes ${name}{
                0%{transform:translate(0px,0px)}
                25%{transform:translate(${p1.x}px,${p1.y}px)}
                50%{transform:translate(${p2.x}px,${p2.y}px)}
                75%{transform:translate(${p3.x}px,${p3.y}px)}
                100%{transform:translate(${p4.x}px,${p4.y}px)}
            }`;
            sheet.insertRule(css, sheet.cssRules.length);
            return name;
        };

        const makeOne = () => {
            const id = crypto?.randomUUID?.() || (Date.now() + Math.random());
            const left = rnd(5, 95);
            const top = rnd(BAND_TOP, BAND_TOP + BAND_HEIGHT);
            const size = rnd(SIZE_MIN, SIZE_MAX);
            const dur = rnd(DUR_MIN, DUR_MAX);
            const delay = rnd(0, 4);
            const drift = pathKF();
            const style = `
                left:${left}%; top:${top}%;
                width:${size}px; height:${size}px; border-radius:9999px;
                background: radial-gradient(circle at 50% 50%, #fff7c2 0%, #ffd86b 35%, rgba(255,215,100,.0) 70%);
                animation:
                  ${drift} ${dur}s ease-in-out ${delay}s infinite alternate,
                  fireflyBlink ${rnd(2.2, 3.6)}s ease-in-out ${rnd(0, 1.5)}s infinite;
                will-change: transform, opacity, filter;
                mix-blend-mode: screen;
            `;
            return { id, style };
        };

        return {
            flies: [],
            init() {
                for (let i = 0; i < COUNT; i++) this.flies.push(makeOne());
                const refill = () => {
                    const need = COUNT - this.flies.length;
                    for (let i = 0; i < need; i++) this.flies.push(makeOne());
                    __schedule(refill, 12000);
                };
                __schedule(refill, 12000);
                window.addEventListener('resize', () => {
                    const target = window.innerWidth < 640 ? 18 : 36;
                    if (this.flies.length > target) this.flies.splice(target);
                    while (this.flies.length < target) this.flies.push(makeOne());
                }, { passive: true });
            }
        }
    }

    // อ่านคุกกี้แบบง่าย
    const readCookie = n => decodeURIComponent((document.cookie.split('; ').find(x => x.startsWith(n + '=')) || '')
        .split('=')[1] || '');
    const __timers = new Set();
    const __schedule = (fn, ms) => { const t = setTimeout(fn, ms); __timers.add(t); return t; };
    const __clearAll = () => { for (const t of __timers) clearTimeout(t); __timers.clear(); };
    window.addEventListener('beforeunload', __clearAll);

    // สร้างห้องเก็บ keyframe ไว้เพิ่มตอนรัน
    function ensureKeyframeSheet() {
        let el = document.getElementById('dyn-keyframes');
        if (!el) { el = document.createElement('style'); el.id = 'dyn-keyframes'; document.head.appendChild(el); }
        if (!el.sheet) {
            const tmp = document.createElement('style'); document.head.appendChild(tmp);
            const sheet = tmp.sheet; document.head.removeChild(tmp); return sheet;
        }
        return el.sheet;
    }

    // ฉากแม่น้ำลอยกระทง — ลอยตามลำดับใหม่→เก่าแบบคงที่ วนซ้ำ และเว้นระยะ "ฟันปลา" สม่ำเสมอ
    function riverScene(types, recent) {
        const WATER_TOP = 25;      // เริ่มน้ำจากบนจอ %
        const WATER_BAND = 28;     // ความสูงแถบน้ำ %
        const DUR_INIT = 28;       // s ความยาววิ่งรอบแรก
        const DUR_LOOP = 24;       // s ความยาววิ่งรอบวนถัดไป
        const MAX_ITEMS = window.innerWidth < 640 ? 40 : 100;
        const typeImg = t => types?.[t]?.img || Object.values(types || {})[0]?.img || '';

        // สองแถบ "ฟันปลา": บนและล่าง สลับกัน
        const TOTAL_LANES = window.innerWidth < 640 ? 6 : 12;
        const HALF = Math.max(1, Math.floor(TOTAL_LANES / 2));
        const bandGap = (WATER_BAND - 6); // ใช้ช่วงเดียวกันเพื่อไม่ชนขอบ
        const upperLanes = Array.from({ length: HALF }, (_, i) =>
            WATER_TOP + 6 + (i * bandGap / Math.max(1, HALF - 1))
        );
        const lowerLanes = Array.from({ length: TOTAL_LANES - HALF }, (_, i) =>
            WATER_TOP + 6 + (i * bandGap / Math.max(1, (TOTAL_LANES - HALF) - 1))
        );

        // เว้นระยะหัวชนแนวนอนให้มากขึ้น และทำ phase shift สำหรับแถบล่างเพื่อให้ฟันปลา
        const TRACK_WIDTH = 140;  // -20% → 120%
        const MIN_GAP_X = window.innerWidth < 640 ? 30 : 32; // ระยะมากขึ้น
        const headwayMs = (durSec) => (MIN_GAP_X / TRACK_WIDTH) * durSec * 1000;
        const now = () => performance.now();

        // เวลาว่างครั้งถัดไปต่อเลน
        const upNext = new Array(upperLanes.length).fill(0);
        const loNext = new Array(lowerLanes.length).fill(0);

        // ทำ phase สำหรับล่างให้กึ่งจังหวะของบน → ฟันปลา
        const PHASE_MS = headwayMs(DUR_LOOP) / 2;
        for (let i = 0; i < loNext.length; i++) loNext[i] = now() + PHASE_MS;

        // index ต่อเลน เพื่อเดินเรียง deterministic
        let upIdx = 0, loIdx = 0;
        let useUpperNext = true; // สลับบน-ล่างทุกครั้ง

        // สร้าง keyframes และ style คงที่
        const makeStyle = (dur, top) => {
            const name = `drift_${Math.random().toString(36).slice(2)}`;
            const sheet = ensureKeyframeSheet();
            sheet.insertRule(
                `@keyframes ${name}{0%{left:-20%;opacity:0}10%{opacity:1}90%{opacity:1}100%{left:120%;opacity:0}}`,
                sheet.cssRules.length
            );
            return `top:${top}%;left:-20%;animation:${name} ${dur}s linear 0s forwards;`;
        };

        // ลิสต์ข้อมูลเรียงใหม่→เก่า ไม่มีการสุ่ม
        const base = Array
            .from(new Map((recent || []).map(r => [r.id, r])).values())
            .sort((a, b) => b.id - a.id);

        // ตั้งเวลาลบ
        const scheduleRemoval = (vm, item, ms) => {
            if (item.__tid) { clearTimeout(item.__tid); __timers.delete(item.__tid); item.__tid = null; }
            item.__deadline = Date.now() + ms;
            item.__tid = __schedule(() => {
                const i = vm.items.findIndex(x => x.clientId === item.clientId);
                if (i > -1) vm.items.splice(i, 1);
            }, ms + 30);
        };

        // เลือกเลนแบบคงที่ สลับบน/ล่าง และเดินชี้ทีละเลน
        function pickLane(durSec) {
            const t = now();
            const tryUpper = useUpperNext;
            // สลับคิวเพื่อความเป็นระเบียบฟันปลา
            useUpperNext = !useUpperNext;

            // ฟังก์ชันทดลองเลือกในแถบ
            const pickInBand = (lanes, nextTimes, ptrName) => {
                if (lanes.length === 0) return null;
                let ptr = (ptrName === 'up' ? upIdx : loIdx);
                for (let k = 0; k < lanes.length; k++) {
                    const idx = (ptr + k) % lanes.length;
                    if (nextTimes[idx] <= t) {
                        if (ptrName === 'up') upIdx = (idx + 1) % lanes.length;
                        else loIdx = (idx + 1) % lanes.length;
                        nextTimes[idx] = t + headwayMs(durSec);
                        return { top: lanes[idx] };
                    }
                }
                return null;
            };

            // ลองในแถบที่ถึงคิวก่อน ถ้าไม่มีลองอีกแถบ
            return tryUpper
                ? (pickInBand(upperLanes, upNext, 'up') || pickInBand(lowerLanes, loNext, 'lo'))
                : (pickInBand(lowerLanes, loNext, 'lo') || pickInBand(upperLanes, upNext, 'up'));
        }

        // สร้างไอเท็มจากเรคคอร์ดแบบ deterministic
        const mkItem = (r, initialBatch = false) => {
            const dur = initialBatch ? DUR_INIT : DUR_LOOP;
            const lane = pickLane(dur);
            if (!lane) return null;
            return {
                id: r.id,
                clientId: `${initialBatch ? 'srv' : 'cli'}_${r.id}_${Math.random().toString(36).slice(2)}`,
                img: typeImg(r.type),
                wish: `${r.nickname} : ${r.wish}`,
                style: makeStyle(dur, lane.top),
                show: false, paused: false,
                __life: dur * 1000,
                __deadline: Date.now() + dur * 1000
            };
        };

        // ตัวควบคุมลำดับวนจากใหม่→เก่าจนครบแล้ววน
        let idx = 0;

        return {
            items: [],
            order: base,

            pause(k) {
                if (!k || k.paused) return;
                k.paused = true;
                k.__remain = Math.max(3000, (k.__deadline || 0) - Date.now());
                if (k.__tid) { clearTimeout(k.__tid); __timers.delete(k.__tid); k.__tid = null; }
            },
            resume(k) {
                if (!k || !k.paused) return;
                k.paused = false;
                const extra = 8000;
                scheduleRemoval(this, k, (k.__remain || 0) + extra);
            },

            init() {
                // ปล่อยล็อตแรกแบบคงที่ ไม่สุ่ม และกระจายด้วยหัวชนต่อเลน
                const initialCount = Math.min(24, this.order.length);
                let spawned = 0;
                const pumpInit = () => {
                    if (spawned >= initialCount) { this._loopPump(); return; }
                    const r = this.order[idx];
                    const item = mkItem(r, true);
                    if (item) {
                        this.items.unshift(item);
                        if (this.items.length > MAX_ITEMS) this.items.splice(MAX_ITEMS);
                        scheduleRemoval(this, item, item.__life);
                        idx = (idx + 1) % this.order.length;
                        spawned++;
                        __schedule(pumpInit, 250); // ระยะห่างคงที่
                    } else {
                        __schedule(pumpInit, 150); // รอเลนว่าง
                    }
                };
                pumpInit();
            },

            _loopPump() {
                // ปล่อยต่อเนื่องแบบคงที่ เรียงใหม่→เก่า วนลูป
                const tick = () => {
                    if (!this.order.length) { __schedule(tick, 600); return; }
                    const r = this.order[idx];
                    const item = mkItem(r, false);
                    if (item) {
                        this.items.unshift(item);
                        if (this.items.length > MAX_ITEMS) this.items.splice(MAX_ITEMS);
                        scheduleRemoval(this, item, item.__life);
                        idx = (idx + 1) % this.order.length;
                        __schedule(tick, 260); // คุมระยะสม่ำเสมอ
                    } else {
                        __schedule(tick, 160); // รอเลนว่างแล้วค่อยปล่อย
                    }
                };
                tick();
            },

            spawnFromRecord(r) {
                // ของใหม่ถูกแทรกหัวลิสต์ และจะเข้าคิวถัดไปแบบ deterministic
                this.order = [r, ...this.order.filter(x => x.id !== r.id)].sort((a, b) => b.id - a.id);
                // ไม่รีเซ็ต idx เพื่อคงลำดับที่กำลังวิ่งอยู่ แต่ให้ปล่อย r เมื่อถึงรอบ
            },

            spawnNew(p) {
                const r = {
                    id: p.id ?? Date.now(),
                    type: p.type,
                    nickname: p.nickname,
                    age: p.age,
                    wish: p.wish
                };
                this.spawnFromRecord(r);
            }
        }
    }

    // ฟอร์มลอยกระทง
    function krathongForm() {
        return {
            form: { type: 'banana', nickname: '', age: '', wish: '' },
            error: '', ok: '',
            async submit() {
                this.error = ''; this.ok = '';
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
                        try { const j = await res.json(); msg = j.message || msg; } catch(_) {}
                        throw new Error(msg);
                    }
                    const data = await res.json();
                    this.ok = 'ลอยแล้ว ✨';
                    const api = Alpine.$data(document.getElementById('river'));
                    api?.spawnNew?.(data);
                    this.form.wish = '';
                    setTimeout(() => { Alpine.store('ui').open = false; this.ok = ''; }, 1500);
                } catch (e) { this.error = e.message; }
            }
        }
    }

    // ยูทิล
    const rnd = (min, max) => Math.random() * (max - min) + min;
    const setVH = () => document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
    window.addEventListener('resize', setVH, { passive: true });
    window.addEventListener('orientationchange', setVH, { passive: true });
    document.addEventListener('DOMContentLoaded', setVH);
</script>
