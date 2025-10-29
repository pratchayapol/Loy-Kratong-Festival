@extends('layouts.app')


@section('content')
    <canvas id="fwCanvas" aria-hidden="true"></canvas>
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
        <div class="fixed top-28 left-1/2 -translate-x-1/2 z-40 select-none">
            <div class="px-4 py-2 rounded-2xl border border-white/20 bg-white/10 backdrop-blur-xl text-center">
                <span class="text-slate-200/90">จำนวนกระทงทั้งหมด</span>
                <span class="ml-2 text-2xl font-extrabold text-white">
                    {{ number_format($total) }}
                </span>
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
                    <div class="absolute top-[30%] left-[20%] w-3 h-3 sm:w-4 sm:h-4 rounded-full bg-slate-400/40 blur-sm">
                    </div>
                    <div class="absolute top-[55%] right-[25%] w-4 h-4 sm:w-5 sm:h-5 rounded-full bg-slate-400/30 blur-sm">
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
            <path d="M0,100 C360,140 1080,60 1440,100" stroke="rgba(255,255,255,0.40)" stroke-width="1" fill="none" />
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
            <div id="river" class="absolute inset-0 overflow-hidden" x-data="riverScene(@js($types), @js($recent))" x-init="init()">
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
        <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="$store.ui.open=false">
        </div>

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
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor">
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
                    <button @click="$store.ui.aboutOpen=false" class="rounded-xl p-2 hover:bg-white/10 transition-colors">
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
                                    <line x1="16" y1="2" x2="16" y2="6" stroke-width="2"
                                        stroke-linecap="round" />
                                    <line x1="8" y1="2" x2="8" y2="6" stroke-width="2"
                                        stroke-linecap="round" />
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
        document.addEventListener('DOMContentLoaded', () => {
            // ==== โค้ดพลุเดิมทั้งก้อนของคุณ วางไว้ในนี้ ====
        });
    </script>
@endsection

@push('scripts')
    @extends('script.js')
@endpush
