@extends('layouts.app')


@section('content')
    <!-- ป้ายเชิญด้านบนตรงกลาง: ซ่อนบนมือถือ -->
    <div class="hidden sm:block sm:fixed sm:top-4 sm:left-1/2 sm:-translate-x-1/2 sm:z-40 sm:select-none">
        <!-- กรอบไล่สีรอบนอก -->
        <div
            class="relative p-[1px] rounded-3xl bg-gradient-to-r from-cyan-400/40 via-blue-400/40 to-purple-400/40 shadow-[0_20px_50px_rgba(0,0,0,0.45)]">
            <!-- กล่องด้านในแบบแก้ว -->
            <div
                class="px-6 sm:px-8 py-4 sm:py-5 rounded-[calc(theme(borderRadius.3xl)-1px)] border border-white/15 bg-white/10 backdrop-blur-xl text-center">

                <!-- หัวเรื่อง -->
                <div
                    class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-wide bg-gradient-to-r from-cyan-300 via-blue-300 to-purple-300 bg-clip-text text-transparent">
                    ลอยกระทงออนไลน์
                </div>

                <!-- คำโปรย -->
                <div class="mt-1 text-base sm:text-lg md:text-xl text-slate-200/90">
                    ตั้งจิตอธิษฐาน แล้วปล่อยความกังวลให้ลอยไปกับสายน้ำ
                </div>

                <!-- แถวสถิติ -->
                <div class="mt-3 sm:mt-4 flex items-center justify-center gap-3 sm:gap-4">

                    <!-- ป้ายจำนวนกระทงทั้งหมด -->
                    <div
                        class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-slate-900/40 px-3.5 py-2 sm:py-2.5 backdrop-blur-md shadow-[0_8px_24px_rgba(0,0,0,0.35)]">

                        <!-- ไอคอนหยดน้ำ -->
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 opacity-90" viewBox="0 0 24 24" fill="currentColor"
                            aria-hidden="true">
                            <path
                                d="M12 2.5c4.2 5.1 6.3 8.6 6.3 11.3 0 3.5-2.8 6.3-6.3 6.3s-6.3-2.8-6.3-6.3C5.7 11.1 7.8 7.6 12 2.5z" />
                        </svg>

                        <span class="text-slate-200/90">จำนวนกระทงทั้งหมด</span>

                        <span class="mx-1 h-1 w-1 rounded-full bg-white/30"></span>

                        <!-- ครอบเลขให้เด่น -->
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-0 rounded-xl bg-cyan-400/25 blur-xl"></div>
                            <span id="totalCount"
                                class="relative text-3xl sm:text-4xl md:text-5xl font-black
                                   bg-gradient-to-r from-cyan-200 via-emerald-200 to-white
                                   bg-clip-text text-transparent
                                   drop-shadow-[0_4px_20px_rgba(0,0,0,0.35)]
                                   tracking-tight font-mono tabular-nums
                                   px-2 py-0.5 rounded-xl"
                                aria-live="polite">
                                {{ number_format($total ?? 0) }}
                            </span>
                        </div>
                    </div>

                    <!-- แถบสถานะอัปเดตล่าสุด -->
                    <div
                        class="hidden lg:inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1.5 text-sm text-slate-200/80">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="absolute inline-flex h-full w-full rounded-full bg-emerald-300/60 animate-ping"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-emerald-300"></span>
                        </span>
                        <span>กำลังอัปเดตอัตโนมัติ</span>
                    </div>

                </div>
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

            <!-- WATER Section - Premium Edition -->
            <div class="absolute left-0 right-0 top-[60%] sm:top-[58%] bottom-0 overflow-hidden">
                <!-- พื้นหลังน้ำ - ไล่สีสวยหรู -->
                <div class="absolute inset-0 bg-gradient-to-b from-[#0a2540] via-[#051a30] to-[#020c1a]"></div>

                <!-- เอฟเฟกต์แสงใต้น้ำ -->
                <div
                    class="absolute inset-0 bg-[radial-gradient(ellipse_at_50%_30%,rgba(34,211,238,0.08),transparent_60%)] mix-blend-screen">
                </div>
                <div
                    class="absolute inset-0 bg-[radial-gradient(ellipse_at_80%_60%,rgba(168,85,247,0.05),transparent_50%)] mix-blend-soft-light">
                </div>

                <!-- คลื่นพื้นผิว - หลายชั้นซับซ้อน -->
                <div
                    class="absolute left-0 w-[200%] h-20 top-[5%] opacity-15 blur-2xl bg-[radial-gradient(ellipse_at_center,_rgba(34,211,238,.7)_0%,_transparent_65%)] animate-[waves_28s_ease-in-out_infinite]">
                </div>
                <div
                    class="absolute left-[-20%] w-[200%] h-24 top-[25%] opacity-12 blur-2xl bg-[radial-gradient(ellipse_at_center,_rgba(255,255,255,.6)_0%,_transparent_70%)] animate-[waves_35s_ease-in-out_infinite]">
                </div>
                <div
                    class="absolute left-0 w-[180%] h-16 top-[50%] opacity-8 blur-xl bg-[radial-gradient(ellipse_at_center,_rgba(168,85,247,.4)_0%,_transparent_75%)] animate-[waves_42s_ease-in-out_infinite]">
                </div>

                <!-- ประกายแสงระยิบระยับบนผิวน้ำ -->
                <div class="absolute inset-0 opacity-20 mix-blend-screen pointer-events-none"
                    style="background-image: radial-gradient(circle at 20% 15%, rgba(255,255,255,0.3) 0%, transparent 1%), radial-gradient(circle at 60% 25%, rgba(255,255,255,0.2) 0%, transparent 1%), radial-gradient(circle at 85% 35%, rgba(255,255,255,0.25) 0%, transparent 1%), radial-gradient(circle at 30% 45%, rgba(255,255,255,0.15) 0%, transparent 1%); animation: sparkle 4s ease-in-out infinite;">
                </div>

                <!-- เงาสะท้อนพระจันทร์ - สวยงาม -->
                <div
                    class="absolute top-[2%] right-[6%] w-40 h-56 opacity-10 blur-[60px] bg-gradient-to-b from-yellow-200/40 via-yellow-100/20 to-transparent pointer-events-none animate-[moonGlow_8s_ease-in-out_infinite]">
                </div>

                <!-- คลื่นริมฝั่ง (ถ้ามี) -->
                <div class="absolute left-0 right-0 top-0 h-8 opacity-25">
                    <div class="absolute inset-0 bg-gradient-to-b from-white/20 via-white/5 to-transparent blur-sm"></div>
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

                    <!-- เงามืดขอบล่าง - Gradient สวยงาม -->
                    <div
                        class="pointer-events-none absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-slate-950/90 via-slate-900/50 to-transparent">
                    </div>

                    <!-- เอฟเฟกต์ความลึก -->
                    <div
                        class="pointer-events-none absolute inset-x-0 bottom-0 h-24 bg-[radial-gradient(ellipse_at_bottom,_rgba(6,182,212,0.03)_0%,_transparent_70%)]">
                    </div>
                </div>

                <!-- Fireflies layer - ปรับปรุง -->
                <div class="absolute inset-0 pointer-events-none opacity-80" x-data="fireflies()"
                    x-init="init()">
                    <template x-for="f in flies" :key="f.id">
                        <div class="absolute rounded-full bg-yellow-200 shadow-[0_0_10px_rgba(250,204,21,0.6)]"
                            :style="f.style" aria-hidden="true"></div>
                    </template>
                </div>
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
