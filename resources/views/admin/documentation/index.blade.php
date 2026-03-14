<x-admin-layout title="Documentation" pageTitle="Documentation">
    <x-slot:breadcrumb>
        <li class="breadcrumb-item active">Documentation</li>
    </x-slot:breadcrumb>

    <style>
        /* ── Layout ── */
        .docs-layout { display: grid; grid-template-columns: 250px 1fr; gap: 1.5rem; align-items: start; }
        @media (max-width: 992px) { .docs-layout { grid-template-columns: 1fr; } }

        .docs-toc {
            position: sticky; top: 80px; background: #fff; border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,.06); padding: 1rem; max-height: calc(100vh - 100px); overflow-y: auto;
        }
        @media (max-width: 992px) { .docs-toc { position: static; max-height: none; } }
        .docs-toc h6 { font-weight: 700; font-size: .7rem; text-transform: uppercase; letter-spacing: .05em; color: var(--text-muted); margin: .75rem 0 .4rem .5rem; }
        .docs-toc h6:first-child { margin-top: 0; }
        .toc-link {
            display: flex; align-items: center; gap: .45rem; padding: .35rem .6rem; border-radius: 7px;
            color: var(--text-secondary); text-decoration: none; font-size: .78rem; font-weight: 500; transition: all .15s;
        }
        .toc-link:hover, .toc-link.active { background: #eef2ff; color: #667eea; }
        .toc-link i { font-size: .95rem; width: 18px; text-align: center; }
        .toc-count { margin-left: auto; font-size: .65rem; background: #f1f5f9; color: var(--text-muted); padding: .1rem .35rem; border-radius: 4px; font-weight: 600; }

        .docs-content { min-width: 0; }

        /* ── Page header banner ── */
        .page-header-banner {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 40%, #1e40af 100%);
            border-radius: 16px; padding: 1.5rem 2rem; margin-bottom: 1.5rem;
            display: flex; align-items: center; gap: 1.25rem; position: relative; overflow: hidden;
        }
        .page-header-banner::before {
            content: ''; position: absolute; top: -60%; right: -5%; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(59,130,246,0.2) 0%, transparent 70%);
        }
        .page-header-banner .h-icon {
            width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; color: #fff; flex-shrink: 0; position: relative; z-index: 1;
            background: linear-gradient(135deg, #7c3aed, #a78bfa);
        }
        .page-header-banner h4 { color: #fff; font-weight: 700; font-size: 1.15rem; margin: 0; position: relative; z-index: 1; }
        .page-header-banner p { color: rgba(255,255,255,0.5); font-size: .82rem; margin: .2rem 0 0; position: relative; z-index: 1; }

        /* ── Tabs ── */
        .doc-tabs {
            display: flex; gap: .5rem; margin-bottom: 1.5rem; background: #fff; padding: .5rem;
            border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.06);
        }
        .doc-tab {
            flex: 1; display: flex; align-items: center; justify-content: center; gap: .6rem;
            padding: .75rem 1.25rem; border-radius: 10px; border: none; background: transparent;
            color: var(--text-secondary); font-size: .88rem; font-weight: 600; cursor: pointer;
            transition: all .2s;
        }
        .doc-tab:hover { background: #f8fafc; color: var(--text-primary); }
        .doc-tab.active {
            background: linear-gradient(135deg, #667eea, #764ba2); color: #fff;
            box-shadow: 0 4px 12px rgba(102,126,234,.3);
        }
        .doc-tab i { font-size: 1.15rem; }
        .doc-tab .tab-count {
            font-size: .65rem; font-weight: 700; padding: .15rem .4rem; border-radius: 4px;
            background: rgba(255,255,255,.2); color: rgba(255,255,255,.9);
        }
        .doc-tab:not(.active) .tab-count { background: #f1f5f9; color: var(--text-muted); }

        .tab-pane { display: none; }
        .tab-pane.active { display: block; }

        /* ── Section headers ── */
        .section-header {
            background: #fff; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.06);
            padding: 1.25rem 1.5rem; margin-bottom: 1rem; display: flex; align-items: center; gap: .75rem;
        }
        .section-icon {
            width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
            font-size: 1.15rem; color: #fff; flex-shrink: 0;
        }
        .section-header h5 { font-weight: 700; font-size: 1.05rem; margin: 0; }
        .section-header p { font-size: .8rem; color: var(--text-muted); margin: .1rem 0 0; }

        /* ── Doc cards (admin guide) ── */
        .doc-card {
            background: #fff; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,.06);
            margin-bottom: 1rem; overflow: hidden;
        }
        .doc-card-body { padding: 1.25rem 1.5rem; }
        .doc-card-body p { color: var(--text-secondary); font-size: .85rem; line-height: 1.7; margin-bottom: .75rem; }
        .doc-card-body p:last-child { margin-bottom: 0; }
        .doc-section-title {
            font-size: .85rem; font-weight: 700; color: var(--text-primary);
            margin: 1rem 0 .5rem; padding-bottom: .35rem; border-bottom: 1px solid #f1f5f9;
        }
        .doc-section-title:first-child { margin-top: 0; }
        .feature-list { list-style: none; padding: 0; margin: 0 0 .5rem; }
        .feature-list li {
            padding: .4rem 0; font-size: .84rem; color: var(--text-secondary); line-height: 1.6;
            display: flex; align-items: flex-start; gap: .5rem;
        }
        .feature-list li::before { content: '\ea41'; font-family: 'boxicons'; color: var(--accent-success); font-size: .9rem; margin-top: 2px; flex-shrink: 0; }
        .tech-badges { display: flex; flex-wrap: wrap; gap: .5rem; margin-top: .5rem; }
        .tech-badge {
            display: inline-flex; align-items: center; gap: .35rem; padding: .3rem .7rem;
            background: #f1f5f9; border-radius: 8px; font-size: .78rem; font-weight: 600; color: var(--text-secondary);
        }
        .img-cat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: .5rem; margin: .5rem 0; }
        .img-cat-item {
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: .5rem .65rem;
            font-size: .78rem; color: var(--text-secondary); text-align: center; font-weight: 500;
        }
        .status-flow { display: flex; align-items: center; flex-wrap: wrap; gap: .35rem; margin: .5rem 0; }
        .status-flow .status-step { padding: .3rem .6rem; border-radius: 6px; font-size: .78rem; font-weight: 600; color: #fff; }
        .status-flow .status-arrow { color: var(--text-muted); font-size: .9rem; }

        /* ── Endpoint cards (API ref) ── */
        .endpoint-card {
            background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.06);
            margin-bottom: .75rem; overflow: hidden; border: 1px solid transparent; transition: border-color .2s;
        }
        .endpoint-card:hover { border-color: #e0e7ff; }
        .endpoint-head {
            padding: .85rem 1.25rem; display: flex; align-items: center; gap: .75rem;
            cursor: pointer; user-select: none; transition: background .15s;
        }
        .endpoint-head:hover { background: #fafbff; }
        .endpoint-head .chevron { margin-left: auto; font-size: 1.1rem; color: var(--text-muted); transition: transform .2s; }
        .endpoint-card.open .endpoint-head .chevron { transform: rotate(180deg); }
        .endpoint-body { display: none; padding: 0 1.25rem 1.25rem; border-top: 1px solid #f1f5f9; }
        .endpoint-card.open .endpoint-body { display: block; }

        .method-badge {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 52px; padding: .2rem .5rem; border-radius: 6px;
            font-size: .7rem; font-weight: 700; letter-spacing: .03em; color: #fff; flex-shrink: 0;
        }
        .method-get { background: #3b82f6; }
        .method-post { background: #22c55e; }
        .method-put { background: #f59e0b; }
        .method-delete { background: #ef4444; }
        .endpoint-path { font-family: 'SFMono-Regular', Consolas, monospace; font-size: .82rem; font-weight: 600; color: var(--text-primary); }
        .endpoint-desc { font-size: .78rem; color: var(--text-muted); margin-left: auto; }

        .auth-badge { font-size: .65rem; font-weight: 600; padding: .15rem .4rem; border-radius: 4px; white-space: nowrap; }
        .auth-public { background: #f0fdf4; color: #16a34a; }
        .auth-token { background: #fef3c7; color: #d97706; }
        .auth-none { background: #f1f5f9; color: #64748b; }

        .ep-section { font-size: .78rem; font-weight: 700; color: var(--text-primary); margin: 1rem 0 .4rem; padding-bottom: .3rem; border-bottom: 1px solid #f1f5f9; }
        .ep-section:first-child { margin-top: .5rem; }

        /* ── Param tables ── */
        .param-table { width: 100%; font-size: .8rem; border-collapse: collapse; margin: .4rem 0; }
        .param-table th { text-align: left; padding: .45rem .6rem; background: #f8fafc; font-weight: 600; color: var(--text-primary); border-bottom: 2px solid #e5e7eb; font-size: .72rem; text-transform: uppercase; letter-spacing: .03em; }
        .param-table td { padding: .45rem .6rem; border-bottom: 1px solid #f1f5f9; color: var(--text-secondary); vertical-align: top; }
        .param-table code { background: #f1f5f9; padding: .1rem .35rem; border-radius: 4px; font-size: .75rem; color: #667eea; }
        .badge-req { background: #fef2f2; color: #dc2626; font-size: .65rem; padding: .08rem .3rem; border-radius: 3px; font-weight: 600; }
        .badge-opt { background: #f0fdf4; color: #059669; font-size: .65rem; padding: .08rem .3rem; border-radius: 3px; font-weight: 600; }

        /* ── Code blocks ── */
        .code-block { position: relative; background: #1e293b; border-radius: 10px; margin: .5rem 0; overflow: hidden; }
        .code-block pre { margin: 0; padding: 1rem 1.25rem; overflow-x: auto; font-size: .78rem; line-height: 1.6; color: #e2e8f0; font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', monospace; }
        .code-block .code-label { display: inline-block; padding: .25rem .6rem; background: rgba(255,255,255,.08); font-size: .65rem; font-weight: 600; color: #94a3b8; border-radius: 0 0 6px 0; }
        .code-block .copy-btn {
            position: absolute; top: .5rem; right: .5rem; background: rgba(255,255,255,.1);
            border: none; color: #94a3b8; padding: .3rem .5rem; border-radius: 6px; cursor: pointer;
            font-size: .75rem; transition: all .15s; display: flex; align-items: center; gap: .3rem;
        }
        .code-block .copy-btn:hover { background: rgba(255,255,255,.2); color: #fff; }
        .code-block .copy-btn.copied { background: #22c55e; color: #fff; }

        .rate-badge { display: inline-flex; align-items: center; gap: .3rem; font-size: .7rem; font-weight: 600; padding: .2rem .5rem; border-radius: 5px; background: #fdf4ff; color: #a855f7; }
        .info-note { background: #eff6ff; border-left: 3px solid #3b82f6; border-radius: 0 8px 8px 0; padding: .6rem .85rem; margin: .5rem 0; font-size: .78rem; color: #1e40af; }
        .info-note strong { color: #1e3a8a; }
        .warn-note { background: #fffbeb; border-left: 3px solid #f59e0b; border-radius: 0 8px 8px 0; padding: .6rem .85rem; margin: .5rem 0; font-size: .78rem; color: #92400e; }

        .mb-section { margin-bottom: 2rem; }
    </style>

    <!-- Page Header -->
    <div class="page-header-banner">
        <div class="h-icon"><i class='bx bx-book-open'></i></div>
        <div>
            <h4>Documentation</h4>
            <p>Complete admin panel guide and API reference for frontend developers</p>
        </div>
    </div>

    <!-- Tab Switcher -->
    <div class="doc-tabs">
        <button class="doc-tab active" data-target="adminTab" onclick="switchTab(this)">
            <i class='bx bxs-dashboard'></i> Admin Panel Guide
            <span class="tab-count">13</span>
        </button>
        <button class="doc-tab" data-target="apiTab" onclick="switchTab(this)">
            <i class='bx bx-code-alt'></i> API Reference
            <span class="tab-count">41</span>
        </button>
    </div>

    <!-- Admin Panel Guide Tab -->
    <div id="adminTab" class="tab-pane active">
        @include('admin.documentation._admin_guide')
    </div>

    <!-- API Reference Tab -->
    <div id="apiTab" class="tab-pane">
        @include('admin.documentation._api_reference')
    </div>

    <x-slot:scripts>
    <script>
    function switchTab(btn) {
        // Toggle tab buttons
        document.querySelectorAll('.doc-tab').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');

        // Toggle tab panes
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.getElementById(btn.dataset.target).classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Toggle endpoint cards (API ref)
        document.querySelectorAll('.endpoint-head').forEach(function(head) {
            head.addEventListener('click', function() {
                this.closest('.endpoint-card').classList.toggle('open');
            });
        });

        // TOC active state on scroll — works for whichever tab is visible
        function updateToc() {
            var activePane = document.querySelector('.tab-pane.active');
            if (!activePane) return;
            var sections = activePane.querySelectorAll('.mb-section[id]');
            var tocLinks = activePane.querySelectorAll('.toc-link');
            var current = '';
            sections.forEach(function(section) {
                var rect = section.getBoundingClientRect();
                if (rect.top <= 120) current = section.id;
            });
            tocLinks.forEach(function(link) {
                link.classList.toggle('active', link.getAttribute('href') === '#' + current);
            });
        }
        window.addEventListener('scroll', updateToc);
        updateToc();
    });

    function copyCode(btn) {
        var pre = btn.closest('.code-block').querySelector('pre');
        navigator.clipboard.writeText(pre.textContent).then(function() {
            btn.classList.add('copied');
            btn.innerHTML = '<i class="bx bx-check"></i> Copied';
            setTimeout(function() {
                btn.classList.remove('copied');
                btn.innerHTML = '<i class="bx bx-copy"></i> Copy';
            }, 2000);
        });
    }
    </script>
    </x-slot:scripts>
</x-admin-layout>
