<?php

namespace Imanghafoori\FacadeTests\Stubs;

class ConcreteFacadeStub
{
    public function m1(FacadeStub1 $p1)
    {
        return $p1->a;
    }

    public function m2(FacadeStub1 $p1, $p2)
    {
        return $p1->a.$p2;
    }

    public function m3(FacadeStub1 $p1, $p2 = 'def2', $p3 = 'def3')
    {
        return $p1->a.$p2.$p3;
    }

    public function m4($p1, $p2 = 'def2')
    {
        return get_class($p1).$p2;
    }

    public function m5($p1, FacadeStub1 $p2, $p3 = 'def3')
    {
        return $p1.get_class($p2).$p3;
    }

    public function m6(FacadeStub1 $p1, FacadeStub2 $p2, $p3, $p4 = 'default')
    {
        return $p1->a.$p2->b.$p3.$p4;
    }

    public function m7(FacadeStub1 $p1, $p2, $p3, FacadeStub2 $p4)
    {
        return $p1->a.$p2.$p3.$p4->b;
    }

    public function faulty(FacadeStub1 $p1)
    {
        $p1->method();
    }
}
