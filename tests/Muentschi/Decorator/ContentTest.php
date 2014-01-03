<?php

namespace Muentschi\Decorator;

use Muentschi\Context;

class ContentTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $decorator = new Content();
        $context = new Context();
        $context->setContent('foo');
        $decorator->context($context);
        $actual = $decorator->render();
        $expected = 'foo';

        $this->assertEquals($expected, $actual);
    }

    public function testPlacementPrepend()
    {
        $decorator = new Content();
        $context = new Context();
        $context->setContent('foo');
        $decorator->context($context);
        $decorator->setOption('placement', 'prepend');
        $expected = 'foobar';
        $actual = $decorator->render('bar');
        $this->assertSame($expected, $actual);
    }

    public function testPlacementAppend()
    {
        $decorator = new Content();
        $context = new Context();
        $context->setContent('foo');
        $decorator->context($context);
        $decorator->setOption('placement', 'append');
        $expected = 'barfoo';
        $actual = $decorator->render('bar');
        $this->assertSame($expected, $actual);
    }

    public function testFormat()
    {
        $decorator = new Content('foo');
        $expected = 'foo';
        $actual = $decorator->render();
        $this->assertEquals($expected, $actual);
    }

    public function testFormatPlaceholders()
    {
        $decorator = new Content('{foo}');
        $context = new Context('foo');
        $context->setContent('foo', 'bar');

        $decorator->context($context);
        $expected = 'bar';
        $actual = $decorator->render();

        $this->assertEquals($expected, $actual);
    }

    public function testNoContentRendersEmpty()
    {
        $decorator = new Content('{baz}');
        $context = new Context();
        $context->setContent('foo', 'bar');
        $decorator->context($context);

        $expected = '';
        $actual = $decorator->render();
        $this->assertEquals($expected, $actual);
    }

    public function testIdDisplay()
    {
        $decorator = new Content(array('id' => true));
        $context = new Context('name', 'id');
        $decorator->context($context);

        $expected = 'id';
        $actual = $decorator->render();
        $this->assertEquals($expected, $actual);
    }

}