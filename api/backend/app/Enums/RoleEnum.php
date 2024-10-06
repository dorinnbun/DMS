<?php

namespace App\Enums;

enum RoleEnum: string
{
  case SUPERADMIN = 'superadmin';
  case ADMIN = 'admin';
  case USER = 'user';
  case GUEST = 'guest';

  public static function toArray(): array
  {
    // return [
    //   self::SUPERADMIN => self::SUPERADMIN,
    //   self::ADMIN => self::ADMIN,
    //   self::USER => self::USER,
    //   self::GUEST => self::GUEST,
    // ];
    //   return array_map(function ($value) {
    //     return (string) $value;
    // }, array_values(self::cases()));
    // }
    $result = [];

    foreach (self::cases() as $key => $value) {
      $result[$key] = $value->value;
    }

    return $result;
  }
}
