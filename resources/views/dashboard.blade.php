<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>داشبورد بات فروش دوره</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f6f7f9;
            --panel: #ffffff;
            --ink: #1f2937;
            --muted: #6b7280;
            --line: #d9dee7;
            --accent: #0f766e;
            --accent-soft: #d8f3ef;
            --warn: #b45309;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Tahoma, Arial, sans-serif;
            background: var(--bg);
            color: var(--ink);
            line-height: 1.7;
        }
        .shell {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
            padding: 28px 0 44px;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }
        h1, h2 { margin: 0; }
        h1 { font-size: 24px; }
        h2 { font-size: 17px; margin-bottom: 12px; }
        .muted { color: var(--muted); font-size: 13px; }
        .grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 20px;
        }
        .stat, .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: 8px;
        }
        .stat { padding: 16px; }
        .stat strong {
            display: block;
            font-size: 27px;
            color: var(--accent);
            margin-top: 4px;
        }
        .panel { padding: 18px; margin-top: 16px; overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }
        th, td {
            padding: 11px 10px;
            border-bottom: 1px solid var(--line);
            text-align: right;
            vertical-align: top;
            font-size: 14px;
        }
        th { color: var(--muted); font-weight: 700; }
        .pill {
            display: inline-flex;
            padding: 3px 8px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: #115e59;
            font-size: 12px;
            white-space: nowrap;
        }
        code {
            direction: ltr;
            display: inline-block;
            color: var(--warn);
        }
        @media (max-width: 860px) {
            .topbar { align-items: flex-start; flex-direction: column; }
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
    </style>
</head>
<body>
<main class="shell">
    <section class="topbar">
        <div>
            <h1>داشبورد بات فروش دوره</h1>
            <div class="muted">وب‌هوک: <code>{{ route('bale.webhook') }}</code></div>
        </div>
        <div class="muted">
            <a href="{{ url('/admin') }}">ورود به پنل ادمین</a>
            <span> | </span>
            آخرین به‌روزرسانی: {{ now()->format('Y-m-d H:i') }}
        </div>
    </section>

    <section class="grid">
        <div class="stat"><span>کل لیدها</span><strong>{{ $leadCount }}</strong></div>
        <div class="stat"><span>خرید دوره</span><strong>{{ $courseCount }}</strong></div>
        <div class="stat"><span>وبینار</span><strong>{{ $webinarCount }}</strong></div>
        <div class="stat"><span>پشتیبانی فنی</span><strong>{{ $technicalCount }}</strong></div>
        <div class="stat"><span>مشاورین</span><strong>{{ $consultantCount }}</strong></div>
    </section>

    <section class="panel">
        <h2>آخرین لیدها</h2>
        <table>
            <thead>
            <tr>
                <th>نام</th>
                <th>نام کاربری</th>
                <th>شناسه بله</th>
                <th>درخواست</th>
                <th>آخرین پیام</th>
                <th>زمان</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($leads as $lead)
                <tr>
                    <td>{{ trim($lead->first_name.' '.$lead->last_name) ?: '-' }}</td>
                    <td>{{ $lead->username ? '@'.$lead->username : '-' }}</td>
                    <td><code>{{ $lead->bale_user_id }}</code></td>
                    <td><span class="pill">{{ $lead->intent }}</span></td>
                    <td>{{ $lead->last_message ?: '-' }}</td>
                    <td>{{ $lead->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6">هنوز لیدی ثبت نشده است.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>

    <section class="panel">
        <h2>آخرین وب‌هوک‌ها</h2>
        <table>
            <thead>
            <tr>
                <th>شناسه رویداد</th>
                <th>زمان</th>
                <th>خلاصه</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($events as $event)
                <tr>
                    <td><code>{{ $event->event_id }}</code></td>
                    <td>{{ $event->created_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ data_get($event->payload, 'message.text', '-') }}</td>
                </tr>
            @empty
                <tr><td colspan="3">هنوز وب‌هوکی دریافت نشده است.</td></tr>
            @endforelse
            </tbody>
        </table>
    </section>
</main>
</body>
</html>
