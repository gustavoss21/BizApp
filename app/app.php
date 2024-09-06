
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="/projeto_api/app/assets/css/style.css">
    <link rel="stylesheet" href="/projeto_api/app/assets/css/style_cliente.css">
    <link rel="stylesheet" href="/projeto_api/app/assets/css/style_form.css">
</head>
<body>
    
    <?php 
    include 'parciais/header.php';
    if(isset($message['error'])){
        echo <<<HTML
                <p style="color:red; text-align:center">{$message['message']}</p> 
            HTML;
    };
    
    echo $body; 
    ?>
</body>
</html>
