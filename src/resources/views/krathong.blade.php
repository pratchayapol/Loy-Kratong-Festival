<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Loy Krathong Festival</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Favicons -->
    <link rel="icon" href="{{ secure_asset('favicon.ico') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ secure_asset('favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ secure_asset('favicon-16x16.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ secure_asset('apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ secure_asset('site.webmanifest') }}" />
    <meta name="theme-color" content="#0b2e4a" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@200..800&display=swap&subset=thai"
        rel="stylesheet">


    <!-- Tailwind config BEFORE CDN -->
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Sarabun"', 'ui-sans-serif', 'system-ui'],
                            display: ['"Sarabun"', 'ui-sans-serif', 'system-ui'],
                        },
                        keyframes: {
                            floatY: {
                                '0%,100%': {
                                    transform: 'translateY(0)'
                                },
                                '50%': {
                                    transform: 'translateY(-6px)'
                                }
                            },
                            sway: {
                                '0%,100%': {
                                    transform: 'rotate(0deg)'
                                },
                                '25%': {
                                    transform: 'rotate(-1.5deg)'
                                },
                                '75%': {
                                    transform: 'rotate(1.5deg)'
                                }
                            },
                            waves: {
                                '0%': {
                                    transform: 'translateX(0)'
                                },
                                '100%': {
                                    transform: 'translateX(-50%)'
                                }
                            },
                            twinkle: {
                                '0%,100%': {
                                    opacity: '.35',
                                    transform: 'scale(1)'
                                },
                                '50%': {
                                    opacity: '1',
                                    transform: 'scale(1.15)'
                                }
                            }
                        },
                        animation: {
                            floatY: 'floatY 3.2s ease-in-out infinite',
                            sway: 'sway 5s ease-in-out infinite',
                            waves: 'waves 18s linear infinite',
                            twinkle: 'twinkle 3.4s ease-in-out infinite'
                        }
                    }
                }
            }
        }; // tailwindcss.com CDN จะอ่านตัวแปรนี้ตอนโหลด
    </script>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Libs: ใส่ defer -->
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <!-- Base styles for layout & chart -->
    <style>
        /* หิ่งห้อยกระพริบ */
        @keyframes fireflyBlink {

            0%,
            100% {
                opacity: .35;
                filter: drop-shadow(0 0 4px rgba(255, 240, 180, .6)) drop-shadow(0 0 10px rgba(255, 230, 140, .35));
            }

            50% {
                opacity: 1;
                filter: drop-shadow(0 0 8px rgba(255, 245, 200, .9)) drop-shadow(0 0 18px rgba(255, 235, 160, .6));
            }
        }

        /* กระทงหยุด*/
        .krathong-item.is-paused {
            animation-play-state: paused !important
        }

        .krathong-pop {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 90%;
            max-width: 280px;
            z-index: 50;
            padding: .6rem .8rem;
            border-radius: 12px;
            background: rgba(2, 6, 23, .85);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, .15);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .4)
        }

        /* โบว์สีดำ */
        .ribbon-black {
            position: fixed;
            right: 0;
            top: 0;
            z-index: 2568
        }

        [x-cloak] {
            display: none !important
        }

        .krathong-item {
            animation: floatY var(--floatDur, 3.2s) ease-in-out infinite, sway var(--swayDur, 5s) ease-in-out infinite;
            will-change: transform
        }

        .modal-enter {
            animation: slideUp .3s ease-out
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(.95)
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1)
            }
        }

        .wrap {
            max-width: 900px;
            margin: 0.75rem auto
        }

        .ping-chart-wrapper {
            max-width: 900px;
            margin: 0.5rem auto
        }

        #pingChart {
            width: 100%;
            height: 200px
        }
    </style>

    <!-- helpers + mobile vh fix + safe area -->
    <script>
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
    <style id="dyn-keyframes"></style>
    <style>
        .pb-safe {
            padding-bottom: max(1rem, env(safe-area-inset-bottom));
        }

        .pt-safe {
            padding-top: max(0.75rem, env(safe-area-inset-top));
        }

        @media (max-width: 420px),
        (prefers-reduced-motion: reduce) {
            .less-anim * {
                animation: none !important;
                transition: none !important
            }
        }

        body {
            touch-action: manipulation
        }
    </style>
</head>

