<?php
/**
 * Muentschi_Context test case.
 */
class Muentschi_Decorator_ContentTest extends PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $decorator = new Muentschi_Decorator_Content();
        $context = new Muentschi_Context();
        $context->setContent('foo');
        $decorator->context($context);
        $actual = $decorator->render();
        $expected = 'foo';

        $this->assertEquals($expected, $actual);
    }

    public function testPlacementPrepend()
    {
        $decorator = new Muentschi_Decorator_Content();
        $context = new Muentschi_Context();
        $context->setContent('foo');
        $decorator->context($context);
        $decorator->setOption('placement', 'prepend');
        $expected = 'foobar';
        $actual = $decorator->render('bar');
        $this->assertSame($expected, $actual);
    }

    public function testPlacementAppend()
    {
        $decorator = new Muentschi_Decorator_Content();
        $context = new Muentschi_Context();
        $context->setContent('foo');
        $decorator->context($context);
        $decorator->setOption('placement', 'append');
        $expected = 'barfoo';
        $actual = $decorator->render('bar');
        $this->assertSame($expected, $actual);
    }

    public function testFormat()
    {
        $decorator = new Muentschi_Decorator_Content('foo');
        $expected = 'foo';
        $actual = $decorator->render();
        $this->assertEquals($expected, $actual);
    }

    public function testFormatPlaceholders()
    {
        $decorator = new Muentschi_Decorator_Content('{foo}');
        $context = new Muentschi_Context('foo');
        $context->setContent('foo', 'bar');

        $decorator->context($context);
        $expected = 'bar';
        $actual = $decorator->render();

        $this->assertEquals($expected, $actual);
    }

    public function testNoContentRendersEmpty()
    {
        $decorator = new Muentschi_Decorator_Content('{baz}');
        $context = new Muentschi_Context();
        $context->setContent('foo', 'bar');
        $decorator->context($context);

        $expected = '';
        $actual = $decorator->render();
        $this->assertEquals($expected, $actual);
    }

    public function testIdDisplay()
    {
        $decorator = new Muentschi_Decorator_Content(array('id' => true));
        $context = new Muentschi_Context('name', 'id');
        $decorator->context($context);

        $expected = 'id';
        $actual = $decorator->render();
        $this->assertEquals($expected, $actual);
    }

}