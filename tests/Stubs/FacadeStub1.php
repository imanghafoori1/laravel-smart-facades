<?php

namespace Imanghafoori\FacadeTests\Stubs;

class FacadeStub1
{
    public $a;

    public function __construct($a = 'def1')
    {
        $this->a = $a;
    }

    public function method($param)
    {
    }
}
