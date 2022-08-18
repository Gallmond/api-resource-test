<?php

namespace App\Models\Interfaces;

interface HasAutoCRUDMethods{

  /**
   * @return string
   */
  public function getTable(): string;

  /**
   * @return array
   */
  public function getFillable(): array;

  /**
   * @return array<string, string>
   */
  public function getCasts(): array;

  /**
   * @return array
   */
  public function getHidden(): array;

}

