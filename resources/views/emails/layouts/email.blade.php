{{--
|--------------------------------------------------------------------------
| Email Master Layout
| resources/views/emails/layouts/email.blade.php
|--------------------------------------------------------------------------
--}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('subject', config('app.name'))</title>
  <style>
    body,table,td,p,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;}
    body{margin:0;padding:0;background:#f2ede4;font-family:'Helvetica Neue',Arial,sans-serif;color:#120f22;}
    /*
      LIGHT MODE PALETTE
      --bg        #f2ede4   warm parchment
      --surface   #ffffff   card white
      --border    #e5e0d6   warm border
      --text      #120f22   deep near-black
      --muted     #6b6578   muted
      --gold      #c8a45a   brand gold
      --gold-dark #a07c35   gold text
      --green     #16a34a   success
      --violet    #7c5ce8   brand violet
    */
    .wrap{width:100%;background:#f2ede4;padding:48px 16px;}
    .box{max-width:600px;margin:0 auto;background:#fff;border-radius:20px;overflow:hidden;border:1px solid #e5e0d6;box-shadow:0 4px 32px rgba(61,43,142,0.08);}
    .hd{background:linear-gradient(135deg,#120f22,#1e1a38);padding:40px 48px 36px;position:relative;overflow:hidden;}
    .hd::before{content:'';position:absolute;top:-60px;right:-60px;width:200px;height:200px;background:radial-gradient(circle,rgba(200,164,90,0.2),transparent 65%);}
    .hd::after{content:'';position:absolute;bottom:-40px;left:-40px;width:160px;height:160px;background:radial-gradient(circle,rgba(124,92,232,0.12),transparent 65%);}
    .logo{font-family:Georgia,serif;font-size:28px;font-weight:700;color:#fff;text-decoration:none;position:relative;z-index:1;letter-spacing:-0.3px;}
    .logo span{color:#c8a45a;}
    .eyebrow{font-size:11px;font-weight:600;letter-spacing:2.5px;text-transform:uppercase;color:rgba(200,164,90,0.65);margin-top:6px;position:relative;z-index:1;}
    .banner{background:#faf8f4;border-bottom:1px solid #e5e0d6;padding:32px 48px 28px;text-align:center;}
    .ic{width:72px;height:72px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:30px;margin-bottom:16px;}
    .ic-gold{background:rgba(200,164,90,0.12);border:1.5px solid rgba(200,164,90,0.3);}
    .ic-green{background:rgba(22,163,74,0.1);border:1.5px solid rgba(22,163,74,0.25);}
    .ic-violet{background:rgba(124,92,232,0.1);border:1.5px solid rgba(124,92,232,0.25);}
    .ic-red{background:rgba(220,38,38,0.08);border:1.5px solid rgba(220,38,38,0.2);}
    .ic-dark{background:rgba(18,15,34,0.08);border:1.5px solid rgba(18,15,34,0.15);}
    .etitle{font-family:Georgia,serif;font-size:24px;font-weight:700;color:#120f22;margin:0 0 6px;line-height:1.2;}
    .esub{font-size:14px;color:#6b6578;margin:0;}
    .bd{padding:40px 48px;}
    p{font-size:15px;color:#3d3550;line-height:1.75;margin:0 0 16px;}
    .icard{background:#faf8f4;border:1px solid #e5e0d6;border-left:4px solid #c8a45a;border-radius:12px;padding:20px 24px;margin:24px 0;}
    .ilabel{font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#c8a45a;margin-bottom:12px;}
    .irow{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #ede8df;font-size:14px;}
    .irow:last-child{border-bottom:none;}
    .ilb{color:#6b6578;font-weight:500;}.ivl{color:#120f22;font-weight:600;}
    .ivl.gold{color:#a07c35;}.ivl.green{color:#16a34a;}
    .cblock{background:#f2ede4;border:2px dashed #c8a45a;border-radius:12px;padding:28px;text-align:center;margin:24px 0;}
    .clabel{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#6b6578;margin-bottom:12px;}
    .cval{font-family:'Courier New',monospace;font-size:40px;font-weight:700;color:#120f22;letter-spacing:10px;line-height:1;}
    .cexp{font-size:12px;color:#6b6578;margin-top:10px;}
    .ablock{text-align:center;padding:28px 24px;background:linear-gradient(135deg,rgba(200,164,90,0.07),rgba(124,92,232,0.04));border:1px solid rgba(200,164,90,0.22);border-radius:16px;margin:24px 0;}
    .alabel{font-size:11px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#6b6578;margin-bottom:8px;}
    .aval{font-family:Georgia,serif;font-size:52px;font-weight:700;color:#120f22;line-height:1;}
    .aval .cur{font-size:26px;vertical-align:super;}
    .adesc{font-size:13px;color:#6b6578;margin-top:8px;}
    .alert{border-radius:10px;padding:16px 20px;margin:20px 0;font-size:14px;line-height:1.65;}
    .a-warn{background:#fffbeb;border:1px solid #fcd34d;color:#78350f;}
    .a-danger{background:#fef2f2;border:1px solid #fca5a5;color:#991b1b;}
    .a-success{background:#f0fdf4;border:1px solid #86efac;color:#14532d;}
    .ctable{width:100%;border-collapse:collapse;margin:20px 0;border:1px solid #e5e0d6;}
    .ctable th{background:#f2ede4;padding:12px 16px;text-align:left;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#6b6578;border-bottom:1px solid #e5e0d6;}
    .ctable td{padding:14px 16px;font-size:14px;color:#120f22;border-bottom:1px solid #f0ebe2;font-family:'Courier New',monospace;}
    .ctable tr:last-child td{border-bottom:none;}
    .flist{list-style:none;padding:0;margin:20px 0;}
    .flist li{display:flex;align-items:flex-start;gap:12px;padding:10px 0;border-bottom:1px solid #f0ebe2;font-size:14px;color:#3d3550;line-height:1.5;}
    .flist li:last-child{border-bottom:none;}
    .chk{width:20px;height:20px;border-radius:50%;background:rgba(22,163,74,0.1);border:1px solid rgba(22,163,74,0.25);color:#16a34a;font-size:10px;font-weight:800;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;}
    .btn-wrap{text-align:center;margin:32px 0;}
    .btn-g{display:inline-block;background:#c8a45a;color:#120f22 !important;text-decoration:none;padding:16px 44px;border-radius:50px;font-size:14px;font-weight:700;box-shadow:0 4px 16px rgba(200,164,90,0.4);}
    .btn-d{display:inline-block;background:#120f22;color:#fff !important;text-decoration:none;padding:16px 44px;border-radius:50px;font-size:14px;font-weight:700;}
    .btn-o{display:inline-block;background:transparent;color:#120f22 !important;text-decoration:none;padding:14px 36px;border-radius:50px;font-size:13px;font-weight:600;border:1.5px solid #c8a45a;}
    .url{background:#f2ede4;border:1px solid #e5e0d6;border-radius:8px;padding:12px 16px;font-family:'Courier New',monospace;font-size:11px;color:#6b6578;word-break:break-all;margin:16px 0;}
    .divider{border:none;border-top:1px solid #e5e0d6;margin:32px 0;}
    .ft{background:#f2ede4;border-top:1px solid #e5e0d6;padding:28px 48px 32px;text-align:center;}
    .ftlogo{font-family:Georgia,serif;font-size:18px;font-weight:700;color:#120f22;margin-bottom:10px;}
    .ftlogo span{color:#c8a45a;}
    .fttag{font-size:12px;color:#6b6578;margin-bottom:18px;line-height:1.6;}
    .ftlinks{margin-bottom:14px;}
    .ftlinks a{font-size:12px;color:#6b6578;text-decoration:none;margin:0 10px;}
    .ftlegal{font-size:11px;color:#9c95a8;line-height:1.7;margin-top:14px;}
    @media(max-width:600px){
      .hd,.bd,.ft{padding-left:24px !important;padding-right:24px !important;}
      .banner{padding:24px !important;}
      .aval{font-size:38px;}.cval{font-size:28px;letter-spacing:6px;}
      .btn-g,.btn-d{padding:14px 28px;font-size:13px;}
    }
  </style>
</head>
<body>
<div class="wrap">
<div class="box">

  <div class="hd">
    <div class="logo">Digital<span> Ajo Ledger</span></div>
    @hasSection('eyebrow')<div class="eyebrow">@yield('eyebrow')</div>@endif
  </div>

  @hasSection('icon')
  <div class="banner">
    <div class="ic @yield('icon_style','ic-gold')">@yield('icon')</div>
    <h1 class="etitle">@yield('title')</h1>
    @hasSection('subtitle')<p class="esub">@yield('subtitle')</p>@endif
  </div>
  @endif

  <div class="bd">
    @yield('content')
    <hr class="divider">
    <p style="font-size:14px;color:#6b6578;margin:0;">
      Warm regards,<br>
      <strong style="color:#120f22;">The Digital Ajo Ledger Team</strong>
    </p>
  </div>

  <div class="ft">
    <div class="ftlogo">Digital <span>Ajo Ledger</span></div>
    <p class="fttag">Transparency · Accountability · Community Trust<br>We never hold or process money — just your shared ledger.</p>
    <div class="ftlinks">
      <a href="{{ config('app.frontend_url') }}/privacy">Privacy</a>
      <a href="{{ config('app.frontend_url') }}/terms">Terms</a>
      <a href="mailto:support@digitalAjoLedger.com">Support</a>
    </div>
    <p class="ftlegal">© {{ date('Y') }} Digital Ajo Ledger Ltd. All rights reserved.</p>
  </div>

</div>
</div>
</body>
</html>
