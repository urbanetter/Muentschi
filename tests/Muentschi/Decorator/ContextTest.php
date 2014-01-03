<?php

namespace Muentschi\Decorator;

use Muentschi\Context as MainContext;

class ContextTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $context = new MainContext('foo');
        $context->add('dummy');
        $context->setContent(array('one', 'two'));
        $decorator = new Contexts('foo');
        $decorator->context($context);
        $actual = $decorator->render();
        $expected = 'dummy()dummy()';

        $this->assertEquals($actual, $expected);
    }

}