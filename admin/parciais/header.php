<header>
    <nav id="navbar">
        <div id="content-nav">
            <div>
                <a href="http://127.0.0.1/projeto_api/admin/">ADMIN</a>
            </div>
            <?php if(isset($_SESSION['user']['username']) and $_SESSION['user']['username']){ ?>
                <div id="content-user" style="color: rgb(255 255 255);" >
                <a href="http://127.0.0.1/projeto_api/app/clientes">App</a>
            </div>
            <div id="content-user" style="color: rgb(255 255 255);" >
                <?php echo $_SESSION['user']['username'] ?>
                <ul id="drop-user">
             <li><a href="http://127.0.0.1/projeto_api/admin/auth/logout.php/">logout</a></li>
            </ul>
            </div>
           
            <?php }else{ ?>
                <div>
                    <a href="http://127.0.0.1/projeto_api/admin/auth/login.php/">login</a> 
                </div>
                <?php }?>
            <div>

                <!-- <div>
                    <input id="search" type="search">
                    <label for="search">perquisar</label>
                </div> -->
            </div>
        </div>
    </nav>
</header>