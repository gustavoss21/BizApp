
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/style_cliente.css">
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
