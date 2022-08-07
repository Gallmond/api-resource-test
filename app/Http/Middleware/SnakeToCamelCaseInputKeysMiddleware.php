<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SnakeToCamelCaseInputKeysMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
  {
    $data = $request->all();
    
    $request->merge($this->snake($data));

    return $next($request);
  }

  protected function snake(array $data): array
  {
    $formatted = [];

    foreach($data as $key => $value){
      if(is_array($value)){
        $value = $this->snake( $value );
      }

      $formatted[ Str::snake( $key ) ] = $value;
    }

    return $formatted;
  }
}
