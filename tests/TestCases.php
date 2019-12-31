<?php

namespace Imanghafoori\FacadeTests;

use TestCase;
use ArgumentCountError;
use Imanghafoori\FacadeTests\Stubs\{FacadeStub, FacadeStub1, FacadeStub2, ApplicationStub, ConcreteFacadeStub};

class TestCases extends TestCase
{
    public function testDoesNotSwallowInternalTypeErrorOfTheTargetClass()
    {
        try {
            FacadeStub::faulty();
        } catch (ArgumentCountError $error) {
            $this->assertRegExp('/Too few arguments to function .*?FacadeStub1::method\(\), 0 passed in .* and exactly 1 expected/', $error->getMessage());
        }
    }

    public function testItCanInjectForFirstParam()
    {
        $this->assertEquals('def1', FacadeStub::m1(new FacadeStub1()));
        $this->assertEquals('def1', FacadeStub::m1());
        $this->assertEquals('def12', FacadeStub::m2('2'));
        $this->assertEquals('def1'.'ab'.'def3', FacadeStub::m3(new FacadeStub1(), 'ab'));
        $this->assertEquals('def1'.'bb'.'def3', FacadeStub::m3('bb'));
        $this->assertEquals('def1'.'bb'.'cc', FacadeStub::m3('bb', 'cc'));
        $this->assertEquals('def1'.'bb'.'cc', FacadeStub::m3('bb', 'cc', 'dd'));
        $this->assertEquals('val1'.'def2'.'def3', FacadeStub::m3(new FacadeStub1('val1')));
        $this->assertEquals('def1'.'def2'.'def3', FacadeStub::m3());
    }

    public function testItCanInjectForSecondParam()
    {
        $this->assertEquals('abc'.FacadeStub1::class.'def3', FacadeStub::m5('abc'));
        $this->assertEquals('abc'.FacadeStub1::class.'def3', FacadeStub::m5('abc', new FacadeStub1()));
        $this->assertEquals('bb'.FacadeStub1::class.'cc', FacadeStub::m5('bb', 'cc'));
        $this->assertEquals('bb'.FacadeStub1::class.'cc', FacadeStub::m5('bb', new FacadeStub1, 'cc'));
    }

    public function testItCanInjectTwoDependencies()
    {
        $this->assertEquals('val1'.'def2'.'x_default', FacadeStub::m6(new FacadeStub1('val1'), 'x_'));
        $this->assertEquals('def1'.'val2'.'x_default', FacadeStub::m6(new FacadeStub2('val2'), 'x_'));
        $this->assertEquals('val1'.'val2'.'x_default', FacadeStub::m6(new FacadeStub1('val1'), new FacadeStub2('val2'), 'x_'));
        $this->assertEquals('val1'.'def2'.'x_y', FacadeStub::m6(new FacadeStub1('val1'), 'x_', 'y'));
        $this->assertEquals('def1'.'val2'.'x_y', FacadeStub::m6(new FacadeStub2('val2'), 'x_', 'y'));
        $this->assertEquals('def1'.'def2'.'x_default', FacadeStub::m6('x_'));
        $this->assertEquals('def1'.'def2'.'x_y', FacadeStub::m6('x_', 'y'));
    }

    public function testItCanInjectTwoDependencies2()
    {

        FacadeStub::setFacadeApplication(app());
        FacadeStub::shouldProxyTo(ConcreteFacadeStub::class);
        $this->assertEquals('def1'.'x_default'.'def2', FacadeStub::m7('x_'));
        $this->assertEquals('val1'.'x_default'.'def2', FacadeStub::m7(new FacadeStub1('val1'),'x_'));
        $this->assertEquals('val1'.'x_y'.'def2', FacadeStub::m7(new FacadeStub1('val1'),'x_', 'y'));
        $this->assertEquals('val1'.'x_y'.'val2', FacadeStub::m7(new FacadeStub1('val1'),'x_', 'y', new FacadeStub2('val2')));
    }

    public function setUp(): void
    {
        parent::setUp();
        $app = new ApplicationStub;
        $app->setAttributes([
            'foo' => new ConcreteFacadeStub,
            FacadeStub1::class => new FacadeStub1(),
            FacadeStub2::class => new FacadeStub2(),
        ]);


        $app = app();
        FacadeStub::setFacadeApplication($app);
        FacadeStub::shouldProxyTo(ConcreteFacadeStub::class);
    }
}
