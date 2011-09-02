<?php
/**
 * Muentschi_Selector test case.
 */
class Muentschi_SelectorTest extends PHPUnit_Framework_TestCase
{
    public function testDecorateWithClass()
    {
        $selector = new Muentschi_Selector();
        $decorator = new Muentschi_Decorator_Dummy();
        $selector->add($decorator);
        $actual = $selector->decorators['dummy'];
        $this->assertSame($decorator, $actual);
    }

    public function testDecorateWithName()
    {
        $selector = new Muentschi_Selector();
        $selector->add('dummy');
        $actual = $selector->decorators['dummy']->getName();;
        $this->assertEquals('dummy', $actual);
    }

    public function testDecorateWithDefaultOption()
    {
        $selector = new Muentschi_Selector();
        $selector->add('dummy', 'default');
        $actual = $selector->decorators['default']->getOption('name');
        $this->assertEquals('default', $actual);
    }

    public function testDecorateWithOptions()
    {
        $selector = new Muentschi_Selector();
        $selector->add('dummy', array('default' => 'default'));
        $actual = $selector->decorators['dummy']->getOption('default');
        $this->assertEquals('default', $actual);
    }

    public function testSetMergeFunction()
    {
        $selector = new Muentschi_Selector();
        $this->assertEquals('merge', $selector->getStrategy());
        $return = $selector->merge();
        $this->assertEquals('merge', $selector->getStrategy());
        $this->assertEquals($selector, $return);
    }

    public function testMergeExtending()
    {
        $selector = new Muentschi_Selector();
        $dummyDecorator = new Muentschi_Decorator_Dummy();
        $in = array('one' => 'foo');

        $selector->add($dummyDecorator);
        $expected = array('one' => 'foo', 'dummy' => $dummyDecorator);
        $this->assertEquals($expected, $selector->apply($in));
    }

    public function testMergeDecorator()
    {
        $selector = new Muentschi_Selector();
        $dummyDecorator = new Muentschi_Decorator_Dummy(array('bar' => 'foo'));
        $in = array('dummy' => $dummyDecorator);

        $selector->add('dummy');
        $expected = array('dummy' => $dummyDecorator);
        $actual = $selector->apply($in);
        $this->assertEquals($expected, $actual);
        $this->assertEquals('foo', $actual['dummy']->bar);
    }

    public function testDefaultDecorator()
    {
        $selector = new Muentschi_Selector();
        $expected = new Muentschi_Decorator_HtmlTag('h1');

        $selector->add('h1');
        $actual = $selector->decorators['h1'];

        $this->assertEquals($actual, $expected);
    }

    public function testSetReplaceFunction()
    {
        $selector = new Muentschi_Selector();
        $return = $selector->replace();
        $this->assertEquals('replace', $selector->getStrategy());
        $this->assertEquals($selector, $return);
    }

    public function testReplace()
    {
        $selector = new Muentschi_Selector();
        $dummyDecorator = new Muentschi_Decorator_Dummy();
        $in = array('one' => 'foo');

        $selector->add($dummyDecorator);
        $selector->replace();
        $expected = array('dummy' => $dummyDecorator);
        $this->assertEquals($expected, $selector->apply($in));
    }

    public function testSetInsteadOfFunction()
    {
        $selector = new Muentschi_Selector();
        $return = $selector->insteadOf('foo');
        $this->assertEquals('insteadOf', $selector->getStrategy());
        $this->assertEquals($selector, $return);
    }

    public function testInsteadOf()
    {
        $selector = new Muentschi_Selector();
        $in = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo');

        $selector->decorators = array('new' => 'bar');
        $selector->insteadOf('two');
        $expected = array('one' => 'foo', 'new' => 'bar', 'three' => 'foo');
        $this->assertEquals($expected, $selector->apply($in));
    }

