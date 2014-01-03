<?php

namespace Muentschi;

class DecoratorTest extends \PHPUnit_Framework_TestCase
{

    public function testOption()
    {
        $decorator = new Decorator();
        $decorator->setOption('foo', 'bar');
        $actual = $decorator->getOption('foo');
        $this->assertSame('bar', $actual);
    }

    public function testOptions()
    {
        $options = array('foo' => 'bar');
        $decorator = new Decorator();
        $decorator->setOptions($options);
        $actual = $decorator->getOption('foo');
        $this->assertSame('bar', $actual);
    }

    public function testOptionsAsConstructor()
    {
        $options = array('foo' => 'bar');
        $decorator = new Decorator($options);
        $actual = $decorator->getOption('foo');
        $this->assertSame('bar', $actual);
    }

    public function testRemoveOption()
    {
        $options = array('foo' => 'bar');
        $decorator = new Decorator($options);
        $removed = $decorator->removeOption('foo');
        $actual = $decorator->getOption('foo');
        $this->assertSame('bar', $removed);
        $this->assertSame(null, $actual);
    }

    public function testClearOptions()
    {
        $options = array('foo' => 'bar');
        $decorator = new Decorator($options);
        $decorator->clearOptions();
        $actual = $decorator->getOption('foo');
        $this->assertSame(null, $actual);
    }

    public function testOptionWithDefault()
    {
        $decorator = new Decorator();
        $actual = $decorator->getOption('foo', 'default');
        $this->assertSame('default', $actual);
    }

    public function testMandatoryOptionFails()
    {
        $decorator = new Decorator();
        $this->setExpectedException('Exception');
        $decorator->getMandatoryOption('foo');
    }

    public function testMandatoryOption()
    {
        $decorator = new Decorator();
        $decorator->setOption('foo', 'bar');
        $actual = $decorator->getMandatoryOption('foo');
        $this->assertEquals('bar', $actual);
    }

    public function testHasOption()
    {
        $decorator = new Decorator();
        $this->assertFalse($decorator->hasOption('foo'));
    }

    public function testGetOptions()
    {
        $decorator = new Decorator();
        $decorator->placement = 'append';
        $decorator->foo = 'bar';
        $expected = array('foo' => 'bar');
        $this->assertEquals($expected, $decorator->getOptions());
    }

    public function testMagicGetter()
    {
        $decorator = new Decorator();
        $decorator->setOption('foo', 'bar');
        $actual = $decorator->foo;
        $this->assertEquals('bar', $actual);
    }

    public function testMagicSetter()
    {
        $decorator = new Decorator();
        $decorator->foo = 'bar';
        $actual = $decorator->getOption('foo');
        $this->assertEquals('bar', $actual);
    }

    public function testDefaultOption()
    {
        $decorator = new Decorator('bar');
        $actual = $decorator->getOption('default');
        $this->assertEquals('bar', $actual);
    }

    public function testGetName()
    {
        $decorator = new Decorator();
        $this->assertSame('decorator', $decorator->getName());

    }

    public function testMerge()
    {
        $toMerge = new Decorator(array('foo' => 'bar'));
        $decorator = new Decorator();
        $decorator->merge($toMerge);
        $this->assertSame('bar', $decorator->getOption('foo'));
    }

    public function testGettingEmptyOptionThrows()
    {
        $decorator = new Decorator();
        $this->setExpectedException('Exception', 'Option name is empty');
        $decorator->getOption('');
    }

    public function testOptionPlaceholder()
    {
        $context = new Context();
        $context->setContent('bar', 'baz');
        $context->setContent('test', 'one');

        $decorator = new Decorator();
        $decorator->setOption('option', 'foo {bar} foo {test}');
        $decorator->context($context);

        $expected = 'foo baz foo one';
        $this->assertEquals($expected, $decorator->getOption('option'));
    }

    public function testOptionPlaceholderTricky()
    {
        $context = new Context();
        $context->setContent('bar', 'baz');
        $context->setContent('test', 'one');

        $decorator = new Decorator();
        $decorator->setOption('option', 'foo {bar} foo and { {test} and }{wrong}');
        $decorator->context($context);

        $expected = 'foo baz foo and { one and }';
        $this->assertEquals($expected, $decorator->getOption('option'));
    }

    public function testOptionPlaceholderOnly()
    {
        $context = new Context();
        $context->setContent('bar', 'baz');

        $decorator = new Decorator();
        $decorator->setOption('option', '{bar}');
        $decorator->context($context);

        $expected = 'baz';
        $this->assertEquals($expected, $decorator->getOption('option'));
    }

    public function testDeepPlaceholder()
    {
        $context = new Context();
        $context->setContent('bar', array('foo' => 'baz'));

        $decorator = new Decorator();
        $decorator->context($context);
        $decorator->setOption('option', '{bar.foo}');

        $expected = 'baz';
        $this->assertEquals($expected, $decorator->getOption('option'));
    }

    public function testMixedPlaceholder()
    {
        $context = new Context();
        $context->setContent('bar', array('foo' => 'baz'));

        $decorator = new Decorator();
        $decorator->context($context);
        $decorator->setOption('option', 'foo: {bar.foo}');

        $expected = 'foo: baz';
        $this->assertEquals($expected, $decorator->getOption('option'));
    }

    public function testSpecialPlaceholder()
    {
        $context = new Context('name', 'id');

        $decorator = new Decorator();
        $decorator->context($context);

    }

    public function testPlacementReplace()
    {
        $context = new Context();
        $context->add('content', array('name' => 'one'));
        $context->add('content', array('name' => 'two'));

        $context->setContent('one', 'outer');
        $context->setContent('two', 'inner');

        $expected = 'outer';
        $actual = $context->render();
        $this->assertEquals($expected, $actual);
    }

    public function testPlacementAppend()
    {
        $context = new Context();
        $context->add('content', array('name' => 'one', 'placement' => 'append'));
        $context->add('content', array('name' => 'two'));

        $context->setContent('one', 'outer');
        $context->setContent('two', 'inner');

        $expected = 'innerouter';
        $actual = $context->render();
        $this->assertEquals($expected, $actual);
    }

    public function testPlacementPrepend()
    {
        $context = new Context();
        $context->add('content', array('name' => 'one', 'placement' => 'prepend'));
        $context->add('content', array('name' => 'two'));

        $context->setContent('one', 'outer');
        $context->setContent('two', 'inner');

        $expected = 'outerinner';
        $actual = $context->render();
        $this->assertEquals($expected, $actual);
    }
}

