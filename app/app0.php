<?php

$array1 = [1, 2, 3];
$array = ['a'=>1, 'b' => 2, 'c'=> 3, 'd'=>4];
$t = '';
foreach($array as $key=>$a){
    // if($a == 2){
    //     continue;
    // }
    print_r($a);
    // next($array);
    @$t .= $array1[$a];
}
// [$t,$el,$a,$d] = $array;
print_r('---------------');
print_r($t);


