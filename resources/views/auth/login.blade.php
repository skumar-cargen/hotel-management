<x-guest-layout>
    <div class="auth-form-header">
        <h2>Welcome Back</h2>
        <p>Sign in to your admin account</p>
    </div>

    @if(session('status'))
    <div class="auth-alert alert alert-success">
        {{ session('status') }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form" id="loginForm">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}" placeholder="admin@dubaihotels.com" required autofocus autocomplete="username">
            </div>
            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class='bx bx-lock-alt'></i></span>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror"
                       placeholder="Enter your password" required autocomplete="current-password">
            </div>
            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="mb-4">
            <div class="form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label class="form-check-label small text-muted" for="remember">Remember me</label>
            </div>
        </div>

        <button type="submit" class="btn btn-auth-primary" id="loginBtn">
            <span class="btn-text"><i class='bx bx-log-in me-1'></i> Sign In</span>
            <span class="btn-loader" style="display:none;">
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Signing in...
            </span>
        </button>
    </form>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function () {
            var btn = document.getElementById('loginBtn');
            btn.querySelector('.btn-text').style.display = 'none';
            btn.querySelector('.btn-loader').style.display = 'inline-flex';
            btn.disabled = true;
            btn.style.opacity = '0.8';
        });
    </script>
</x-guest-layout>
