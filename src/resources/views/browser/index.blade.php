<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <title>เปิดนอก Facebook</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen flex items-center justify-center bg-slate-100">
    <div id="content" class="w-full max-w-md mx-auto text-center p-6 bg-white rounded-2xl shadow">
        <h1 class="text-lg font-semibold text-slate-800 mb-2">กำลังเปิด...</h1>
        <p class="text-sm text-slate-500">ถ้าไม่เด้งให้กดปุ่มด้านล่าง</p>
    </div>

    <script>
        var url = "https://loykrathong.pcnone.com";
        var isAndroid = /Android/i.test(navigator.userAgent);

        if (isAndroid) {
            // ไม่ล็อกแพ็กเกจ เพื่อให้มีสิทธิ์ขึ้นตัวเลือก
            window.location = "intent://loykrathong.pcnone.com#Intent;scheme=https;end";

            // กันพัง
            setTimeout(function() {
                window.location = url;
            }, 800);
        } else {
            var c = document.getElementById('content');
            c.innerHTML = `
      <h1 class="text-lg font-semibold text-slate-800 mb-3">เปิดในเบราว์เซอร์</h1>
      <p class="text-sm text-slate-500 mb-4">กดปุ่มด้านล่างเพื่อเปิด</p>
      <a href="${url}"
         class="inline-flex items-center justify-center w-full sm:w-auto px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition">
        เปิดเว็บไซต์
      </a>
      <p class="text-xs text-slate-400 mt-4">ถ้ายังเปิดใน Facebook อยู่ ให้กด ⋯ > Open in browser</p>
    `;
        }
    </script>

</body>

</html>
