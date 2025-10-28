<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ลอยกระทงออนไลน์</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily:{display:['Inter','ui-sans-serif','system-ui']},
          colors:{river1:'#0e3a5f',river2:'#0b2e4a',glass:'rgba(255,255,255,0.08)'},
          boxShadow:{glass:'0 25px 80px rgba(0,0,0,0.45)',btn:'0 10px 40px rgba(34,211,238,0.45)'},
          keyframes:{
            floatY:{'0%,100%':{transform:'translateY(0)'},'50%':{transform:'translateY(-6px)'}},
            waves:{'0%':{transform:'translateX(0)'},'100%':{transform:'translateX(-50%)'}},
            sway:{'0%,100%':{transform:'translateX(0) rotate(0deg)'},'25%':{transform:'translateX(-10px) rotate(-2deg)'},'75%':{transform:'translateX(10px) rotate(2deg)'}},
            twinkle:{'0%,100%':{opacity:'0.3',transform:'scale(1)'},'50%':{opacity:'1',transform:'scale(1.2)'}},
            moonGlow:{'0%,100%':{boxShadow:'0 0 50px rgba(255,244,200,0.5), 0 0 90px rgba(255,244,200,0.25)'},'50%':{boxShadow:'0 0 70px rgba(255,244,200,0.7), 0 0 130px rgba(255,244,200,0.35)'}}
          },
          animation:{
            floatY:'floatY 3.2s ease-in-out infinite',
            waves:'waves 18s linear infinite',
            sway:'sway 5s ease-in-out infinite',
            twinkle:'twinkle 3s ease-in-out infinite',
            moonGlow:'moonGlow 4s ease-in-out infinite'
          }
        }
      }
    }
  </script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>const rnd=(min,max)=>Math.random()*(max-min)+min;</script>
  <style id="dyn-keyframes"></style>
  <style>
    [x-cloak]{display:none !important}
    
    .star {
      position: absolute;
      width: 2px;
      height: 2px;
      background: white;
      border-radius: 50%;
      box-shadow: 0 0 3px rgba(255,255,255,0.8);
    }
    
    .krathong-item {
      animation: floatY 3.2s ease-in-out infinite, sway 5s ease-in-out infinite;
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
  </style>
</head>
<body x-data="{}" class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100 font-display overflow-hidden">

  <!-- Alpine store -->
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

  <!-- ฉากสายน้ำ -->
  <main class="relative min-h-screen">
    <div class="absolute inset-0 bg-gradient-to-b from-river1 to-river2"></div>

    <!-- พระจันทร์ -->
    <div class="absolute top-[8%] right-[10%] w-16 h-16 sm:w-20 sm:h-20 rounded-full 
                bg-gradient-to-br from-yellow-50 via-yellow-100 to-yellow-200
                animate-moonGlow pointer-events-none z-10 opacity-90"></div>

    <!-- ดาวระยิบระยับ (pattern แบบเดิม) -->
    <div class="pointer-events-none absolute inset-0 opacity-40"
         style="background-image: radial-gradient(circle at 15% 8%, rgba(255,255,255,.25) 0 1.5px, transparent 2px),
                                radial-gradient(circle at 35% 15%, rgba(255,255,255,.18) 0 1px, transparent 2px),
                                radial-gradient(circle at 75% 12%, rgba(255,255,255,.22) 0 1.5px, transparent 2px),
                                radial-gradient(circle at 85% 25%, rgba(255,255,255,.15) 0 1px, transparent 2px),
                                radial-gradient(circle at 25% 30%, rgba(255,255,255,.2) 0 1px, transparent 2px),
                                radial-gradient(circle at 60% 8%, rgba(255,255,255,.17) 0 1px, transparent 2px),
                                radial-gradient(circle at 90% 40%, rgba(255,255,255,.19) 0 1px, transparent 2px),
                                radial-gradient(circle at 45% 35%, rgba(255,255,255,.16) 0 1px, transparent 2px),
                                radial-gradient(circle at 10% 45%, rgba(255,255,255,.21) 0 1.5px, transparent 2px),
                                radial-gradient(circle at 55% 50%, rgba(255,255,255,.14) 0 1px, transparent 2px),
                                radial-gradient(circle at 70% 55%, rgba(255,255,255,.18) 0 1px, transparent 2px),
                                radial-gradient(circle at 20% 60%, rgba(255,255,255,.2) 0 1px, transparent 2px),
                                radial-gradient(circle at 95% 65%, rgba(255,255,255,.16) 0 1px, transparent 2px),
                                radial-gradient(circle at 40% 70%, rgba(255,255,255,.19) 0 1.5px, transparent 2px);
                background-size: 100% 100%;"></div>

    <!-- คลื่นน้ำ + กระทง (สุ่มแสดงอัตโนมัติ) -->
    <div class="absolute inset-0 overflow-hidden" x-data="riverScene(@js($types), @js($recent))">
      <!-- คลื่นน้ำ -->
      <div class="absolute left-0 w-[200%] h-28 top-[12%] opacity-30 blur-2xl bg-[radial-gradient(ellipse_at_center,_white_0%,_transparent_60%)] animate-waves"></div>
      <div class="absolute left-0 w-[200%] h-28 top-[44%] opacity-25 blur-2xl bg-[radial-gradient(ellipse_at_center,_white_0%,_transparent_60%)] animate-[waves_22s_linear_infinite]"></div>
      <div class="absolute left-0 w-[200%] h-28 top-[72%] opacity-20 blur-2xl bg-[radial-gradient(ellipse_at_center,_white_0%,_transparent_60%)] animate-[waves_26s_linear_infinite]"></div>

      <template x-for="k in items" :key="k.clientId">
        <div class="absolute flex flex-col items-center krathong-item will-change-transform" :style="k.style">
          <div class="px-3 py-2 rounded-2xl text-xs sm:text-sm max-w-[220px] sm:max-w-[280px] 
                      text-cyan-50 bg-slate-900/80 backdrop-blur-xl border border-cyan-400/30 
                      shadow-lg shadow-cyan-500/20 whitespace-nowrap overflow-hidden text-ellipsis" 
               x-text="k.wish"></div>
          <div class="relative mt-2">
            <img :src="k.img" alt="krathong" class="w-16 h-16 sm:w-20 sm:h-20 drop-shadow-[0_15px_25px_rgba(0,0,0,0.6)]">
            <!-- แสงเรืองรอบกระทง -->
            <div class="absolute inset-0 -z-10 blur-xl opacity-50 bg-gradient-radial from-amber-300/50 to-transparent rounded-full"></div>
          </div>
        </div>
      </template>

      <div class="pointer-events-none absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-slate-950/80 via-slate-950/40 to-transparent"></div>
    </div>
  </main>

  <!-- Modal ฟอร์ม -->
  <div x-show="$store.ui.open" x-cloak class="fixed inset-0 z-50" @keydown.escape.window="$store.ui.open=false">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="$store.ui.open=false"></div>

    <div class="absolute inset-0 grid place-items-center p-4 sm:p-6" @click.stop>
      <div class="w-full max-w-xl modal-enter backdrop-blur-2xl rounded-3xl border border-white/20 bg-slate-900/50 shadow-glass"
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
              @foreach($types as $key=>$t)
              <label
                @click="form.type='{{ $key }}'"
                class="group relative cursor-pointer rounded-2xl border border-white/10 bg-white/5 
                       hover:bg-white/10 hover:border-cyan-400/40 hover:scale-105
                       transition-all duration-300 p-3 sm:p-4 flex flex-col items-center gap-2"
                :class="form.type==='{{ $key }}' ? 'ring-2 ring-cyan-400/80 bg-cyan-500/10' : 'ring-0'">
                <input class="sr-only" type="radio" name="type" x-model="form.type" value="{{ $key }}">
                <img src="{{ $t['img'] }}" alt="{{ $t['label'] }}" class="w-10 h-10 sm:w-12 sm:h-12 drop-shadow-lg" loading="lazy">
                <span class="text-xs sm:text-sm font-medium">{{ $t['label'] }}</span>
                <span class="absolute -top-2 -right-2 transition-all duration-200" 
                      :class="form.type==='{{ $key }}' ? 'scale-100 opacity-100' : 'scale-0 opacity-0'">
                  <span class="w-6 h-6 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 
                               text-white text-xs shadow-lg grid place-items-center font-bold">✓</span>
                </span>
              </label>
              @endforeach
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

  <script>
  const readCookie = n => decodeURIComponent((document.cookie.split('; ').find(x=>x.startsWith(n+'='))||'').split('=')[1]||'');

  function riverScene(types, recent){
    // เร่งความเร็ว: ลดช่วงเวลาเคลื่อน
    const DUR_INIT_MIN=12, DUR_INIT_MAX=20;
    const DUR_LOOP_MIN=10, DUR_LOOP_MAX=16;
    const DELAY_MAX=10;

    // สุ่มชุดเริ่มต้นจากฐานข้อมูล
    const shuffled=[...(recent||[])].sort(()=>Math.random()-0.5).slice(0,24);

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
      recentPool: recent||[],
      init(){
        // ปล่อยกระทงจากฐานข้อมูลต่อเนื่อง โดย "ไม่ซ้ำกับที่กำลังแสดงอยู่"
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
      // ใช้หลังบันทึกใหม่สำเร็จ
      spawnNew(p){
        const r={ id:Date.now(), type:p.type, nickname:p.nickname, age:p.age, wish:p.wish };
        this.recentPool.push(r);
        this.spawnFromRecord(r);
      }
    }
  }

  function krathongForm(){
    return {
      form:{ type:'banana', nickname:'', age:'', wish:'' },
      error:'', ok:'',
      async submit(){
        this.error=''; this.ok='';
        try{
          const meta=document.querySelector('meta[name="csrf-token"]').content;
          const xsrf=readCookie('XSRF-TOKEN');
          const res=await fetch('/krathongs',{
            method:'POST',
            headers:{
              'Content-Type':'application/json','Accept':'application/json',
              'X-CSRF-TOKEN':meta,'X-XSRF-TOKEN':xsrf,'X-Requested-With':'XMLHttpRequest'
            },
            credentials:'same-origin',
            body:JSON.stringify(this.form)
          });
          if(!res.ok){
            let msg=`HTTP ${res.status}`; try{const j=await res.json(); msg=j.message||msg;}catch(_){}
            throw new Error(msg);
          }
          const data=await res.json();
          this.ok='ลอยแล้ว ✨';
          const scene=document.querySelector('main [x-data]');
          const api=scene?._x_dataStack?.[0];
          api?.spawnNew?.(data);
          this.form.wish='';
          setTimeout(()=>{
            Alpine.store('ui').open=false;
            this.ok='';
          }, 1500);
        }catch(e){ this.error=e.message; }
      }
    }
  }
</script>

</body>
</html>