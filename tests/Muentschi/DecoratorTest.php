<?php
/**
 * Ray_Form test case.
 */
class Muentschi_DecoratorTest extends PHPUnit_Framework_TestCase
{

    public function testOption()
    {
        $decorator = new Muentschi_Decorator();
        $decorator->setOption('foo', 'bar');
        $actual = $decorator->getOption('foo');
        $this->assertSame('bar', $actual);
    }

    public function testOptions()
    {
        $options = array('foo' => 'bar');
        $decorator = new Muentschi_Decorator();
        $decorator->setOptions($options);
        $actual = $decorator->getOption('foo');
        $this->assertSame('bar', $actual);
    }

    public function testOptionsAsConstructor()
    {
        $options = array('foo' => 'bar');
        $decorator = new Muentschi_Decorator($options);
        $actual = $decorator->getOption('foo');
        $this->assertSame('bar', $actual);
    }

    public function testRemoveOption()
    {
        $options = array('foo' => 'bar');
        $decorator = new Muentschi_Decorator($options);
        $removed = $decorator->removeOption('foo');
        $actual = $decorator->getOption('foo');
        $this->assertSame('bar', $removed);
        $this->assertSame(null, $actual);
    }

    public function testClearOptions()
    {
        $options = array('foo' => 'bar');
        $decorator = new Muentschi_Decorator($options);
        $decorator->clearOptions();
        $actual = $decorator->getOption('foo');
        $this->assertSame(null, $actual);
    }

    public function testOptionWithDefault()
    {
        $decorator = new Muentschi_Decorator();
        $actual = $decorator->getOption('foo', 'default');
        $this->assertSame('default', $actual);
    }

    public function testMandatoryOptionFails()
    {
        $decorator = new Muentschi_Decorator();
        $this->setExpectedException('Muentschi_Exception');
        $decorator->getMandatoryOption('foo');
    }

    public function testMandatoryOption()
    {
        $decorator = new Muentschi_Decorator();
        $decorator->setOption('foo', 'bar');
        $actual = $decorator->getMandatoryOption('foo');
        $this->assertEquals('bar', $actual);
    }

    public function testHasOption()
    {
        $decorator = new Muentschi_Decorator();
        $this->assertFalse($decorator->hasOption('foo'));
    }

    public function testGetOptions()
    {
        $decorator = new Muentschi_Decorator();
        $decorator->placement = 'append';
        $decorator->foo = 'bar';
        $expected = array('foo' => 'bar');
        $this->assertEquals($expected, $decorator->getOptions());
    }

    public function testMagicGetter()
    {
        $decorator = new Muentschi_Decorator();
        $decorator->setOption('foo', 'bar');
        $actual = $decorator->foo;
        $this->assertEquals('bar', $actual);
    }

    public function testMagicSetter()
    {
        $decorator = new Muentschi_Decorator();
        $decorator->foo = 'bar';
        $actual = $decorator->getOption('foo');
        $this->assertEquals('bar', $actual);
    }

    public function testDefaultOption()
    {
        $decorator = new Muentschi_Decorator('bar');
        $actual = $decorator->getOption('default');
        $this->assertEquals('bar', $actual);
    }

    public function testGetName()
    {
        $decorator = new Muentschi_Decorator();
        $this->assertSame('decorator', $decorator->getName());

    }

    public function testMerge()
    {
        $toMerge = new Muentschi_Decorator(array('foo' => 'bar'));
        $decorator = new Muentschi_Decorator();
        $decorator->merge($toMerge);
        $this->assertSame('bar', $decorator->getOption('foo'));
    }

    public function testGettingEmptyOptionThrows()
    {
        $decorator = new Muentschi_Decorator();
        $this->setExpectedException('Muentschi_Exception', 'Option name is empty');
        $decorator->getOption('');
    }

    public function testOptionPlaceholder()
    {
        $context = new Muentschi_Context();
        $context->setContent('bar', 'baz');
        $context->setContent('test', 'one');

        $decorator = new Muentschi_Decorator();
        $decorator->setOption('option', 'foo {bar} foo {test}');
        $decorator->context($context);

        $expected = 'foo baz foo one';
        $this->assertEquals($expected, $decorator->getOption('option'));
    }

    public function testOptionPlaceholderTricky()
    {
        $context = new Muentschi_Context();
        $context->setContent('bar', 'baz');
        $context->setContent('test', 'one');

        $decorator = new Muentschi_Decorator();
        $decorator->setOption('option', 'foo {bar} foo and { {test} and }{wrong}');
        $decorator->context($context);

        $expected = 'foo baz foo and { one and }';
        $this->assertEquals($expected, $decorator->getOption('option'));
    }

    public function testOptionPlaceholderOnly()
    {
        $context = new Muentschi_Context();
        $context->setContent('bar', 'baz');

        $decorator = new Muentschi_Decorator();
        $decorator->setOption('option', '{bar}');
        $decorator->context($context);

        $expected = 'baz';
        $this->assertEquals($expected, $decorator->getOption('option'));
    }

    public function testDeepPlaceholder()
    {
        $context = new Muentschi_Context();
        $context->setContent('bar', array('foo' => 'baz'));

        $decorator = new Muentschi_Decorator();
        $decorator->context($context);
        $decorator->setOption('option', '{bar.foo}');

        $expected = 'baz';
        $this->assertEquals($expected, $decorator->getOption('option'));
    }

    public function testMixedPlaceholder()
    {
        $context = new Muentschi_Context();
        $context->setContent('bar', array('foo' => 'baz'));

        $decorator = new Muentschi_Decorator();
        $decorator->context($context);
        $decorator->setOption('option', 'foo: {bar.foo}');

        $expected = 'foo: baz';
        $this->assertEquals($expected, $decorator->getOption('option'));
    }

    public function testSpecialPlaceholder()
    {
        $context = new Muentschi_Context('name', 'id');

        $decorator = new Muentschi_Decorator();
        $decorator->context($context);

    }

    public function testPlacementReplace()
    {
        $context = new Muentschi_Context();
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
        $context = new Muentschi_Context();
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
        $context = new Muentschi_Context();
        $context->add('content', array('name' => 'one', 'placement' => 'prepend'));
        $context->add('content', array('name' => 'two'));

        $context->setContent('one', 'outer');
        $context->setContent('two', 'inner');

        $expected = 'outerinner';
        $actual = $context->render();
        $this->assertEquals($expected, $actual);
    }
}

