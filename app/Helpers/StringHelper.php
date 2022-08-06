<?php

namespace App\Helpers;

use Illuminate\Support\Str as SupportStr;

class StringHelper extends SupportStr{

  public static function camelArrayKeys(array $array, bool $recursive = true): array
  {
    $formatted = [];

    foreach($array as $key => $value){
      if($recursive && is_array($value)){
        $value = self::camelArrayKeys( $value );
      }

      $formatted[ self::camel( $key ) ] = $value;
    }

    return $formatted;
  }

}
