
<?php
if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="/projeto_api/admin/assets/css/style.css">
    <link rel="stylesheet" href="/projeto_api/admin/assets/css/style_cliente.css">
    <link rel="stylesheet" href="/projeto_api/admin/assets/css/style_form.css">
</head>
<body>
    
    <?php
    include 'parciais/header.php';

    if ($message) {
        echo <<<HTML
                <p style="color:{$message['color']}; text-align:center">{$message['msg']}</p> 
            HTML;
    };
    echo $body;
    ?>
    <script src="/projeto_api/admin/assets/js/script.js"></script>
    <script src="/projeto_api/admin/assets/js/form.js"></script>
</body>
</html>