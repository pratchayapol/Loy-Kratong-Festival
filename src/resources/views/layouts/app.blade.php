@vite(['resources/css/app.css', 'resources/js/app.js'])

<head>
    <meta charset="utf-8" />
    <meta name="description"
        content="ลอยกระทงออนไลน์ 68 เว็บไซต์ร่วมประเพณีไทย ปี 2568 ส่งคำอธิษฐานผ่านกระทงออนไลน์ ต้อนรับ Loy Krathong Festival 2025">
    <meta name="keywords"
        content="ลอยกระทง, ลอยกระทงออนไลน์68, ลอยกระทง2568, Loy Krathong 2025, ประเพณีไทย, กระทง, งานลอยกระทง">

    <meta property="og:title" content="ลอยกระทงออนไลน์ 68 | ลอยกระทงปี 2568">
    <meta property="og:description" content="ร่วมประเพณีไทย ลอยกระทงออนไลน์ปี 2568 ส่งคำอธิษฐานผ่านเว็บไซต์กับเรา">
    <meta property="og:image" content="https://loykrathong.pcnone.com/images/krathongs/banana.png">
    <meta property="og:url" content="https://loykrathong.pcnone.com">

    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Loy Krathong Festival</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Favicons -->
    <link rel="icon" href="{{ secure_asset('favicon.ico') }}" />
    <link rel="icon" type="image/png" sizes="32x32" href="{{ secure_asset('favicon-32x32.png') }}" />
    <link rel="icon" type="image/png" sizes="16x16" href="{{ secure_asset('favicon-16x16.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ secure_asset('apple-touch-icon.png') }}" />
    <link rel="manifest" href="{{ secure_asset('site.webmanifest') }}" />
    <meta name="theme-color" content="#0b2e4a" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=K2D:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap"
        rel="stylesheet">



    <!-- Tailwind config BEFORE CDN -->
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        fontFamily: {
                            k2d: ['K2D', 'sans-serif'],
                            sans: ['K2D', 'ui-sans-serif', 'system-ui', 'sans-serif']
                        },
                        keyframes: {
                            floatY: {
                                '0%,100%': {
                                    transform: 'translateY(0)'
                                },
                                '50%': {
                                    transform: 'translateY(-6px)'
                                }
                            },
                            sway: {
                                '0%,100%': {
                                    transform: 'rotate(0deg)'
                                },
                                '25%': {
                                    transform: 'rotate(-1.5deg)'
                                },
                                '75%': {
                                    transform: 'rotate(1.5deg)'
                                }
                            },
                            waves: {
                                '0%': {
                                    transform: 'translateX(0)'
                                },
                                '100%': {
                                    transform: 'translateX(-50%)'
                                }
                            },
                            twinkle: {
                                '0%,100%': {
                                    opacity: '.35',
                                    transform: 'scale(1)'
                                },
                                '50%': {
                                    opacity: '1',
                                    transform: 'scale(1.15)'
                                }
                            }
                        },
                        animation: {
                            floatY: 'floatY 3.2s ease-in-out infinite',
                            sway: 'sway 5s ease-in-out infinite',
                            waves: 'waves 18s linear infinite',
                            twinkle: 'twinkle 3.4s ease-in-out infinite'
                        }
                    }
                }
            }
        }; // tailwindcss.com CDN จะอ่านตัวแปรนี้ตอนโหลด
    </script>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

    <!-- Libs: ใส่ defer -->
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <!-- Base styles for layout & chart -->
    <style>
        /* จุดเริ่มพุ่งขึ้น */
        .fw-shell {
            position: absolute;
            width: 6px;
            height: 16px;
            background: radial-gradient(circle, #fff, rgba(255, 255, 255, 0));
            filter: drop-shadow(0 0 6px rgba(255, 255, 255, 0.8));
            animation: fw-rise 0.6s ease-out forwards;
        }

        @keyframes fw-rise {
            0% {
                transform: translateY(0);
                opacity: 1;
            }

            100% {
                transform: translateY(-140px);
                opacity: 0;
            }
        }

        /* ดอกพลุแตก */
        .fw-burst {
            position: absolute;
            width: 4px;
            height: 4px;
            background: var(--fw-color, #fff);
            border-radius: 999px;
            filter: drop-shadow(0 0 10px rgba(255, 255, 255, 0.9));
            animation: fw-particle 1.4s ease-out forwards;
            transform-origin: center;
        }

        @keyframes fw-particle {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }

            80% {
                opacity: 1;
            }

            100% {
                transform: translate(var(--tx), var(--ty)) scale(0);
                opacity: 0;
            }
        }

        /* ดอกกลางนุ่มๆ */
        .fw-core {
            position: absolute;
            width: 14px;
            height: 14px;
            border-radius: 9999px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0));
            filter: drop-shadow(0 0 20px rgba(255, 255, 255, 0.9));
            animation: fw-core 1.2s ease-out forwards;
            pointer-events: none;
        }

        @keyframes fw-core {
            0% {
                transform: scale(0);
                opacity: 1;
            }

            50% {
                transform: scale(1.4);
                opacity: 1;
            }

            100% {
                transform: scale(1.8);
                opacity: 0;
            }
        }

        /* มือถือเบาลง */
        @media (max-width: 640px) {
            .fw-burst {
                animation-duration: 1.1s;
            }
        }

        body {
            font-family: 'K2D', Arial, sans-serif;
        }

        button,
        input,
        select,
        textarea {
            font-family: inherit;
        }

        /* หิ่งห้อยกระพริบ */
        @keyframes fireflyBlink {

            0%,
            100% {
                opacity: .35;
                filter: drop-shadow(0 0 4px rgba(255, 240, 180, .6)) drop-shadow(0 0 10px rgba(255, 230, 140, .35));
            }

            50% {
                opacity: 1;
                filter: drop-shadow(0 0 8px rgba(255, 245, 200, .9)) drop-shadow(0 0 18px rgba(255, 235, 160, .6));
            }
        }

        /* กระทงหยุด*/
        .krathong-item.is-paused {
            animation-play-state: paused !important
        }

        .krathong-pop {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            bottom: 90%;
            max-width: 280px;
            z-index: 50;
            padding: .6rem .8rem;
            border-radius: 12px;
            background: rgba(2, 6, 23, .85);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(255, 255, 255, .15);
            box-shadow: 0 10px 30px rgba(0, 0, 0, .4)
        }

        /* โบว์สีดำ */
        .ribbon-black {
            position: fixed;
            right: 0;
            top: 0;
            z-index: 2568
        }

        [x-cloak] {
            display: none !important
        }

        .krathong-item {
            animation: floatY var(--floatDur, 3.2s) ease-in-out infinite, sway var(--swayDur, 5s) ease-in-out infinite;
            will-change: transform
        }

        .modal-enter {
            animation: slideUp .3s ease-out
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(.95)
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1)
            }
        }

        .wrap {
            max-width: 900px;
            margin: 0.75rem auto
        }

        .ping-chart-wrapper {
            max-width: 900px;
            margin: 0.5rem auto
        }

        #pingChart {
            width: 100%;
            height: 200px
        }

        .pb-safe {
            padding-bottom: max(1rem, env(safe-area-inset-bottom));
        }

        .pt-safe {
            padding-top: max(0.75rem, env(safe-area-inset-top));
        }

        @media (max-width: 420px),
        (prefers-reduced-motion: reduce) {
            .less-anim * {
                animation: none !important;
                transition: none !important
            }
        }

        body {
            touch-action: manipulation
        }

        /* ดาวตก */

        @keyframes shootingStar {
            0% {
                transform: translate(0, 0);
                opacity: 1
            }

            100% {
                transform: translate(-200px, 200px);
                opacity: 0
            }
        }

        #fwCanvas {
            position: fixed;
            inset: 0;
            z-index: 9999;
            pointer-events: none
        }

        @media (prefers-reduced-motion: reduce) {
            #fwCanvas {
                display: none
            }
        }
    </style>
    @stack('head')
</head>

<body
    class="font-k2d min-h-screen bg-slate-950 text-slate-100
         overflow-hidden md:overflow-hidden
         [@supports(height:100dvh)]:min-h-[100dvh]">

    {{-- เนื้อหาหน้า --}}
    <div class="h-screen md:h-screen overflow-y-auto md:overflow-y-visible px-4 py-4">
        @yield('content')
    </div>

    {{-- ช่องให้เพจยัดสคริปต์ท้ายหน้า --}}
    @stack('scripts')

</body>

</html>
