<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>RealtyLinkPH</title>

    <!-- Google Fonts: Non-blocking load with preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet"></noscript>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #333;
        }

        /* ═══════════════════════════════════════════
           PREMIUM LOADING SCREEN (before Vue mounts)
           ═══════════════════════════════════════════ */
        #splash-loader {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at center, #0F172A 0%, #0B1120 100%);
            overflow: hidden;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        #splash-loader.hide {
            opacity: 0;
            visibility: hidden;
        }

        /* Floating background shapes */
        .splash-shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.02);
            animation: splash-float-up 18s linear infinite;
        }
        @keyframes splash-float-up {
            0% { transform: translateY(0) scale(1); opacity: 0; }
            20% { opacity: 0.4; }
            100% { transform: translateY(-110vh) scale(0.8); opacity: 0; }
        }
        /* Bottom gradient overlay */
        .splash-gradient-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, #0B1120, transparent, transparent);
            opacity: 0.8;
            pointer-events: none;
        }

        /* Logo + loading content */
        .splash-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            max-width: 360px;
            width: 100%;
            opacity: 0;
            transform: translateY(20px);
            animation: splash-fade-in-up 1s ease-out 0.2s forwards;
        }
        @keyframes splash-fade-in-up {
            to { opacity: 1; transform: translateY(0); }
        }

        /* Wordmark */
        .splash-wordmark {
            font-family: 'Plus Jakarta Sans', 'Outfit', sans-serif;
            font-size: 3.2rem;
            letter-spacing: -0.03em;
            color: #FAFAF9;
            text-align: center;
            margin-bottom: 2.5rem;
            text-shadow: 0 4px 30px rgba(0, 0, 0, 0.5);
        }
        .splash-wordmark .brand-realty { font-weight: 400; color: rgba(255,255,255,0.85); }
        .splash-wordmark .brand-link   { font-weight: 800; color: #FFFFFF; }
        .splash-wordmark .gold { color: #D89B0F; }

        /* Shuttle loading bar */
        .splash-bar-track {
            width: 16rem;
            height: 3px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 9999px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        .splash-bar-shuttle {
            position: absolute;
            inset: 0;
            width: 50%;
            background: linear-gradient(90deg, transparent, #D4A853, transparent);
            opacity: 0.8;
            animation: splash-shuttle 2.5s ease-in-out infinite;
        }
        @keyframes splash-shuttle {
            0% { transform: translateX(-100%); }
            50% { transform: translateX(200%); }
            100% { transform: translateX(-100%); }
        }

        /* Loading text */
        .splash-text {
            font-family: 'Inter', -apple-system, sans-serif;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            color: #78716C;
            margin-top: 1.5rem;
            font-weight: 500;
            animation: splash-pulse 2s ease-in-out infinite;
        }
        @keyframes splash-pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }

        /* Mobile responsive */
        @media (max-width: 480px) {
            .splash-wordmark {
                font-size: 2.5rem;
            }
            .splash-bar-track {
                width: 12rem;
            }
        }
    </style>

    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body>
    <!-- Premium Loading Screen (shows instantly, fades out when Vue mounts) -->
    <div id="splash-loader">
        <!-- Floating shapes -->
        <div class="splash-shape" style="left:8%;bottom:-150px;width:120px;height:120px;animation-duration:22s;animation-delay:0s"></div>
        <div class="splash-shape" style="left:25%;bottom:-150px;width:80px;height:80px;animation-duration:18s;animation-delay:1s"></div>
        <div class="splash-shape" style="left:42%;bottom:-150px;width:140px;height:140px;animation-duration:25s;animation-delay:2.5s"></div>
        <div class="splash-shape" style="left:60%;bottom:-150px;width:60px;height:60px;animation-duration:16s;animation-delay:0.5s"></div>
        <div class="splash-shape" style="left:75%;bottom:-150px;width:100px;height:100px;animation-duration:20s;animation-delay:3s"></div>
        <div class="splash-shape" style="left:90%;bottom:-150px;width:70px;height:70px;animation-duration:17s;animation-delay:1.5s"></div>
        <div class="splash-shape" style="left:15%;bottom:-150px;width:90px;height:90px;animation-duration:23s;animation-delay:4s"></div>
        <div class="splash-shape" style="left:50%;bottom:-150px;width:110px;height:110px;animation-duration:19s;animation-delay:2s"></div>
        <div class="splash-shape" style="left:35%;bottom:-150px;width:75px;height:75px;animation-duration:21s;animation-delay:3.5s"></div>
        <div class="splash-shape" style="left:68%;bottom:-150px;width:130px;height:130px;animation-duration:24s;animation-delay:1.2s"></div>
        <div class="splash-shape" style="left:5%;bottom:-150px;width:55px;height:55px;animation-duration:15s;animation-delay:0.8s"></div>
        <div class="splash-shape" style="left:82%;bottom:-150px;width:95px;height:95px;animation-duration:20s;animation-delay:2.8s"></div>
        <div class="splash-shape" style="left:48%;bottom:-150px;width:65px;height:65px;animation-duration:17s;animation-delay:4.2s"></div>
        <div class="splash-shape" style="left:20%;bottom:-150px;width:85px;height:85px;animation-duration:19s;animation-delay:1.8s"></div>
        <div class="splash-shape" style="left:55%;bottom:-150px;width:105px;height:105px;animation-duration:22s;animation-delay:3.2s"></div>

        <!-- Bottom gradient overlay -->
        <div class="splash-gradient-overlay"></div>

        <!-- Logo + loading bar -->
        <div class="splash-content">
            <h1 class="splash-wordmark">
                <span class="brand-realty">Realty</span><span class="brand-link">Link</span><span class="gold">PH</span>
            </h1>

            <div class="splash-bar-track">
                <div class="splash-bar-shuttle"></div>
            </div>

            <p class="splash-text">Loading Application</p>
        </div>
    </div>

    <div id="app"></div>

    <script>
        // Fade out splash screen when Vue app is ready
        (function() {
            var splash = document.getElementById('splash-loader');
            if (!splash) return;

            // Watch for Vue to mount (app div gets content)
            var observer = new MutationObserver(function() {
                var app = document.getElementById('app');
                if (app && app.children.length > 0) {
                    splash.classList.add('hide');
                    setTimeout(function() { splash.remove(); }, 600);
                    observer.disconnect();
                }
            });
            observer.observe(document.getElementById('app'), { childList: true });

            // Fallback: remove after 8s max
            setTimeout(function() {
                if (splash.parentNode) {
                    splash.classList.add('hide');
                    setTimeout(function() { splash.remove(); }, 600);
                }
            }, 8000);
        })();
    </script>
</body>
</html>
