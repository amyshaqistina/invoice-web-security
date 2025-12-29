<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Internal Server Error</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .error-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 60px 40px;
            text-align: center;
            max-width: 600px;
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #f5576c;
            margin-bottom: 20px;
            line-height: 1;
        }
        .error-title {
            font-size: 32px;
            color: #2d3748;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .error-message {
            font-size: 16px;
            color: #718096;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .error-details {
            background: #f7fafc;
            border-left: 4px solid #f5576c;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: left;
            font-size: 14px;
            color: #4a5568;
            max-height: 300px;
            overflow-y: auto;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        }
        .error-button {
            display: inline-block;
            background: #f5576c;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }
        .error-button:hover {
            background: #e83e53;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">500</div>
        <div class="error-title">{{ $title ?? 'Internal Server Error' }}</div>
        <div class="error-message">{{ $message ?? 'Something went wrong. Please try again later.' }}</div>

        @if ($exception && config('app.debug'))
            <div class="error-details">
                <strong>Exception:</strong><br>
                {{ class_basename($exception) }}<br><br>
                <strong>Message:</strong><br>
                {{ $exception->getMessage() }}<br><br>
                @if ($exception->getFile())
                    <strong>File:</strong><br>
                    {{ $exception->getFile() }}:{{ $exception->getLine() }}
                @endif
            </div>
        @endif

        <a href="{{ url('/admin') }}" class="error-button">Back to Dashboard</a>
    </div>
</body>
</html>