    public function testInsteadOfWithWrongNameGetsAppended()
    {
        $selector = new Muentschi_Selector();
        $in = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo');

        $selector->decorators = array('new' => 'bar');
        $selector->insteadOf('baz');
        $expected = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo', 'new' => 'bar');
        $this->assertEquals($expected, $selector->apply($in));
    }

    public function testInsteadOfWithoutParamThrows()
    {
        $selector = new Muentschi_Selector();
        $in = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo');

        $selector->decorators = array('new' => 'bar');
        $selector->insteadOf();

        $this->setExpectedException('Muentschi_Exception', 'Strategy insteadOf needs param!');
        $selector->apply($in);
    }

    public function testSetAfterFunction()
    {
        $selector = new Muentschi_Selector();
        $return = $selector->after('foo');
        $this->assertEquals('after', $selector->getStrategy());
        $this->assertEquals($selector, $return);
    }

    public function testAfter()
    {
        $selector = new Muentschi_Selector();
        $in = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo');

        $selector->decorators = array('new' => 'bar');
        $selector->after('two');
        $expected = array('one' => 'foo', 'two' => 'foo', 'new' => 'bar', 'three' => 'foo');
        $this->assertEquals($expected, $selector->apply($in));
    }

    public function testAfterWithWrongNameGetsAppended()
    {
        $selector = new Muentschi_Selector();
        $in = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo');

        $selector->decorators = array('new' => 'bar');
        $selector->after('baz');
        $expected = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo', 'new' => 'bar');
        $this->assertEquals($expected, $selector->apply($in));
    }

    public function testAfterWithoutParamAppends()
    {
        $selector = new Muentschi_Selector();
        $in = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo');

        $selector->decorators = array('new' => 'bar');
        $selector->after('baz');
        $expected = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo', 'new' => 'bar');
        $this->assertEquals($expected, $selector->apply($in));
    }

    public function testSetBeforeFunction()
    {
        $selector = new Muentschi_Selector();
        $return = $selector->before('foo');
        $this->assertEquals('before', $selector->getStrategy());
        $this->assertEquals($selector, $return);
    }

    public function testBefore()
    {
        $selector = new Muentschi_Selector();
        $in = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo');

        $selector->decorators = array('new' => 'bar');
        $selector->before('two');
        $expected = array('one' => 'foo', 'new' => 'bar', 'two' => 'foo', 'three' => 'foo');
        $this->assertEquals($expected, $selector->apply($in));
    }

    public function testBeforeWithWrongNameGetsPrepended()
    {
        $selector = new Muentschi_Selector();
        $in = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo');

        $selector->decorators = array('new' => 'bar');
        $selector->before('baz');
        $expected = array( 'new' => 'bar', 'one' => 'foo', 'two' => 'foo', 'three' => 'foo');
        $this->assertEquals($expected, $selector->apply($in));
    }

    public function testBeforeWithoutParamPrepends()
    {
        $selector = new Muentschi_Selector();
        $in = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo');

        $selector->decorators = array('new' => 'bar');
        $selector->before('baz');
        $expected = array( 'new' => 'bar', 'one' => 'foo', 'two' => 'foo', 'three' => 'foo');
        $this->assertEquals($expected, $selector->apply($in));
    }

    public function testSetRemoveFunction()
    {
        $selector = new Muentschi_Selector();
        $return = $selector->remove('foo');
        $this->assertEquals('remove', $selector->getStrategy());
        $this->assertEquals($selector, $return);
    }

    public function testRemove()
    {
        $selector = new Muentschi_Selector();
        $in = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo');

        $selector->remove('two');
        $expected = array('one' => 'foo', 'three' => 'foo');
        $this->assertEquals($expected, $selector->apply($in));
    }

    public function testRemoveWithWrongParameterDoesNotChangeAnything()
    {
        $selector = new Muentschi_Selector();
        $in = array('one' => 'foo', 'two' => 'foo', 'three' => 'foo');

        $selector->remove('foo');
        $this->assertEquals($in, $selector->apply($in));
    }
}