<body class="min-h-screen bg-slate-950 text-slate-100 font-sans overflow-hidden" x-data="{}">
    {{-- <div class="ribbon-black">
        <img src="https://roietonline.net/images/black-ribbon.png" alt="โบว์แสดงความอาลัย"
            title="ปวงพสกนิกรชาวไทยน้อมสำนึกในพระมหากรุณาธิคุณตราบนิจนิรันดร์" class="img-responsive" loading="lazy"
            decoding="async">
    </div> --}}
    <!-- ป้ายเชิญด้านบนตรงกลาง -->
    <div class="fixed top-4 left-1/2 -translate-x-1/2 z-40 select-none">
        <div
            class="px-8 sm:px-10 py-4 sm:py-5 rounded-3xl border border-white/20 bg-white/10 backdrop-blur-xl shadow-[0_20px_50px_rgba(0,0,0,0.45)] text-center">
            <div
                class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-wide bg-gradient-to-r from-cyan-300 via-blue-300 to-purple-300 bg-clip-text text-transparent">
                ลอยกระทงออนไลน์
            </div>
            <div class="mt-1 text-base sm:text-lg md:text-xl text-slate-200/90">
                ตั้งจิตอธิษฐาน แล้วปล่อยความกังวลให้ลอยไปกับสายน้ำ
            </div>
        </div>
    </div>

    <!-- Alpine store -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('ui', {
                open: false,
                aboutOpen: false
            });
        });
    </script>

    <!-- ปุ่มมุมซ้ายบน -->
    <button @click="$store.ui.open=true"
        class="fixed left-4 top-4 z-40 inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-600 px-5 py-3 font-semibold shadow-btn hover:shadow-[0_15px_50px_rgba(34,211,238,0.55)] hover:scale-105 active:scale-100 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-cyan-400/50">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0M12 3v9" />
        </svg>
        <span class="hidden sm:inline">ลอยกระทงด้วย</span><span class="sm:hidden">ลอย</span>
    </button>

    <!-- ปุ่มเกี่ยวกับ -->
    <button @click="$store.ui.aboutOpen=true"
        class="fixed right-4 bottom-4 z-40 w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg hover:shadow-purple-500/50 hover:scale-110 active:scale-100 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-purple-400/50 flex items-center justify-center group"
        title="เกี่ยวกับ">
        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 group-hover:rotate-12 transition-transform"
            viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <circle cx="12" cy="12" r="10" stroke-width="2" />
            <path d="M12 16v-4M12 8h.01" stroke-width="2" stroke-linecap="round" />
        </svg>
    </button>

    <!-- ฉากฟ้า/น้ำ แบบ responsive: ฟ้า 60% มือถือ, 58% บน sm+ -->
    <main class="relative min-h-[calc(var(--vh,1vh)*100)] sm:min-h-screen less-anim">
        <!-- SKY -->
        <div class="absolute top-0 left-0 right-0 h-[60%] sm:h-[58%]">
            <div class="absolute inset-0 bg-gradient-to-b from-[#020510] via-[#0a1628] to-[#0e2845]"></div>

            <!-- เมฆบาง ๆ เคลื่อนไหว -->
            <div class="absolute inset-0 opacity-15">
                <div
                    class="absolute top-[20%] left-0 w-[400%] h-32 bg-gradient-to-r from-transparent via-slate-300/20 to-transparent blur-3xl animate-[waves_65s_linear_infinite]">
                </div>
                <div
                    class="absolute top-[35%] left-[-50%] w-[400%] h-24 bg-gradient-to-r from-transparent via-slate-400/15 to-transparent blur-3xl animate-[waves_85s_linear_infinite]">
                </div>
            </div>

            <!-- แสงฟ้า -->
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_70%_40%,rgba(100,150,255,0.12),transparent_50%)] mix-blend-screen">
            </div>
            <div
                class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_30%_50%,rgba(180,140,255,0.08),transparent_45%)] mix-blend-soft-light">
            </div>

            <!-- พระจันทร์ -->
            <div
                class="absolute top-[18%] right-[10%] w-16 h-16 sm:w-24 sm:h-24 rounded-full bg-gradient-to-br from-[#fffef0] via-[#fff8dc] to-[#ffe4b5] animate-moonGlow pointer-events-none z-10 opacity-95 shadow-[0_0_60px_rgba(255,248,220,0.8),0_0_120px_rgba(255,248,220,0.4),inset_0_0_20px_rgba(255,255,255,0.3)]">
                <div class="absolute inset-0 rounded-full overflow-hidden opacity-20">
                    <div
                        class="absolute top-[30%] left-[20%] w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-slate-400/40 blur-sm">
                    </div>
                    <div
                        class="absolute top-[55%] right-[25%] w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-slate-400/30 blur-sm">
                    </div>
                    <div
                        class="absolute bottom-[35%] left-[40%] w-2 h-2 sm:w-3 sm:h-3 rounded-full bg-slate-400/35 blur-sm">
                    </div>
                </div>
            </div>

            <!-- ลำแสง -->
            <div
                class="absolute top-[18%] right-[10%] w-1 sm:w-1.5 h-[50vh] opacity-8 bg-gradient-to-b from-yellow-100/30 via-yellow-50/10 to-transparent blur-xl rotate-[8deg] pointer-events-none">
            </div>

            <!-- ดาวระยิบ: จำนวนปรับตามจอ -->
            <div class="pointer-events-none absolute inset-0" x-data="{ stars: [] }" x-init="const base = 90 + Math.min(window.innerWidth, window.innerHeight) / 10;
            const cap = window.innerWidth < 400 ? 80 : 150;
            const count = Math.round(Math.min(cap, base));
            for (let i = 0; i < count; i++) {
                const s = Math.random();
                stars.push({ left: Math.random() * 100, top: Math.random() * 100, delay: Math.random() * 5, duration: 3 + Math.random() * 6, size: s > 0.92 ? 2.5 : (s > 0.75 ? 1.5 : 1), opacity: s > 0.85 ? 1 : 0.7 });
            }">
                <template x-for="(s,i) in stars" :key="i">
                    <div class="absolute rounded-full bg-white animate-twinkle"
                        :style="`left:${s.left}%;top:${s.top}%;width:${s.size}px;height:${s.size}px;animation-delay:${s.delay}s;animation-duration:${s.duration}s;opacity:${s.opacity};box-shadow:0 0 ${s.size*3}px rgba(255,255,255,${s.opacity*0.9}),0 0 ${s.size*6}px rgba(200,220,255,${s.opacity*0.5})`">
                    </div>
                </template>
            </div>

            <!-- ดาวพุ่ง -->
            <div class="pointer-events-none absolute inset-0" x-data="{ shooting: [] }" x-init="const add = () => {
                const id = Date.now() + Math.random();
                shooting.push({ id, top: Math.random() * 40, left: 20 + Math.random() * 60, duration: 1.5 + Math.random() * 1 });
                setTimeout(() => { shooting = shooting.filter(s => s.id !== id) }, 3000);
                setTimeout(add, 8000 + Math.random() * 15000)
            };
            setTimeout(add, 3000);">
                <template x-for="s in shooting" :key="s.id">
                    <div class="absolute w-1 h-1 bg-white rounded-full"
                        :style="`top:${s.top}%;left:${s.left}%;animation: shootingStar ${s.duration}s ease-out forwards;box-shadow:0 0 8px rgba(255,255,255,0.9),0 0 16px rgba(200,220,255,0.6)`">
                    </div>
                </template>
            </div>
        </div>

        <!-- HORIZON โค้ง -->
        <svg class="absolute left-0 right-0 top-[60%] sm:top-[58%] h-20 pointer-events-none" viewBox="0 0 1440 160"
            preserveAspectRatio="none" aria-hidden="true">
            <path d="M0,100 C360,140 1080,60 1440,100" stroke="rgba(255,255,255,0.40)" stroke-width="1"
                fill="none" />
            <path d="M0,100 C360,140 1080,60 1440,100 L1440,160 L0,160 Z" fill="rgba(255,255,255,0.08)" />
        </svg>

        <!-- WATER -->
        <div class="absolute left-0 right-0 top-[60%] sm:top-[58%] bottom-0 overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-[#0b2e4a] via-[#082237] to-[#051827]"></div>

            <!-- คลื่นพื้นผิว -->
            <div
                class="absolute left-0 w-[220%] h-24 top-[8%] opacity-28 blur-2xl bg-[radial-gradient(ellipse_at_center,_rgba(255,255,255,.9)_0%,_transparent_60%)] animate-[waves_28s_linear_infinite]">
            </div>
            <div
                class="absolute left-0 w-[220%] h-24 top-[36%] opacity-20 blur-2xl bg-[radial-gradient(ellipse_at_center,_rgba(255,255,255,.85)_0%,_transparent_60%)] animate-[waves_34s_linear_infinite]">
            </div>
            <div
                class="absolute left-0 w-[220%] h-24 top-[64%] opacity-16 blur-2xl bg-[radial-gradient(ellipse_at_center,_rgba(255,255,255,.8)_0%,_transparent_60%)] animate-[waves_40s_linear_infinite]">
            </div>

            <!-- กระทง -->
            <div id="river" class="absolute inset-0 overflow-hidden" x-data="riverScene(@js($types), @js($recent))"
                x-init="init()">
                <template x-for="k in items" :key="k.clientId">
                    <div class="absolute flex flex-col items-center will-change-transform krathong-item"
                        :class="k.paused ? 'is-paused' : ''" :style="k.style" @mouseenter="pause(k); k.show=true"
                        @mouseleave="resume(k); k.show=false" @touchstart.passive="pause(k); k.show=true"
                        @touchend.passive="resume(k); k.show=false">
                        <div class="px-3 py-2 rounded-2xl text-xs sm:text-sm max-w-[240px] sm:max-w-[300px] text-cyan-50 bg-slate-900/80 backdrop-blur-xl border border-cyan-400/30 shadow-lg shadow-cyan-500/20 whitespace-nowrap overflow-hidden text-ellipsis"
                            x-text="k.wish"></div>

                        <!-- popup ข้อความเต็ม -->
                        <div x-show="k.show" x-transition.opacity.duration.120ms class="krathong-pop">
                            <div class="text-[11px] leading-5 sm:text-sm text-slate-100" x-text="k.wish"></div>
                        </div>

                        <div class="relative mt-2">
                            <img :src="k.img" alt="krathong" decoding="async" fetchpriority="low"
                                class="w-16 h-16 sm:w-20 sm:h-20 drop-shadow-[0_15px_25px_rgba(0,0,0,0.6)] relative z-10">
                            <div
                                class="absolute inset-0 -z-10 blur-xl opacity-50 bg-gradient-radial from-amber-300/50 to-transparent rounded-full">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- เงามืดขอบล่าง -->
                <div
                    class="pointer-events-none absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-slate-950/80 via-slate-950/40 to-transparent">
                </div>
            </div>

            <!-- Fireflies layer -->
            <div class="absolute inset-0 pointer-events-none" x-data="fireflies()" x-init="init()">
                <template x-for="f in flies" :key="f.id">
                    <div class="absolute rounded-full bg-yellow-200" :style="f.style" aria-hidden="true"></div>
                </template>
            </div>

        </div>
    </main>

    <!-- Modal ฟอร์ม -->
    <div x-show="$store.ui.open" x-cloak class="fixed inset-0 z-50" @keydown.escape.window="$store.ui.open=false">
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity"
            @click="$store.ui.open=false"></div>

        <div class="absolute inset-0 flex items-center justify-center p-0 sm:p-6 overflow-y-auto" @click.stop>
            <div class="w-full max-w-full sm:max-w-xl modal-enter backdrop-blur-2xl rounded-none sm:rounded-3xl border border-white/20 bg-slate-900/50 shadow-glass h-[calc(var(--vh,1vh)*100)] sm:h-auto flex flex-col pb-safe pt-safe"
                x-data="krathongForm()">
                <div
                    class="flex items-start justify-between px-5 sm:px-6 pb-4 sm:pb-0 pt-2 sm:pt-6 border-b border-white/10">
                    <div>
                        <h2
                            class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-cyan-400 via-blue-400 to-purple-400 bg-clip-text text-transparent">
                            ลอยกระทง</h2>
                        <p class="text-sm text-slate-300 mt-1.5">เลือกแบบ กรอกข้อมูล แล้วปล่อยลอยเลย</p>
                    </div>
                    <button @click="$store.ui.open=false"
                        class="rounded-xl p-2 hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-cyan-400/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submit" class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-5">
                    <div>
                        <label class="text-sm font-semibold text-slate-200">เลือกแบบกระทง</label>
                        <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach ($types as $key => $t)
                                <label @click="form.type='{{ $key }}'"
                                    class="group relative cursor-pointer rounded-2xl border border-white/10 bg-white/5 hover:bg-white/10 hover:border-cyan-400/40 hover:scale-105 transition-all duration-300 p-3 sm:p-4 flex flex-col items-center gap-2"
                                    :class="form.type === '{{ $key }}' ? 'ring-2 ring-cyan-400/80 bg-cyan-500/10' :
                                        'ring-0'">
                                    <input class="sr-only" type="radio" name="type" x-model="form.type"
                                        value="{{ $key }}">
                                    <img src="{{ $t['img'] }}" alt="{{ $t['label'] }}"
                                        class="w-10 h-10 sm:w-12 sm:h-12 drop-shadow-lg" loading="lazy">
                                    <span class="text-xs sm:text-sm font-medium">{{ $t['label'] }}</span>
                                    <span class="absolute -top-2 -right-2 transition-all duration-200"
                                        :class="form.type === '{{ $key }}' ? 'scale-100 opacity-100' :
                                            'scale-0 opacity-0'">
                                        <span
                                            class="w-6 h-6 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 text-white text-xs shadow-lg grid place-items-center font-bold">✓</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-200">ชื่อเล่น</label>
                            <input x-model="form.nickname" type="text" maxlength="50" required
                                class="min-h-12 w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 outline-none focus:border-cyan-400/60 focus:ring-2 focus:ring-cyan-400/30 transition-all placeholder:text-slate-500"
                                placeholder="เช่น โฟกัส">
                        </div>
                        <div class="space-y-2">
                            <label class="text-sm font-semibold text-slate-200">อายุ</label>
                            <input x-model.number="form.age" type="number" inputmode="numeric" pattern="[0-9]*"
                                min="1" max="120" required
                                class="min-h-12 w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 outline-none focus:border-cyan-400/60 focus:ring-2 focus:ring-cyan-400/30 transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-slate-200">คำอธิษฐาน</label>
                        <textarea x-model="form.wish" maxlength="200" required rows="3"
                            class="min-h-12 w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 outline-none focus:border-cyan-400/60 focus:ring-2 focus:ring-cyan-400/30 transition-all placeholder:text-slate-500 resize-none"
                            placeholder="ขอให้..."></textarea>
                        <div class="text-xs text-slate-400 flex justify-between"><span>ไม่เกิน 200 ตัวอักษร</span><span
                                x-text="`${form.wish?.length||0}/200`"></span></div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <button type="submit"
                            class="min-h-12 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-600 px-6 py-3 font-semibold shadow-btn hover:shadow-[0_15px_50px_rgba(34,211,238,0.55)] hover:scale-105 active:scale-100 transition-all duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0M12 3v9" />
                            </svg>
                            ลอยเลย
                        </button>
                        <button type="button" @click="$store.ui.open=false"
                            class="min-h-12 rounded-xl border border-white/20 px-5 py-3 hover:bg-white/10 transition-colors font-medium">ปิด</button>
                        <span x-show="ok" x-text="ok"
                            class="text-emerald-400 text-sm font-semibold animate-pulse"></span>
                        <span x-show="error" x-text="error" class="text-rose-400 text-sm font-semibold"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal เกี่ยวกับ -->
    <div x-show="$store.ui.aboutOpen" x-cloak class="fixed inset-0 z-50"
        @keydown.escape.window="$store.ui.aboutOpen=false">
        <div class="absolute inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity"
            @click="$store.ui.aboutOpen=false"></div>

        <div class="absolute inset-0 flex items-center justify-center p-4" @click.stop>
            <div
                class="w-full max-w-md modal-enter backdrop-blur-2xl rounded-3xl border border-purple-400/30 bg-gradient-to-br from-slate-900/80 to-purple-900/30 shadow-glass">
                <div class="flex items-start justify-between p-6 border-b border-white/10">
                    <div>
                        <h2
                            class="text-2xl font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-purple-500 bg-clip-text text-transparent">
                            เกี่ยวกับ</h2>
                    </div>
                    <button @click="$store.ui.aboutOpen=false"
                        class="rounded-xl p-2 hover:bg-white/10 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <div class="text-center space-y-3">
                        <div
                            class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-500 to-purple-600 shadow-lg mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-white" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0M12 3v9" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white">ลอยกระทงออนไลน์</h3>
                        <p class="text-sm text-slate-300">ระบบลอยกระทงออนไลน์ เพื่อส่งต่อความปรารถนาดีในวันลอยกระทง</p>
                    </div>

                    <div class="h-px bg-gradient-to-r from-transparent via-purple-400/30 to-transparent"></div>

                    <div class="space-y-4">
                        <div
                            class="flex items-center gap-3 p-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-white" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-slate-400 mb-0.5">นักพัฒนา</div>
                                <div class="font-semibold text-white">ปรัชญาพล จำปาลาด</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 rounded-2xl bg-white/5 border border-white/10">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-white" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"
                                        stroke-width="2" />
                                    <line x1="16" y1="2" x2="16" y2="6"
                                        stroke-width="2" stroke-linecap="round" />
                                    <line x1="8" y1="2" x2="8" y2="6"
                                        stroke-width="2" stroke-linecap="round" />
                                    <line x1="3" y1="10" x2="21" y2="10"
                                        stroke-width="2" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-xs text-slate-400 mb-0.5">เวอร์ชัน</div>
                                <div class="font-semibold text-white">1.0.1</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-4 rounded-2xl bg-white/5 border border-white/10 w-full">
                            <!-- ==== Ping Chart (Recent Only) ==== -->
                            <div id="recent-ping"
                                class="ping-chart-wrapper select-none w-full flex-1 min-w-0 block sm:block">
                                <div class="mb-2 text-sm text-slate-300">Ping ล่าสุด</div>
                                <div class="chart-wrapper relative w-full" style="position:relative;height:200px;">
                                    <canvas id="pingChart" class="w-full block"></canvas>
                                </div>
                                <p id="pingErr" class="text-xs text-rose-400 mt-1"></p>
                            </div>
                        </div>

                    </div>

                    <div class="text-center pt-2">
                        <p class="text-xs text-slate-400">© 2025 สงวนลิขสิทธิ์ PCNONE GROUP </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
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

        // === Krathong effect config ===
        const AMP_PX = 10; // แอมพลิจูดการลอย (px)
        const SPEED = 1.2; // ความเร็วลอย (รอบ/วินาที)
        const STAGGER_MS = 300; // เว้นระยะปล่อยทีละกระทง
        const SHOW_TRAIL = true; // วาดเส้นตามหรือไม่

        // Triangle wave 0..1..0
        const tri = (t) => 2 * Math.abs(t - Math.floor(t + 0.5));
        let baseSeries = [];

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

            // เรียงล่าสุดก่อน เพื่อปล่อยใหม่->เก่า
            series.sort((a, b) => b.x - a.x);
            baseSeries = series.map(p => ({
                x: p.x,
                y: p.y
            }));

            const xmin = series[series.length - 1].x.getTime(); // เก่าสุดซ้าย
            const xmax = series[0].x.getTime(); // ล่าสุดขวา

            const ctx = document.getElementById('pingChart');
            if (!ctx) return;

            if (!pingChart) {
                pingChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        datasets: [{
                            label: 'Ping',
                            data: series, // เส้นฐาน
                            pointRadius: 0,
                            borderWidth: SHOW_TRAIL ? 1 : 0,
                            spanGaps: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        parsing: false,
                        animation: {
                            duration: 600,
                            delay: (c) => (c?.dataIndex ?? 0) * STAGGER_MS, // ปล่อยทีละกระทง
                        },
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
                        },
                        transitions: {
                            active: {
                                animation: {
                                    duration: 0
                                }
                            }
                        }
                    },
                    plugins: [{
                        id: 'floatingKrathong',
                        beforeDraw(chart) {
                            const {
                                ctx
                            } = chart;
                            const yScale = chart.scales.y;
                            const xScale = chart.scales.x;
                            if (!baseSeries.length) return;

                            const now = performance.now();
                            chart._startTime ??= now;
                            const elapsed = now - chart._startTime;

                            // จำนวนที่ปล่อยแล้ว
                            const released = Math.min(
                                baseSeries.length,
                                Math.floor(elapsed / STAGGER_MS) + 1
                            );

                            ctx.save();
                            ctx.lineWidth = 1;

                            for (let i = 0; i < released; i++) {
                                // index 0 = ล่าสุด
                                const p = baseSeries[i];

                                // ฟันปลาแนวดิ่ง
                                const phase = (now / 1000) * SPEED + i * 0.35;
                                const tri01 = tri(phase % 1); // 0..1..0
                                const offsetPx = (tri01 - 0.5) * 2 * AMP_PX;

                                const px = xScale.getPixelForValue(p.x);
                                const pyBase = yScale.getPixelForValue(p.y);
                                const py = pyBase + offsetPx;

                                // โปร่งลงตามลำดับ
                                const alpha = Math.max(0.25, 1 - i / (released + 4));
                                ctx.globalAlpha = alpha;

                                // ตัวกระทง
                                ctx.beginPath();
                                ctx.arc(px, py, 4, 0, Math.PI * 2);
                                ctx.fillStyle = '#ffcc00';
                                ctx.fill();

                                // เปลวเทียน
                                ctx.beginPath();
                                ctx.arc(px, py - 6, 1.5, 0, Math.PI * 2);
                                ctx.fillStyle = '#ffffff';
                                ctx.fill();

                                // เส้นตามน้ำ
                                if (SHOW_TRAIL && i + 1 < released) {
                                    const p2 = baseSeries[i + 1];
                                    const px2 = xScale.getPixelForValue(p2.x);
                                    const py2 = yScale.getPixelForValue(p2.y) +
                                        (tri(((now / 1000) * SPEED + (i + 1) * 0.35) % 1) - 0.5) * 2 *
                                        AMP_PX;
                                    ctx.beginPath();
                                    ctx.moveTo(px, py);
                                    ctx.lineTo(px2, py2);
                                    ctx.strokeStyle = 'rgba(255,204,0,0.25)';
                                    ctx.stroke();
                                }
                            }
                            ctx.restore();
                        }
                    }]
                });

                // อัปเดตต่อเนื่องให้เอฟเฟกต์เคลื่อนไหว
                (function loop() {
                    if (!pingChart) return;
                    pingChart.update('none');
                    requestAnimationFrame(loop);
                })();
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
                    }));
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
    </script>

    </script>
    {{-- หิ่งห้อยกระพริบ --}}
    <script>
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
          fireflyBlink ${rnd(2.2,3.6)}s ease-in-out ${rnd(0,1.5)}s infinite;
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
    </script>

    <!-- Logic ลอยกระทง -->
    <script>
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
            const WATER_TOP = 25; // เริ่มน้ำที่ 60% ของจอในมือถือ
            const WATER_BAND = 28;
            const DUR_INIT_MIN = 22,
                DUR_INIT_MAX = 34; // ชุดแรก
            const DUR_LOOP_MIN = 18,
                DUR_LOOP_MAX = 28; // รอบต่อไป

            const MAX_ITEMS = window.innerWidth < 640 ? 40 : 100; // จำกัดจำนวนบนมือถือ
            const typeImg = t => types?.[t]?.img || Object.values(types || {})[0]?.img || '';

            const makeStyle = (dur, delay, top) => {
                const name = `drift_${Math.random().toString(36).slice(2)}`;
                const sheet = ensureKeyframeSheet();
                sheet.insertRule(
                    `@keyframes ${name}{0%{left:-20%;opacity:0}10%{opacity:1}90%{opacity:1}100%{left:120%;opacity:0}}`,
                    sheet.cssRules.length);
                return `top:${top}%;left:-20%;--floatDur:${rnd(2.8,4.4)}s;--swayDur:${rnd(4.5,6.5)}s;animation:${name} ${dur}s linear ${delay}s forwards,var(--_dummy,0s);`;
            };

            const mkItem = (r, init = false) => {
                const dur = init ? rnd(DUR_INIT_MIN, DUR_INIT_MAX) : rnd(DUR_LOOP_MIN, DUR_LOOP_MAX);
                const delay = init ? rnd(0, 12) : 0;
                const top = rnd(WATER_TOP + 6, WATER_TOP + WATER_BAND + (init ? 0 : 4));
                return {
                    id: r.id,
                    clientId: `${init?'srv':'cli'}_${r.id}_${Math.random().toString(36).slice(2)}`,
                    img: typeImg(r.type),
                    wish: `${r.nickname} : ${r.wish}`,
                    style: makeStyle(dur, delay, top),
                    show: false,
                    paused: false,
                    __life: (dur + delay) * 1000,
                    __deadline: Date.now() + (dur + delay) * 1000
                };
            };

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
                        __schedule(tick, window.innerWidth < 400 ? rnd(6500, 9000) : rnd(4500, 7200));
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
    </script>

    <!-- keyframes ดาวพุ่ง -->
    <style>
        @keyframes shootingStar {
            0% {
                transform: translate(0, 0);
                opacity: 1
            }

            100% {
                transform: translate(-200px, 200px);
                opacity: 0
            }
        }
    </style>

</body>

</html>
