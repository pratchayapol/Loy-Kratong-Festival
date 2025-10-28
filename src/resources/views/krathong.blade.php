<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ลอยกระทงออนไลน์</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily:{display:['Inter','ui-sans-serif','system-ui']},
          colors:{
            sky1:'#0a1828',
            sky2:'#1a2332',
            river1:'#0d2847',
            river2:'#051424'
          },
          boxShadow:{
            glass:'0 25px 80px rgba(0,0,0,0.45)',
            btn:'0 10px 40px rgba(34,211,238,0.45)',
            moonGlow:'0 0 80px rgba(255,244,200,0.4), 0 0 120px rgba(255,244,200,0.2)'
          },
          keyframes:{
            floatY:{'0%,100%':{transform:'translateY(0)'},'50%':{transform:'translateY(-8px)'}},
            sway:{'0%,100%':{transform:'translateX(0) rotate(0deg)'},'25%':{transform:'translateX(-12px) rotate(-2deg)'},'75%':{transform:'translateX(12px) rotate(2deg)'}},
            twinkle:{'0%,100%':{opacity:'0.2',transform:'scale(0.8)'},'50%':{opacity:'1',transform:'scale(1.3)'}},
            moonGlow:{'0%,100%':{opacity:'0.95',transform:'scale(1)'},'50%':{opacity:'1',transform:'scale(1.05)'}},
            ripple:{'0%':{transform:'scale(0.8)',opacity:'0.8'},'100%':{transform:'scale(2)',opacity:'0'}}
          },
          animation:{
            floatY:'floatY 3.5s ease-in-out infinite',
            sway:'sway 6s ease-in-out infinite',
            twinkle:'twinkle 3s ease-in-out infinite',
            moonGlow:'moonGlow 5s ease-in-out infinite',
            ripple:'ripple 4s ease-out infinite'
          }
        }
      }
    }
  </script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style id="dyn-keyframes"></style>
  
  <style>
    [x-cloak]{display:none !important}
    
    .krathong-item {
      animation: floatY 3.5s ease-in-out infinite, sway 6s ease-in-out infinite;
    }
    
    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
      }
      to {
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }
    
    .modal-enter {
      animation: slideUp 0.3s ease-out;
    }

    /* คลื่นน้ำที่เคลื่อนไหว */
    @keyframes wave {
      0%, 100% { transform: translateX(0) translateY(0); }
      25% { transform: translateX(-5%) translateY(-2%); }
      50% { transform: translateX(-10%) translateY(0); }
      75% { transform: translateX(-5%) translateY(2%); }
    }

    .water-wave {
      animation: wave 8s ease-in-out infinite;
    }

    /* เมฆบางๆ */
    @keyframes cloud-drift {
      from { transform: translateX(-100%); }
      to { transform: translateX(100vw); }
    }
  </style>
