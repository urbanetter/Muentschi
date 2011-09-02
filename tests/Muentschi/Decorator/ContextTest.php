<?php

/**
 * Muentschi_Context test case.
 */
class Muentschi_Decorator_ContextTest extends PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $context = new Muentschi_Context('foo');
        $context->add('dummy');
        $context->setContent(array('one', 'two'));
        $decorator = new Muentschi_Decorator_Contexts('foo');
        $decorator->context($context);
        $actual = $decorator->render();
        $expected = 'dummy()dummy()';

        $this->assertEquals($actual, $expected);
    }

    /*public function testPlacementPrepend()
    {
        $decorator = new Muentschi_Decorator_Context();
        $context = new Muentschi_Context();
        $context->content('foo');
        $decorator->context($context);
        $decorator->setOption('placement', 'prepend');
        $expected = 'foobar';
        $actual = $decorator->render('bar');
        $this->assertSame($expected, $actual);
    }

    public function testPlacementAppend()
    {
        $decorator = new Muentschi_Decorator_Context();
        $context = new Muentschi_Context();
        $context->content('foo');
        $decorator->context($context);
        $decorator->setOption('placement', 'append');
        $expected = 'barfoo';
        $actual = $decorator->render('bar');
        $this->assertSame($expected, $actual);
    }*/

}