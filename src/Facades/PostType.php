<?php

namespace WebTheory\SortOrder\Facades;

use WebTheory\Leonidas\PostType\Factory;

class PostType extends _Facade
{
    protected static function _getFacadeAccessor()
    {
        return Factory::class;
    }
}
