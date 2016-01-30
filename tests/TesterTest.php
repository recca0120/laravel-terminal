<?php

use Mockery as m;

class TesterTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $app = App::getInstance();
        $app->migrate('up');
    }

    public function tearDown()
    {
        m::close();
        $app = App::getInstance();
        $app->migrate('down');
    }

    public function test_tester()
    {
        $this->assertTrue(true);
    }
}
