<?php

$pathern = '/(([^:;]+):([^;\s]+))(;\1|$)/';
$params = ['filter'=>'id_produto:22;quantidade:5'];
if (@!$params['filter'] || preg_match($pathern,$params['filter']) == 0) {
    print_r('error');
}else{
    print_r('não error');
}


