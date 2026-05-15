<?php

namespace App\UserManagement\Application\ListUser;

class ListUserQuery
{
    public function __construct(
      public ?string    $term       = null,
      public ?string    $role       = null,
      public ?bool      $isActive   = null,
      public int        $page       = 1,
      public int        $limit      = 10,
    ) {}
}
