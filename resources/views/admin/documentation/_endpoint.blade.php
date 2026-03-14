@props([])
@php
    $method = $method ?? 'GET';
    $path = $path ?? '';
    $desc = $desc ?? '';
    $auth = $auth ?? 'public';
    $rate = $rate ?? 'Standard';
    $body = $body ?? [];
    $query = $query ?? [];
    $route_params = $route_params ?? [];
    $params = $params ?? null;
    $response_label = $response_label ?? '200 OK';
    $response = $response ?? '';
    $errors = $errors ?? '';
    $note = $note ?? '';

    $methodLower = strtolower($method);
    $authClass = match($auth) {
        'token' => 'auth-token',
        'none' => 'auth-none',
        default => 'auth-public',
    };
    $authLabel = match($auth) {
        'token' => 'Bearer Token',
        'none' => 'No Auth',
        default => 'Public',
    };
@endphp

<div class="endpoint-card">
    <div class="endpoint-head">
        <span class="method-badge method-{{ $methodLower }}">{{ $method }}</span>
        <span class="endpoint-path">{{ $path }}</span>
        <span class="auth-badge {{ $authClass }}">{{ $authLabel }}</span>
        <span class="endpoint-desc d-none d-lg-inline">{{ $desc }}</span>
        <i class='bx bx-chevron-down chevron'></i>
    </div>
    <div class="endpoint-body">
        <p class="d-lg-none" style="font-size:.82rem;color:var(--text-secondary);margin:.5rem 0;">{{ $desc }}</p>

        @if($rate && $rate !== 'Standard' && $rate !== 'None')
            <span class="rate-badge"><i class='bx bx-time-five'></i> Rate limit: {{ $rate }}</span>
        @endif

        @if($note)
            <div class="info-note"><strong>Note:</strong> {{ $note }}</div>
        @endif

        @if(count($route_params) > 0)
            <h6 class="ep-section">Route Parameters</h6>
            <table class="param-table">
                <thead><tr><th>Parameter</th><th>Type</th><th>Description</th></tr></thead>
                <tbody>
                    @foreach($route_params as $p)
                        <tr>
                            <td><code>{{ $p['field'] }}</code></td>
                            <td>{{ $p['type'] }}</td>
                            <td><span class="badge-req">Required</span> {{ $p['desc'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(count($query) > 0)
            <h6 class="ep-section">Query Parameters</h6>
            <table class="param-table">
                <thead><tr><th>Parameter</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                <tbody>
                    @foreach($query as $p)
                        <tr>
                            <td><code>{{ $p['field'] }}</code></td>
                            <td>{{ $p['type'] }}</td>
                            <td>@if($p['req'] ?? false)<span class="badge-req">Required</span>@else<span class="badge-opt">Optional</span>@endif</td>
                            <td>{{ $p['desc'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if(count($body) > 0)
            <h6 class="ep-section">Request Body</h6>
            <table class="param-table">
                <thead><tr><th>Field</th><th>Type</th><th>Required</th><th>Description</th></tr></thead>
                <tbody>
                    @foreach($body as $p)
                        <tr>
                            <td><code>{{ $p['field'] }}</code></td>
                            <td>{{ $p['type'] }}</td>
                            <td>@if($p['req'])<span class="badge-req">Required</span>@else<span class="badge-opt">Optional</span>@endif</td>
                            <td>{{ $p['desc'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        @if($response)
            <h6 class="ep-section">Response — {{ $response_label }}</h6>
            <div class="code-block">
                <span class="code-label">JSON</span>
                <button class="copy-btn" onclick="copyCode(this)"><i class='bx bx-copy'></i> Copy</button>
                <pre>{{ $response }}</pre>
            </div>
        @endif

        @if($errors)
            <h6 class="ep-section">Error Responses</h6>
            <p style="font-size:.8rem;color:var(--text-secondary);">
                @foreach(explode('|', $errors) as $err)
                    <code style="background:#fef2f2;color:#dc2626;">{{ trim($err) }}</code>{{ !$loop->last ? ' ' : '' }}
                @endforeach
            </p>
        @endif
    </div>
</div>
