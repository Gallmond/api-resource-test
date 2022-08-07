<?php

$arr1 = [
  'some' => 'aaa' ,
  'keys' => 'bbb' ,
  'here' => 'ccc' 
];

$arr2 = [
  'array' => 'one',
  'with' => 'two',
  'some' => 'three',
  'keys' => 'four',
  'fizz' => 'five',
];

print_r(array_intersect_key(
  $arr2, $arr1 
));

