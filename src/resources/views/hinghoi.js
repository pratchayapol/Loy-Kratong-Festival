
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

