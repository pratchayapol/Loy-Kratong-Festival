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
            river1:'#0a1f3d',
            river2:'#051428',
            glass:'rgba(255,255,255,0.08)'
          },
          boxShadow:{
            glass:'0 25px 80px rgba(0,0,0,0.45)',
            btn:'0 10px 40px rgba(34,211,238,0.4)',
            krathong:'0 15px 35px rgba(0,0,0,0.5)'
          }
        }
      }
    }
  </script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style id="dyn-keyframes"></style>
  <style>
    [x-cloak]{display:none !important}
    
    @keyframes floatNatural {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      25% { transform: translateY(-8px) rotate(-2deg); }
      50% { transform: translateY(-4px) rotate(1deg); }
      75% { transform: translateY(-10px) rotate(-1deg); }
    }
    
    @keyframes sway {
      0%, 100% { transform: translateX(0) rotate(0deg); }
      25% { transform: translateX(-15px) rotate(-3deg); }
      50% { transform: translateX(0) rotate(2deg); }
      75% { transform: translateX(15px) rotate(-2deg); }
    }
    
    @keyframes twinkle {
      0%, 100% { opacity: 0.3; }
      50% { opacity: 0.8; }
    }
    
    @keyframes ripple {
      0% { transform: scale(0.8); opacity: 0.8; }
      100% { transform: scale(2.5); opacity: 0; }
    }
    
    @keyframes modalSlideUp {
      from { 
        opacity: 0; 
        transform: translateY(30px) scale(0.95); 
      }
      to { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
      }
    }
    
    .star {
      position: absolute;
      width: 2px;
      height: 2px;
      background: white;
      border-radius: 50%;
      animation: twinkle 3s ease-in-out infinite;
    }
    
    .moon {
      position: absolute;
      top: 8%;
      right: 10%;
      width: 80px;
      height: 80px;
      background: radial-gradient(circle at 30% 30%, #fff9e6, #ffe4a3);
      border-radius: 50%;
      box-shadow: 0 0 60px rgba(255, 244, 179, 0.6), 
                  0 0 100px rgba(255, 244, 179, 0.3);
    }
    
    @media (max-width: 640px) {
      .moon {
        width: 50px;
        height: 50px;
        top: 5%;
        right: 5%;
      }
    }
    
    .water-ripple {
      position: absolute;
      border: 2px solid rgba(34, 211, 238, 0.4);
      border-radius: 50%;
      animation: ripple 3s ease-out infinite;
    }
    
    .modal-content {
      animation: modalSlideUp 0.3s ease-out;
    }
    
    .krathong-container {
      animation: floatNatural 4s ease-in-out infinite, sway 6s ease-in-out infinite;
    }
  </style>
</head>
<body x-data="{}" class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100 font-display overflow-x-hidden">

  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.store('ui', { open:false });
    });
  </script>

  <!-- ปุ่มลอยกระทง -->
  <button @click="$store.ui.open=true"
          class="fixed left-4 top-4 z-40 inline-flex items-center gap-2 rounded-2xl
                 bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-600 px-5 py-3 font-semibold
                 shadow-btn hover:shadow-[0_15px_50px_rgba(34,211,238,0.5)] hover:scale-105 
                 active:scale-100 transition-all duration-300 ease-out
                 focus:outline-none focus:ring-2 focus:ring-cyan-400/60">
    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0M12 3v9"/>
    </svg>
    <span class="hidden sm:inline">ลอยกระทง</span>
    <span class="sm:hidden">ลอย</span>
  </button>

  <!-- ฉากสายน้ำ -->
  <main class="relative min-h-screen overflow-hidden">
    <!-- พื้นหลังน้ำ -->
    <div class="absolute inset-0 bg-gradient-to-b from-river1 via-[#0d2847] to-river2"></div>

    <!-- ดวงจันทร์ -->
    <div class="moon"></div>

    <!-- ดาวระยิบระยับ -->
    <div class="pointer-events-none absolute inset-0" x-data="stars()" x-init="init()">
      <template x-for="(star, i) in starList" :key="i">
        <div class="star" :style="`left: ${star.x}%; top: ${star.y}%; animation-delay: ${star.delay}s; opacity: ${star.opacity};`"></div>
      </template>
    </div>

    <!-- คลื่นน้ำ -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
      <div class="absolute left-0 w-full h-32 top-[15%] opacity-20">
        <div class="w-[200%] h-full bg-gradient-to-r from-transparent via-cyan-300/20 to-transparent blur-2xl"
             style="animation: drift 25s linear infinite;"></div>
      </div>
      <div class="absolute left-0 w-full h-32 top-[45%] opacity-15">
        <div class="w-[200%] h-full bg-gradient-to-r from-transparent via-blue-300/20 to-transparent blur-2xl"
             style="animation: drift 30s linear infinite; animation-delay: -5s;"></div>
      </div>
      <div class="absolute left-0 w-full h-32 top-[75%] opacity-10">
        <div class="w-[200%] h-full bg-gradient-to-r from-transparent via-cyan-300/20 to-transparent blur-2xl"
             style="animation: drift 35s linear infinite; animation-delay: -10s;"></div>
      </div>
    </div>

    <!-- กระทงลอย -->
    <div class="absolute inset-0 overflow-hidden" x-data="riverScene()">
      <template x-for="k in items" :key="k.clientId">
        <div class="absolute krathong-container" :style="k.style">
          <div class="flex flex-col items-center gap-2">
            <!-- คำอธิษฐาน -->
            <div class="px-4 py-2 rounded-2xl text-xs sm:text-sm max-w-[200px] sm:max-w-[280px] 
                        text-cyan-50 bg-slate-900/80 backdrop-blur-xl border border-cyan-400/20 
                        shadow-lg shadow-cyan-500/20 whitespace-nowrap overflow-hidden text-ellipsis"
                 x-text="k.wish"></div>
            
            <!-- รูปกระทง -->
            <div class="relative">
              <img :src="k.img" alt="krathong" 
                   class="w-16 h-16 sm:w-20 sm:h-20 drop-shadow-[0_15px_25px_rgba(0,0,0,0.6)]">
              
              <!-- แสงเรืองรอบกระทง -->
              <div class="absolute inset-0 -z-10 blur-xl opacity-60 bg-gradient-radial from-amber-300/40 to-transparent"></div>
            </div>
          </div>
        </div>
      </template>

      <!-- ไล่สีเงาด้านล่าง -->
      <div class="pointer-events-none absolute inset-x-0 bottom-0 h-48 bg-gradient-to-t from-slate-950/90 via-slate-950/50 to-transparent"></div>
    </div>
  </main>

  <!-- Modal ฟอร์ม -->
  <div x-show="$store.ui.open" x-cloak class="fixed inset-0 z-50" @keydown.escape.window="$store.ui.open=false">
    <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm" @click="$store.ui.open=false"></div>

    <div class="absolute inset-0 grid place-items-center p-4 sm:p-6" @click.stop>
      <div class="w-full max-w-xl modal-content backdrop-blur-2xl rounded-3xl border border-white/20 bg-slate-900/40 shadow-glass"
           x-data="krathongForm()">
        
        <!-- Header -->
        <div class="flex items-start justify-between p-5 sm:p-6 border-b border-white/10">
          <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text text-transparent">
              ลอยกระทง
            </h2>
            <p class="text-sm text-slate-300 mt-1.5">เลือกแบบกระทง กรอกข้อมูล แล้วปล่อยลอยเลย</p>
          </div>
          <button @click="$store.ui.open=false" 
                  class="rounded-xl p-2 hover:bg-white/10 transition-colors focus:outline-none focus:ring-2 focus:ring-cyan-400/40">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="p-5 sm:p-6 space-y-5">
          <!-- เลือกแบบกระทง -->
          <div>
            <label class="text-sm font-semibold text-slate-200">เลือกแบบกระทง</label>
            <div class="mt-3 grid grid-cols-2 sm:grid-cols-4 gap-3">
              <label x-for="type in types" @click="form.type=type.key"
                class="group relative cursor-pointer rounded-2xl border border-white/10 bg-white/5 
                       hover:bg-white/10 hover:border-cyan-400/40 hover:scale-105
                       transition-all duration-300 p-3 sm:p-4 flex flex-col items-center gap-2"
                :class="form.type===type.key ? 'ring-2 ring-cyan-400/80 bg-cyan-500/10' : ''">
                <img :src="type.img" :alt="type.label" class="w-10 h-10 sm:w-12 sm:h-12 drop-shadow-lg">
                <span class="text-xs sm:text-sm font-medium" x-text="type.label"></span>
                <span class="absolute -top-2 -right-2 transition-all duration-200" 
                      :class="form.type===type.key ? 'scale-100 opacity-100' : 'scale-0 opacity-0'">
                  <span class="w-6 h-6 rounded-full bg-gradient-to-br from-cyan-400 to-blue-500 text-white text-xs shadow-lg grid place-items-center">✓</span>
                </span>
              </label>
            </div>
          </div>

          <!-- ชื่อและอายุ -->
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

          <!-- คำอธิษฐาน -->
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

          <!-- ปุ่มส่ง -->
          <div class="flex flex-wrap items-center gap-3 pt-2">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-xl 
                           bg-gradient-to-r from-cyan-500 via-blue-500 to-purple-600 
                           px-6 py-3 font-semibold shadow-btn 
                           hover:shadow-[0_15px_50px_rgba(34,211,238,0.5)] hover:scale-105
                           active:scale-100 transition-all duration-300">
              <svg xmlns="http://www.w3.org/2000/svg" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.5 12a7.5 7.5 0 0015 0m-15 0a7.5 7.5 0 0115 0M12 3v9"/>
              </svg>
              ลอยเลย
            </button>
            <button type="button" @click="$store.ui.open=false"
                    class="rounded-xl border border-white/20 px-5 py-3 
                           hover:bg-white/10 transition-colors font-medium">
              ยกเลิก
            </button>
            <span x-show="ok" x-text="ok" 
                  class="text-emerald-400 text-sm font-medium animate-pulse"></span>
            <span x-show="error" x-text="error" 
                  class="text-rose-400 text-sm font-medium"></span>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
  const rnd = (min, max) => Math.random() * (max - min) + min;

  function stars() {
    return {
      starList: [],
      init() {
        for (let i = 0; i < 100; i++) {
          this.starList.push({
            x: rnd(0, 100),
            y: rnd(0, 70),
            delay: rnd(0, 3),
            opacity: rnd(0.3, 0.9)
          });
        }
      }
    };
  }

  function riverScene() {
    const types = [
      { key: 'banana', label: 'กระทงใบตอง', img: 'https://cdn-icons-png.flaticon.com/512/2917/2917995.png' },
      { key: 'bread', label: 'กระทงขนมปัง', img: 'https://cdn-icons-png.flaticon.com/512/3075/3075977.png' },
      { key: 'ice', label: 'กระทงน้ำแข็ง', img: 'https://cdn-icons-png.flaticon.com/512/2917/2917949.png' },
      { key: 'flower', label: 'กระทงดอกไม้', img: 'https://cdn-icons-png.flaticon.com/512/3074/3074879.png' }
    ];

    const DUR_MIN = 25;
    const DUR_MAX = 35;

    const makeKrathong = (type, nickname, age, wish) => {
      const typeData = types.find(t => t.key === type) || types[0];
      const dur = rnd(DUR_MIN, DUR_MAX);
      const top = rnd(15, 85);
      const delay = rnd(0, 2);
      
      const name = `drift_${Math.random().toString(36).slice(2,11)}`;
      const sheet = document.getElementById('dyn-keyframes').sheet;
      sheet.insertRule(
        `@keyframes ${name} {
          0% { left: -15%; opacity: 0; }
          5% { opacity: 1; }
          95% { opacity: 1; }
          100% { left: 115%; opacity: 0; }
        }`,
        sheet.cssRules.length
      );

      return {
        id: Date.now() + Math.random(),
        clientId: `k_${Date.now()}_${Math.random().toString(36).slice(2)}`,
        img: typeData.img,
        wish: `${nickname} (${age}) : ${wish}`,
        style: `top: ${top}%; animation: ${name} ${dur}s linear ${delay}s forwards;`,
        duration: dur
      };
    };

    return {
      items: [],
      types: types,
      init() {
        // เพิ่มกระทงตัวอย่างเริ่มต้น
        const samples = [
          { type: 'banana', nickname: 'ดาว', age: 25, wish: 'ขอให้มีความสุขตลอดไป' },
          { type: 'flower', nickname: 'ใบเฟิร์น', age: 30, wish: 'ขอให้ทุกคนมีสุขภาพแข็งแรง' },
          { type: 'bread', nickname: 'ปอ', age: 22, wish: 'ขอให้การงานก้าวหน้า' },
          { type: 'ice', nickname: 'น้ำตาล', age: 28, wish: 'ขอให้ครอบครัวอบอุ่น' }
        ];

        samples.forEach((s, i) => {
          setTimeout(() => {
            this.spawn(s.type, s.nickname, s.age, s.wish);
          }, i * 3000);
        });

        // เพิ่มกระทงใหม่ทุก 8-12 วินาที
        const spawnRandom = () => {
          const s = samples[Math.floor(Math.random() * samples.length)];
          this.spawn(s.type, s.nickname, s.age, s.wish);
          setTimeout(spawnRandom, rnd(8000, 12000));
        };
        setTimeout(spawnRandom, 10000);
      },
      spawn(type, nickname, age, wish) {
        const k = makeKrathong(type, nickname, age, wish);
        this.items.push(k);
        
        // ลบกระทงที่ลอยจบแล้ว
        setTimeout(() => {
          this.items = this.items.filter(item => item.clientId !== k.clientId);
        }, (k.duration + 2) * 1000);
      },
      spawnNew(data) {
        this.spawn(data.type, data.nickname, data.age, data.wish);
      }
    };
  }

  function krathongForm() {
    const types = [
      { key: 'banana', label: 'ใบตอง', img: 'https://cdn-icons-png.flaticon.com/512/2917/2917995.png' },
      { key: 'bread', label: 'ขนมปัง', img: 'https://cdn-icons-png.flaticon.com/512/3075/3075977.png' },
      { key: 'ice', label: 'น้ำแข็ง', img: 'https://cdn-icons-png.flaticon.com/512/2917/2917949.png' },
      { key: 'flower', label: 'ดอกไม้', img: 'https://cdn-icons-png.flaticon.com/512/3074/3074879.png' }
    ];

    return {
      types: types,
      form: { type: 'banana', nickname: '', age: '', wish: '' },
      error: '',
      ok: '',
      async submit() {
        this.error = '';
        this.ok = '';
        
        // จำลองการบันทึก
        try {
          await new Promise(resolve => setTimeout(resolve, 500));
          
          this.ok = 'ลอยแล้ว! 🎉';
          
          const scene = document.querySelector('[x-data*="riverScene"]');
          if (scene && scene._x_dataStack) {
            scene._x_dataStack[0].spawnNew({
              type: this.form.type,
              nickname: this.form.nickname,
              age: this.form.age,
              wish: this.form.wish
            });
          }
          
          this.form.wish = '';
          setTimeout(() => {
            Alpine.store('ui').open = false;
            this.ok = '';
          }, 1500);
        } catch (e) {
          this.error = 'เกิดข้อผิดพลาด กรุณาลองใหม่';
        }
      }
    };
  }
  </script>

  <style>
    @keyframes drift {
      0% { transform: translateX(0); }
      100% { transform: translateX(-50%); }
    }
  </style>
</body>
</html>