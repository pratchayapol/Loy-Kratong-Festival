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

    // ฉากแม่น้ำลอยกระทง — ไม่สุ่ม, ใหม่→เก่า, เว้นระยะมาก, ฟันปลา, ไม่ซ้อน
    function riverScene(types, recent) {
        const WATER_TOP = 25; // %
        const WATER_BAND = 28; // %
        const DUR = 26; // s ความยาวการลอยต่อชิ้น (คงที่ทุกชิ้น เพื่อตีระยะให้เนียน)
        const MAX_ITEMS = window.innerWidth < 640 ? 40 : 100;
        const typeImg = t => types?.[t]?.img || Object.values(types || {})[0]?.img || '';

        // เลนคงที่
        const TOTAL_LANES = window.innerWidth < 640 ? 6 : 12;
        const laneTops = Array.from({
                length: TOTAL_LANES
            }, (_, i) =>
            WATER_TOP + 6 + (i * (WATER_BAND - 6) / Math.max(1, TOTAL_LANES - 1))
        );

        // เว้นระยะหัวชนแนวนอน “มากขึ้น” แบบคงที่
        // เส้นทาง -20% → 120% = 140% viewport
        const TRACK_WIDTH = 140;
        const MIN_GAP_X = window.innerWidth < 640 ? 38 : 42; // เพิ่ม spacing ชัดเจน
        const HEADWAY_MS = (MIN_GAP_X / TRACK_WIDTH) * DUR * 1000;

        // ทำฟันปลา: ครึ่งช่วงให้แถวล่างตั้งต้นช้ากว่า
        const HALF = Math.floor(TOTAL_LANES / 2);
        const upperIdx = Array.from({
            length: HALF
        }, (_, i) => i);
        const lowerIdx = Array.from({
            length: TOTAL_LANES - HALF
        }, (_, i) => HALF + i);

        // next time ต่อเลน
        const now = () => performance.now();
        const laneNext = new Array(TOTAL_LANES).fill(0);
        const PHASE_MS = HEADWAY_MS / 2;
        for (const li of lowerIdx) laneNext[li] = now() + PHASE_MS; // phase shift แถวล่าง → ฟันปลา

        // เดินคิว lane แบบ round-robin (ลดโอกาสเกาะกลุ่ม)
        let upPtr = 0,
            loPtr = 0,
            useUpper = true;

        function pickLane() {
            const t = now();
            // ลอง band ตามคิวก่อน ถ้าไม่ว่างค่อยสลับ
            const tryBand = (band, ptrRef) => {
                if (band.length === 0) return -1;
                let ptr = (ptrRef === 'up' ? upPtr : loPtr);
                for (let k = 0; k < band.length; k++) {
                    const li = band[(ptr + k) % band.length];
                    if (laneNext[li] <= t) {
                        if (ptrRef === 'up') upPtr = (li - band[0] + 1) % band.length;
                        else loPtr = (li - band[0] + 1) % band.length;
                        laneNext[li] = t + HEADWAY_MS;
                        return li;
                    }
                }
                return -1;
            };
            const first = useUpper ? tryBand(upperIdx, 'up') : tryBand(lowerIdx, 'lo');
            useUpper = !useUpper;
            if (first !== -1) return first;
            return useUpper ? tryBand(upperIdx, 'up') : tryBand(lowerIdx, 'lo');
        }

        // สร้าง keyframes + style คงที่
        const makeStyle = (topPct) => {
            const name = `drift_${Math.random().toString(36).slice(2)}`;
            const sheet = ensureKeyframeSheet();
            sheet.insertRule(
                `@keyframes ${name}{0%{left:-20%;opacity:0}10%{opacity:1}90%{opacity:1}100%{left:120%;opacity:0}}`,
                sheet.cssRules.length
            );
            // ไม่มี animation-delay เพิ่ม เพราะจัด phase ด้วย laneNext แล้ว
            return `top:${topPct}%;left:-20%;animation:${name} ${DUR}s linear 0s forwards;`;
        };

        // ลิสต์ข้อมูลเรียงใหม่→เก่าแบบคงที่
        const order = Array
            .from(new Map((recent || []).map(r => [r.id, r])).values())
            .sort((a, b) => b.id - a.id);

        // สร้างชิ้น
        const mkItem = (rec) => {
            const li = pickLane();
            if (li === -1) return null; // ยังไม่มีเลนว่าง
            return {
                id: rec.id,
                clientId: `item_${rec.id}_${Math.random().toString(36).slice(2)}`,
                img: typeImg(rec.type),
                wish: `${rec.nickname} : ${rec.wish}`,
                style: makeStyle(laneTops[li]),
                show: false,
                paused: false,
                __life: DUR * 1000,
                __deadline: Date.now() + DUR * 1000
            };
        };

        // ตั้งลบ
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

        // ตัวเดินลูป deterministic ใหม่→เก่า วนต่อเนื่อง
        let recPtr = 0;

        function nextRecord() {
            const r = order[recPtr];
            recPtr = (recPtr + 1) % order.length;
            return r;
        }

        // ตัวคำนวณ “เวลาถัดไปที่สามารถยิงได้” จากทุกเลน เพื่อไม่ยิงถี่เกิน
        function nextFeasibleDelayMs() {
            const t = now();
            const soonest = Math.min(...laneNext);
            return Math.max(120, soonest - t); // อย่างน้อย 120ms กัน tight loop
        }

        return {
            items: [],
            order, // คงไว้เผื่อ UI อื่นใช้อ่าน
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
                const extra = 8000;
                scheduleRemoval(this, k, (k.__remain || 0) + extra);
            },

            init() {
                // เติมล็อตแรกแบบคงที่ พอดีช่อง ไม่สุ่ม
                const initCount = Math.min(24, order.length);
                let spawned = 0;
                const pump = () => {
                    if (spawned >= initCount) {
                        this._loop();
                        return;
                    }
                    const item = mkItem(nextRecord());
                    if (item) {
                        this.items.unshift(item);
                        if (this.items.length > MAX_ITEMS) this.items.splice(MAX_ITEMS);
                        scheduleRemoval(this, item, item.__life);
                        spawned++;
                        __schedule(pump, nextFeasibleDelayMs());
                    } else {
                        __schedule(pump, nextFeasibleDelayMs());
                    }
                };
                pump();
            },

            _loop() {
                const tick = () => {
                    const item = mkItem(nextRecord());
                    if (item) {
                        this.items.unshift(item);
                        if (this.items.length > MAX_ITEMS) this.items.splice(MAX_ITEMS);
                        scheduleRemoval(this, item, item.__life);
                    }
                    __schedule(tick, nextFeasibleDelayMs()); // ยิงเมื่อเลนพร้อมเท่านั้น
                };
                tick();
            },

            // แทรกของใหม่ไว้ต้นลิสต์ และระบบจะหยิบตามคิวเอง
            spawnFromRecord(r) {
                const fresh = [r, ...order.filter(x => x.id !== r.id)].sort((a, b) => b.id - a.id);
                order.length = 0;
                order.push(...fresh);
                // recPtr ไม่รีเซ็ต เพื่อคงความต่อเนื่อง
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
