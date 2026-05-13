<?php

namespace App\ProjectManagement\Domain\Development\Link;

interface LinkRepositoryInterface
{

    public function save(Link $link, bool $flush): void;
    public function delete(Link $link): void;

}
