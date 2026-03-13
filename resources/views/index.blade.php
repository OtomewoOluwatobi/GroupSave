<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GroupSave — Save Together, Grow Together</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=neue-montreal:400,500,600,700,800&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        /* ══════════════════════════════════════
       THEME TOKENS
    ══════════════════════════════════════ */
        /*
      BRAND PALETTE
      #120f22  deep near-black   — primary dark / text
      #7c5ce8  violet            — secondary accent
      #c8a45a  gold              — primary accent
      #e0c48a  light gold        — light accent
      #16a34a  green             — highlight / CTA
    */
        :root {
            --gold: #c8a45a;
            --gold-light: #e0c48a;
            --gold-glow: rgba(200, 164, 90, 0.25);
            --violet-bright: #7c5ce8;
            --green-bright: #16a34a;
            --blue-mid: #7c5ce8;
            --red-accent: #dc2626;
            --navy-dark: #120f22;
            --trans: all 0.45s cubic-bezier(.4, 0, .2, 1);
        }

        /* DARK */
        :root {
            --bg: #120f22;
            --bg-alt: rgba(255, 255, 255, 0.015);
            --surface: rgba(255, 255, 255, 0.04);
            --surface-2: rgba(255, 255, 255, 0.025);
            --border: rgba(255, 255, 255, 0.07);
            --border-hi: rgba(255, 255, 255, 0.12);
            --text: #f2ede4;
            --text-muted: rgba(242, 237, 228, 0.5);
            --text-dim: rgba(242, 237, 228, 0.22);
            --nav-bg: rgba(18, 15, 34, 0.85);
            --card-bg: rgba(18, 15, 34, 0.90);
            --stat-div: rgba(255, 255, 255, 0.05);
            --how-bg: rgba(255, 255, 255, 0.015);
            --how-border: rgba(255, 255, 255, 0.05);
            --av-border: rgba(18, 15, 34, 0.9);
            --footer-bg: rgba(18, 15, 34, 0.45);
            --footer-border: rgba(255, 255, 255, 0.06);

            --shadow-card: rgba(0, 0, 0, 0.5);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow-x: hidden;
            cursor: none;
            transition: background 0.45s ease, color 0.45s ease;
        }

        /* ── CUSTOM CURSOR ── */
        #cursor {
            position: fixed;
            z-index: 9999;
            width: 10px;
            height: 10px;
            background: var(--gold);
            border-radius: 50%;
            pointer-events: none;
            mix-blend-mode: multiply;
            transition: transform 0.1s;
        }

        #cursor-ring {
            position: fixed;
            z-index: 9998;
            width: 38px;
            height: 38px;
            border: 1.5px solid rgba(200, 164, 90, 0.6);
            border-radius: 50%;
            pointer-events: none;
            transition: width 0.3s, height 0.3s, border-color 0.3s;
        }

        /* ── CANVAS ── */
        #particle-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            transition: opacity 0.5s;
        }

        /* grain */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            transition: opacity 0.45s;
        }

        /* ══════════════════════════════════════
       THEME TOGGLE
    ══════════════════════════════════════ */







        /* Sun/moon icons inside thumb */


        /* ── NAV ── */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 64px;
            transition: var(--trans), padding 0.4s;
        }

        nav.scrolled {
            background: var(--nav-bg);
            backdrop-filter: blur(22px);
            border-bottom: 1px solid rgba(200, 164, 90, 0.13);
            padding: 15px 64px;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 48px;
        }

        .logo {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--text);
            text-decoration: none;
            transition: color 0.4s;
        }

        .logo span {
            color: var(--gold);
        }

        .nav-links {
            display: flex;
            gap: 38px;
            list-style: none;
        }

        .nav-links a {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-decoration: none;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: color 0.25s;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 1px;
            background: var(--gold);
            transition: width 0.3s;
        }

        .nav-links a:hover {
            color: var(--text);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .nav-btn {
            position: relative;
            overflow: hidden;
            background: transparent;
            border: 1px solid rgba(200, 164, 90, 0.45);
            color: var(--gold);
            padding: 11px 26px;
            border-radius: 40px;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            text-decoration: none;
            transition: color 0.3s;
        }

        .nav-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--gold);
            transform: translateY(101%);
            transition: transform 0.3s ease;
        }

        .nav-btn:hover {
            color: var(--bg);
        }

        .nav-btn:hover::before {
            transform: translateY(0);
        }

        .nav-btn span {
            position: relative;
            z-index: 1;
        }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 120px 64px 80px;
            position: relative;
            z-index: 2;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            animation: morphBlob 10s ease-in-out infinite;
            transition: opacity 0.6s;
        }

        .blob-1 {
            width: 650px;
            height: 650px;
            background: radial-gradient(circle, rgba(124, 92, 232, 0.22), transparent 70%);
            top: -150px;
            right: -150px;
        }

        .blob-2 {
            width: 450px;
            height: 450px;
            background: radial-gradient(circle, rgba(200, 164, 90, 0.18), transparent 70%);
            bottom: -50px;
            left: 5%;
            animation-delay: -5s;
        }

        .blob-3 {
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(42, 82, 160, 0.14), transparent 70%);
            top: 45%;
            right: 28%;
            animation-delay: -2.5s;
        }

        @@keyframes morphBlob {

            0%,
            100% {
                border-radius: 60% 40% 70% 30%/50% 60% 40% 50%;
                transform: scale(1);
            }

            33% {
                border-radius: 40% 60% 30% 70%/60% 40% 60% 40%;
                transform: scale(1.06);
            }

            66% {
                border-radius: 70% 30% 50% 50%/40% 70% 30% 60%;
                transform: scale(0.94);
            }
        }

        .hero-inner {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
        }

        .hero-label {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(200, 164, 90, 0.08);
            border: 1px solid rgba(200, 164, 90, 0.28);
            color: var(--gold);
            padding: 8px 18px;
            border-radius: 40px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 32px;
            opacity: 0;
            animation: fadeSlideRight 0.7s 0.3s ease forwards;
        }

        .dot-pulse {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--gold);
            animation: pulseDot 2s infinite;
        }

        @@keyframes pulseDot {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(200, 164, 90, 0.5);
            }

            50% {
                box-shadow: 0 0 0 6px transparent;
            }
        }

        .hero h1 {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: clamp(52px, 6.5vw, 90px);
            font-weight: 700;
            line-height: 1.0;
            letter-spacing: -1px;
            margin-bottom: 28px;
            overflow: hidden;
            color: var(--text);
            transition: color 0.45s;
        }

        .hero-line {
            display: block;
            opacity: 0;
            transform: translateY(70px);
            animation: lineReveal 0.9s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .hero-line:nth-child(2) {
            animation-delay: 0.12s;
        }

        .hero-line:nth-child(3) {
            animation-delay: 0.24s;
            color: var(--gold);
            font-style: italic;
        }

        @@keyframes lineReveal {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hero-sub {
            font-size: 17px;
            color: var(--text-muted);
            line-height: 1.75;
            max-width: 460px;
            margin-bottom: 48px;
            font-weight: 400;
            opacity: 0;
            animation: fadeSlideRight 0.8s 0.6s ease forwards;
            transition: color 0.45s;
        }

        @@keyframes fadeSlideRight {
            from {
                opacity: 0;
                transform: translateX(-24px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .hero-actions {
            display: flex;
            gap: 18px;
            align-items: center;
            opacity: 0;
            animation: fadeSlideRight 0.8s 0.8s ease forwards;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: var(--gold);
            color: #120f22;
            padding: 17px 36px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.3px;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            transition: transform 0.25s, box-shadow 0.25s;
            box-shadow: 0 4px 20px var(--gold-glow);
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.22);
            transform: translateX(-100%) skewX(-15deg);
            transition: transform 0.5s;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 40px var(--gold-glow);
        }

        .btn-primary:hover::after {
            transform: translateX(150%) skewX(-15deg);
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            padding: 17px 8px;
            transition: color 0.25s, gap 0.25s;
        }

        .btn-ghost:hover {
            color: var(--text);
            gap: 16px;
        }

        /* ── HERO CARD ── */
        .hero-visual {
            position: relative;
            opacity: 0;
            animation: heroCardIn 1s 0.5s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        @@keyframes heroCardIn {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.93);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .card-main {
            background: var(--surface);
            backdrop-filter: blur(30px);
            border: 1px solid var(--border-hi);
            border-radius: 28px;
            padding: 34px;
            position: relative;
            overflow: hidden;
            transition: background 0.45s, border-color 0.45s;
            box-shadow: 0 20px 60px var(--shadow-card);
        }

        .card-main::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(200, 164, 90, 0.7), transparent);
        }

        .card-inner-glow {
            position: absolute;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(124, 92, 232, 0.2), transparent);
            top: -80px;
            right: -80px;
            pointer-events: none;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .card-tag {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            transition: color 0.45s;
        }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 700;
            color: var(--green-bright);
        }

        .live-badge::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green-bright);
            animation: pulseDot 2s infinite;
        }

        .card-name {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 22px;
            color: var(--text);
            transition: color 0.45s;
        }

        .card-currency {
            font-size: 20px;
            color: var(--gold);
        }

        .card-num {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 56px;
            font-weight: 700;
            line-height: 1;
            color: var(--text);
            transition: color 0.45s;
        }

        .card-of {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 4px;
            margin-bottom: 22px;
            transition: color 0.45s;
        }

        .prog-wrap {
            background: var(--border);
            border-radius: 100px;
            height: 8px;
            overflow: hidden;
            margin-bottom: 8px;
            transition: background 0.45s;
        }

        .prog-fill {
            height: 100%;
            border-radius: 100px;
            background: linear-gradient(90deg, var(--violet-bright), var(--gold));
            width: 0;
            transition: width 2s 1.5s cubic-bezier(.16, 1, .3, 1);
            position: relative;
        }

        .prog-fill.active {
            width: 68%;
        }

        .prog-fill::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 60px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.35));
            animation: shimmer 2.5s 3s ease-in-out infinite;
        }

        @@keyframes shimmer {
            0% {
                transform: translateX(-100px);
                opacity: 0;
            }

            40% {
                opacity: 1;
            }

            100% {
                transform: translateX(100px);
                opacity: 0;
            }
        }

        .prog-labels {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 22px;
            transition: color 0.45s;
        }

        .prog-labels span:first-child {
            color: var(--gold);
        }

        .members-strip {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            transition: border-color 0.45s;
        }

        .avatars {
            display: flex;
        }

        .av {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid var(--av-border);
            margin-left: -9px;
            font-size: 11px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.25s, border-color 0.45s;
            cursor: pointer;
        }

        .av:first-child {
            margin-left: 0;
        }

        .av:hover {
            transform: translateY(-5px) scale(1.15);
            z-index: 5;
        }

        .av-a {
            background: #120f22;
        }

        .av-b {
            background: #7c5ce8;
        }

        .av-c {
            background: var(--gold);
            color: #120f22;
        }

        .av-d {
            background: #16a34a;
        }

        .av-e {
            background: var(--violet-bright);
        }

        .members-info {
            font-size: 12px;
            color: var(--text-muted);
            transition: color 0.45s;
        }

        .members-info strong {
            color: var(--green-bright);
        }

        /* Floating mini cards */
        .mini-card {
            position: absolute;
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-hi);
            border-radius: 16px;
            padding: 14px 18px;
            white-space: nowrap;
            transition: background 0.45s, border-color 0.45s;
        }

        .mc-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 4px;
            transition: color 0.45s;
        }

        .mc-val {
            font-size: 18px;
            font-weight: 800;
        }

        .mc-sub {
            font-size: 11px;
            color: var(--text-muted);
            transition: color 0.45s;
        }

        .mc-1 {
            top: -30px;
            right: -44px;
            animation: floatA 3.5s ease-in-out infinite;
        }

        .mc-2 {
            bottom: 16px;
            left: -52px;
            animation: floatB 4.2s ease-in-out infinite;
        }

        .mc-3 {
            top: 45%;
            right: -56px;
            animation: floatA 5s 1s ease-in-out infinite;
        }

        @@keyframes floatA {

            0%,
            100% {
                transform: translateY(0) rotate(-1deg);
            }

            50% {
                transform: translateY(-10px) rotate(1deg);
            }
        }

        @@keyframes floatB {

            0%,
            100% {
                transform: translateY(0) rotate(1deg);
            }

            50% {
                transform: translateY(-14px) rotate(-1deg);
            }
        }

        .notif-pop {
            position: absolute;
            top: -18px;
            left: -18px;
            background: var(--card-bg);
            border: 1px solid rgba(218, 69, 63, 0.35);
            border-radius: 14px;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 0;
            animation: popIn 0.6s 2.8s cubic-bezier(.175, .885, .32, 1.4) forwards, floatB 5s 3.4s ease-in-out infinite;
            white-space: nowrap;
            transition: background 0.45s;
        }

        @@keyframes popIn {
            from {
                opacity: 0;
                transform: scale(0.6) translateY(10px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .notif-icon {
            font-size: 18px;
        }

        .notif-text {
            font-size: 12px;
            font-weight: 700;
            color: var(--text);
            transition: color 0.45s;
        }

        .notif-time {
            font-size: 10px;
            color: var(--text-muted);
            transition: color 0.45s;
        }

        /* ── LIVE ACTIVITY FEED ── */
        .activity-section {
            position: relative;
            z-index: 2;
            padding: 80px 64px;
            overflow: hidden;
        }

        .activity-section::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(200, 164, 90, 0.04) 0%, transparent 50%, rgba(124, 92, 232, 0.04) 100%);
            transition: opacity 0.45s;
        }

        .activity-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1.6fr;
            gap: 64px;
            align-items: center;
        }

        .activity-copy {}

        .activity-eyebrow {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 16px;
        }

        .activity-title {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: clamp(30px, 3.5vw, 46px);
            font-weight: 700;
            line-height: 1.1;
            color: var(--text);
            margin-bottom: 16px;
            transition: color 0.45s;
        }

        .activity-sub {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.7;
            transition: color 0.45s;
            margin-bottom: 28px;
        }

        .activity-stat {
            display: flex;
            gap: 32px;
        }

        .astat {}

        .astat-num {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--gold);
            line-height: 1;
        }

        .astat-label {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: color 0.45s;
        }

        /* The feed itself */
        .feed-wrap {
            position: relative;
            height: 340px;
            overflow: hidden;
        }

        .feed-wrap::before,
        .feed-wrap::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            z-index: 2;
            pointer-events: none;
        }

        .feed-wrap::before {
            top: 0;
            height: 60px;
            background: linear-gradient(to bottom, var(--bg), transparent);
            transition: background 0.45s;
        }

        .feed-wrap::after {
            bottom: 0;
            height: 80px;
            background: linear-gradient(to top, var(--bg), transparent);
            transition: background 0.45s;
        }

        .feed-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            position: absolute;
            width: 100%;
            top: 0;
        }

        .feed-item {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: background 0.45s, border-color 0.45s;
            opacity: 0;
            transform: translateX(30px);
        }

        .feed-item.feed-in {
            animation: feedSlideIn 0.55s cubic-bezier(.16, 1, .3, 1) forwards;
        }

        .feed-item.feed-out {
            animation: feedSlideOut 0.4s ease forwards;
        }

        @@keyframes feedSlideIn {
            from {
                opacity: 0;
                transform: translateX(30px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1);
            }
        }

        @@keyframes feedSlideOut {
            from {
                opacity: 1;
                transform: translateY(0) scale(1);
                max-height: 80px;
                margin-bottom: 0;
            }

            to {
                opacity: 0;
                transform: translateY(-8px) scale(0.97);
                max-height: 0;
                margin-bottom: -12px;
            }
        }

        .feed-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 800;
            color: #fff;
            flex-shrink: 0;
        }

        .feed-body {
            flex: 1;
            min-width: 0;
        }

        .feed-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 2px;
            transition: color 0.45s;
        }

        .feed-desc {
            font-size: 12px;
            color: var(--text-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            transition: color 0.45s;
        }

        .feed-right {
            text-align: right;
            flex-shrink: 0;
        }

        .feed-amount {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 20px;
            font-weight: 700;
            line-height: 1;
        }

        .feed-time {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 3px;
            font-weight: 600;
            transition: color 0.45s;
        }

        .feed-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 20px;
            flex-shrink: 0;
        }

        .badge-paid {
            background: rgba(200, 164, 90, 0.15);
            color: #120f22;
            border: 1px solid rgba(200, 164, 90, 0.4);
        }

        .badge-joined {
            background: rgba(200, 164, 90, 0.12);
            color: var(--gold);
            border: 1px solid rgba(200, 164, 90, 0.25);
        }

        .badge-goal {
            background: rgba(124, 92, 232, 0.12);
            color: #c8a45a;
            border: 1px solid rgba(124, 92, 232, 0.3);
        }

        .badge-new {
            background: rgba(42, 82, 160, 0.12);
            color: #7c5ce8;
            border: 1px solid rgba(42, 82, 160, 0.3);
        }

        /* Pulse dot on header */
        .activity-live-dot {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 11px;
            font-weight: 700;
            color: #16a34a;
            margin-bottom: 10px;
        }

        .activity-live-dot::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #16a34a;
            animation: pulseDot 2s infinite;
        }

        /* ── STATS ── */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            position: relative;
            z-index: 2;
        }

        .stat-box {
            padding: 64px 40px;
            border-right: 1px solid var(--stat-div);
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: background 0.3s, border-color 0.45s;
        }

        .stat-box:last-child {
            border-right: none;
        }

        .stat-box::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--gold);
            transition: width 0.4s;
        }

        .stat-box:hover::after {
            width: 60%;
        }

        .stat-box:hover {
            background: rgba(200, 164, 90, 0.04);
        }

        .stat-num {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 60px;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 8px;
            color: var(--text);
            transition: color 0.45s;
        }

        .stat-num .gold {
            color: var(--gold);
        }

        .stat-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-muted);
            transition: color 0.45s;
        }

        /* ── GENERIC REVEAL ── */
        .reveal {
            opacity: 0;
            transform: translateY(36px);
            transition: opacity 0.75s, transform 0.75s;
        }

        .reveal.vis {
            opacity: 1;
            transform: translateY(0);
        }

        /* ── FEATURES ── */
        .features {
            padding: 120px 64px;
            position: relative;
            z-index: 2;
        }

        .eyebrow {
            text-align: center;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 20px;
        }

        .section-h {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: clamp(36px, 5vw, 62px);
            font-weight: 700;
            text-align: center;
            line-height: 1.05;
            letter-spacing: -0.5px;
            margin-bottom: 16px;
            color: var(--text);
            transition: color 0.45s;
        }

        .section-p {
            text-align: center;
            color: var(--text-muted);
            font-size: 16px;
            max-width: 520px;
            margin: 0 auto 80px;
            line-height: 1.75;
            font-weight: 400;
            transition: color 0.45s;
        }

        .bento {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .bento-card {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 36px 32px;
            position: relative;
            overflow: hidden;
            transition: transform 0.4s cubic-bezier(.25, .46, .45, .94), border-color 0.4s, box-shadow 0.4s, background 0.45s;
        }

        .bento-card:hover {
            transform: translateY(-7px);
            border-color: rgba(200, 164, 90, 0.3);
            box-shadow: 0 24px 60px var(--shadow-card), 0 0 40px rgba(200, 164, 90, 0.07);
        }

        .bento-card.wide {
            grid-column: span 2;
        }

        .bento-card::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            background: radial-gradient(circle, rgba(200, 164, 90, 0.1), transparent 70%);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.4s;
            left: var(--mx, 50%);
            top: var(--my, 50%);
            transform: translate(-50%, -50%);
        }

        .bento-card:hover::after {
            opacity: 1;
        }

        .bento-icon {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            background: rgba(200, 164, 90, 0.09);
            border: 1px solid rgba(200, 164, 90, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            margin-bottom: 22px;
            transition: transform 0.35s, background 0.35s;
        }

        .bento-card:hover .bento-icon {
            transform: rotate(-8deg) scale(1.12);
            background: rgba(200, 164, 90, 0.2);
        }

        .bento-title {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--text);
            transition: color 0.45s;
        }

        .bento-text {
            font-size: 14px;
            line-height: 1.75;
            color: var(--text-muted);
            font-weight: 400;
            transition: color 0.45s;
        }

        .bento-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 24px;
        }

        .chip {
            background: var(--bg-alt);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 7px 14px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            transition: all 0.25s;
            cursor: default;
        }

        .chip:hover {
            background: rgba(200, 164, 90, 0.1);
            border-color: rgba(200, 164, 90, 0.3);
            color: var(--gold);
        }

        /* ── HOW IT WORKS ── */
        .how {
            padding: 120px 64px;
            background: var(--how-bg);
            border-top: 1px solid var(--how-border);
            border-bottom: 1px solid var(--how-border);
            position: relative;
            z-index: 2;
            transition: background 0.45s, border-color 0.45s;
        }

        .steps-wrap {
            max-width: 1000px;
            margin: 0 auto;
            position: relative;
        }

        .timeline-line {
            position: absolute;
            left: 39px;
            top: 20px;
            bottom: 20px;
            width: 1px;
            background: linear-gradient(180deg, transparent, rgba(200, 164, 90, 0.3) 15%, rgba(200, 164, 90, 0.3) 85%, transparent);
        }

        .step-row {
            display: flex;
            gap: 48px;
            align-items: flex-start;
            padding: 40px 0;
            border-bottom: 1px solid var(--border);
            opacity: 0;
            transform: translateX(-40px);
            transition: opacity 0.7s, transform 0.7s, border-color 0.45s;
        }

        .step-row.vis {
            opacity: 1;
            transform: translateX(0);
        }

        .step-row:last-child {
            border-bottom: none;
        }

        .step-circle {
            flex-shrink: 0;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(200, 164, 90, 0.07);
            border: 1px solid rgba(200, 164, 90, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            transition: transform 0.35s, background 0.35s;
        }

        .step-row:hover .step-circle {
            transform: scale(1.1) rotate(6deg);
            background: rgba(200, 164, 90, 0.18);
        }

        .step-circle span {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--gold);
        }

        .step-body {
            padding-top: 18px;
        }

        .step-title {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--text);
            transition: color 0.45s;
        }

        .step-text {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.7;
            max-width: 560px;
            transition: color 0.45s;
        }

        .step-badge {
            display: inline-block;
            margin-top: 16px;
            background: rgba(200, 164, 90, 0.07);
            border: 1px solid rgba(200, 164, 90, 0.2);
            color: var(--gold);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 20px;
        }

        /* ── TESTIMONIALS ── */
        .testimonials {
            padding: 120px 64px;
            position: relative;
            z-index: 2;
        }

        .testi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            max-width: 1100px;
            margin: 0 auto;
        }

        .testi-card {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 36px;
            transition: transform 0.4s, border-color 0.4s, background 0.45s;
        }

        .testi-card:hover {
            transform: translateY(-8px);
            border-color: rgba(200, 164, 90, 0.25);
        }

        .stars {
            color: var(--gold);
            font-size: 14px;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        .testi-q {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 18px;
            line-height: 1.65;
            font-style: italic;
            color: var(--text);
            margin-bottom: 28px;
            opacity: 0.85;
            transition: color 0.45s;
        }

        .testi-foot {
            display: flex;
            align-items: center;
            gap: 14px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            transition: border-color 0.45s;
        }

        .tav {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 14px;
            color: #fff;
            flex-shrink: 0;
        }

        .tav-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
            transition: color 0.45s;
        }

        .tav-role {
            font-size: 12px;
            color: var(--text-muted);
            transition: color 0.45s;
        }

        /* ══════════════════════════════════════
       PRICING — ILLUSTRATED CARDS
    ══════════════════════════════════════ */
        .pricing {
            padding: 120px 64px;
            position: relative;
            z-index: 2;
            background: var(--how-bg);
            border-top: 1px solid var(--how-border);
            transition: background 0.45s, border-color 0.45s;
            overflow: hidden;
        }

        /* Background decorative arc */
        .pricing::before {
            content: '';
            position: absolute;
            bottom: -200px;
            left: 50%;
            transform: translateX(-50%);
            width: 900px;
            height: 600px;
            background: radial-gradient(ellipse, rgba(200, 164, 90, 0.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
            max-width: 1100px;
            margin: 0 auto;
            align-items: stretch;
            border: 1px solid var(--border);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 40px 100px var(--shadow-card);
        }

        /* Each card is a column */
        .price-card {
            background: var(--surface-2);
            border-right: 1px solid var(--border);
            padding: 0;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            transition: background 0.45s, border-color 0.45s;
        }

        .price-card:last-child {
            border-right: none;
        }

        /* Hover lift on whole card — subtle */
        .price-card:hover {
            background: var(--surface);
        }

        /* Featured (Growth) — highlighted column */
        .price-card.featured {
            background: linear-gradient(180deg, rgba(200, 164, 90, 0.07) 0%, rgba(124, 92, 232, 0.06) 100%);
            border-right: 1px solid rgba(200, 164, 90, 0.22);
            border-left: 1px solid rgba(200, 164, 90, 0.22);
            z-index: 1;
        }

        .price-card.featured::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
        }

        /* Card body */
        .price-body {
            padding: 32px 32px 36px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        /* Popular badge above card */
        .popular-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--gold);
            color: #120f22;
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 5px 14px;
            border-radius: 0 0 12px 12px;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            white-space: nowrap;
            z-index: 2;
            box-shadow: 0 4px 16px rgba(124, 92, 232, 0.25);
        }

        .price-tier {
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .tier-starter {
            color: var(--green-bright);
        }

        .tier-growth {
            color: var(--gold);
        }

        .tier-ent {
            color: #9ab8b2;
        }

        .price-name {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
            line-height: 1.1;
            transition: color 0.45s;
        }

        .price-tagline {
            font-size: 12px;
            color: var(--text-muted);
            margin-bottom: 24px;
            line-height: 1.5;
            transition: color 0.45s;
        }

        /* Price display */
        .price-display {
            display: flex;
            align-items: flex-end;
            gap: 4px;
            margin-bottom: 6px;
        }

        .price-currency {
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            line-height: 1.6;
            transition: color 0.45s;
        }

        .price-main {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 64px;
            font-weight: 700;
            line-height: 1;
            color: var(--text);
            transition: color 0.45s;
        }

        .price-main.gold {
            color: var(--gold);
        }

        .price-cycle {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 8px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border);
            transition: color 0.45s, border-color 0.45s;
        }

        /* Feature list */
        .price-feats {
            list-style: none;
            margin: 24px 0 28px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .price-feats li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 9px 0;
            font-size: 13px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border);
            transition: color 0.45s, border-color 0.45s;
            line-height: 1.45;
        }

        .price-feats li:last-child {
            border-bottom: none;
        }

        .price-feats li strong {
            color: var(--text);
            font-weight: 700;
            transition: color 0.45s;
        }

        .chk-g {
            color: var(--green-bright);
            font-size: 11px;
            font-weight: 900;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .chk-gold {
            color: var(--gold);
            font-size: 11px;
            font-weight: 900;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .chk-v {
            color: #9ab8b2;
            font-size: 11px;
            font-weight: 900;
            flex-shrink: 0;
            margin-top: 2px;
        }

        /* Note / callout inside card */
        .price-note {
            margin: 0 0 18px;
            font-size: 11.5px;
            line-height: 1.6;
            color: var(--text-muted);
            background: rgba(218, 69, 63, 0.06);
            border: 1px solid rgba(218, 69, 63, 0.15);
            border-radius: 10px;
            padding: 11px 14px;
            transition: color 0.45s, background 0.45s;
        }

        .price-note.gold-note {
            background: rgba(200, 164, 90, 0.07);
            border-color: rgba(200, 164, 90, 0.2);
        }

        .price-for-whom {
            font-size: 12px;
            color: var(--text-muted);
            line-height: 1.6;
            font-style: italic;
            margin-bottom: 20px;
            transition: color 0.45s;
        }

        .price-designed {
            margin-bottom: 20px;
        }

        .price-designed-title {
            font-size: 10px;
            font-weight: 900;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 8px;
            transition: color 0.45s;
        }

        .price-designed-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .price-designed-list li {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
            transition: color 0.45s;
        }

        .price-designed-list li::before {
            content: '→';
            color: #9ab8b2;
            font-size: 10px;
            font-weight: 800;
        }

        .price-tag-enterprise {
            display: inline-block;
            margin-bottom: 8px;
            background: rgba(124, 92, 232, 0.12);
            border: 1px solid rgba(124, 92, 232, 0.25);
            color: #9ab8b2;
            font-size: 9px;
            font-weight: 900;
            letter-spacing: 2px;
            text-transform: uppercase;
            padding: 3px 10px;
            border-radius: 20px;
        }

        .price-ideal {
            display: inline-block;
            margin-bottom: 16px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.3px;
            color: var(--green-bright);
        }

        /* Buttons */
        .price-btn {
            display: block;
            text-align: center;
            padding: 15px 20px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            text-decoration: none;
            transition: all 0.3s;
            margin-top: auto;
            position: relative;
            overflow: hidden;
        }

        .pb-out {
            border: 1px solid var(--border-hi);
            color: var(--text-muted);
            background: transparent;
        }

        .pb-out:hover {
            border-color: var(--gold);
            color: var(--gold);
            background: rgba(200, 164, 90, 0.05);
        }

        .pb-gold {
            background: var(--gold);
            color: #120f22;
            box-shadow: 0 4px 24px rgba(200, 164, 90, 0.4);
        }

        .pb-gold::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(-100%) skewX(-15deg);
            transition: transform 0.5s;
        }

        .pb-gold:hover::after {
            transform: translateX(150%) skewX(-15deg);
        }

        .pb-gold:hover {
            box-shadow: 0 8px 40px rgba(200, 164, 90, 0.5);
        }

        .pb-violet {
            border: 1px solid rgba(124, 92, 232, 0.4);
            color: #9ab8b2;
            background: rgba(124, 92, 232, 0.07);
        }

        .pb-violet:hover {
            background: rgba(124, 92, 232, 0.15);
            border-color: #9ab8b2;
        }

        /* ── PRICE NOTE (starter) ── */
        .price-note {
            margin-top: 16px;
            margin-bottom: 0;
            font-size: 12px;
            line-height: 1.6;
            color: var(--text-muted);
            background: var(--bg-alt);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            transition: color 0.45s, background 0.45s, border-color 0.45s;
        }

        .price-ideal {
            display: inline-block;
            margin-top: 14px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: var(--green-bright);
        }

        .price-for-whom {
            margin-top: 20px;
            margin-bottom: 24px;
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.6;
            font-style: italic;
            transition: color 0.45s;
        }

        .price-designed {
            margin-top: 20px;
        }

        .price-designed-title {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 10px;
            transition: color 0.45s;
        }

        .price-designed-list {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 7px;
            margin-bottom: 24px;
        }

        .price-designed-list li {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
            transition: color 0.45s;
        }

        .price-designed-list li::before {
            content: '→';
            color: var(--violet-bright);
            font-size: 11px;
            font-weight: 800;
        }

        .price-tag-enterprise {
            display: inline-block;
            margin-bottom: 6px;
            background: rgba(124, 92, 232, 0.12);
            border: 1px solid rgba(124, 92, 232, 0.25);
            color: #9ab8b2;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 20px;
        }

        /* ── REWARDS SECTION ── */
        .rewards {
            padding: 100px 64px;
            position: relative;
            z-index: 2;
            border-top: 1px solid var(--how-border);
            transition: border-color 0.45s;
        }

        .rewards-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .rewards-copy {}

        .rewards-title {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: clamp(32px, 4vw, 52px);
            font-weight: 700;
            line-height: 1.1;
            color: var(--text);
            margin-bottom: 18px;
            transition: color 0.45s;
        }

        .rewards-title em {
            color: var(--gold);
            font-style: italic;
        }

        .rewards-sub {
            font-size: 15px;
            color: var(--text-muted);
            line-height: 1.75;
            margin-bottom: 36px;
            transition: color 0.45s;
        }

        .rewards-manifesto {
            font-size: 14px;
            font-weight: 700;
            color: var(--gold);
            border-left: 3px solid var(--gold);
            padding-left: 16px;
            line-height: 1.6;
            font-style: italic;
        }

        .rewards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .reward-pill {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            transition: transform 0.3s, border-color 0.3s, background 0.45s;
        }

        .reward-pill:hover {
            transform: translateY(-4px);
            border-color: rgba(200, 164, 90, 0.3);
        }

        .reward-pill-icon {
            font-size: 22px;
            line-height: 1;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .reward-pill-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            line-height: 1.5;
            transition: color 0.45s;
        }

        .reward-pill-text strong {
            display: block;
            font-size: 14px;
            color: var(--text);
            margin-bottom: 2px;
            transition: color 0.45s;
        }

        /* ── TRUST BANNER ── */
        .trust-banner {
            padding: 80px 64px;
            position: relative;
            z-index: 2;
            background: var(--how-bg);
            border-top: 1px solid var(--how-border);
            border-bottom: 1px solid var(--how-border);
            transition: background 0.45s, border-color 0.45s;
        }

        .trust-inner {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }

        .trust-lock {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(218, 69, 63, 0.1);
            border: 1px solid rgba(218, 69, 63, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: 0 auto 24px;
            animation: lockPulse 3s ease-in-out infinite;
        }

        @@keyframes lockPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(218, 69, 63, 0.2);
            }

            50% {
                box-shadow: 0 0 0 16px transparent;
            }
        }

        .trust-title {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: clamp(28px, 3.5vw, 44px);
            font-weight: 700;
            color: var(--text);
            margin-bottom: 16px;
            transition: color 0.45s;
        }

        .trust-body {
            font-size: 16px;
            color: var(--text-muted);
            line-height: 1.8;
            max-width: 620px;
            margin: 0 auto 40px;
            transition: color 0.45s;
        }

        .trust-pills {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .trust-pill {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 100px;
            padding: 10px 22px;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: border-color 0.3s, color 0.3s, background 0.45s;
        }

        .trust-pill:hover {
            border-color: rgba(218, 69, 63, 0.4);
            color: var(--green-bright);
        }

        .trust-pill-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--green-bright);
            flex-shrink: 0;
        }



        /* ── CTA ── */
        .cta {
            padding: 140px 64px;
            text-align: center;
            position: relative;
            z-index: 2;
            overflow: hidden;
        }

        .cta-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(200, 164, 90, 0.07);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
            animation: ringPulse 5s ease-in-out infinite;
        }

        .cta-ring:nth-child(1) {
            width: 400px;
            height: 400px;
            animation-delay: 0s;
        }

        .cta-ring:nth-child(2) {
            width: 660px;
            height: 660px;
            animation-delay: 1.2s;
        }

        .cta-ring:nth-child(3) {
            width: 920px;
            height: 920px;
            animation-delay: 2.4s;
        }

        @@keyframes ringPulse {

            0%,
            100% {
                opacity: 0.3;
                transform: translate(-50%, -50%) scale(1);
            }

            50% {
                opacity: 0.9;
                transform: translate(-50%, -50%) scale(1.04);
            }
        }

        .cta-inner {
            position: relative;
            z-index: 1;
        }

        .cta h2 {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: clamp(44px, 7vw, 82px);
            font-weight: 700;
            line-height: 1.05;
            margin-bottom: 24px;
            color: var(--text);
            transition: color 0.45s;
        }

        .cta h2 em {
            font-style: italic;
            color: var(--gold);
        }

        .cta-sub {
            font-size: 17px;
            color: var(--text-muted);
            max-width: 440px;
            margin: 0 auto 48px;
            line-height: 1.7;
            transition: color 0.45s;
        }

        .cta-btns {
            display: flex;
            justify-content: center;
            gap: 18px;
        }

        /* ── FOOTER ── */
        footer {
            background: var(--footer-bg);
            border-top: 1px solid var(--footer-border);
            padding: 70px 64px 32px;
            position: relative;
            z-index: 2;
            transition: background 0.45s, border-color 0.45s;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2.2fr 1fr 1fr 1fr;
            gap: 48px;
            margin-bottom: 56px;
        }

        .footer-logo {
            font-family: 'Neue Montreal', 'Helvetica Neue', Arial, sans-serif;
            font-size: 30px;
            font-weight: 700;
            color: var(--text);
            display: block;
            margin-bottom: 16px;
            transition: color 0.45s;
        }

        .footer-logo span {
            color: var(--gold);
        }

        .footer-desc {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.75;
            max-width: 260px;
            transition: color 0.45s;
        }

        .footer-h {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 20px;
            transition: color 0.45s;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            font-size: 14px;
            color: var(--text-dim);
            text-decoration: none;
            transition: color 0.2s, padding-left 0.2s;
        }

        .footer-links a:hover {
            color: var(--gold);
            padding-left: 6px;
        }

        .footer-bottom {
            border-top: 1px solid var(--footer-border);
            padding-top: 28px;
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: var(--text-dim);
            transition: border-color 0.45s, color 0.45s;
        }

        /* magnetic wrapper */
        .mag {
            display: inline-block;
        }

        /* ── THEME FLASH (overlay when toggling) ── */

        @@media (max-width: 1000px) {
        nav {
            padding: 16px 24px;
        }

        nav.scrolled {
            padding: 13px 24px;
        }

        .nav-links {
            display: none;
        }

        .hero {
            padding: 100px 24px 60px;
        }

        .hero-inner {
            grid-template-columns: 1fr;
            gap: 56px;
            text-align: center;
        }

        .hero-actions {
            justify-content: center;
        }

        .hero-sub {
            margin-left: auto;
            margin-right: auto;
        }

        .mc-1,
        .mc-2,
        .mc-3,
        .notif-pop {
            display: none;
        }

        .stats {
            grid-template-columns: 1fr 1fr;
        }

        .stat-box {
            border-bottom: 1px solid var(--stat-div);
        }

        .stat-box:nth-child(2n) {
            border-right: none;
        }

        .bento {
            grid-template-columns: 1fr;
        }

        .bento-card.wide {
            grid-column: span 1;
        }

        .timeline-line {
            display: none;
        }

        .step-row {
            flex-direction: column;
            gap: 20px;
        }

        .testi-grid,
        .pricing-grid {
            grid-template-columns: 1fr;
        }

        .pricing-grid {
            border-radius: 20px;
        }

        .price-card {
            border-right: none;
            border-bottom: 1px solid var(--border);
        }

        .price-card:last-child {
            border-bottom: none;
        }

        .price-card.featured {
            border-left: none;
            border-right: none;
            border-top: 1px solid rgba(200, 164, 90, 0.22);
            border-bottom: 1px solid rgba(200, 164, 90, 0.22);
        }

        .rewards-inner {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .rewards-grid {
            grid-template-columns: 1fr;
        }

        .rewards,
        .trust-banner {
            padding: 60px 24px;
        }

        .price-card.featured {
            transform: none;
        }

        .price-card.featured:hover {
            transform: translateY(-7px);
        }

        .footer-grid {
            grid-template-columns: 1fr 1fr;
        }

        .activity-inner {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .activity-section {
            padding: 60px 24px;
        }

        .feed-wrap {
            height: 280px;
        }

        .features,
        .how,
        .testimonials,
        .pricing,
        .cta {
            padding: 80px 24px;
        }

        footer {
            padding: 60px 24px 28px;
        }

        body {
            cursor: auto;
        }

        #cursor,
        #cursor-ring {
            display: none;
        }
        }

    </style>
</head>

<body>
    <div id="cursor"></div>
    <div id="cursor-ring"></div>
    <canvas id="particle-canvas"></canvas>

    <!-- NAV -->
    <nav id="nav">
        <div class="nav-left">
            <a href="#" class="logo">Group<span>Save</span></a>
            <ul class="nav-links">
                <li><a href="#features">Features</a></li>
                <li><a href="#how">Process</a></li>
                <li><a href="#pricing">Pricing</a></li>
                <li><a href="#testimonials">Reviews</a></li>
            </ul>
        </div>
        <div class="nav-right">
            <!-- THEME TOGGLE -->
            <a href="#" class="nav-btn mag"><span>Start Free</span></a>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
        <div class="hero-inner">
            <div>
                <div class="hero-label">
                    <div class="dot-pulse"></div>Trusted by 10,000+ UK savers
                </div>
                <h1>
                    <span class="hero-line">Save More.</span>
                    <span class="hero-line">Together We</span>
                    <span class="hero-line">Grow Richer.</span>
                </h1>
                <p class="hero-sub">GroupSave brings your circles together — family, friends, colleagues — to save with
                    purpose, transparency, and zero stress.</p>
                <div class="hero-actions">
                    <a href="#" class="btn-primary mag">
                        Start For Free
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7" /></svg>
                    </a>
                    <a href="#how" class="btn-ghost">
                        How it works
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" /></svg>
                    </a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="notif-pop">
                    <div class="notif-icon">🎉</div>
                    <div>
                        <div class="notif-text">Sarah just paid £350</div>
                        <div class="notif-time">2 seconds ago</div>
                    </div>
                </div>
                <div class="mini-card mc-1">
                    <div class="mc-label">This month</div>
                    <div class="mc-val" style="color:#16a34a;">+£2,100</div>
                    <div class="mc-sub">group total</div>
                </div>
                <div class="mini-card mc-2">
                    <div class="mc-label">Goal</div>
                    <div class="mc-val" style="color:var(--gold);">68%</div>
                    <div class="mc-sub">complete</div>
                </div>
                <div class="mini-card mc-3">
                    <div class="mc-label">Members</div>
                    <div class="mc-val">6/6</div>
                    <div class="mc-sub">all paid ✓</div>
                </div>
                <div class="card-main">
                    <div class="card-inner-glow"></div>
                    <div class="card-top">
                        <div class="card-tag">Savings Group</div>
                        <div class="live-badge">Live</div>
                    </div>
                    <div class="card-name">🌍 Family Holiday Fund</div>
                    <div><span class="card-currency">£</span><span class="card-num" id="heroNum">0</span></div>
                    <div class="card-of">of £7,000 target</div>
                    <div class="prog-wrap">
                        <div class="prog-fill" id="progFill"></div>
                    </div>
                    <div class="prog-labels"><span>68% complete</span><span>4 months left</span></div>
                    <div class="members-strip">
                        <div class="avatars">
                            <div class="av av-a">JD</div>
                            <div class="av av-b">SP</div>
                            <div class="av av-c">MK</div>
                            <div class="av av-d">RT</div>
                            <div class="av av-e">+2</div>
                        </div>
                        <div class="members-info"><strong>All 6 paid</strong> this month ✓</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- LIVE ACTIVITY FEED -->
    <section class="activity-section">
        <div class="activity-inner">
            <div class="activity-copy reveal">
                <div class="activity-live-dot">Live right now</div>
                <div class="activity-eyebrow">Real-Time Activity</div>
                <h2 class="activity-title">Your community is<br>saving as you read this</h2>
                <p class="activity-sub">Every second, someone across the UK makes a contribution, hits a goal, or starts
                    a new group. GroupSave never sleeps.</p>
                <div class="activity-stat">
                    <div class="astat">
                        <div class="astat-num">£284</div>
                        <div class="astat-label">avg. contributed today</div>
                    </div>
                    <div class="astat">
                        <div class="astat-num">43</div>
                        <div class="astat-label">groups active right now</div>
                    </div>
                </div>
            </div>
            <div class="feed-wrap reveal" style="transition-delay:.15s">
                <div class="feed-list" id="feedList"></div>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <div class="stats">
        <div class="stat-box">
            <div class="stat-num">£<span class="gold counter" data-target="12">0</span>M+</div>
            <div class="stat-label">Total Savings Managed</div>
        </div>
        <div class="stat-box">
            <div class="stat-num"><span class="counter" data-target="10000">0</span>+</div>
            <div class="stat-label">Active Savers</div>
        </div>
        <div class="stat-box">
            <div class="stat-num"><span class="counter" data-target="3200">0</span>+</div>
            <div class="stat-label">Groups Completed</div>
        </div>
        <div class="stat-box">
            <div class="stat-num"><span class="counter" data-target="98">0</span><span class="gold">%</span></div>
            <div class="stat-label">On-time Payment Rate</div>
        </div>
    </div>

    <!-- FEATURES -->
    <section class="features" id="features">
        <div class="eyebrow reveal">Why GroupSave</div>
        <h2 class="section-h reveal">Built for serious savers</h2>
        <p class="section-p reveal">Every tool you need, beautifully designed. From creating your first group to
            celebrating your biggest financial win.</p>
        <div class="bento">
            <div class="bento-card wide reveal" style="transition-delay:.05s">
                <div class="bento-icon">👥</div>
                <div class="bento-title">Savings Groups, Your Way</div>
                <div class="bento-text">Create a circle with anyone you trust. Define the target, monthly amount, start
                    date, and payout day. Invite members instantly — even new users get onboarded automatically with
                    login credentials.</div>
                <div class="bento-chips">
                    <div class="chip">Custom targets</div>
                    <div class="chip">Flexible dates</div>
                    <div class="chip">Instant invites</div>
                </div>
            </div>
            <div class="bento-card reveal" style="transition-delay:.1s">
                <div class="bento-icon">📊</div>
                <div class="bento-title">Live Progress Tracking</div>
                <div class="bento-text">Every member sees contributions and group performance in real-time. Full
                    transparency, zero guesswork, total trust.</div>
            </div>
            <div class="bento-card reveal" style="transition-delay:.15s">
                <div class="bento-icon">🔔</div>
                <div class="bento-title">Smart Reminders</div>
                <div class="bento-text">Automated email alerts ensure no one misses a payment. Set it, forget it —
                    GroupSave keeps everyone accountable.</div>
            </div>
            <div class="bento-card reveal" style="transition-delay:.2s">
                <div class="bento-icon">🔒</div>
                <div class="bento-title">Bank-Grade Security</div>
                <div class="bento-text">Enterprise-level encryption protects every transaction, account, and data point.
                    Your money, fully protected.</div>
            </div>
            <div class="bento-card wide reveal" style="transition-delay:.25s">
                <div class="bento-icon">💸</div>
                <div class="bento-title">Seamless Payouts</div>
                <div class="bento-text">When your group hits its goal, funds are distributed on your chosen payout day —
                    automatically, accurately, and on time. No manual transfers, no awkward conversations.</div>
                <div class="bento-chips">
                    <div class="chip">Automated payouts</div>
                    <div class="chip">Custom payout day</div>
                    <div class="chip">Full audit trail</div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="how" id="how">
        <div class="eyebrow reveal">The Process</div>
        <h2 class="section-h reveal">Four steps to your goal</h2>
        <p class="section-p reveal">No complicated setup. No hidden steps. A clear, elegant path from zero to savings
            success.</p>
        <div class="steps-wrap">
            <div class="timeline-line"></div>
            <div class="step-row reveal" style="transition-delay:.05s">
                <div class="step-circle"><span>01</span></div>
                <div class="step-body">
                    <div class="step-title">Create Your Account</div>
                    <div class="step-text">Sign up in under two minutes. Verify your email, complete your profile, and
                        connect your bank account.</div><span class="step-badge">2 minute setup</span>
                </div>
            </div>
            <div class="step-row reveal" style="transition-delay:.15s">
                <div class="step-circle"><span>02</span></div>
                <div class="step-body">
                    <div class="step-title">Launch a Savings Group</div>
                    <div class="step-text">Name your group, set your financial target, decide on monthly contributions,
                        and choose your payout day.</div><span class="step-badge">Full customisation</span>
                </div>
            </div>
            <div class="step-row reveal" style="transition-delay:.25s">
                <div class="step-circle"><span>03</span></div>
                <div class="step-body">
                    <div class="step-title">Invite Your Circle</div>
                    <div class="step-text">Send email invitations. New members receive login credentials and group
                        details automatically — no friction.</div><span class="step-badge">Instant onboarding</span>
                </div>
            </div>
            <div class="step-row reveal" style="transition-delay:.35s">
                <div class="step-circle"><span>04</span></div>
                <div class="step-body">
                    <div class="step-title">Watch Your Savings Grow</div>
                    <div class="step-text">Track contributions month by month, celebrate milestones, and enjoy the
                        fruits of collective discipline.</div><span class="step-badge">Goal achieved 🎉</span>
                </div>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="testimonials" id="testimonials">
        <div class="eyebrow reveal">Real Stories</div>
        <h2 class="section-h reveal">Savers who made it happen</h2>
        <p class="section-p reveal">Thousands of groups across the UK have hit their goals. Here's what they say.</p>
        <div class="testi-grid">
            <div class="testi-card reveal" style="transition-delay:.05s">
                <div class="stars">★★★★★</div>
                <div class="testi-q">"We saved for a family holiday to Portugal in 8 months. I never had to chase anyone
                    — GroupSave kept the whole group accountable automatically."</div>
                <div class="testi-foot">
                    <div class="tav" style="background:#c8a45a;">AN</div>
                    <div>
                        <div class="tav-name">Amara Nwosu</div>
                        <div class="tav-role">Group Leader · Manchester</div>
                    </div>
                </div>
            </div>
            <div class="testi-card reveal" style="transition-delay:.12s">
                <div class="stars">★★★★★</div>
                <div class="testi-q">"Our work team used it for a Christmas party fund. The live tracker kept morale
                    high — watching the number climb every month was genuinely exciting."</div>
                <div class="testi-foot">
                    <div class="tav" style="background:var(--gold);color:#120f22;">DJ</div>
                    <div>
                        <div class="tav-name">Daniel Johnson</div>
                        <div class="tav-role">Member of 3 Groups · Birmingham</div>
                    </div>
                </div>
            </div>
            <div class="testi-card reveal" style="transition-delay:.2s">
                <div class="stars">★★★★★</div>
                <div class="testi-q">"We hit our house deposit goal three months early. The reminders meant no one ever
                    forgot to contribute. Truly life-changing product."</div>
                <div class="testi-foot">
                    <div class="tav" style="background:#1e7a50;">SP</div>
                    <div>
                        <div class="tav-name">Saoirse Patel</div>
                        <div class="tav-role">First-Time Homeowner · London</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- PRICING -->
    <section class="pricing" id="pricing">
        <div class="eyebrow reveal">Simple, Transparent Pricing</div>
        <h2 class="section-h reveal">Run Your Ajo With Confidence</h2>
        <p class="section-p reveal">Whether you're just getting started or managing multiple groups, GroupSave
            gives you the tools to stay organised, transparent, and trusted — with no hidden fees.</p>

        <div class="pricing-grid reveal" style="transition-delay:.1s">

            <!-- ── STARTER ── -->
            <div class="price-card">

                <div class="price-body">
                    <div class="price-tier tier-starter">Starter</div>
                    <div class="price-name">Begin Growing</div>
                    <div class="price-tagline">Perfect for small friend groups testing the platform</div>

                    <div class="price-display">
                        <span class="price-currency">£</span>
                        <span class="price-main">0</span>
                    </div>
                    <div class="price-cycle">Free, forever</div>

                    <ul class="price-feats">
                        <li><span class="chk-g">✦</span> 1 active savings group</li>
                        <li><span class="chk-g">✦</span> Up to 5 members</li>
                        <li><span class="chk-g">✦</span> Basic contribution tracking</li>
                        <li><span class="chk-g">✦</span> Shared transparent ledger</li>
                        <li><span class="chk-g">✦</span> Email notifications</li>
                        <li><span class="chk-g">✦</span> Reward-based unlocks</li>
                    </ul>

                    <div class="price-note">
                        Unlock more groups by <strong>earning points</strong> or watching short rewarded ads — no card
                        needed.
                    </div>
                    <span class="price-ideal">👉 Great for testing with your circle</span>

                    <a href="#" class="price-btn pb-out mag" style="margin-top:20px;">Get Started Free</a>
                </div>
            </div>

            <!-- ── GROWTH ── -->
            <div class="price-card featured">
                <div class="popular-badge">⭐ Most Popular</div>

                <div class="price-body">
                    <div class="price-tier tier-growth">Growth</div>
                    <div class="price-name">Scale Your Ajo</div>
                    <div class="price-tagline">For serious, committed savers who run multiple groups</div>

                    <div class="price-display">
                        <span class="price-currency">£</span>
                        <span class="price-main gold">4.99</span>
                    </div>
                    <div class="price-cycle">per month · cancel anytime</div>

                    <ul class="price-feats">
                        <li><span class="chk-gold">✦</span> Everything in Starter</li>
                        <li><span class="chk-gold">✦</span> <strong>Unlimited</strong> groups</li>
                        <li><span class="chk-gold">✦</span> Up to 20 members per group</li>
                        <li><span class="chk-gold">✦</span> Smart automated reminders</li>
                        <li><span class="chk-gold">✦</span> Advanced analytics dashboard</li>
                        <li><span class="chk-gold">✦</span> Detailed trust score insights</li>
                        <li><span class="chk-gold">✦</span> Export reports (PDF / CSV)</li>
                        <li><span class="chk-gold">✦</span> <strong style="color:var(--green-bright);">Zero ads,
                                always</strong></li>
                        <li><span class="chk-gold">✦</span> Priority support</li>
                    </ul>

                    <div class="price-note gold-note" style="margin-bottom:24px;">
                        Removes all friction. Ideal if you coordinate multiple groups or want a fully professional
                        experience.
                    </div>

                    <a href="#" class="price-btn pb-gold mag" style="margin-top:8px;">Start Growth Plan</a>
                </div>
            </div>

            <!-- ── ENTERPRISE ── -->
            <div class="price-card">

                <div class="price-body">
                    <span class="price-tag-enterprise">Premium · Annual</span>
                    <div class="price-tier tier-ent" style="margin-top:8px;">Enterprise</div>
                    <div class="price-name">Lead Your Community</div>
                    <div class="price-tagline">For organisations, churches, and serious community leaders</div>

                    <div class="price-display">
                        <span class="price-currency">£</span>
                        <span class="price-main">199</span>
                    </div>
                    <div class="price-cycle">per year · best value</div>

                    <ul class="price-feats">
                        <li><span class="chk-v">✦</span> Everything in Growth</li>
                        <li><span class="chk-v">✦</span> <strong>Unlimited</strong> members</li>
                        <li><span class="chk-v">✦</span> Custom branding</li>
                        <li><span class="chk-v">✦</span> Organisation-wide dashboard</li>
                        <li><span class="chk-v">✦</span> Multi-group oversight tools</li>
                        <li><span class="chk-v">✦</span> Dedicated account manager</li>

                    </ul>

                    <div class="price-designed">
                        <div class="price-designed-title">Built for</div>
                        <ul class="price-designed-list">
                            <li>Community associations</li>
                            <li>Churches &amp; cultural organisations</li>
                            <li>Migrant support groups</li>
                            <li>Savings networks &amp; cooperatives</li>
                        </ul>
                    </div>

                    <a href="#" class="price-btn pb-violet mag">Contact Sales</a>
                </div>
            </div>

        </div>
    </section>

    <!-- REWARDS -->
    <section class="rewards" id="rewards">
        <div class="rewards-inner">
            <div class="rewards-copy reveal">
                <div class="eyebrow" style="text-align:left;margin-bottom:16px;">Community Rewards</div>
                <h2 class="rewards-title">🎁 Earn Rewards<br><em>While You Save</em></h2>
                <p class="rewards-sub">With our community rewards system, you grow the platform and the platform grows
                    with you. Every referral, every completed savings cycle, every milestone — rewarded.</p>
                <p class="rewards-manifesto">We believe in rewarding community growth — not punishing it.</p>
            </div>
            <div class="rewards-grid reveal" style="transition-delay:.15s">
                <div class="reward-pill">
                    <div class="reward-pill-icon">🤝</div>
                    <div class="reward-pill-text"><strong>Referral Points</strong>Earn points every time a friend joins
                        through your link</div>
                </div>
                <div class="reward-pill">
                    <div class="reward-pill-icon">🏆</div>
                    <div class="reward-pill-text"><strong>Cycle Completion</strong>Earn points for completing full
                        savings cycles</div>
                </div>
                <div class="reward-pill">
                    <div class="reward-pill-icon">🔓</div>
                    <div class="reward-pill-text"><strong>Unlock Features</strong>Use points to unlock extra
                        capabilities on demand</div>
                </div>
                <div class="reward-pill">
                    <div class="reward-pill-icon">📈</div>
                    <div class="reward-pill-text"><strong>Increase Limits</strong>Temporarily expand group limits with
                        earned rewards</div>
                </div>
                <div class="reward-pill">
                    <div class="reward-pill-icon">🚫</div>
                    <div class="reward-pill-text"><strong>Ad-Free Periods</strong>Enjoy short ad-free windows as a
                        community bonus</div>
                </div>
                <div class="reward-pill">
                    <div class="reward-pill-icon">🌍</div>
                    <div class="reward-pill-text"><strong>Community Milestones</strong>Collective achievements unlock
                        group-wide rewards</div>
                </div>
            </div>
        </div>
    </section>

    <!-- TRUST BANNER -->
    <section class="trust-banner">
        <div class="trust-inner reveal">
            <div class="trust-lock">🔒</div>
            <h2 class="trust-title">Built for Trust, By Design</h2>
            <p class="trust-body">GroupSave does <strong>not</strong> hold or process money. All contributions
                happen directly between members, outside the platform. We simply provide a secure, transparent, shared
                ledger so your community stays organised and dispute-free.</p>
            <div class="trust-pills">
                <div class="trust-pill"><span class="trust-pill-dot"></span>No money handling</div>
                <div class="trust-pill"><span class="trust-pill-dot"></span>Shared transparent ledger</div>
                <div class="trust-pill"><span class="trust-pill-dot"></span>Dispute-free record keeping</div>
                <div class="trust-pill"><span class="trust-pill-dot"></span>Community-first design</div>
                <div class="trust-pill"><span class="trust-pill-dot"></span>End-to-end encrypted</div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta">
        <div class="cta-ring"></div>
        <div class="cta-ring"></div>
        <div class="cta-ring"></div>
        <div class="cta-inner">
            <div class="eyebrow reveal">🚀 Ready to Simplify Your Ajo?</div>
            <h2 class="reveal">Transparency.<br><em>Accountability.</em><br>Community Trust.</h2>
            <p class="cta-sub reveal">Start free in minutes. Upgrade when your community grows. No credit card required
                — just bring your circle.</p>
            <div class="cta-btns reveal">
                <a href="#" class="btn-primary mag">
                    Start Free — No Card Needed
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7" /></svg>
                </a>
                <a href="#features" class="btn-ghost">Explore features</a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <div class="footer-grid">
            <div>
                <span class="footer-logo">Group<span>Save</span></span>
                <p class="footer-desc">Building a community of disciplined, collaborative savers across the United
                    Kingdom since 2023.</p>
            </div>
            <div>
                <div class="footer-h">Product</div>
                <ul class="footer-links">
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">Security</a></li>
                    <li><a href="#">Roadmap</a></li>
                </ul>
            </div>
            <div>
                <div class="footer-h">Company</div>
                <ul class="footer-links">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            <div>
                <div class="footer-h">Legal</div>
                <ul class="footer-links">
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                    <li><a href="#">Cookie Policy</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom"><span>© 2025 GroupSave Ltd. All rights reserved.</span><span>Made with ♥ in the
                UK</span></div>
    </footer>

    <script>
        // ══════════════════════════════════════
        // CUSTOM CURSOR
        // ══════════════════════════════════════
        const cursor = document.getElementById('cursor');
        const ring = document.getElementById('cursor-ring');
        let mx = 0,
            my = 0,
            rx = 0,
            ry = 0;
        document.addEventListener('mousemove', e => {
            mx = e.clientX;
            my = e.clientY;
            cursor.style.cssText = `left:${mx-5}px;top:${my-5}px`;
        });
        (function animRing() {
            rx += (mx - rx) * 0.13;
            ry += (my - ry) * 0.13;
            ring.style.cssText = `left:${rx-19}px;top:${ry-19}px`;
            requestAnimationFrame(animRing);
        })();
        document.querySelectorAll('a,button').forEach(el => {
            el.addEventListener('mouseenter', () => {
                ring.style.width = '60px';
                ring.style.height = '60px';
                ring.style.borderColor = 'var(--gold)';
            });
            el.addEventListener('mouseleave', () => {
                ring.style.width = '38px';
                ring.style.height = '38px';
                ring.style.borderColor = 'rgba(200,164,90,0.6)';
            });
        });

        // ══════════════════════════════════════
        // MAGNETIC BUTTONS
        // ══════════════════════════════════════
        document.querySelectorAll('.mag').forEach(el => {
            el.addEventListener('mousemove', e => {
                const r = el.getBoundingClientRect();
                el.style.transform =
                    `translate(${(e.clientX-(r.left+r.width/2))*0.28}px,${(e.clientY-(r.top+r.height/2))*0.28}px)`;
                el.style.transition = 'transform 0.1s';
            });
            el.addEventListener('mouseleave', () => {
                el.style.transform = '';
                el.style.transition = 'transform 0.55s cubic-bezier(.25,.46,.45,.94)';
            });
        });

        // ══════════════════════════════════════
        // PARTICLE CANVAS — performance-safe
        // ══════════════════════════════════════
        const canvas = document.getElementById('particle-canvas');
        const ctx = canvas.getContext('2d');

        let resizeTimer;

        function resize() {
            canvas.width = innerWidth;
            canvas.height = innerHeight;
        }
        resize();
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(resize, 200);
        });

        let darkMode = true;

        // 32 particles — visually rich but O(n²) stays at ~500 pairs max
        const parts = Array.from({
            length: 32
        }, () => ({
            x: Math.random() * innerWidth,
            y: Math.random() * innerHeight,
            size: Math.random() * 1.6 + 0.4,
            vx: (Math.random() - 0.5) * 0.28,
            vy: (Math.random() - 0.5) * 0.28,
            op: Math.random() * 0.38 + 0.07,
            gold: Math.random() > 0.5
        }));

        function getColors(isDark) {
            return {
                a: isDark ? [200, 164, 90] : [18, 15, 34],
                b: isDark ? [124, 92, 232] : [18, 15, 34],
                line: isDark ? '200,164,90' : '18,15,34'
            };
        }
        let colors = getColors(darkMode);

        function updateParticleColors(theme) {
            darkMode = theme === 'dark';
            colors = getColors(darkMode);
        }

        // Throttle to 30fps — halves GPU work vs 60fps
        let lastFrame = 0;
        const FPS_CAP = 1000 / 30;

        // Pause when tab is hidden — zero CPU when user switches tabs
        let pageVisible = true;
        document.addEventListener('visibilitychange', () => {
            pageVisible = !document.hidden;
            if (pageVisible) requestAnimationFrame(drawParticles);
        });

        // Use squared distance to skip sqrt() in the hot loop
        const THRESH_SQ = 115 * 115;

        function drawParticles(now = 0) {
            if (!pageVisible) return;
            requestAnimationFrame(drawParticles);
            if (now - lastFrame < FPS_CAP) return;
            lastFrame = now;

            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Move & draw dots
            parts.forEach(p => {
                p.x += p.vx;
                p.y += p.vy;
                if (p.x < 0 || p.x > canvas.width) p.vx *= -1;
                if (p.y < 0 || p.y > canvas.height) p.vy *= -1;
                const c = p.gold ? colors.a : colors.b;
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(${c[0]},${c[1]},${c[2]},${p.op})`;
                ctx.fill();
            });

            // Connection lines — squared distance, no sqrt
            ctx.lineWidth = 0.5;
            for (let i = 0; i < parts.length; i++) {
                for (let j = i + 1; j < parts.length; j++) {
                    const dx = parts[i].x - parts[j].x;
                    const dy = parts[i].y - parts[j].y;
                    const dSq = dx * dx + dy * dy;
                    if (dSq < THRESH_SQ) {
                        ctx.beginPath();
                        ctx.moveTo(parts[i].x, parts[i].y);
                        ctx.lineTo(parts[j].x, parts[j].y);
                        ctx.strokeStyle = `rgba(${colors.line},${0.06 * (1 - dSq / THRESH_SQ)})`;
                        ctx.stroke();
                    }
                }
            }
        }
        requestAnimationFrame(drawParticles);

        // ══════════════════════════════════════
        // NAV SCROLL
        // ══════════════════════════════════════
        window.addEventListener('scroll', () => {
            document.getElementById('nav').classList.toggle('scrolled', scrollY > 60);
        });

        // ══════════════════════════════════════
        // BLOB PARALLAX
        // ══════════════════════════════════════
        window.addEventListener('mousemove', e => {
            const x = (e.clientX / innerWidth - .5) * 30,
                y = (e.clientY / innerHeight - .5) * 22;
            document.querySelector('.blob-1').style.transform = `translate(${x*.45}px,${y*.45}px)`;
            document.querySelector('.blob-2').style.transform = `translate(${-x*.3}px,${-y*.3}px)`;
            document.querySelector('.blob-3').style.transform = `translate(${x*.2}px,${y*.2}px)`;
        });

        // ══════════════════════════════════════
        // SCROLL REVEAL
        // ══════════════════════════════════════
        const revealObs = new IntersectionObserver(entries => {
            entries.forEach(e => {
                if (e.isIntersecting) e.target.classList.add('vis');
            });
        }, {
            threshold: 0.1
        });
        document.querySelectorAll('.reveal,.step-row,.testi-card,.price-card,.reward-pill,.trust-pill').forEach(el =>
            revealObs.observe(el));

        // ══════════════════════════════════════
        // COUNTER ANIMATION
        // ══════════════════════════════════════
        function easeOut(t) {
            return 1 - Math.pow(1 - t, 3);
        }
        const counterObs = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target._done) {
                    entry.target._done = true;
                    const target = +entry.target.dataset.target,
                        dur = 2200,
                        t0 = performance.now();

                    function tick(now) {
                        const p = Math.min((now - t0) / dur, 1),
                            v = Math.round(easeOut(p) * target);
                        entry.target.textContent = target >= 1000 ? v.toLocaleString() : v;
                        if (p < 1) requestAnimationFrame(tick);
                        else entry.target.textContent = target >= 1000 ? target.toLocaleString() :
                            target;
                    }
                    requestAnimationFrame(tick);
                }
            });
        }, {
            threshold: 0.5
        });
        document.querySelectorAll('.counter').forEach(el => counterObs.observe(el));

        // ══════════════════════════════════════
        // HERO NUMBER + PROGRESS BAR
        // ══════════════════════════════════════
        const heroNumEl = document.getElementById('heroNum');
        const progFill = document.getElementById('progFill');
        let heroStarted = false;
        new IntersectionObserver(entries => {
            if (entries[0].isIntersecting && !heroStarted) {
                heroStarted = true;
                progFill.classList.add('active');
                const end = 4800,
                    dur = 2600,
                    t0 = performance.now();

                function go(now) {
                    const p = Math.min((now - t0) / dur, 1);
                    heroNumEl.textContent = Math.round(easeOut(p) * end).toLocaleString();
                    if (p < 1) requestAnimationFrame(go);
                    else heroNumEl.textContent = '4,800';
                }
                requestAnimationFrame(go);
            }
        }, {
            threshold: 0.4
        }).observe(heroNumEl);

        // ══════════════════════════════════════
        // BENTO CARD MOUSE GLOW
        // ══════════════════════════════════════
        document.querySelectorAll('.bento-card').forEach(card => {
            card.addEventListener('mousemove', e => {
                const r = card.getBoundingClientRect();
                card.style.setProperty('--mx', ((e.clientX - r.left) / r.width * 100) + '%');
                card.style.setProperty('--my', ((e.clientY - r.top) / r.height * 100) + '%');
            });
        });

        // ══════════════════════════════════════
        // LIVE ACTIVITY FEED
        // ══════════════════════════════════════
        const feedEvents = [{
                name: 'Amara N.',
                initials: 'AN',
                color: '#120f22',
                desc: 'Paid into "Family Holiday Fund"',
                amount: '+£350',
                badge: 'paid',
                badgeClass: 'badge-paid',
                time: 'just now'
            },
            {
                name: 'Daniel J.',
                initials: 'DJ',
                color: '#7c5ce8',
                desc: 'Created group "Christmas 2025"',
                amount: 'New',
                badge: 'new',
                badgeClass: 'badge-new',
                time: '1m ago'
            },
            {
                name: 'Saoirse P.',
                initials: 'SP',
                color: '#7c5ce8',
                desc: '"House Deposit" hit 100% goal! 🎉',
                amount: '£18,500',
                badge: 'goal',
                badgeClass: 'badge-goal',
                time: '3m ago'
            },
            {
                name: 'Marcus T.',
                initials: 'MT',
                color: '#16a34a',
                desc: 'Joined "Office Party Fund"',
                amount: '',
                badge: 'joined',
                badgeClass: 'badge-joined',
                time: '5m ago'
            },
            {
                name: 'Priya K.',
                initials: 'PK',
                color: '#7c5ce8',
                desc: 'Paid into "World Trip Fund"',
                amount: '+£200',
                badge: 'paid',
                badgeClass: 'badge-paid',
                time: '6m ago'
            },
            {
                name: 'Liam O.',
                initials: 'LO',
                color: '#16a34a',
                desc: 'Created group "Wedding Savings"',
                amount: 'New',
                badge: 'new',
                badgeClass: 'badge-new',
                time: '9m ago'
            },
            {
                name: 'Fatima A.',
                initials: 'FA',
                color: '#f59e0b',
                desc: 'Paid into "Business Starter"',
                amount: '+£500',
                badge: 'paid',
                badgeClass: 'badge-paid',
                time: '11m ago'
            },
            {
                name: 'James R.',
                initials: 'JR',
                color: '#9ab8b2',
                desc: '"Education Fund" reached 50%',
                amount: '£3,150',
                badge: 'goal',
                badgeClass: 'badge-goal',
                time: '14m ago'
            },
            {
                name: 'Yemi A.',
                initials: 'YA',
                color: '#34d399',
                desc: 'Joined "Family Holiday Fund"',
                amount: '',
                badge: 'joined',
                badgeClass: 'badge-joined',
                time: '16m ago'
            },
            {
                name: 'Chloe B.',
                initials: 'CB',
                color: '#f472b6',
                desc: 'Paid into "Christmas 2025"',
                amount: '+£150',
                badge: 'paid',
                badgeClass: 'badge-paid',
                time: '18m ago'
            },
            {
                name: 'Dev S.',
                initials: 'DS',
                color: '#60a5fa',
                desc: 'Created group "Tech Team Fund"',
                amount: 'New',
                badge: 'new',
                badgeClass: 'badge-new',
                time: '21m ago'
            },
            {
                name: 'Ngozi M.',
                initials: 'NM',
                color: '#120f22',
                desc: 'Paid into "Wedding Savings"',
                amount: '+£400',
                badge: 'paid',
                badgeClass: 'badge-paid',
                time: '24m ago'
            },
        ];

        const feedList = document.getElementById('feedList');
        const MAX_VISIBLE = 5;
        let feedQueue = [...feedEvents];
        let activeItems = [];
        let feedRunning = false;

        function buildFeedItem(event) {
            const el = document.createElement('div');
            el.className = 'feed-item';
            el.innerHTML = `
        <div class="feed-avatar" style="background:${event.color}">${event.initials}</div>
        <div class="feed-body">
          <div class="feed-name">${event.name}</div>
          <div class="feed-desc">${event.desc}</div>
        </div>
        <div class="feed-right">
          ${event.amount ? `<div class="feed-amount" style="color:${event.badgeClass==='badge-paid'?'var(--green-bright)':event.badgeClass==='badge-goal'?'var(--gold)':'var(--text)'}">${event.amount}</div>` : ''}
          <div class="feed-badge ${event.badgeClass}">${event.badge}</div>
          <div class="feed-time">${event.time}</div>
        </div>`;
            return el;
        }

        function addFeedItem() {
            if (!feedRunning) return;
            // Rotate queue
            if (feedQueue.length === 0) feedQueue = [...feedEvents];
            const event = feedQueue.shift();
            feedQueue.push(event);

            // If at max, fade out the oldest
            if (activeItems.length >= MAX_VISIBLE) {
                const oldest = activeItems.shift();
                oldest.classList.remove('feed-in');
                oldest.classList.add('feed-out');
                setTimeout(() => oldest.remove(), 400);
            }

            const el = buildFeedItem(event);
            feedList.appendChild(el);
            activeItems.push(el);

            // Trigger slide-in on next frame
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    el.classList.add('feed-in');
                });
            });

            // Update times on all visible items
            const timeLabels = ['just now', '1m ago', '2m ago', '4m ago', '6m ago'];
            activeItems.forEach((item, i) => {
                const t = item.querySelector('.feed-time');
                if (t) t.textContent = timeLabels[activeItems.length - 1 - i] ||
                    `${(activeItems.length - i) * 2}m ago`;
            });

            // Random interval between 1.8s and 3.6s
            const delay = 1800 + Math.random() * 1800;
            setTimeout(addFeedItem, delay);
        }

        // Seed with initial items
        function seedFeed() {
            const initial = feedEvents.slice(0, MAX_VISIBLE);
            initial.forEach((event, i) => {
                const el = buildFeedItem(event);
                feedList.appendChild(el);
                activeItems.push(el);
                setTimeout(() => {
                    el.classList.add('feed-in');
                }, i * 120);
            });
            feedQueue = feedEvents.slice(MAX_VISIBLE);
            feedRunning = true;
            setTimeout(addFeedItem, 2800);
        }

        // Start when feed section is visible
        const feedObs = new IntersectionObserver(entries => {
            if (entries[0].isIntersecting && !feedRunning) seedFeed();
        }, {
            threshold: 0.3
        });
        feedObs.observe(document.querySelector('.feed-wrap'));

    </script>
</body>

</html>
