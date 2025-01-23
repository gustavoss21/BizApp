<?php
$email_html = <<<HTML
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Email Validation</title>
        <style>
            .button:hover {
                background-color:rgb(124, 58, 57);
            }
            .footer {
                text-align: center;
                margin-top: 20px;
                font-size: 12px;
                color: #666;
            }
        </style>
    </head>
    <body style="font-family: Arial, sans-serif;
                line-height: 1.6;
                background-color: #f9f9f9;
                margin: 0;
                padding: 0;"
            >
        <div style=" max-width: 600px;
                margin: 20px auto;
                background: #ffffff;
                border: 1px solid #ddd;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                padding: 20px;"
            >
            <h1 style=" text-align: center;
                color: #a01f1c;
                margin-bottom: 20px;"
                >
                Confirme seu Endereço de Email
            </h1>
            <div style="color: #333;">
                <p>Olá, $username</p>
                <p>Obrigado por se cadastrar! Por favor, clique no botão abaixo para validar seu endereço de email.</p>
                <a style="display: block;
                    margin: auto;
                    padding: 10px 0 10px 10px;
                    margin-top: 20px;
                    background-color: #a01f1c;
                    color: #ffffff;
                    text-decoration: none;
                    border-radius: 5px;
                    font-weight: bold;
                    width: 102px;" href="{$validation_url}" 
                    >
                    Validar Email
                </a>
                <p>Se você não realizou esta solicitação, por favor, ignore este email.</p>
            </div>
            <div class="footer">
                <p>© 2025 Sua Empresa. Todos os direitos reservados.</p>
            </div>
        </div>
    </body>
    </html>
HTML;

return $email_html;