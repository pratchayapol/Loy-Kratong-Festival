<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ลอยกระทงออนไลน์</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    // Tailwind config
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { display: ['Inter', 'ui-sans-serif', 'system-ui'] },
          colors: {
            river1: '#0e3a5f',
            river2: '#0b2e4a',
            glass: 'rgba(255,255,255,0.06)'
          },
          boxShadow: {
            glass: '0 20px 50px rgba(0,0,0,0.35)'
          },
          keyframes: {
            floatY: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-6px)' } },
            waves: { '0%': { transform: 'translateX(0)' }, '100%': { transform: 'translateX(-50%)' } },
          },
          animation: {
            floatY: 'floatY 3.2s ease-in-out infinite',
            waves: 'waves 18s linear infinite',
          }
        }
      }
    }
  </script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <script>const rnd=(min,max)=>Math.random()*(max-min)+min;</script>
  <style>
    /* dynamic drift keyframes will be injected at runtime */
  </style>
</head>
<body class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100 font-display">
  <div class="grid min-h-screen lg:grid-cols-[420px_1fr] gap-4">
    <!-- Sidebar / Form -->
    <aside class="p-4 lg:p-6">
      <div class="backdrop-blur-xl rounded-2xl border border-white/10 bg-glass shadow-glass" x-data="krathongForm()">
        <div class="p-5 lg:p-6 border-b border-white/10">
          <h1 class="text-2xl font-semibold tracking-tight">ลอยกระทง</h1>
          <p class="text-sm text-slate-300 mt-1">เลือกแบบกระทง กรอกชื่อ อายุ และคำอธิษฐาน แล้วปล่อยลอยได้ทันที</p>
        </div>

        <form @submit.prevent="submit" class="p-5 lg:p-6 space-y-5">
          <!-- Types -->
          <div>
            <label class="text-sm font-medium">เลือกแบบกระทง</label>
            <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3">
              @foreach($types as $key=>$t)
              <label
                class="group relative cursor-pointer rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 transition
                       ring-0 data-[checked=true]:ring-2 data-[checked=true]:ring-cyan-400/60 p-3 flex flex-col items-center gap-2"
                :data-checked="form.type==='{{ $key }}'">
                <input class="sr-only" type="radio" name="type" x-model="form.type" value="{{ $key }}">
                <img src="{{ $t['img'] }}" alt="{{ $t['label'] }}" class="w-12 h-12 drop-shadow" loading="lazy">
                <span class="text-sm">{{ $t['label'] }}</span>
                <span class="absolute -top-2 -right-2 hidden data-[checked=true]:inline-flex items-center justify-center
                             w-6 h-6 rounded-full bg-cyan-500 text-white text-xs shadow">✓</span>
              </label>
              @endforeach
            </div>
          </div>

          <!-- Nickname -->
          <div class="space-y-2">
            <label class="text-sm font-medium">ชื่อเล่น</label>
            <input x-model="form.nickname" type="text" maxlength="50" required
                   class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 outline-none
                          focus:border-cyan-500/60 focus:ring-2 focus:ring-cyan-500/20 placeholder:text-slate-400"
                   placeholder="เช่น โฟกัส">
          </div>

          <!-- Age -->
          <div class="space-y-2">
            <label class="text-sm font-medium">อายุ</label>
            <input x-model.number="form.age" type="number" min="1" max="120" required
                   class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 outline-none
                          focus:border-cyan-500/60 focus:ring-2 focus:ring-cyan-500/20">
          </div>

          <!-- Wish -->
          <div class="space-y-2">
            <label class="text-sm font-medium">คำอธิษฐาน</label>
            <textarea x-model="form.wish" maxlength="200" required rows="3"
                      class="w-full rounded-xl bg-white/5 border border-white/10 px-4 py-3 outline-none
                             focus:border-cyan-500/60 focus:ring-2 focus:ring-cyan-500/20 placeholder:text-slate-400"
                      placeholder="ขอให้..."></textarea>
            <div class="text-xs text-slate-400">ไม่เกิน 200 ตัวอักษร</div>
          </div>

          <!-- Submit -->
          <div class="flex items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600
                           px-5 py-3 font-medium shadow-lg shadow-cyan-900/30 hover:brightness-110 active:scale-[.99] transition">
              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6"
                      d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0M12 3v9"/>
              </svg>
              ลอยเลย
            </button>
            <span x-text="ok" class="text-emerald-400 text-sm"></span>
            <span x-text="error" class="text-rose-400 text-sm"></span>
          </div>
        </form>

        <div class="px-5 lg:px-6 py-4 border-t border-white/10 text-sm text-slate-300">
          ดับเบิลคลิกที่สายน้ำเพื่อปล่อยกระทงสุ่มเพิ่ม
        </div>
      </div>
    </aside>

    <!-- River -->
    <main class="relative">
      <div class="absolute inset-0 bg-gradient-to-b from-river1 to-river2"></div>

      <!-- Stars bokeh -->
      <div class="pointer-events-none absolute inset-0 opacity-30"
           style="background-image: radial-gradient(circle at 20% 10%, rgba(255,255,255,.18) 0 1px, transparent 2px),
                                  radial-gradient(circle at 80% 30%, rgba(255,255,255,.12) 0 1px, transparent 2px),
                                  radial-gradient(circle at 60% 80%, rgba(255,255,255,.15) 0 1px, transparent 2px);
                  background-size: 160px 160px, 200px 200px, 180px 180px;"></div>

      <!-- Water waves -->
      <div class="absolute inset-0 overflow-hidden" x-data="riverScene(@js($types), @js($recent))" @dblclick="spawnRandom()">
        <div class="absolute left-0 w-[200%] h-28 top-[12%] opacity-30 blur-2xl bg-[radial-gradient(ellipse_at_center,_white_0%,_transparent_60%)] animate-waves"></div>
        <div class="absolute left-0 w-[200%] h-28 top-[44%] opacity-25 blur-2xl bg-[radial-gradient(ellipse_at_center,_white_0%,_transparent_60%)] animate-[waves_22s_linear_infinite]"></div>
        <div class="absolute left-0 w-[200%] h-28 top-[72%] opacity-20 blur-2xl bg-[radial-gradient(ellipse_at_center,_white_0%,_transparent_60%)] animate-[waves_26s_linear_infinite]"></div>

        <!-- Floating krathongs -->
        <template x-for="k in items" :key="k.clientId">
          <div class="absolute flex flex-col items-center animate-floatY will-change-transform" :style="k.style">
            <div class="px-3 py-1.5 rounded-2xl text-sm max-w-[260px] text-cyan-50/95 bg-slate-900/60 backdrop-blur
                        border border-white/10 shadow ring-1 ring-white/10" x-text="k.wish"></div>
            <img :src="k.img" alt="krathong" class="w-20 h-auto drop-shadow-[0_12px_18px_rgba(0,0,0,0.45)] mt-1.5">
          </div>
        </template>

        <!-- River gradient vignette -->
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-slate-950/70 to-transparent"></div>
      </div>
    </main>
  </div>

  <script>
    function riverScene(types, recent){
      const typeImg = (t)=>types[t]?.img || Object.values(types)[0]?.img || '';
      const makeStyle = (dur, delay, topPct)=>{
        const name = `drift_${Math.random().toString(36).slice(2)}`;
        const sheet = document.styleSheets[document.styleSheets.length-1];
        sheet.insertRule(`@keyframes ${name}{0%{transform:translateX(-12%)}100%{transform:translateX(112%)}}`, sheet.cssRules.length);
        return `top:${topPct}%;left:-12%;animation:${name} ${dur}s linear ${delay}s forwards`;
      };
      const initial = (recent||[]).map(r => ({
        clientId:`srv_${r.id}`,
        img:typeImg(r.type),
        wish:`${r.nickname} (${r.age}) : ${r.wish}`,
        style:makeStyle(rnd(24,40), rnd(0,20), rnd(8,88)),
        ttl:Date.now() + 1000*(rnd(24,40)+rnd(0,20)+1)
      }));
      return {
        items: initial,
        spawn(p){
          const k = {
            clientId:`cli_${crypto.randomUUID()}`,
            img:typeImg(p.type),
            wish:`${p.nickname} (${p.age}) : ${p.wish}`,
            style:makeStyle(rnd(22,34), 0, rnd(10,90)),
            ttl:Date.now() + 1000*(rnd(22,34)+1)
          };
          this.items.push(k);
          if(this.items.length>80) this.items.splice(0, this.items.length-80);
        },
        spawnRandom(){
          const keys = Object.keys(types);
          const type = keys[Math.floor(Math.random()*keys.length)];
          this.spawn({type, nickname:'ผู้ร่วมงาน', age: Math.floor(rnd(10,60)), wish:'สุขภาพแข็งแรง'});
        }
      }
    }

    function readCookie(name){
      return decodeURIComponent(document.cookie.split('; ').find(r=>r.startsWith(name+'='))?.split('=')[1]||'');
    }

    function krathongForm(){
      return {
        form:{ type:'banana', nickname:'', age:'', wish:'' },
        error:'', ok:'',
        async submit(){
          this.error=''; this.ok='';
          try{
            const tokenMeta = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const tokenCookie = readCookie('XSRF-TOKEN');
            const res = await fetch(`{{ route('krathong.store') }}`, {
              method:'POST',
              headers:{
                'Content-Type':'application/json',
                'Accept':'application/json',
                'X-CSRF-TOKEN': tokenMeta,
                'X-XSRF-TOKEN': tokenCookie
              },
              credentials:'same-origin',
              body: JSON.stringify(this.form)
            });
            if(!res.ok){
              const j = await res.json().catch(()=>({}));
              throw new Error(j.message || `HTTP ${res.status}`);
            }
            const data = await res.json();
            this.ok = 'ลอยแล้ว';
            document.querySelectorAll('main [x-data]').forEach(el=>{
              if(el._x_dataStack?.[0]?.spawn){
                el._x_dataStack[0].spawn({ type:data.type, nickname:data.nickname, age:data.age, wish:data.wish });
              }
            });
            this.form.wish = '';
          }catch(e){ this.error = e.message; }
        }
      }
    }
  </script>
</body>
</html>
