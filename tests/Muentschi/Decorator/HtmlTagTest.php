<?php
/**
 * Muentschi_Context test case.
 */
class Muentschi_Decorator_HtmlTagTest extends PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $decorator = new Muentschi_Decorator_HtmlTag('div');
        $actual = $decorator->render('foo');
        $expected = '<div>foo</div>';

        $this->assertEquals($expected, $actual);
    }

    public function testTagOption()
    {
        $decorator = new Muentschi_Decorator_HtmlTag();
        $decorator->setOption('tag', 'h1');
        $expected = '<h1>foo</h1>';
        $actual = $decorator->render('foo');
        $this->assertSame($expected, $actual);
    }

    public function testPlacementPrepend()
    {
        $decorator = new Muentschi_Decorator_HtmlTag('div');
        $decorator->setOption('placement', 'prepend');
        $expected = '<div/>foo';
        $actual = $decorator->render('foo');
        $this->assertSame($expected, $actual);
    }

    public function testPlacementAppend()
    {
        $decorator = new Muentschi_Decorator_HtmlTag('div');
        $decorator->setOption('placement', 'append');
        $expected = 'foo<div/>';
        $actual = $decorator->render('foo');
        $this->assertSame($expected, $actual);
    }

    public function testAttributes()
    {
        $decorator = new Muentschi_Decorator_HtmlTag('div');
        $decorator->setOption('class', 'class');
        $expected = '<div class="class">foo</div>';
        $actual = $decorator->render('foo');
        $this->assertSame($expected, $actual);
    }

    public function testClassMerge()
    {
        $decorator = new Muentschi_Decorator_HtmlTag('div');
        $decorator->setOption('class', 'class');
        $toMerge = new Muentschi_Decorator_HtmlTag(array('class' => 'second'));
        $decorator->merge($toMerge);
        $expected = '<div class="class second">foo</div>';
        $actual = $decorator->render('foo');
        $this->assertSame($expected, $actual);
    }

}