// === Config ===
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
                } catch { }
            }, 150);
        }
        if (!open) once = false;
    });
});
