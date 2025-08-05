<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Error de Preview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            text-align: center;
            color: #333;
        }
        .error-container {
            border: 2px dashed #dc3545;
            padding: 40px;
            border-radius: 10px;
            margin: 20px;
            background-color: #f8f9fa;
        }
        .error-icon {
            font-size: 48px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .error-title {
            color: #dc3545;
            font-size: 24px;
            margin: 20px 0;
        }
        .error-message {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">⚠️</div>
        <h1 class="error-title">Error en Preview PDF</h1>
        <div class="error-message">
            <p><strong>{{ $message ?? 'Error desconocido' }}</strong></p>
            <p>Por favor, revisa la configuración y vuelve a intentar.</p>
        </div>
    </div>
</body>
</html>
