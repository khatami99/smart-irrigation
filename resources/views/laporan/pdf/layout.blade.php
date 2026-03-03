<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0;padding:0;box-sizing:border-box; }
    body { font-family:'DejaVu Sans',sans-serif;font-size:9px;color:#3d2b1f;background:#fff; }

    /* Header */
    .header { border-bottom:3px solid #3d2b1f;padding-bottom:10px;margin-bottom:16px; }
    .header-top { display:flex;justify-content:space-between;align-items:flex-start; }
    .brand { }
    .brand-name { font-size:18px;font-weight:bold;color:#3d2b1f;letter-spacing:-0.5px; }
    .brand-name span { color:#4a7c6f; }
    .brand-sub { font-size:8px;color:#7a6355;letter-spacing:2px;text-transform:uppercase;margin-top:2px; }
    .doc-info { text-align:right;font-size:8px;color:#7a6355; }
    .doc-title { font-size:14px;font-weight:bold;color:#3d2b1f;margin-top:8px; }
    .doc-meta { font-size:8px;color:#7a6355;margin-top:3px; }

    /* Table */
    table { width:100%;border-collapse:collapse;margin-top:12px; }
    th { background:#3d2b1f;color:#e8d5a3;padding:6px 8px;font-size:8px;font-weight:bold;text-align:center;letter-spacing:0.5px;text-transform:uppercase; }
    td { padding:5px 8px;border-bottom:1px solid #e8d5a3;font-size:8.5px;color:#4a3728;vertical-align:middle; }
    tr:nth-child(even) td { background:#faf6ef; }
    tr:last-child td { border-bottom:none; }

    /* Footer */
    .footer { margin-top:16px;padding-top:8px;border-top:1px solid #e8d5a3;display:flex;justify-content:space-between;font-size:7.5px;color:#7a6355; }

    /* Badge */
    .badge { display:inline-block;padding:2px 6px;border-radius:3px;font-size:7.5px;font-weight:bold; }
    .badge-water { background:#e0f0ec;color:#4a7c6f; }
    .badge-leaf  { background:#e8f0e3;color:#5a7a47; }
    .badge-clay  { background:#f5ede0;color:#8b5e3c; }
    .badge-red   { background:#fde8e5;color:#a03828; }

    /* Stat row */
    .stat-row { display:flex;gap:12px;margin-bottom:12px; }
    .stat-box { flex:1;background:#faf6ef;border:1px solid #e8d5a3;border-radius:6px;padding:8px 10px; }
    .stat-box-label { font-size:7px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;color:#7a6355; }
    .stat-box-val { font-size:14px;font-weight:bold;color:#3d2b1f;margin-top:2px; }
    .stat-box-unit { font-size:8px;font-weight:normal;color:#7a6355; }

    /* Page break */
    .page-break { page-break-after:always; }
</style>
</head>
<body>
    <div class="header">
        <div class="header-top">
            <div class="brand">
                <div class="brand-name">Smart<span>Irigasi</span></div>
                <div class="brand-sub">Sistem Monitoring Irigasi Cerdas</div>
            </div>
            <div class="doc-info">
                <div>Dicetak: {{ now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }} WIB</div>
                <div style="margin-top:2px;">{{ config('app.url') }}</div>
            </div>
        </div>
        <div class="doc-title">@yield('doc-title')</div>
        <div class="doc-meta">@yield('doc-meta')</div>
    </div>

    @yield('content')

    <div class="footer">
        <div>Smart<span style="color:#4a7c6f">Irigasi</span> — Sistem Monitoring Irigasi Cerdas</div>
        <div>Dokumen ini digenerate otomatis oleh sistem</div>
    </div>
</body>
</html>
