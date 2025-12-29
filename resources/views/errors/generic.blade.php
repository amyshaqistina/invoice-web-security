<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error') - {{ config('app.name', 'Invoice System') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #374151;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .error-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ef4444, #f59e0b, #10b981);
        }

        .error-code {
            font-size: 72px;
            font-weight: 800;
            background: linear-gradient(135deg, #ef4444, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .error-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 16px;
        }

        .error-message {
            color: #6b7280;
            margin-bottom: 32px;
            font-size: 16px;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        .error-details {
            margin-top: 32px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 8px;
            text-align: left;
            display: none;
        }

        .error-details.show {
            display: block;
        }

        .details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .details-title {
            font-weight: 600;
            color: #374151;
        }

        .toggle-details {
            background: none;
            border: none;
            color: #6b7280;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .toggle-details:hover {
            color: #374151;
        }

        .details-content {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 12px;
            color: #374151;
            white-space: pre-wrap;
            overflow-x: auto;
            max-height: 200px;
            overflow-y: auto;
        }

        @media (max-width: 640px) {
            .error-container {
                padding: 30px 20px;
            }

            .error-code {
                font-size: 56px;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">{{ $errorCode ?? 500 }}</div>
        <h1 class="error-title">{{ $errorTitle ?? 'Internal Server Error' }}</h1>
        <p class="error-message">{{ $errorMessage ?? 'Something went wrong. Please try again later.' }}</p>

        <div class="actions">
            <a href="javascript:history.back()" class="btn btn-secondary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Go Back
            </a>
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Go Home
            </a>
        </div>

        @if(($showDetails ?? false) && isset($exception))
        <div class="error-details" id="errorDetails">
            <div class="details-header">
                <div class="details-title">Error Details</div>
                <button type="button" class="toggle-details" onclick="toggleDetails()">
                    Hide Details
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                </button>
            </div>
            <div class="details-content">
                <strong>Message:</strong> {{ $exception->getMessage() }}

                @if($exception->getFile())
                <br><strong>File:</strong> {{ $exception->getFile() }}:{{ $exception->getLine() }}
                @endif

                @if($exception->getTrace())
                <br><br><strong>Stack Trace:</strong><br>
                {{ substr($exception->getTraceAsString(), 0, 1000) }}...
                @endif
            </div>
        </div>
        @endif

        <div style="margin-top: 32px; font-size: 12px; color: #9ca3af;">
            Need help? Contact support at support@{{ config('app.domain', 'example.com') }}
        </div>
    </div>

    <script>
        function toggleDetails() {
            const details = document.getElementById('errorDetails');
            const button = document.querySelector('.toggle-details');

            if (details.classList.contains('show')) {
                details.classList.remove('show');
                button.innerHTML = 'Show Details <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>';
            } else {
                details.classList.add('show');
                button.innerHTML = 'Hide Details <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>';
            }
        }
    </script>
</body>
</html>
