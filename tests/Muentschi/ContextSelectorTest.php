<?php
/**
 * Muentschi_Context test case.
 */
class Muentschi_ContextSelectorTest extends PHPUnit_Framework_TestCase
{
    public function testContextName()
    {
        $context = new Muentschi_Context('foo');
        $this->assertTrue($context->applies('foo'));
        $this->assertFalse($context->applies('bar'));
        $this->assertFalse($context->applies(''));
    }

    public function testContextId()
    {
        $context = new Muentschi_Context('foo', 'bar');
        $this->assertTrue($context->applies('foo'));
        $this->assertFalse($context->applies('bar'));
        $this->assertFalse($context->applies('foo#baz'));
        $this->assertTrue($context->applies('foo#bar'));
        $this->assertFalse($context->applies('faz#bar'));
        $this->assertTrue($context->applies('#bar'));
    }

    public function testNestedSelectors()
    {
        $context = new Muentschi_Context();
        $context->select('foo bar')->add('dummy');
        $sub = $context->createContext('foo');

        $actual = $sub->select('bar')->decorators['dummy'];
        $expected = new Muentschi_Decorator_Dummy();

        $this->assertEquals($expected, $actual);

    }

    public function testContentSelectors()
    {
        $context = new Muentschi_Context('baz');
        $context->setContent(array('foo' => 'bar'));

        $this->assertTrue($context->applies('baz[foo=bar]'));
        $this->assertFalse($context->applies('bar[foo=bar]'));
        $this->assertFalse($context->applies('baz[foo=baz]'));

        $context->setContent('foo');
        $this->assertTrue($context->applies('baz[foo]'));
        $this->assertFalse($context->applies('bar[foo]'));
        $this->assertFalse($context->applies('baz[fooo]'));

        $context->id('test');
        $this->assertTrue($context->applies('baz[foo]#test'));
        $this->assertTrue($context->applies('baz#test[foo]'));
        $this->assertFalse($context->applies('baz[foo]#wrong'));
        $this->assertFalse($context->applies('baz#wrong[foo]'));
    }

    public function testMultiContentSelectors()
    {
        $context = new Muentschi_Context('baz');
        $context->setContent(array('foo' => 'bar', 'one' => 'two'));

        $this->assertTrue($context->applies('baz[foo=bar][one=two]'));
        $this->assertFalse($context->applies('baz[foo=bar][one=three]'));
        $this->assertFalse($context->applies('baz[foo=baz][one=two]'));
    }

    public function testTagSelectors()
    {
        $context = new Muentschi_Context('baz');

        $context->addTag('foo');
        $this->assertTrue($context->applies('baz.foo'));
        $this->assertFalse($context->applies('baz.bar'));

        $context->addTag('bar');
        $this->assertTrue($context->applies('baz.foo.bar'));
        $this->assertTrue($context->applies('baz.bar.foo'));
        $this->assertTrue($context->applies('baz.bar'));
        $this->assertTrue($context->applies('baz.foo'));

    }

    public function testComputedTagSelectors()
    {
        $context = new Muentschi_Context('baz');

        $context->addComputedTag('foo');
        $this->assertTrue($context->applies('baz:foo'));
        $this->assertFalse($context->applies('baz:bar'));

        $context->addComputedTag('bar');
        $this->assertTrue($context->applies('baz:foo:bar'));
        $this->assertTrue($context->applies('baz:bar:foo'));
        $this->assertTrue($context->applies('baz:bar'));
        $this->assertTrue($context->applies('baz:foo'));

    }

}