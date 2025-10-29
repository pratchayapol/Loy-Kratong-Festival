<head>
    <meta charset="utf-8" />
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

    {{-- Google Fonts: Sarabun --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@200;300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Tailwind config BEFORE CDN -->
    <script>
        tailwind = {
            config: {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['"Sarabun"', 'ui-sans-serif', 'system-ui'],
                            display: ['"Sarabun"', 'ui-sans-serif', 'system-ui'],
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
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Libs: ใส่ defer -->
    <script defer src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>


    <!-- Base styles for layout & chart -->
    <style>
        html,
        body {
            font-family: "Sarabun", "TH Sarabun New", ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Noto Sans Thai", Tahoma, Arial, sans-serif;
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
            z-index: 60;
            pointer-events: none;
        }

        @media (prefers-reduced-motion: reduce) {
            #fwCanvas {
                display: none
            }
        }
    </style>
    @stack('head')
</head>

<body class="min-h-screen bg-slate-950 text-slate-100 font-sans overflow-hidden" x-data="{}">

    {{-- เนื้อหาหน้า --}}
    @yield('content')

    {{-- ช่องให้เพจยัดสคริปต์ท้ายหน้า --}}
    @stack('scripts')

</body>

</html>
