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
          boxShadow:{glass:'0 25px 80px rgba(0,0,0,0.45)',btn:'0 10px 30px rgba(34,211,238,0.35)'},
          keyframes:{
            floatY:{'0%,100%':{transform:'translateY(0)'},'50%':{transform:'translateY(-6px)'}},
            waves:{'0%':{transform:'translateX(0)'},'100%':{transform:'translateX(-50%)'}},
            overlayIn:{'0%':{opacity:0},'100%':{opacity:1}},
            scaleIn:{'0%':{transform:'scale(.96)',opacity:0},'100%':{transform:'scale(1)',opacity:1}}
          },
          animation:{
            floatY:'floatY 3.2s ease-in-out infinite',
            waves:'waves 18s linear infinite',
            overlayIn:'overlayIn .15s ease-out',
            scaleIn:'scaleIn .18s ease-out'
          }
        }
      }
    }
  </script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>const rnd=(min,max)=>Math.random()*(max-min)+min;</script>
  <style id="dyn-keyframes"></style>
  <style>[x-cloak]{display:none !important}</style>
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100 font-display">

  <!-- Alpine store กลาง -->
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.store('ui', { open:false });
    });
  </script>

  <!-- ปุ่มมุมซ้ายบน -->
  <button @click="$store.ui.open=true"
          class="fixed left-4 top-4 z-40 inline-flex items-center gap-2 rounded-xl
                 bg-gradient-to-r from-cyan-500 to-blue-600 px-4 py-2.5 font-medium
                 shadow-btn hover:brightness-110 active:scale-[.99] focus:outline-none focus:ring-2 focus:ring-cyan-400/40">
    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0M12 3v9"/>
    </svg>
    ลอยกระทงด้วย
  </button>

  <!-- ฉากสายน้ำ -->
  <main class="relative min-h-screen">
    <div class="absolute inset-0 bg-gradient-to-b from-river1 to-river2"></div>

    <!-- ดาวระยิบ -->
    <div class="pointer-events-none absolute inset-0 opacity-30"
         style="background-image: radial-gradient(circle at 20% 10%, rgba(255,255,255,.18) 0 1px, transparent 2px),
                                radial-gradient(circle at 80% 30%, rgba(255,255,255,.12) 0 1px, transparent 2px),
                                radial-gradient(circle at 60% 80%, rgba(255,255,255,.15) 0 1px, transparent 2px);
                background-size: 160px 160px, 200px 200px, 180px 180px;"></div>

    <!-- คลื่นน้ำ + กระทง -->
    <div class="absolute inset-0 overflow-hidden" x-data="riverScene(@js($types), @js($recent))" @dblclick="spawnRandom()">
      <div class="absolute left-0 w-[200%] h-28 top-[12%] opacity-30 blur-2xl bg-[radial-gradient(ellipse_at_center,_white_0%,_transparent_60%)] animate-waves"></div>
      <div class="absolute left-0 w-[200%] h-28 top-[44%] opacity-25 blur-2xl bg-[radial-gradient(ellipse_at_center,_white_0%,_transparent_60%)] animate-[waves_22s_linear_infinite]"></div>
      <div class="absolute left-0 w-[200%] h-28 top-[72%] opacity-20 blur-2xl bg-[radial-gradient(ellipse_at_center,_white_0%,_transparent_60%)] animate-[waves_26s_linear_infinite]"></div>

      <template x-for="k in items" :key="k.clientId">
        <div class="absolute flex flex-col items-center animate-floatY will-change-transform" :style="k.style">
          <div class="px-3 py-1.5 rounded-2xl text-sm max-w-[260px] text-cyan-50/95 bg-slate-900/60 backdrop-blur border border-white/10 shadow ring-1 ring-white/10" x-text="k.wish"></div>
          <img :src="k.img" alt="krathong" class="w-20 h-auto drop-shadow-[0_12px_18px_rgba(0,0,0,0.45)] mt-1.5">
        </div>
      </template>

      <div class="pointer-events-none absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-slate-950/70 to-transparent"></div>
    </div>

    <div class="absolute right-4 bottom-4 text-slate-300/90 text-sm backdrop-blur-xl bg-glass border border-white/10 px-4 py-2 rounded-xl shadow-glass">
      ดับเบิลคลิกที่สายน้ำเพื่อปล่อยกระทงสุ่ม
    </div>
  </main>

  <!-- Modal ฟอร์ม -->
  <div x-show="$store.ui.open" x-cloak class="fixed inset-0 z-50" @keydown.escape.window="$store.ui.open=false">
    <div class="absolute inset-0 bg-slate-950/70 animate-overlayIn" @click="$store.ui.open=false"></div>

    <div class="absolute inset-0 grid place-items-center p-4">
      <div class="w-full max-w-xl animate-scaleIn backdrop-blur-2xl rounded-2xl border border-white/10 bg-glass shadow-glass"
           x-data="krathongForm()">
        <div class="flex items-start justify-between p-5 lg:p-6 border-b border-white/10">
          <div>
            <h2 class="text-xl font-semibold">ลอยกระทง</h2>
            <p class="text-sm text-slate-300 mt-1">เลือกแบบ กรอกข้อมูล แล้วปล่อยลอยเลย</p>
          </div>
          <button @click="$store.ui.open=false" class="rounded-lg p-2 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-cyan-400/40">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="submit" class="p-5 lg:p-6 space-y-5">
          <div>
            <label class="text-sm font-medium">เลือกแบบกระทง</label>
            <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3">
              @foreach($types as $key=>$t)
              <label
                @click="form.type='{{ $key }}'"
                class="group relative cursor-pointer rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 transition p-3 flex flex-col items-center gap-2"
                :class="form.type==='{{ $key }}' ? 'ring-2 ring-cyan-400/60' : 'ring-0'">
                <input class="sr-only" type="radio" name="type" x-model="form.type" value="{{ $key }}">
                <img src="{{ $t['img'] }}" alt="{{ $t['label'] }}" class="w-12 h-12 drop-shadow" loading="lazy">
                <span class="text-sm">{{ $t['label'] }}</span>
                <span class="absolute -top-2 -right-2" :class="form.type==='{{ $key }}' ? 'inline-flex' : 'hidden'">
                  <span class="w-6 h-6 rounded-full bg-cyan-500 text-white text-xs shadow grid place-items-center">✓</span>
                </span>
              </label>
              @endforeach
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-sm font-medium">ชื่อเล่น</label>
              <input x-model="form.nickname" type="text" maxlength="50" required
                     class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 outline-none focus:border-cyan-500/60 focus:ring-2 focus:ring-cyan-500/20 placeholder:text-slate-400"
                     placeholder="เช่น โฟกัส">
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium">อายุ</label>
              <input x-model.number="form.age" type="number" min="1" max="120" required
                     class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 outline-none focus:border-cyan-500/60 focus:ring-2 focus:ring-cyan-500/20">
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">คำอธิษฐาน</label>
            <textarea x-model="form.wish" maxlength="200" required rows="3"
                      class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 outline-none focus:border-cyan-500/60 focus:ring-2 focus:ring-cyan-500/20 placeholder:text-slate-400"
                      placeholder="ขอให้..."></textarea>
            <div class="text-xs text-slate-400 flex justify-between">
              <span>ไม่เกิน 200 ตัวอักษร</span>
              <span x-text="`${form.wish?.length||0}/200`"></span>
            </div>
          </div>

          <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 px-5 py-3 font-medium shadow-btn hover:brightness-110 active:scale-[.99] transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0M12 3v9"/>
              </svg>
              ลอยเลย
            </button>
            <button type="button" @click="$store.ui.open=false"
                    class="rounded-xl border border-white/10 px-4 py-3 hover:bg-white/10">ปิด</button>
            <span x-text="ok" class="text-emerald-400 text-sm"></span>
            <span x-text="error" class="text-rose-400 text-sm"></span>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const readCookie = n => decodeURIComponent((document.cookie.split('; ').find(x=>x.startsWith(n+'='))||'').split('=')[1]||'');

    function riverScene(types, recent){
      const typeImg=(t)=>types?.[t]?.img || Object.values(types||{})[0]?.img || '';
      const makeStyle=(dur,delay,top)=>{
        const name=`drift_${Math.random().toString(36).slice(2)}`;
        const sheet=document.getElementById('dyn-keyframes').sheet;
        sheet.insertRule(`@keyframes ${name}{0%{transform:translateX(-12%)}100%{transform:translateX(112%)}}`,sheet.cssRules.length);
        return `top:${top}%;left:-12%;animation:${name} ${dur}s linear ${delay}s forwards`;
      };
      const initial=(recent||[]).map(r=>({
        clientId:`srv_${r.id}`, img:typeImg(r.type), wish:`${r.nickname} (${r.age}) : ${r.wish}`,
        style:makeStyle(rnd(24,40),rnd(0,20),rnd(8,88)),
        ttl:Date.now()+1000*(rnd(24,40)+rnd(0,20)+1)
      }));
      return {
        items: initial,
        spawn(p){
          const k={clientId:`cli_${crypto.randomUUID?.()||Math.random().toString(36).slice(2)}`, img:typeImg(p.type),
                   wish:`${p.nickname} (${p.age}) : ${p.wish}`, style:makeStyle(rnd(22,34),0,rnd(10,90)),
                   ttl:Date.now()+1000*(rnd(22,34)+1)};
          this.items.push(k); if(this.items.length>80) this.items.splice(0,this.items.length-80);
        },
        spawnRandom(){
          const keys=Object.keys(types||{}); const type=keys[Math.floor(Math.random()*keys.length)]||'banana';
          this.spawn({type,nickname:'ผู้ร่วมงาน',age:Math.floor(rnd(10,60)),wish:'สุขภาพแข็งแรง'});
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
            const meta = document.querySelector('meta[name="csrf-token"]').content;
            const xsrf = readCookie('XSRF-TOKEN');
            const res = await fetch('/krathongs', {
              method:'POST',
              headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN': meta,
                'X-XSRF-TOKEN': xsrf,
                'X-Requested-With':'XMLHttpRequest'
              },
              credentials:'same-origin',
              body: JSON.stringify(this.form)
            });
            if(!res.ok){
              let msg=`HTTP ${res.status}`;
              try{ const j=await res.json(); msg=j.message||msg; }catch(_){}
              throw new Error(msg);
            }
            const data = await res.json();
            this.ok='ลอยแล้ว';
            // spawn กระทงใหม่ในฉาก
            document.querySelectorAll('main [x-data]').forEach(el=>{
              const x = el._x_dataStack?.[0];
              if(x?.spawn){ x.spawn({ type:data.type, nickname:data.nickname, age:data.age, wish:data.wish }); }
            });
            this.form.wish='';
            Alpine.store('ui').open = false; // ปิดโมดัล
          }catch(e){
            this.error=e.message;
          }
        }
      }
    }
  </script>
</body>
</html>
