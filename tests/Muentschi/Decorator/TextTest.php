<?php
/**
 * Muentschi_Context test case.
 */
class Muentschi_Decorator_TextTest extends PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $decorator = new Muentschi_Decorator_Text('foo');
        $actual = $decorator->render();
        $expected = 'foo';

        $this->assertEquals($expected, $actual);
    }

    public function testPlacementPrepend()
    {
        $decorator = new Muentschi_Decorator_Text('foo');
        $decorator->setOption('placement', 'prepend');
        $expected = 'foobar';
        $actual = $decorator->render('bar');
        $this->assertSame($expected, $actual);
    }

    public function testPlacementAppend()
    {
        $decorator = new Muentschi_Decorator_Text('foo');
        $decorator->setOption('placement', 'append');
        $expected = 'barfoo';
        $actual = $decorator->render('bar');
        $this->assertSame($expected, $actual);
    }
}