</head>
<body x-data="{}" class="min-h-screen bg-gradient-to-b from-sky1 via-sky2 to-river1 text-slate-100 font-display overflow-hidden">

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.store('ui', { open:false, aboutOpen:false });
    });
  </script>

  <!-- ปุ่มมุมซ้ายบน -->
  <button @click="$store.ui.open=true"
          class="fixed left-4 top-4 z-40 inline-flex items-center gap-2 rounded-2xl
                 bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-600 px-5 py-3 font-semibold
                 shadow-btn hover:shadow-[0_15px_50px_rgba(34,211,238,0.55)] hover:scale-105
                 active:scale-100 transition-all duration-300
                 focus:outline-none focus:ring-2 focus:ring-cyan-400/50">
    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0M12 3v9"/>
    </svg>
    <span class="hidden sm:inline">ลอยกระทงด้วย</span>
    <span class="sm:hidden">ลอย</span>
  </button>

  <!-- ปุ่มเกี่ยวกับ -->
  <button @click="$store.ui.aboutOpen=true"
          class="fixed right-4 bottom-4 z-40 w-12 h-12 rounded-full
                 bg-gradient-to-br from-purple-500 to-pink-600 
                 shadow-lg hover:shadow-purple-500/50 hover:scale-110
                 active:scale-100 transition-all duration-300
                 focus:outline-none focus:ring-2 focus:ring-purple-400/50
                 flex items-center justify-center group"
          title="เกี่ยวกับ">
    <svg xmlns="http://www.w3.org/2000/svg" class="size-6 group-hover:rotate-12 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <circle cx="12" cy="12" r="10" stroke-width="2"/>
      <path d="M12 16v-4M12 8h.01" stroke-width="2" stroke-linecap="round"/>
    </svg>
  </button>

  <main class="relative min-h-screen">
    <!-- พื้นหลังท้องฟ้ากลางคืน -->
    <div class="absolute inset-0 bg-gradient-to-b from-sky1 via-sky2 to-river1"></div>

    <!-- พระจันทร์ -->
    <div class="absolute top-[12%] right-[15%] w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-full 
                bg-gradient-to-br from-yellow-50 via-yellow-100 to-amber-200
                shadow-moonGlow animate-moonGlow pointer-events-none z-20">
      <!-- รอยบนดวงจันทร์ -->
      <div class="absolute top-[20%] left-[25%] w-3 h-3 rounded-full bg-yellow-200/40"></div>
      <div class="absolute top-[45%] right-[30%] w-4 h-4 rounded-full bg-yellow-200/30"></div>
      <div class="absolute bottom-[30%] left-[40%] w-2 h-2 rounded-full bg-yellow-200/35"></div>
    </div>

    <!-- ดาวระยิบระยับ (Alpine.js) -->
    <div class="pointer-events-none absolute inset-0 z-10" x-data="{stars:[]}" x-init="
      for(let i=0;i<150;i++){
        stars.push({
          left:Math.random()*100,
          top:Math.random()*70,
          delay:Math.random()*5,
          duration:2+Math.random()*4,
          size:Math.random()>0.7?(Math.random()>0.5?3:2):1
        })
      }
    ">
      <template x-for="(s,i) in stars" :key="i">
        <div class="absolute rounded-full bg-white animate-twinkle" 
             :style="`left:${s.left}%;top:${s.top}%;width:${s.size}px;height:${s.size}px;animation-delay:${s.delay}s;animation-duration:${s.duration}s;box-shadow:0 0 ${s.size*3}px rgba(255,255,255,0.9)`">
        </div>
      </template>
    </div>

    <!-- เมฆบางๆ -->
    <div class="absolute inset-0 opacity-20 pointer-events-none z-5">
      <div class="absolute top-[15%] left-0 w-64 h-16 bg-gradient-to-r from-transparent via-white/10 to-transparent blur-2xl rounded-full"
           style="animation: cloud-drift 60s linear infinite;"></div>
      <div class="absolute top-[25%] left-0 w-48 h-12 bg-gradient-to-r from-transparent via-white/8 to-transparent blur-2xl rounded-full"
           style="animation: cloud-drift 80s linear infinite; animation-delay: -20s;"></div>
    </div>

    <!-- คลองน้ำ (ครึ่งล่าง) -->
    <div class="absolute inset-x-0 bottom-0 h-[45%] bg-gradient-to-b from-river1 to-river2 overflow-hidden">
      <!-- เงาสะท้อนดวงจันทร์บนน้ำ -->
      <div class="absolute top-[15%] right-[15%] w-16 h-32 sm:w-20 sm:h-40 
                  bg-gradient-to-b from-yellow-200/30 via-yellow-200/20 to-transparent 
                  blur-2xl opacity-60 water-wave rounded-full"></div>
      
      <!-- คลื่นน้ำ -->
      <div class="absolute inset-0 opacity-20">
        <div class="absolute top-[20%] left-0 w-full h-1 bg-gradient-to-r from-transparent via-cyan-300/40 to-transparent water-wave"></div>
        <div class="absolute top-[40%] left-0 w-full h-1 bg-gradient-to-r from-transparent via-cyan-300/30 to-transparent water-wave" style="animation-delay: -2s; animation-duration: 10s;"></div>
        <div class="absolute top-[60%] left-0 w-full h-1 bg-gradient-to-r from-transparent via-cyan-300/25 to-transparent water-wave" style="animation-delay: -4s; animation-duration: 12s;"></div>
      </div>

      <!-- ระลอกคลื่นวงกลม -->
      <div class="absolute top-[30%] left-[20%] w-32 h-32 rounded-full border border-cyan-400/20 animate-ripple"></div>
      <div class="absolute top-[50%] right-[30%] w-24 h-24 rounded-full border border-cyan-400/15 animate-ripple" style="animation-delay: -2s;"></div>
      
      <!-- Gradient overlay ด้านล่าง -->
      <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-slate-950/60 to-transparent"></div>
    </div>

    <!-- พื้นที่แสดงกระทง -->
    <div class="absolute inset-x-0 bottom-0 h-[45%] overflow-hidden z-15" x-data="riverScene(mockTypes, mockRecent)">
      <template x-for="k in items" :key="k.clientId">
        <div class="absolute flex flex-col items-center krathong-item will-change-transform" :style="k.style">
          <!-- คำอธิษฐาน -->
          <div class="px-3 py-2 rounded-2xl text-xs sm:text-sm max-w-[220px] sm:max-w-[280px] 
                      text-cyan-50 bg-slate-900/80 backdrop-blur-xl border border-cyan-400/30 
                      shadow-lg shadow-cyan-500/20 whitespace-nowrap overflow-hidden text-ellipsis" 
               x-text="k.wish"></div>
          <!-- กระทง -->
          <div class="relative mt-2">
            <img :src="k.img" alt="krathong" class="w-16 h-16 sm:w-20 sm:h-20 drop-shadow-[0_15px_30px_rgba(0,0,0,0.7)]">
            <!-- แสงเทียน -->
            <div class="absolute top-1 left-1/2 -translate-x-1/2 w-8 h-8 bg-amber-300/40 rounded-full blur-lg animate-pulse"></div>
          </div>
        </div>
      </template>
    </div>
  </main>

  <!-- Modal ฟอร์ม -->
  <div x-show="$store.ui.open" x-cloak class="fixed inset-0 z-50" @keydown.escape.window="$store.ui.open=false">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="$store.ui.open=false"></div>

    <div class="absolute inset-0 flex items-center justify-center p-4 sm:p-6 overflow-y-auto" @click.stop>
      <div class="w-full max-w-xl modal-enter backdrop-blur-2xl rounded-3xl border border-white/20 bg-slate-900/50 shadow-glass my-8"
           x-data="krathongForm()">
        <div class="flex items-start justify-between p-5 sm:p-6 border-b border-white/10">
          <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-cyan-400 via-blue-400 to-purple-400 bg-clip-text text-transparent">
              ลอยกระทง
            </h2>
            <p class="text-sm text-slate-300 mt-1.5">เลือกแบบ กรอกข้อมูล แล้วปล่อยลอยเลย</p>
          </div>
          <button @click="$store.ui.open=false" 
                  class="rounded-xl p-2 hover:bg-white/10 transition-colors 
                         focus:outline-none focus:ring-2 focus:ring-cyan-400/40">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="submit" class="p-5 sm:p-6 space-y-5">
          <div>
            <label class="text-sm font-semibold text-slate-200">เลือกแบบกระทง</label>
            <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3">
              <template x-for="type in types" :key="type.key">
                <label
                  @click="form.type=type.key"
                  class="group relative cursor-pointer rounded-2xl border border-white/10 bg-white/5 
                         hover:bg-white/10 hover:border-cyan-400/40 hover:scale-105
                         transition-all duration-300 p-3 sm:p-4 flex flex-col items-center gap-2"
                  :class="form.type===type.key ? 'ring-2 ring-cyan-400/80 bg-cyan-500/10' : 'ring-0'">
                  <input class="sr-only" type="radio" name="type" x-model="form.type" :value="type.key">
                  <img :src="type.img" :alt="type.label" class="w-10 h-10 sm:w-12 sm:h-12 drop-shadow-lg" loading="lazy">
                  <span class="text-xs sm:text-sm font-medium" x-text="type.label"></span>
                  <span class="absolute -top-2 -right-2 transition-all duration-200" 
                        :class="form.type===type.key ? 'scale-100 opacity-100' : 'scale-0 opacity-0'">
                    <span class="w-6 h-6 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 
                                 text-white text-xs shadow-lg grid place-items-center font-bold">✓</span>
                  </span>
                </label>
              </template>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-200">ชื่อเล่น</label>
              <input x-model="form.nickname" type="text" maxlength="50" required
                     class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 
                            outline-none focus:border-cyan-400/60 focus:ring-2 focus:ring-cyan-400/30 
                            transition-all placeholder:text-slate-500"
                     placeholder="เช่น โฟกัส">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-semibold text-slate-200">อายุ</label>
              <input x-model.number="form.age" type="number" min="1" max="120" required
                     class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 
                            outline-none focus:border-cyan-400/60 focus:ring-2 focus:ring-cyan-400/30 
                            transition-all">
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-semibold text-slate-200">คำอธิษฐาน</label>
            <textarea x-model="form.wish" maxlength="200" required rows="3"
                      class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 
                             outline-none focus:border-cyan-400/60 focus:ring-2 focus:ring-cyan-400/30 
                             transition-all placeholder:text-slate-500 resize-none"
                      placeholder="ขอให้..."></textarea>
            <div class="text-xs text-slate-400 flex justify-between">
              <span>ไม่เกิน 200 ตัวอักษร</span>
              <span x-text="`${form.wish?.length||0}/200`"></span>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl 
                           bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-600 
                           px-6 py-3 font-semibold shadow-btn 
                           hover:shadow-[0_15px_50px_rgba(34,211,238,0.55)] hover:scale-105
                           active:scale-100 transition-all duration-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0M12 3v9"/>
              </svg>
              ลอยเลย
            </button>
            <button type="button" @click="$store.ui.open=false"
                    class="rounded-xl border border-white/20 px-5 py-3 
                           hover:bg-white/10 transition-colors font-medium">
              ปิด
            </button>
            <span x-show="ok" x-text="ok" class="text-emerald-400 text-sm font-semibold animate-pulse"></span>
            <span x-show="error" x-text="error" class="text-rose-400 text-sm font-semibold"></span>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal เกี่ยวกับ -->
  <div x-show="$store.ui.aboutOpen" x-cloak class="fixed inset-0 z-50" @keydown.escape.window="$store.ui.aboutOpen=false">
    <div class="absolute inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity" @click="$store.ui.aboutOpen=false"></div>

    <div class="absolute inset-0 flex items-center justify-center p-4" @click.stop>
      <div class="w-full max-w-md modal-enter backdrop-blur-2xl rounded-3xl border border-purple-400/30 bg-gradient-to-br from-slate-900/80 to-purple-900/30 shadow-glass">
        <div class="flex items-start justify-between p-6 border-b border-white/10">
          <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-purple-500 bg-clip-text text-transparent">
              เกี่ยวกับ
            </h2>
          </div>
          <button @click="$store.ui.aboutOpen=false" 
                  class="rounded-xl p-2 hover:bg-white/10 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="p-6 space-y-6">
          <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-500 to-purple-600 shadow-lg mb-2">
              <svg xmlns="http://www.w3.org/2000/svg" class="size-8 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0M12 3v9"/>
              </svg>
            </div>
            <h3 class="text-xl font-bold text-white">ลอยกระทงออนไลน์</h3>
            <p class="text-sm text-slate-300">ระบบลอยกระทงออนไลน์ เพื่อส่งต่อความปรารถนาดีในวันลอยกระทง</p>
          </div>

          <div class="h-px bg-gradient-to-r from-transparent via-purple-400/30 to-transparent"></div>

          <div class="space-y-4">
            <div class="flex items-center gap-3 p-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-colors">
              <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
              <div class="flex-1">
                <div class="text-xs text-slate-400 mb-0.5">นักพัฒนา</div>
                <div class="font-semibold text-white">นายปรัชญาพล จำปาลาด</div>
              </div>
            </div>

            <div class="flex items-center gap-3 p-4 rounded-2xl bg-white/5 border border-white/10">
              <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke-width="2"/>
                  <line x1="16" y1="2" x2="16" y2="6" stroke-width="2" stroke-linecap="round"/>
                  <line x1="8" y1="2" x2="8" y2="6" stroke-width="2" stroke-linecap="round"/>
                  <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                </svg>
              </div>
              <div class="flex-1">
                <div class="text-xs text-slate-400 mb-0.5">เวอร์ชัน</div>
                <div class="font-semibold text-white">1.0.0</div>
              </div>
            </div>
          </div>

          <div class="text-center pt-2">
            <p class="text-xs text-slate-400">© 2024 สงวนลิขสิทธิ์</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
  const rnd=(min,max)=>Math.random()*(max-min)+min;

  function riverScene(types, recent){
    const DUR_INIT_MIN=12, DUR_INIT_MAX=20;
    const DUR_LOOP_MIN=10, DUR_LOOP_MAX=16;
    const DELAY_MAX=10;

    // เรียงจากใหม่ไปเก่า แล้วสุ่ม 24 รายการ
    const sorted = [...(recent||[])].sort((a,b)=>b.id-a.id);
    const shuffled = sorted.slice(0,24).sort(()=>Math.random()-0.5);

    const typeImg=t=>types?.[t]?.img || Object.values(types||{})[0]?.img || '';
    const makeStyle=(dur,delay,top)=>{
      const name=`drift_${Math.random().toString(36).slice(2)}`;
      const sheet=document.getElementById('dyn-keyframes').sheet;
      sheet.insertRule(`@keyframes ${name}{0%{left:-15%;opacity:0}3%{opacity:1}97%{opacity:1}100%{left:115%;opacity:0}}`,sheet.cssRules.length);
      return `top:${top}%;animation:${name} ${dur}s linear ${delay}s forwards`;
    };
    const toItem=r=>({
      id:r.id,
      clientId:`srv_${r.id}_${Math.random().toString(36).slice(2)}`,
      img:typeImg(r.type),
      wish:`${r.nickname} (${r.age}) : ${r.wish}`,
      style:makeStyle(rnd(DUR_INIT_MIN,DUR_INIT_MAX), rnd(0,DELAY_MAX), rnd(8,88))
    });

    const initial=shuffled.map(toItem);

    return{
      items: initial,
      recentPool: sorted,
      init(){
        const tick=()=>{
          const pool=this.recentPool;
          if(pool.length){
            const visibleIds=new Set(this.items.map(i=>i.id));
            const candidates=pool.filter(x=>!visibleIds.has(x.id));
            if(candidates.length){
              const r=candidates[Math.floor(Math.random()*candidates.length)];
              this.spawnFromRecord(r);
            }
          }
          setTimeout(tick, rnd(3500,6000));
        };
        setTimeout(tick, 1000);
      },
      spawnFromRecord(r){
        const k={
          id:r.id,
          clientId:`cli_${r.id}_${Math.random().toString(36).slice(2)}`,
          img:typeImg(r.type),
          wish:`${r.nickname} (${r.age}) : ${r.wish}`,
          style:makeStyle(rnd(DUR_LOOP_MIN,DUR_LOOP_MAX), 0, rnd(10,90))
        };
        this.items.push(k);
        if(this.items.length>80) this.items.splice(0,this.items.length-80);
      },
      spawnNew(p){
        const r={ id:Date.now(), type:p.type, nickname:p.nickname, age:p.age, wish:p.wish };
        this.recentPool.unshift(r);
        this.spawnFromRecord(r);
      }
    }
  }

  function krathongForm(){
    const types = {
      banana: {label:'กระทงใบตอง', img:'https://img.icons8.com/color/96/lotus.png'},
      bread: {label:'กระทงขนมปัง', img:'https://img.icons8.com/color/96/bread.png'},
      flower: {label:'กระทงดอกไม้', img:'https://img.icons8.com/color/96/flower-bouquet.png'},
      ice: {label:'กระทงน้ำแข็ง', img:'https://img.icons8.com/color/96/snowflake.png'}
    };

    return {
      types: Object.entries(types).map(([key,val])=>({key,...val})),
      form:{ type:'banana', nickname:'', age:'', wish:'' },
      error:'', ok:'',
      async submit(){
        this.error=''; this.ok='';
        try{
          // จำลองการบันทึก
          await new Promise(resolve=>setTimeout(resolve,500));
          this.ok='ลอยแล้ว ✨';
          const scene=document.querySelector('main [x-data]');
          const api=scene?._x_dataStack?.[0];
          api?.spawnNew?.(this.form);
          this.form.wish='';
          setTimeout(()=>{
            Alpine.store('ui').open=false;
            this.ok='';
          }, 1500);
        }catch(e){ this.error=e.message; }
      }
    }
  }

  // ข้อมูลจำลอง
  const mockTypes = {
    banana: {label:'กระทงใบตอง', img:'https://img.icons8.com/color/96/lotus.png'},
    bread: {label:'กระทงขนมปัง', img:'https://img.icons8.com/color/96/bread.png'},
    flower: {label:'กระทงดอกไม้', img:'https://img.icons8.com/color/96/flower-bouquet.png'},
    ice: {label:'กระทงน้ำแข็ง', img:'https://img.icons8.com/color/96/snowflake.png'}
  };

  const mockRecent = [
    {id:1,type:'banana',nickname:'สมชาย',age:25,wish:'ขอให้โชคดีตลอดปี'},
    {id:2,type:'flower',nickname:'สมหญิง',age:22,wish:'ขอให้ครอบครัวมีความสุข'},
    {id:3,type:'bread',nickname:'นิด',age:30,wish:'ขอให้งานราบรื่น'},
    {id:4,type:'ice',nickname:'แอน',age:28,wish:'ขอให้สุขภาพแข็งแรง'},
    {id:5,type:'banana',nickname:'บอล',age:35,wish:'ขอให้ธุรกิจเจริญรุ่งเรือง'}
  ]