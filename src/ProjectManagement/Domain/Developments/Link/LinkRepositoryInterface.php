<?php

namespace App\ProjectManagement\Domain\Developments\Link;

interface LinkRepositoryInterface
{

    public function save(Link $link): void;
    public function delete(Link $link): void;

}
