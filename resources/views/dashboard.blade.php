@php
    $isRtl = __('dashboard.direction') === 'rtl';

    $intentLabel = static fn (?string $intent): string => __("dashboard.intent.{$intent}") !== "dashboard.intent.{$intent}"
        ? __("dashboard.intent.{$intent}")
        : ($intent ?: __('dashboard.unknown'));

    $eventSummary = static fn ($event): string => data_get($event->payload, 'message.text')
        ?: data_get($event->payload, 'callback_query.data')
        ?: __('dashboard.unknown');
@endphp
<!doctype html>
<html lang="{{ __('dashboard.html_lang') }}" dir="{{ __('dashboard.direction') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('dashboard.title') }}</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f4f6f8;
            --panel: #ffffff;
            --ink: #17212b;
            --muted: #667085;
            --line: #dbe2ea;
            --soft: #eef4f7;
            --brand: #0f766e;
            --brand-strong: #115e59;
            --blue: #2563eb;
            --rose: #be123c;
            --amber: #b45309;
            --shadow: 0 18px 45px rgba(15, 35, 55, .08);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            background:
                linear-gradient(180deg, #e9f3f1 0, rgba(233, 243, 241, 0) 310px),
                var(--bg);
            color: var(--ink);
            font-family: {{ $isRtl ? "'Vazirmatn', Tahoma, Arial, sans-serif" : "Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif" }};
            line-height: 1.65;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .shell {
            width: min(1200px, calc(100% - 32px));
            margin: 0 auto;
            padding: 28px 0 44px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 22px;
        }

        .brand {
            min-width: 0;
        }

        .eyebrow {
            color: var(--brand-strong);
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        h1, h2, p { margin: 0; }

        h1 {
            font-size: clamp(28px, 4vw, 44px);
            line-height: 1.15;
            letter-spacing: 0;
        }

        .subtitle {
            color: var(--muted);
            max-width: 760px;
            margin-top: 9px;
            font-size: 15px;
        }

        .actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .button, .lang-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            border-radius: 8px;
            border: 1px solid var(--line);
            background: rgba(255, 255, 255, .78);
            padding: 8px 13px;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
        }

        .button {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            box-shadow: 0 10px 20px rgba(15, 118, 110, .18);
        }

        .language {
            display: inline-flex;
            gap: 4px;
            padding: 4px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: rgba(255, 255, 255, .7);
        }

        .lang-link.is-active {
            background: var(--ink);
            color: #fff;
            border-color: var(--ink);
        }

        .meta {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 14px;
            align-items: center;
            padding: 14px 16px;
            margin-bottom: 18px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: rgba(255, 255, 255, .75);
        }

        .meta-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        code {
            direction: ltr;
            unicode-bidi: plaintext;
            display: inline-block;
            color: var(--amber);
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .updated {
            color: var(--muted);
            font-size: 13px;
            white-space: nowrap;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .stat, .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .stat {
            padding: 16px;
            min-height: 116px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat span {
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
        }

        .stat strong {
            display: block;
            font-size: 34px;
            line-height: 1;
            color: var(--brand-strong);
        }

        .panel {
            margin-top: 16px;
            overflow: hidden;
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            padding: 18px 18px 14px;
            border-bottom: 1px solid var(--line);
        }

        h2 {
            font-size: 18px;
            line-height: 1.3;
        }

        .hint {
            color: var(--muted);
            font-size: 13px;
            margin-top: 3px;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 820px;
        }

        th, td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--line);
            text-align: {{ $isRtl ? 'right' : 'left' }};
            vertical-align: top;
            font-size: 14px;
        }

        th {
            color: var(--muted);
            background: #fafbfc;
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
        }

        tr:last-child td {
            border-bottom: 0;
        }

        .name {
            font-weight: 900;
        }

        .muted {
            color: var(--muted);
        }

        .pill {
            display: inline-flex;
            align-items: center;
            min-height: 26px;
            padding: 3px 10px;
            border-radius: 999px;
            background: #e7f5f2;
            color: var(--brand-strong);
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .empty {
            color: var(--muted);
            padding: 22px 16px;
        }

        @media (max-width: 980px) {
            .topbar, .meta {
                grid-template-columns: 1fr;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .actions {
                justify-content: flex-start;
            }

            .stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .updated {
                white-space: normal;
            }
        }

        @media (max-width: 560px) {
            .shell {
                width: min(100% - 22px, 1200px);
                padding-top: 18px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .panel-head {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<main class="shell">
    <section class="topbar">
        <div class="brand">
            <div class="eyebrow">{{ __('dashboard.eyebrow') }}</div>
            <h1>{{ __('dashboard.title') }}</h1>
            <p class="subtitle">{{ __('dashboard.subtitle') }}</p>
        </div>

        <div class="actions" aria-label="{{ __('dashboard.language') }}">
            <div class="language">
                <a class="lang-link {{ $locale === 'fa' ? 'is-active' : '' }}" href="{{ route('dashboard', ['lang' => 'fa']) }}">{{ __('dashboard.persian') }}</a>
                <a class="lang-link {{ $locale === 'en' ? 'is-active' : '' }}" href="{{ route('dashboard', ['lang' => 'en']) }}">{{ __('dashboard.english') }}</a>
            </div>
            <a class="button" href="{{ url('/admin') }}">{{ __('dashboard.open_admin') }}</a>
        </div>
    </section>

    <section class="meta">
        <div>
            <div class="meta-label">{{ __('dashboard.webhook') }}</div>
            <code>{{ route('bale.webhook') }}</code>
        </div>
        <div class="updated">{{ __('dashboard.last_updated') }}: {{ now()->format('Y-m-d H:i') }}</div>
    </section>

    <section class="stats">
        <div class="stat"><span>{{ __('dashboard.stats.leads') }}</span><strong>{{ number_format($leadCount) }}</strong></div>
        <div class="stat"><span>{{ __('dashboard.stats.courses') }}</span><strong>{{ number_format($courseCount) }}</strong></div>
        <div class="stat"><span>{{ __('dashboard.stats.webinars') }}</span><strong>{{ number_format($webinarCount) }}</strong></div>
        <div class="stat"><span>{{ __('dashboard.stats.technical') }}</span><strong>{{ number_format($technicalCount) }}</strong></div>
        <div class="stat"><span>{{ __('dashboard.stats.consultants') }}</span><strong>{{ number_format($consultantCount) }}</strong></div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>{{ __('dashboard.recent_leads') }}</h2>
                <p class="hint">{{ __('dashboard.recent_leads_hint') }}</p>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>{{ __('dashboard.columns.name') }}</th>
                    <th>{{ __('dashboard.columns.username') }}</th>
                    <th>{{ __('dashboard.columns.bale_id') }}</th>
                    <th>{{ __('dashboard.columns.intent') }}</th>
                    <th>{{ __('dashboard.columns.message') }}</th>
                    <th>{{ __('dashboard.columns.time') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($leads as $lead)
                    <tr>
                        <td class="name">{{ trim($lead->first_name.' '.$lead->last_name) ?: '-' }}</td>
                        <td class="muted">{{ $lead->username ? '@'.$lead->username : '-' }}</td>
                        <td><code>{{ $lead->bale_user_id }}</code></td>
                        <td><span class="pill">{{ $intentLabel($lead->intent) }}</span></td>
                        <td>{{ $lead->last_message ?: '-' }}</td>
                        <td class="muted">{{ $lead->created_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr><td class="empty" colspan="6">{{ __('dashboard.empty.leads') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>{{ __('dashboard.recent_events') }}</h2>
                <p class="hint">{{ __('dashboard.recent_events_hint') }}</p>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>{{ __('dashboard.columns.event_id') }}</th>
                    <th>{{ __('dashboard.columns.time') }}</th>
                    <th>{{ __('dashboard.columns.summary') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($events as $event)
                    <tr>
                        <td><code>{{ $event->event_id }}</code></td>
                        <td class="muted">{{ $event->created_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $eventSummary($event) }}</td>
                    </tr>
                @empty
                    <tr><td class="empty" colspan="3">{{ __('dashboard.empty.events') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>
</body>
</html>
