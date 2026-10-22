<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            align-items: center;
            background: #f6f1e9;
            color: #201913;
            display: flex;
            font-family: Arial, sans-serif;
            justify-content: center;
            margin: 0;
            min-height: 100vh;
            padding: 24px;
            text-align: center;
        }

        main {
            max-width: 640px;
        }

        .code {
            color: #123f0b;
            font-size: clamp(5rem, 18vw, 8rem);
            font-weight: 800;
            line-height: 1;
        }

        h1 {
            font-size: clamp(1.8rem, 6vw, 2.5rem);
            margin: 16px 0 10px;
        }

        p {
            color: #82786d;
            line-height: 1.6;
            margin: 0 0 24px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }

        .button {
            border: 1px solid #a96e37;
            border-radius: 999px;
            color: #8e562a;
            font-size: 14px;
            font-weight: 700;
            padding: 13px 22px;
            text-decoration: none;
        }

        .button-primary {
            background: linear-gradient(120deg, #9a5727, #c09a58);
            border: 0;
            box-shadow: 0 8px 20px rgb(100 61 30 / 20%);
            color: #fff;
        }
    </style>
</head>
<body>
    <main>
        <div class="code">404</div>
        <h1>Page Not Found</h1>
        <p>The page you’re looking for doesn’t exist or may have moved.</p>
        <div class="actions">
            @if (request()->routeIs('admin.*'))
                <a href="{{ route('admin.dashboard') }}" class="button button-primary">Back to Dashboard</a>
                <a href="{{ route('admin.appointment.create') }}" class="button">Book Appointment</a>
            @else
                <a href="{{ route('saas.home') }}" class="button button-primary">Back Home</a>
                <a href="{{ route('book') }}" class="button">Book Appointment</a>
            @endif
        </div>
    </main>
</body>
</html>
