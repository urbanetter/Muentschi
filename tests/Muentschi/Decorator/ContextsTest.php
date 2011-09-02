<?php

/**
 * Muentschi_Context test case.
 */
class Muentschi_Decorator_ContextsTest extends PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $context = new Muentschi_Context('foo');
        $context->setContent(array('one' => 1,'two' => 2, 'three' => 3));
        $context->add('content');
        $decorator = new Muentschi_Decorator_Contexts('foo');
        $decorator->context($context);
        $actual = $decorator->render();
        $expected = '123';

        $this->assertEquals($expected, $actual);
    }

    public function testIdsByParam()
    {
        $context = new Muentschi_Context('foo');
        $context->setContent(array('one' => 1,'two' => 2, 'three' => 3));
        $context->add('content');
        $decorator = new Muentschi_Decorator_Contexts(array('name' => 'foo', 'ids' => 'one,two'));
        $decorator->context($context);
        $actual = $decorator->render();
        $expected = '12';

        $this->assertEquals($expected, $actual);
    }
    
    public function testIdsByContext()
    {
        $context = new Muentschi_Context('foo');
        $context->setContent(array('one' => 1,'two' => 2, 'three' => 3));
        $context->add('content');
        $context->ids('foo', 'one,two');
        $decorator = new Muentschi_Decorator_Contexts('foo');
        $decorator->context($context);
        $actual = $decorator->render();
        $expected = '12';

        $this->assertEquals($expected, $actual);
    }
    
    public function testComputedTags()
    {
        $context = new Muentschi_Context('foo');
        $context->setContent(array('one' => 'value','two' => '', 'three' => 'value'));
        $context->select('sub')->add('content', array('computedTags' => true));
        $decorator = new Muentschi_Decorator_Contexts(array('name' => 'sub', 'separator' => ';'));
        $decorator->context($context);
        $actual = $decorator->render();
        $expected = 'first,even;empty,odd;last,even;';

        $this->assertEquals($expected, $actual);
    }

}