<?php
$filter = [];
// $filter = [1, 2, 3];
$t = ['key' => 'value'];
$v = count($filter) - 1 ?? 100;

print_r(count($filter) / 2?array_fill(0, count($filter) - 1, 'abc'):100);
// print_r($v);

