<?php

namespace Muentschi\Decorator;

class HtmlTagTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $decorator = new HtmlTag('div');
        $actual = $decorator->render('foo');
        $expected = '<div>foo</div>';

        $this->assertEquals($expected, $actual);
    }

    public function testTagOption()
    {
        $decorator = new HtmlTag();
        $decorator->setOption('tag', 'h1');
        $expected = '<h1>foo</h1>';
        $actual = $decorator->render('foo');
        $this->assertSame($expected, $actual);
    }

    public function testPlacementPrepend()
    {
        $decorator = new HtmlTag('div');
        $decorator->setOption('placement', 'prepend');
        $expected = '<div/>foo';
        $actual = $decorator->render('foo');
        $this->assertSame($expected, $actual);
    }

    public function testPlacementAppend()
    {
        $decorator = new HtmlTag('div');
        $decorator->setOption('placement', 'append');
        $expected = 'foo<div/>';
        $actual = $decorator->render('foo');
        $this->assertSame($expected, $actual);
    }

    public function testAttributes()
    {
        $decorator = new HtmlTag('div');
        $decorator->setOption('class', 'class');
        $expected = '<div class="class">foo</div>';
        $actual = $decorator->render('foo');
        $this->assertSame($expected, $actual);
    }

    public function testClassMerge()
    {
        $decorator = new HtmlTag('div');
        $decorator->setOption('class', 'class');
        $toMerge = new HtmlTag(array('class' => 'second'));
        $decorator->merge($toMerge);
        $expected = '<div class="class second">foo</div>';
        $actual = $decorator->render('foo');
        $this->assertSame($expected, $actual);
    }

}