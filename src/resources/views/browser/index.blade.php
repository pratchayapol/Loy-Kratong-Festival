<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>เปิดนอก Facebook</title>
</head>
<body>
  <p>กำลังเปิด...</p>
  <script>
    // ถ้าเป็น Android ลองยิง intent ก่อน
    var url = "https://loykrathong.pcnone.com";
    var isAndroid = /Android/i.test(navigator.userAgent);

    if (isAndroid) {
      window.location = "intent://loykrathong.pcnone.com#Intent;scheme=https;package=com.android.chrome;end";
      setTimeout(function () {
        window.location = url;
      }, 800);
    } else {
      // iOS/อื่นๆ ให้โชว์ปุ่มแทน
      document.body.innerHTML = '<a href="'+url+'" style="font-size:18px">กดเปิดใน Safari/เบราว์เซอร์</a>';
    }
  </script>
</body>
</html>
