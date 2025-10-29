<script>
    // โหลดและแสดงกราฟ Ping Monitoring
    const STATUS_SLUG = "loykratong";
    const MONITOR_ID = "34";
    const ENDPOINT = `/kuma/heartbeat/${STATUS_SLUG}`;
    const Y_MIN = 0,
        Y_MAX = 5000;
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
                data: {
                    datasets: [{
                        label: 'Ping',
                        data: series,
                        pointRadius: 0,
                        spanGaps: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    parsing: false,
                    animation: false,
                    normalized: true,
                    datasets: {
                        line: {
                            tension: 0,
                            cubicInterpolationMode: 'monotone'
                        }
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        x: {
                            type: 'time',
                            bounds: 'data',
                            min: xmin,
                            max: xmax,
                            title: {
                                display: true,
                                text: 'เวลา'
                            },
                            ticks: {
                                source: 'data',
                                callback: (v) => new Date(v).toLocaleString('th-TH', {
                                    timeZone: TZ,
                                    hour12: false,
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit'
                                })
                            },
                            time: {
                                displayFormats: {
                                    millisecond: 'HH:mm:ss',
                                    second: 'HH:mm:ss',
                                    minute: 'HH:mm',
                                    hour: 'HH:mm',
                                    day: 'dd/MM HH:mm'
                                },
                                tooltipFormat: 'HH:mm:ss'
                            }
                        },
                        y: {
                            min: Y_MIN,
                            max: Y_MAX,
                            ticks: {
                                precision: 0
                            },
                            title: {
                                display: true,
                                text: 'ms'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                title: (items) => {
                                    const ts = items?.[0]?.parsed?.x;
                                    return new Date(ts).toLocaleString('th-TH', {
                                        timeZone: TZ,
                                        hour12: false,
                                        year: 'numeric',
                                        month: '2-digit',
                                        day: '2-digit',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                        second: '2-digit'
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
            const res = await fetch(ENDPOINT, {
                credentials: 'same-origin',
                signal: controller.signal
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            const raw = data.heartbeatList?.[MONITOR_ID] ?? [];
            const series = raw
                .filter(h => Number.isFinite(h.ping) && h.ping > 0 && h.ping < 60000 && h.time)
                .map(h => ({
                    x: toUTCDate(h.time),
                    y: Math.min(h.ping, Y_MAX)
                }))
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
        pollTimer = setInterval(() => {
            if (document.hidden) return;
            loadPingExact();
        }, POLL_MS);
    }

    function stopPolling() {
        clearInterval(pollTimer);
        pollTimer = null;
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) return;
        loadPingExact();
    });
    document.addEventListener('DOMContentLoaded', () => {
        loadPingExact();
        startPolling();
    });

    document.addEventListener('alpine:init', () => {
        let once = false;
        Alpine.effect(() => {
            const open = Alpine.store('ui')?.aboutOpen;
            if (open && !once) {
                once = true;
                setTimeout(() => {
                    loadPingExact();
                    try {
                        pingChart?.resize();
                    } catch {}
                }, 150);
            }
            if (!open) once = false;
        });
    });
















    // สร้างหิ่งห้อยบินบนผิวน้ำ
    function fireflies() {
        const COUNT = window.innerWidth < 640 ? 18 : 36;
        const DUR_MIN = 8,
            DUR_MAX = 16; // ระยะเวลาบินหนึ่งลูป
        const SIZE_MIN = 2,
            SIZE_MAX = 3.5; // ขนาดหิ่งห้อย px
        const BAND_TOP = 6; // % จากขอบบนของน้ำ
        const BAND_HEIGHT = 30; // ช่วงบินเหนือผิวน้ำ
        const sheet = ensureKeyframeSheet();

        // สร้าง keyframes เป็นรายตัว เพื่อลดงาน JS runtime
        const pathKF = () => {
            const name = 'ff_' + Math.random().toString(36).slice(2);
            // จุดทางผ่าน 5 ช่วง ให้ลอยซ้ายขวา เบี่ยงขึ้นลงเล็กน้อย
            const step = () => ({
                x: rnd(-30, 30),
                y: rnd(-18, 18)
            });
            const p1 = step(),
                p2 = step(),
                p3 = step(),
                p4 = step();
            const css = `@keyframes ${name}{
        0%   { transform: translate(0px,0px) }
        25%  { transform: translate(${p1.x}px,${p1.y}px) }
        50%  { transform: translate(${p2.x}px,${p2.y}px) }
        75%  { transform: translate(${p3.x}px,${p3.y}px) }
        100% { transform: translate(${p4.x}px,${p4.y}px) }
      }`;
            sheet.insertRule(css, sheet.cssRules.length);
            return name;
        };

        const makeOne = () => {
            const id = crypto?.randomUUID?.() || (Date.now() + Math.random());
            const left = rnd(5, 95); // %
            const top = rnd(BAND_TOP, BAND_TOP + BAND_HEIGHT); // % ภายในผิวน้ำ
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
            return {
                id,
                style
            };
        };

        return {
            flies: [],
            init() {
                // สร้างรอบแรก
                for (let i = 0; i < COUNT; i++) this.flies.push(makeOne());
                // เติมตัวที่หายไปเมื่อ DOM repaint นานๆ
                // ไม่ใช้ setInterval ถาวร เพื่อลดงานแบ็คกราวด์
                const refill = () => {
                    const need = COUNT - this.flies.length;
                    for (let i = 0; i < need; i++) this.flies.push(makeOne());
                    __schedule(refill, 12000);
                };
                __schedule(refill, 12000);

                // รีเลย์เอาต์เมื่อขนาดจอเปลี่ยน
                window.addEventListener('resize', () => {
                    const target = window.innerWidth < 640 ? 18 : 36;
                    if (this.flies.length > target) this.flies.splice(target);
                    while (this.flies.length < target) this.flies.push(makeOne());
                }, {
                    passive: true
                });
            }
        }
    }

    // อ่านคุกกี้แบบง่าย
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


    // สร้างห้องเก็บ keyframe ไว้เพิ่มตอนรัน

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

    // ฉากแม่น้ำลอยกระทง
    function riverScene(types, recent) {
        const WATER_TOP = 25;
        const WATER_BAND = 28;
        const DUR_INIT_MIN = 22,
            DUR_INIT_MAX = 34;
        const DUR_LOOP_MIN = 18,
            DUR_LOOP_MAX = 28;

        // เพิ่ม: จำนวนเลนและตำแหน่งเลนแบบเว้นระยะคงที่
        const LANES = window.innerWidth < 640 ? 6 : 12; // ปรับตามจอ
        const laneTops = Array.from({
                length: LANES
            }, (_, i) => // กระจายสม่ำเสมอ
            WATER_TOP + 6 + (i * (WATER_BAND - 6) / Math.max(1, LANES - 1))
        );
        let nextLane = 0;

        const MAX_ITEMS = window.innerWidth < 640 ? 40 : 100; // จำกัดจำนวนบนมือถือ
        const typeImg = t => types?.[t]?.img || Object.values(types || {})[0]?.img || '';

        // สร้างสไตล์การลอย
        const makeStyle = (dur, delay, top) => {
            const name = `drift_${Math.random().toString(36).slice(2)}`;
            const sheet = ensureKeyframeSheet();
            sheet.insertRule(
                `@keyframes ${name}{0%{left:-20%;opacity:0}10%{opacity:1}90%{opacity:1}100%{left:120%;opacity:0}}`,
                sheet.cssRules.length);
            return `top:${top}%;left:-20%;--floatDur:${rnd(2.8, 4.4)}s;--swayDur:${rnd(4.5, 6.5)}s;animation:${name} ${dur}s linear ${delay}s forwards,var(--_dummy,0s);`;
        };

        // สร้างไอเท็มจากเรคคอร์ด
        const mkItem = (r, init = false) => {
            const dur = init ? rnd(DUR_INIT_MIN, DUR_INIT_MAX) : rnd(DUR_LOOP_MIN, DUR_LOOP_MAX);
            const delay = init ? rnd(0, 12) : 0;

            // เลือกเลนแบบรอบ-โรบิน เพื่อไม่ซ้อนกันแนวตั้ง
            const top = laneTops[nextLane];
            nextLane = (nextLane + 1) % LANES;

            return {
                id: r.id,
                clientId: `${init ? 'srv' : 'cli'}_${r.id}_${Math.random().toString(36).slice(2)}`,
                img: typeImg(r.type),
                wish: `${r.nickname} : ${r.wish}`,
                style: makeStyle(dur, delay, top),
                show: false,
                paused: false,
                __life: (dur + delay) * 1000,
                __deadline: Date.now() + (dur + delay) * 1000
            };
        };

        // ตั้งเวลาลบไอเท็ม
        const scheduleRemoval = (vm, item, ms) => {
            // เคลียร์ตัวเดิม
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
                // คงเหลือเท่าไร
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
                // ยืดเวลาอ่านอีกหน่อย
                const extra = 8000;
                const ms = (k.__remain || 0) + extra;
                scheduleRemoval(this, k, ms);
            },
            order: base,
            seenInCycle: new Set(),
            idx: 0,
            init() {
                const initCount = Math.min(24, this.order.length);
                for (let k = 0; k < initCount; k++) this._spawnNext(true);
                const tick = () => {
                    this._spawnNext(false);
                    __schedule(tick, window.innerWidth < 400 ? rnd(7500, 10000) : rnd(5200, 8200));
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
                while (this.seenInCycle.has(this.order[this.idx]?.id) && guard < this.order.length) {
                    this.idx = (this.idx + 1) % this.order.length;
                    guard++;
                }
                const r = this.order[this.idx];
                if (!r) return;
                this.seenInCycle.add(r.id);
                this.idx = (this.idx + 1) % this.order.length;
                const item = mkItem(r, isInitial);
                this.items.unshift(item);
                if (this.items.length > MAX_ITEMS) this.items.splice(MAX_ITEMS);
                scheduleRemoval(this, item, item.__life);
            },
            spawnFromRecord(r) {
                this.order = [r, ...this.order.filter(x => x.id !== r.id)].sort((a, b) => b.id - a.id);
                this.seenInCycle.clear();
                this.idx = 0;
                const item = mkItem(r, false);
                this.items.unshift(item);
                if (this.items.length > MAX_ITEMS) this.items.splice(MAX_ITEMS);
                scheduleRemoval(this, item, item.__life);
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
                        } catch (_) {}
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

    // ตั้งตัวแปร CSS --vh ให้เท่ากับ 1% ของความสูง viewport จริง
    const rnd = (min, max) => Math.random() * (max - min) + min;
    const setVH = () => document.documentElement.style.setProperty('--vh', `${window.innerHeight * 0.01}px`);
    window.addEventListener('resize', setVH, {
        passive: true
    });
    window.addEventListener('orientationchange', setVH, {
        passive: true
    });
    document.addEventListener('DOMContentLoaded', setVH);
</script>
