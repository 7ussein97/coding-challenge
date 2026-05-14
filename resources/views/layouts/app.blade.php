<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CP Platform') — UTAS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
    <style>
        :root {
            --sidebar-width: 240px;
            --sidebar-bg: #1a1f2e;
            --sidebar-text: #a8b2c8;
            --sidebar-active: #4f8ef7;
            --topbar-height: 60px;
        }
        * { box-sizing: border-box; }
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; margin: 0; }

        /* ── Sidebar ── */
        #sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            z-index: 1000;
            display: flex; flex-direction: column;
            transition: width .25s ease;
            overflow: hidden;
        }
        .sidebar-brand {
            height: var(--topbar-height);
            display: flex; align-items: center; padding: 0 20px;
            border-bottom: 1px solid #2d3548;
            color: #fff; font-weight: 700; font-size: 1.1rem;
            white-space: nowrap;
        }
        .sidebar-brand i { font-size: 1.4rem; margin-right: 10px; color: var(--sidebar-active); }
        .sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
        .sidebar-section {
            font-size: .68rem; font-weight: 700; letter-spacing: .08em;
            text-transform: uppercase; color: #4a5578;
            padding: 12px 20px 4px;
        }
        .sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 20px; color: var(--sidebar-text);
            text-decoration: none; font-size: .9rem;
            transition: background .15s, color .15s;
            white-space: nowrap;
        }
        .sidebar-link i { width: 18px; text-align: center; font-size: .95rem; }
        .sidebar-link:hover { background: #2d3548; color: #fff; }
        .sidebar-link.active { background: rgba(79,142,247,.15); color: var(--sidebar-active); border-right: 3px solid var(--sidebar-active); }
        .sidebar-footer { padding: 16px 20px; border-top: 1px solid #2d3548; }
        .sidebar-footer .user-info { color: var(--sidebar-text); font-size: .82rem; }
        .sidebar-footer .user-info strong { display: block; color: #fff; font-size: .9rem; }

        /* ── Main ── */
        #main { margin-left: var(--sidebar-width); min-height: 100vh; display: flex; flex-direction: column; }
        #topbar {
            height: var(--topbar-height); background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; position: sticky; top: 0; z-index: 900;
        }
        #topbar .page-title { font-weight: 600; font-size: 1rem; color: #1e293b; }
        .content-area { padding: 24px; flex: 1; }

        /* ── Role badge ── */
        .role-badge-admin  { background: #3b82f6; }
        .role-badge-judge  { background: #8b5cf6; }
        .role-badge-team   { background: #10b981; }

        /* ── Cards ── */
        .stat-card { border: none; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .stat-card .icon-wrap { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }

        /* ── Status badges ── */
        .badge-pending  { background: #fef3c7; color: #92400e; }
        .badge-accepted { background: #d1fae5; color: #065f46; }
        .badge-rejected { background: #fee2e2; color: #991b1b; }
        .status-badge { padding: 4px 12px; border-radius: 999px; font-size: .78rem; font-weight: 600; }

        /* ── Code block ── */
        .code-block { background: #1e293b; color: #e2e8f0; border-radius: 8px; padding: 16px; font-family: 'Cascadia Code','Fira Code',monospace; font-size: .85rem; overflow-x: auto; max-height: 500px; overflow-y: auto; white-space: pre; position: relative; }
        .copy-btn { position: absolute; top: 8px; right: 8px; }

        /* ── Tables ── */
        .table-card { background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.08); overflow: hidden; }
        .table-card .table { margin: 0; }
        .table-card .table thead th { background: #f8fafc; border-bottom: 2px solid #e2e8f0; font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #64748b; }
        .table-hover tbody tr:hover { background: #f8fafc; }

        /* ── Alerts ── */
        .alert { border-radius: 8px; border: none; font-size: .9rem; }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            #sidebar { width: 0; }
            #main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

@auth
<div id="sidebar">
    <div class="sidebar-brand">
        <i class="fas fa-code"></i>
        <span>UTAS CP</span>
    </div>
    <nav class="sidebar-nav">
        @if(auth()->user()->isAdmin())
            <div class="sidebar-section">Admin</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('admin.teams.index') }}" class="sidebar-link {{ request()->routeIs('admin.teams.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Teams
            </a>
            <a href="{{ route('admin.judges.index') }}" class="sidebar-link {{ request()->routeIs('admin.judges.*') ? 'active' : '' }}">
                <i class="fas fa-user-tie"></i> Judges
            </a>
            <a href="{{ route('admin.questions.index') }}" class="sidebar-link {{ request()->routeIs('admin.questions.*') ? 'active' : '' }}">
                <i class="fas fa-question-circle"></i> Questions
            </a>
        @elseif(auth()->user()->isJudge())
            <div class="sidebar-section">Judge</div>
            <a href="{{ route('judge.dashboard') }}" class="sidebar-link {{ request()->routeIs('judge.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('judge.submissions.index') }}" class="sidebar-link {{ request()->routeIs('judge.submissions.*') ? 'active' : '' }}">
                <i class="fas fa-inbox"></i> Submissions
            </a>
        @elseif(auth()->user()->isTeam())
            <div class="sidebar-section">Team</div>
            <a href="{{ route('team.dashboard') }}" class="sidebar-link {{ request()->routeIs('team.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="{{ route('team.questions.index') }}" class="sidebar-link {{ request()->routeIs('team.questions.*') ? 'active' : '' }}">
                <i class="fas fa-list-ol"></i> Problems
            </a>
            <a href="{{ route('team.submissions.history') }}" class="sidebar-link {{ request()->routeIs('team.submissions.*') ? 'active' : '' }}">
                <i class="fas fa-history"></i> My Submissions
            </a>
        @endif

        <div class="sidebar-section" style="margin-top: 8px;">General</div>
        <a href="{{ route('leaderboard.index') }}" class="sidebar-link {{ request()->routeIs('leaderboard.*') ? 'active' : '' }}">
            <i class="fas fa-trophy"></i> Leaderboard
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <strong>{{ auth()->user()->name }}</strong>
            <span class="badge rounded-pill role-badge-{{ auth()->user()->role }} text-white mt-1" style="font-size:.7rem;">
                {{ ucfirst(auth()->user()->role) }}
            </span>
        </div>
    </div>
</div>

<div id="main">
    <div id="topbar">
        <span class="page-title">@yield('page-title', 'Dashboard')</span>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small d-none d-md-inline">{{ auth()->user()->email }}</span>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </button>
            </form>
        </div>
    </div>

    <div class="content-area">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i>{!! session('success') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{!! session('error') !!}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

@else
    @yield('content')
@endauth

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.umd.js"></script>
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        const btn = event.currentTarget;
        const orig = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
        btn.classList.replace('btn-secondary', 'btn-success');
        setTimeout(() => { btn.innerHTML = orig; btn.classList.replace('btn-success', 'btn-secondary'); }, 1800);
    });
}

@auth
window.Echo = new window.Echo({
    broadcaster: 'reverb',
    key: '{{ env("REVERB_APP_KEY") }}',
    wsHost: window.location.hostname,
    wsPort: {{ env("REVERB_PORT", 8080) }},
    wssPort: {{ env("REVERB_PORT", 8080) }},
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
});

@if(auth()->user()->isJudge() || auth()->user()->isAdmin())
window.Echo.private('judges')
    .listen('.new-submission',     () => Livewire.dispatch('submissions-changed'))
    .listen('.submission-updated', () => Livewire.dispatch('submissions-changed'))
    .listen('.lock-changed',       () => Livewire.dispatch('submissions-changed'));
@endif

@if(auth()->user()->isTeam() && auth()->user()->team)
window.Echo.private('team.{{ auth()->user()->team->id }}')
    .listen('.submission-updated', () => Livewire.dispatch('my-submission-updated'));
@endif
@endauth
</script>
@livewireScripts
@stack('scripts')
</body>
</html>
