<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Leaderboard — UTAS Competitive Programming</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
    <style>
        body { background: #0f172a; color: #e2e8f0; font-family: 'Segoe UI', sans-serif; min-height: 100vh; }
        .lb-header { background: linear-gradient(135deg, #1e293b, #0f172a); border-bottom: 1px solid #1e293b; padding: 20px 32px; }
        .lb-header h1 { font-size: 1.5rem; font-weight: 700; color: #fff; margin: 0; }
        .lb-header .subtitle { color: #64748b; font-size: .85rem; }
        .lb-card { background: #1e293b; border-radius: 16px; overflow: hidden; border: 1px solid #2d3548; }
        .lb-table thead th { background: #0f172a; color: #64748b; font-size: .75rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; border-bottom: 1px solid #2d3548; padding: 12px 16px; }
        .lb-table tbody tr { border-bottom: 1px solid #2d3548; transition: background .15s; }
        .lb-table tbody tr:hover { background: rgba(255,255,255,.03); }
        .lb-table tbody tr:last-child { border-bottom: none; }
        .lb-table td { padding: 14px 16px; vertical-align: middle; }
        .rank-badge { width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .9rem; }
        .rank-1 { background: linear-gradient(135deg,#ffd700,#ffa500); color: #000; box-shadow: 0 0 12px rgba(255,215,0,.4); }
        .rank-2 { background: linear-gradient(135deg,#c0c0c0,#a8a8a8); color: #000; }
        .rank-3 { background: linear-gradient(135deg,#cd7f32,#b06020); color: #fff; }
        .rank-other { background: #2d3548; color: #94a3b8; }
        .team-name { font-weight: 600; color: #f1f5f9; font-size: .95rem; }
        .solved-count { font-weight: 700; color: #10b981; font-size: 1.1rem; }
        .last-time { color: #94a3b8; font-size: .8rem; }
        .badge-accepted { background: rgba(16,185,129,.15); color: #34d399; padding: 4px 10px; border-radius: 999px; font-size: .78rem; font-weight: 600; }
        .top-row-1 { background: rgba(255,215,0,.04); }
        .top-row-2 { background: rgba(192,192,192,.03); }
        .top-row-3 { background: rgba(205,127,50,.03); }
        .live-bar { background: #1e293b; border-radius: 8px; padding: 10px 16px; display: flex; align-items: center; gap: 12px; font-size: .82rem; color: #64748b; }
        .live-dot { width: 8px; height: 8px; border-radius: 50%; background: #10b981; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:.3;} }
        .first-solver-badge { background: rgba(245,158,11,.15); color: #fbbf24; padding: 2px 8px; border-radius: 999px; font-size: .72rem; font-weight: 600; }
        .no-solve { color: #475569; }
        .back-btn { background: #1e293b; border: 1px solid #2d3548; color: #94a3b8; border-radius: 8px; padding: 8px 16px; text-decoration: none; font-size: .85rem; transition: all .15s; }
        .back-btn:hover { background: #2d3548; color: #e2e8f0; }
    </style>
</head>
<body>

<div class="lb-header d-flex align-items-center justify-content-between">
    <div>
        <h1><i class="fas fa-trophy text-warning me-2"></i>Leaderboard</h1>
        <div class="subtitle">UTAS Competitive Programming Platform</div>
    </div>
    <div class="d-flex align-items-center gap-3">
        @auth
        <a href="{{ match(auth()->user()->role) { 'admin' => route('admin.dashboard'), 'judge' => route('judge.dashboard'), default => route('team.dashboard') } }}" class="back-btn">
            <i class="fas fa-arrow-left me-1"></i>Dashboard
        </a>
        @else
        <a href="{{ route('login') }}" class="back-btn">
            <i class="fas fa-sign-in-alt me-1"></i>Login
        </a>
        @endauth
    </div>
</div>

<div class="container-fluid py-4 px-4">

    {{-- Live status bar --}}
    <div class="live-bar mb-4">
        <div class="live-dot"></div>
        <span>Live — updates instantly via WebSocket</span>
    </div>

    {{-- Leaderboard Table (Livewire) --}}
    <livewire:leaderboard-table />

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.umd.js"></script>
@livewireScripts
<script>
window.Echo = new window.Echo({
    broadcaster: 'reverb',
    key: '{{ env("REVERB_APP_KEY") }}',
    wsHost: window.location.hostname,
    wsPort: {{ env("REVERB_PORT", 8080) }},
    wssPort: {{ env("REVERB_PORT", 8080) }},
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

window.Echo.channel('leaderboard')
    .listen('.submission-updated', () => {
        Livewire.dispatch('leaderboard-updated');
    });
</script>
</body>
</html>
