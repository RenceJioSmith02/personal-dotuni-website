<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 – Page Not Found | DoTUni</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #111827;
        }

        .container {
            text-align: center;
            padding: 3rem 2rem;
            max-width: 480px;
            width: 100%;
        }

        .badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: #1e40af;
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 9999px;
            padding: 0.25rem 0.9rem;
            margin-bottom: 1.5rem;
        }

        .error-code {
            font-size: 7rem;
            font-weight: 700;
            color: #3b82f6;
            line-height: 1;
            letter-spacing: -4px;
            margin-bottom: 0.75rem;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.75rem;
        }

        .error-message {
            font-size: 0.9rem;
            color: #6b7280;
            line-height: 1.7;
            margin-bottom: 2rem;
        }

        .actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 0.6rem 1.4rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background 0.15s;
        }

        .btn-primary {
            background-color: #1e3a5f;
            color: #ffffff;
        }

        .btn-primary:hover { background-color: #16304f; }

        .btn-secondary {
            background-color: #ffffff;
            color: #374151;
            border-color: #d1d5db;
        }

        .btn-secondary:hover { background-color: #f9fafb; }

        .divider {
            margin: 2rem auto 1rem;
            border: none;
            border-top: 1px solid #e5e7eb;
            max-width: 160px;
        }

        .footer-note {
            font-size: 0.78rem;
            color: #9ca3af;
        }
    </style>
</head>
<body>
    <div class="container">
        <span class="badge">Error 404</span>
        <div class="error-code">404</div>
        <h1 class="error-title">Page Not Found</h1>
        <p class="error-message">
            The page you're looking for doesn't exist or may have been moved.<br>
            Double-check the URL, or head back to the homepage.
        </p>
        <div class="actions">
            <a href="{{ route('website.home') }}" class="btn btn-primary">Back to Home</a>
            <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
        </div>
        <hr class="divider">
        <p class="footer-note">DoTUni CMS &mdash; Department of Technology</p>
    </div>
</body>
</html>