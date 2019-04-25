<?php

namespace Imanghafoori\FacadeTests\Stubs;

use Imanghafoori\SmartFacades\Facade;

class FacadeStub extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'foo';
    }
}
