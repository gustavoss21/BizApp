<!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Email Validation</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                background-color: #f9f9f9;
                margin: 0;
                padding: 0;
            }
            .email-container {
                max-width: 600px;
                margin: 20px auto;
                background: #ffffff;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                padding: 20px;
            }
            .email-header {
                text-align: center;
                color: #a01f1c;
                margin-bottom: 20px;
            }
            .email-content {
                color: #333;
            }
            .button {
                display: block;
                margin: auto;
                padding: 10px 20px;
                margin-top: 20px;
                background-color: #a01f1c;
                color: #ffffff;
                text-decoration: none;
                border-radius: 5px;
                font-weight: bold;
                width: 102px;
            }
            .button:hover {
                background-color:rgb(207, 97, 95);
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                font-size: 12px;
                color: #666;
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <h1 class="email-header">Confirme seu Endereço de Email</h1>
            <div class="email-content">
                <p>Olá, $username</p>
                <p>Obrigado por se cadastrar! Por favor, clique no botão abaixo para validar seu endereço de email.</p>
                <a href="{$validation_url}" class="button">Validar Email</a>
                <p>Se você não realizou esta solicitação, por favor, ignore este email.</p>
            </div>
            <div class="footer">
                <p>© 2025 Sua Empresa. Todos os direitos reservados.</p>
            </div>
        </div>
    </body>
    </html>