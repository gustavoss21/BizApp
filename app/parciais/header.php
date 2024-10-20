<?php
if(!isset($allowedRoute)){
    die('<div style="color:red;">Rota não encontrada</div>');
}
?>

<header>
    <nav id="navbar">
        <div id="content-nav">
            <div>
                <a href="#">home</a>
            </div>
            <div>
                <a href="http://127.0.0.1/projeto_api/app/produtos/">produtos</a>
            </div>
            <div>
                <a href="http://127.0.0.1/projeto_api/app/clientes/">clientes</a>
            </div>
            <?php if(isset($_SESSION['user']['username']) and $_SESSION['user']['username']){ ?>
                <div id="content-user" style="color: rgb(255 255 255);" >
                <a href="http://127.0.0.1/projeto_api/admin">Admin</a>
            </div>
            <?php } ?>
        </div>
    </nav>
</header>