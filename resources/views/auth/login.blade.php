<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — UTAS Competitive Programming</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a1f2e 0%, #2d3548 50%, #1a1f2e 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: #fff; border-radius: 16px;
            box-shadow: 0 25px 60px rgba(0,0,0,.4);
            padding: 40px; width: 100%; max-width: 420px;
        }
        .login-logo { text-align: center; margin-bottom: 28px; }
        .login-logo .icon-wrap {
            width: 72px; height: 72px; border-radius: 18px;
            background: linear-gradient(135deg, #4f8ef7, #8b5cf6);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 2rem; color: #fff; margin-bottom: 14px;
            box-shadow: 0 8px 20px rgba(79,142,247,.4);
        }
        .login-logo h1 { font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0; }
        .login-logo p { color: #64748b; font-size: .9rem; margin: 4px 0 0; }
        .form-label { font-weight: 600; font-size: .85rem; color: #374151; }
        .form-control { border-radius: 8px; padding: 10px 14px; border: 1px solid #d1d5db; }
        .form-control:focus { border-color: #4f8ef7; box-shadow: 0 0 0 3px rgba(79,142,247,.15); }
        .btn-login {
            width: 100%; padding: 12px; border-radius: 8px; font-weight: 600;
            background: linear-gradient(135deg, #4f8ef7, #8b5cf6);
            border: none; color: #fff; font-size: 1rem;
            transition: opacity .2s;
        }
        .btn-login:hover { opacity: .9; color: #fff; }
        .input-group-text { background: #f8fafc; border-color: #d1d5db; }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <div class="icon-wrap"><i class="fas fa-code"></i></div>
        <h1>UTAS CP Platform</h1>
        <p>Competitive Programming Judge System</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 rounded-3 mb-3 py-2">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="your@email.com" autofocus required>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                <input id="password" type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label text-muted small" for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn btn-login">
            <i class="fas fa-sign-in-alt me-2"></i>Sign In
        </button>
    </form>

    <p class="text-center text-muted mt-4 mb-0" style="font-size:.8rem;">
        <i class="fas fa-shield-alt me-1"></i>Secure login for Teams, Judges &amp; Admins
    </p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
