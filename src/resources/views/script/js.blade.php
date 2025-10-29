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

    // เริ่มต้นการโพลข้อมูลเป็นระยะ
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
            DUR_MAX = 16;
        const SIZE_MIN = 2,
            SIZE_MAX = 3.5;
        const BAND_TOP = 6;
        const BAND_HEIGHT = 30;
        const sheet = ensureKeyframeSheet();

        const pathKF = () => {
            const name = 'ff_' + Math.random().toString(36).slice(2);
            const step = () => ({
                x: rnd(-30, 30),
                y: rnd(-18, 18)
            });
            const p1 = step(),
                p2 = step(),
                p3 = step(),
                p4 = step();
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

        // สร้างหิ่งห้อยตัวหนึ่ง
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
            return {
                id,
                style
            };
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

    // ฉากแม่น้ำลอยกระทง — เว้นแถวชัด ไม่ถี่ ระยะห่างสม่ำเสมอ
    function riverScene(types, recent) {
        const WATER_TOP = 18; // จุดเริ่มเปอร์เซ็นต์แนวตั้งของแถบน้ำ
        const WATER_BAND = 24; // ความสูงแถบน้ำเป็นเปอร์เซ็นต์
        const DUR = 50;
        const MAX_ITEMS = window.innerWidth < 640 ? 15 : 35;
        const typeImg = t => types?.[t]?.img || Object.values(types || {})[0]?.img || '';

        const TOTAL_LANES = 2;
        const TOP_PAD = window.innerWidth < 640 ? 6 : 4;
        const BOTTOM_PAD = window.innerWidth < 640 ? 6 : 4;

        // คำนวณพื้นที่ที่เหลือให้วางเลน
        const AVAILABLE = Math.max(0, WATER_BAND - TOP_PAD - BOTTOM_PAD);

        // ตำแหน่ง top ของแต่ละเลน
        const laneTops = Array.from({
            length: TOTAL_LANES
        }, (_, i) => {
            if (TOTAL_LANES === 1) {
                // วางกลางพื้นที่ AVAILABLE
                return WATER_TOP + TOP_PAD + AVAILABLE / 2;
            }
            // กระจายเต็มช่วง AVAILABLE โดยมีขอบบน–ล่างตาม PAD
            const step = AVAILABLE / (TOTAL_LANES - 1);
            return WATER_TOP + TOP_PAD + i * step;
        });

        // ระยะห่างแนวนอนเดิม
        const TRACK_WIDTH = 140;
        const KRATHONG_WIDTH_PCT = window.innerWidth < 640 ? 20 : 15;
        const SAFE_GAP_PCT = KRATHONG_WIDTH_PCT * 1.25;
        const TIME_GAP_MS = (SAFE_GAP_PCT / TRACK_WIDTH) * DUR * 1000;

        console.log(
            `Config: ${TOTAL_LANES} lanes, padTop:${TOP_PAD}%, padBot:${BOTTOM_PAD}%, avail:${AVAILABLE.toFixed(1)}%, gap:${(TIME_GAP_MS/1000).toFixed(1)}s`
        );

        const laneNextTime = new Array(TOTAL_LANES).fill(0);
        let currentLaneIndex = 0;

        function pickLane() {
            const now = performance.now();
            for (let attempt = 0; attempt < TOTAL_LANES; attempt++) {
                const laneIdx = (currentLaneIndex + attempt) % TOTAL_LANES;
                if (laneNextTime[laneIdx] <= now) {
                    laneNextTime[laneIdx] = now + TIME_GAP_MS;
                    currentLaneIndex = (laneIdx + 1) % TOTAL_LANES;
                    return laneIdx;
                }
            }
            return -1;
        }
        // สร้าง animation ที่นุ่มนวล
        const makeStyle = (topPct, laneIdx) => {
            const name = `drift_${Math.random().toString(36).slice(2)}`;
            const sheet = ensureKeyframeSheet();

            // คลื่นน้ำอ่อนๆ
            const waveAmp = 1.5;
            const wavePhase = (laneIdx / TOTAL_LANES) * Math.PI * 2;
            const waveFreq = 2;

            const steps = 10;
            let keyframeRules = '';
            for (let i = 0; i <= steps; i++) {
                const progress = (i / steps) * 100;
                const xPos = -20 + (progress / 100) * 140;
                const wave = Math.sin(((i / steps) * Math.PI * waveFreq) + wavePhase) * waveAmp;
                const opacity = progress < 6 ? progress / 6 : (progress > 94 ? (100 - progress) / 6 : 1);
                const rotate = Math.sin(((i / steps) * Math.PI * waveFreq) + wavePhase) * 1.8;

                keyframeRules += `${progress.toFixed(1)}%{
                left:${xPos.toFixed(2)}%;
                top:calc(${topPct}% + ${wave.toFixed(2)}%);
                opacity:${opacity.toFixed(3)};
                transform:rotate(${rotate.toFixed(2)}deg);
            }`;
            }

            sheet.insertRule(`@keyframes ${name}{${keyframeRules}}`, sheet.cssRules.length);
            return `animation:${name} ${DUR}s ease-in-out forwards;will-change:left,top,opacity,transform;`;
        };

        const order = Array
            .from(new Map((recent || []).map(r => [r.id, r])).values())
            .sort((a, b) => b.id - a.id);

        const mkItem = (rec) => {
            const li = pickLane();
            if (li === -1) return null;

            return {
                id: rec.id,
                clientId: `item_${rec.id}_${Math.random().toString(36).slice(2)}`,
                img: typeImg(rec.type),
                wish: `${rec.nickname} : ${rec.wish}`,
                style: makeStyle(laneTops[li], li),
                show: false,
                paused: false,
                __life: DUR * 1000,
                __deadline: Date.now() + DUR * 1000
            };
        };

        const scheduleRemoval = (vm, item, ms) => {
            if (item.__tid) {
                clearTimeout(item.__tid);
                __timers.delete(item.__tid);
                item.__tid = null;
            }
            item.__deadline = Date.now() + ms;
            item.__tid = __schedule(() => {
                const i = vm.items.findIndex(x => x.clientId === item.clientId);
                if (i > -1) {
                    vm.items.splice(i, 1);
                    usedIds.delete(item.id);
                }
            }, ms + 50);
        };

        let recPtr = 0;
        const usedIds = new Set();

        function nextRecord() {
            if (order.length === 0) return null;

            let attempts = 0;
            while (attempts < order.length) {
                const r = order[recPtr];
                recPtr = (recPtr + 1) % order.length;

                if (!usedIds.has(r.id)) {
                    return r;
                }
                attempts++;
            }

            // รีเซ็ตถ้าใช้ครบ
            usedIds.clear();
            const r = order[recPtr];
            recPtr = (recPtr + 1) % order.length;
            return r;
        }

        // รอให้มีเลนว่าง - เพิ่มเวลารออีก
        function getNextSpawnDelay() {
            const now = performance.now();
            const waitTimes = laneNextTime.map(t => Math.max(0, t - now));
            const minWait = Math.min(...waitTimes);
            // เพิ่ม buffer ให้มากขึ้น เพื่อไม่ให้ปล่อยถี่
            return Math.max(300, minWait + 200);
        }

        return {
            items: [],
            order,

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
                if (order.length === 0) return;

                // เริ่มต้นด้วยจำนวนน้อย ค่อยๆ เติม
                const initialGap = TIME_GAP_MS * 1.2; // เพิ่มช่องว่างตอนเริ่มต้น
                let spawnCount = 0;
                const targetCount = Math.min(TOTAL_LANES, order.length);

                const initialSpawn = () => {
                    if (spawnCount >= targetCount) {
                        // รอนานก่อนเริ่มลูปต่อเนื่อง
                        setTimeout(() => this._continuousLoop(), TIME_GAP_MS);
                        return;
                    }

                    const rec = nextRecord();
                    if (!rec) return;

                    const item = mkItem(rec);
                    if (item) {
                        usedIds.add(item.id);
                        this.items.push(item);
                        scheduleRemoval(this, item, item.__life);
                        spawnCount++;
                    }

                    __schedule(initialSpawn, initialGap);
                };

                initialSpawn();
            },

            _continuousLoop() {
                const spawn = () => {
                    const rec = nextRecord();
                    if (!rec) {
                        __schedule(spawn, 2000);
                        return;
                    }

                    const item = mkItem(rec);
                    if (item) {
                        usedIds.add(item.id);
                        this.items.push(item);
                        if (this.items.length > MAX_ITEMS) {
                            const removed = this.items.shift();
                            if (removed.__tid) {
                                clearTimeout(removed.__tid);
                                __timers.delete(removed.__tid);
                            }
                            usedIds.delete(removed.id);
                        }
                        scheduleRemoval(this, item, item.__life);
                    }

                    const delay = getNextSpawnDelay();
                    __schedule(spawn, delay);
                };

                spawn();
            },

            spawnFromRecord(r) {
                if (usedIds.has(r.id)) return;

                const fresh = [r, ...order.filter(x => x.id !== r.id)].sort((a, b) => b.id - a.id);
                order.length = 0;
                order.push(...fresh);

                const item = mkItem(r);
                if (item) {
                    usedIds.add(item.id);
                    this.items.unshift(item);
                    if (this.items.length > MAX_ITEMS) {
                        const removed = this.items.pop();
                        if (removed.__tid) {
                            clearTimeout(removed.__tid);
                            __timers.delete(removed.__tid);
                        }
                        usedIds.delete(removed.id);
                    }
                    scheduleRemoval(this, item, item.__life);
                }
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
                    window.__fw?.burst();
                    const api = Alpine.$data(document.getElementById('river'));
                    api?.spawnNew?.(data);
                    this.form.wish = '';
                    setTimeout(() => {
                        Alpine.store('ui').open = false;
                        this.ok = '';
                    }, 750);
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


    // พลุไฟบนท้องฟ้า
    (() => {
        const TAU = Math.PI * 2;
        const rand = (a, b) => Math.random() * (b - a) + a;
        const clamp = (v, a, b) => Math.min(b, Math.max(a, v));

        class Part {
            constructor(x, y, ang, speed, col, grav, drag, life) {
                this.x = x;
                this.y = y;
                this.vx = Math.cos(ang) * speed;
                this.vy = Math.sin(ang) * speed;
                this.col = col;
                this.g = grav;
                this.d = drag;
                this.t = 0;
                this.life = life;
                this.dead = false;
            }
            step(dt) {
                this.t += dt;
                if (this.t > this.life) {
                    this.dead = true;
                    return;
                }
                this.vx *= this.d;
                this.vy = this.vy * this.d + this.g * dt;
                this.x += this.vx * dt * 60;
                this.y += this.vy * dt * 60;
            }
        }

        class Fireworks {
            constructor(canvas) {
                this.c = canvas;
                this.ctx = canvas.getContext('2d');
                this.parts = [];
                this.last = 0;
                this.running = false;
                this.dpr = window.devicePixelRatio || 1;
                this.resize();
                window.addEventListener('resize', () => this.resize(), {
                    passive: true
                });
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden) this.stop();
                    else this.start();
                });
                // แตะหน้าจอเพื่อยิงเพิ่ม
                window.addEventListener('pointerdown', e => {
                    const r = this.c.getBoundingClientRect();
                    this.burst((e.clientX - r.left) * this.dpr, (e.clientY - r.top) * this.dpr);
                }, {
                    passive: true
                });
            }
            resize() {
                const w = innerWidth,
                    h = innerHeight;
                this.c.width = Math.floor(w * this.dpr);
                this.c.height = Math.floor(h * this.dpr);
            }
            palette() {
                const h = Math.floor(rand(0, 360));
                const toRGB = (h, s, l) => {
                    // hsl -> rgb mini
                    s /= 100;
                    l /= 100;
                    const k = n => (n + h / 30) % 12,
                        a = s * Math.min(l, 1 - l);
                    const f = n => l - a * Math.max(-1, Math.min(k(n) - 3, Math.min(9 - k(n), 1)));
                    return `rgba(${Math.round(255*f(0))},${Math.round(255*f(8))},${Math.round(255*f(4))},`;
                };
                const base = toRGB(h, 90, 60);
                return n => `${base}${n})`;
            }
            burst(x, y, opts = {}) {
                const N = opts.n || 180;
                const speed = opts.speed || rand(4.2, 6.2);
                const grav = opts.grav ?? 0.12;
                const drag = opts.drag ?? 0.988;
                const life = opts.life || rand(0.9, 1.4);
                const color = this.palette();
                for (let i = 0; i < N; i++) {
                    const ang = (i / N) * TAU + rand(-0.05, 0.05);
                    const sp = speed * rand(0.6, 1.1);
                    const alphaStart = rand(0.6, 1);
                    const p = new Part(x, y, ang, sp, color(alphaStart), grav, drag, life * rand(0.7, 1.3));
                    p.spark = rand(0, 1) > 0.7;
                    this.parts.push(p);
                }
            }
            ring(x, y) {
                const N = 140,
                    color = this.palette();
                for (let i = 0; i < N; i++) {
                    const ang = (i / N) * TAU;
                    const p = new Part(x, y, ang, rand(5.2, 5.8), color(0.9), 0.10, 0.99, 1.2);
                    this.parts.push(p);
                }
            }
            randomAuto() {
                const x = rand(this.c.width * 0.15, this.c.width * 0.85);
                const y = rand(this.c.height * 0.15, this.c.height * 0.45);
                Math.random() > 0.35 ? this.burst(x, y) : this.ring(x, y);
            }
            start() {
                if (this.running) return;
                this.running = true;
                this.last = performance.now();
                const loop = (t) => {
                    if (!this.running) return;
                    const dt = clamp((t - this.last) / 1000, 0, 0.033);
                    this.last = t;
                    this.step(dt);
                    this.draw();
                    requestAnimationFrame(loop);
                };
                this._autoTick = setInterval(() => this.randomAuto(), 1800);
                requestAnimationFrame(loop);
            }
            stop() {
                this.running = false;
                clearInterval(this._autoTick);
            }
            step(dt) {
                for (const p of this.parts) p.step(dt);
                this.parts = this.parts.filter(p => !p.dead);
            }
            draw() {
                const ctx = this.ctx,
                    w = this.c.width,
                    h = this.c.height;
                ctx.globalCompositeOperation = 'destination-out';
                ctx.fillStyle = 'rgba(0,0,0,0.20)';
                ctx.fillRect(0, 0, w, h);
                ctx.globalCompositeOperation = 'lighter';
                for (const p of this.parts) {
                    const a = clamp(1 - p.t / p.life, 0, 1);
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, 1.2 + a * 1.8, 0, TAU);
                    ctx.fillStyle = p.col.replace(/[\d.]+\)$/i, `${a})`);
                    ctx.fill();
                    if (p.spark && Math.random() < 0.2) {
                        ctx.fillRect(p.x, p.y, 1, 1);
                    }
                }
            }
        }

        // bootstrap
        const canvas = document.getElementById('fwCanvas');
        if (canvas) {
            const fw = new Fireworks(canvas);
            // ให้ใช้ทั่วหน้า
            window.__fw = {
                start: () => fw.start(),
                stop: () => fw.stop(),
                burst: (x, y) => fw.burst(x || fw.c.width * 0.5, y || fw.c.height * 0.35),
                ring: (x, y) => fw.ring(x || fw.c.width * 0.5, y || fw.c.height * 0.35),
            };
            // เริ่มทำงานอัตโนมัติ
            fw.start();
        }
    })();
</script>
