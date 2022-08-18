<?php

interface FooInterface{
  public function returnString():string;
}

class FooClass implements FooInterface{
  public function returnString(): string
  {
    return 'aaaa';
  }
}

foreach(class_implements(FooClass::class) as $key => $val){
  print_r([
    'key' => $key,
    'val' => $val,
  ]);
}