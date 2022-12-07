<?php

namespace Imanghafoori\FacadeTests\Stubs;

class ConcreteFacadeStub2 extends ConcreteFacadeStub
{
    public function m1(FacadeStub1 $p1)
    {
        return 'I am stub2';
    }
}
