<?php
/**
 * Muentschi_Context test case.
 */
class Muentschi_ContextTest extends PHPUnit_Framework_TestCase
{

    public function testConstructor()
    {
        $context = new Muentschi_Context('name');
        $this->assertSame('name', $context->name());
    }


    public function testNameSetter()
    {
        $context = new Muentschi_Context('name');
        $this->assertEquals('name', $context->name());
        $context->name('new name');
        $this->assertEquals('new name', $context->name());
    }

    public function testContent()
    {
        $context = new Muentschi_Context();
        $context->setContent('foo');

        $actual = $context->getContent();
        $this->assertEquals('foo', $actual);
    }

    public function testContentWithArray()
    {
        $context = new Muentschi_Context('main');
        $context->setContent(array('foo' => 'bar'));

        $actual = $context->getContent('main.foo');
        $this->assertEquals('bar', $actual);
    }

    public function testContentWithStrings()
    {
        $context = new Muentschi_Context();
        $context->setContent('foo', 'bar');

        $actual = $context->getContent('foo');
        $this->assertEquals('bar', $actual);
    }

    public function testForReturnsSelector()
    {
        $context = new Muentschi_Context();
        $selector = $context->select('foo');
        $this->assertTrue($selector instanceof Muentschi_Selector);
    }

    public function testDecorateReturnsContext()
    {
        $context = new Muentschi_Context();
        $return = $context->add('dummy');
        $this->assertTrue($return instanceof Muentschi_Context);
    }

    public function testRenderWithoutDecoratorThrowsException()
    {
        $context = new Muentschi_Context();
        $this->setExpectedException('Muentschi_Exception');
        $context->render('foo');
    }

    public function testRender()
    {
        $context = new Muentschi_Context();
        $context->select('main')->add('dummy');

        $actual = $context->render();
        $this->assertEquals('dummy()', $actual);
    }

    public function testRenderWithReplace()
    {
        $context = new Muentschi_Context();
        $context->add('dummy', 'outer');
        $context->add('dummy', 'inner');

        $expected = 'outer(inner())';
        $actual = $context->render();
        $this->assertEquals($actual, $expected);
    }

    public function testRenderWithAppend()
    {
        $context = new Muentschi_Context();
        $context->add('dummy', array('name' => 'outer', 'placement' => 'append'));
        $context->add('dummy', 'inner');

        $expected = 'inner()outer()';
        $actual = $context->render();
        $this->assertEquals($actual, $expected);
    }

    public function testRenderWithPrepend()
    {
        $context = new Muentschi_Context();
        $context->add('dummy', array('name' => 'outer', 'placement' => 'prepend'));
        $context->add('dummy', 'inner');

        $expected = 'outer()inner()';
        $actual = $context->render();
        $this->assertEquals($actual, $expected);
    }

    public function testCreateContext()
    {
        $context = new Muentschi_Context();
        $context->setContent(array('one' => 1, 'two' => 2));

        $expected = new Muentschi_Context('one');
        $expected->setContent('main', array('one' => 1, 'two' => 2));
        $expected->setContent(1);

        $actual = $context->createContext('one');
        $this->assertEquals($expected, $actual);
    }

    public function testCreateContextWithId()
    {
        $context = new Muentschi_Context();
        $context->setContent(array('one' => 1, 'two' => 2));

        $expected = new Muentschi_Context('foo', 'one');
        $expected->setContent('main', array('one' => 1, 'two' => 2));
        $expected->setContent(1);

        $actual = $context->createContext('foo', 'one');
        $this->assertEquals($expected, $actual);
    }

    public function testContetOfSubContext()
    {
        $context = new Muentschi_Context('foo');
        $context->setContent(array('one' => '1', 'two' => 2));

        $subContext = $context->createContext('one');

        $this->assertEquals(1, $subContext->getContent());
        $this->assertEquals(2, $subContext->getContent('foo.two'));
    }

    public function testIdSetter()
    {
        $context = new Muentschi_Context();
        $context->id('myId');

        $this->assertEquals('myId', $context->id());
    }

    public function testIdConstructor()
    {
        $context = new Muentschi_Context('foo', 'bar');

        $this->assertEquals('bar', $context->id());
    }

    public function testEachContextSeperatly()
    {
        $context = new Muentschi_Context();
        $context->add('contexts', 'sub');
        $context->select('sub')->add('content', 'general');

        $context->select('sub#one')->add('content', 'one:');
        $context->setContent(array('one' => 1, 'two' => 2));

        $expected = 'one:general';
        $actual = $context->render();
        $this->assertEquals($actual, $expected);

    }
    public function testEachContextHasProperIds()
    {
        $context = new Muentschi_Context();
        $context->add('contexts', 'sub');
        $context->select('sub')->add('content', array('id' => true));

        $context->setContent(array('one' => 1, 'two' => 2));

        $expected = 'onetwo';
        $actual = $context->render();
        $this->assertEquals($actual, $expected);

    }

    public function testSubContextHasSameData()
    {
        $context = new Muentschi_Context();
        $context->setContent('foo');

        $sub = $context->createContext('sub');
        $actual = $sub->getContent();

        $this->assertEquals('foo', $actual);
    }

    public function testSelectorIncludesContent()
    {
        $context = new Muentschi_Context();
        $context->add('context', 'sub');
        $context->select('sub')->setContent('foo', 'bar')->add('content', '{foo}');

        $expected = 'bar';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTag()
    {
        $context = new Muentschi_Context();
        $context->addTag('tag');
        $context->addTag('bar');

        $this->assertTrue($context->hasTag('tag'));
        $this->assertTrue($context->hasTag('bar'));
        $this->assertFalse($context->hasTag('foo'));
    }

    public function testRemoveTag()
    {
        $context = new Muentschi_Context();
        $context->addTag('tag');

        $this->assertTrue($context->hasTag('tag'));
        $context->removeTag('tag');
        $this->assertFalse($context->hasTag('tag'));
    }

    public function testTagInheritance()
    {
        $context = new Muentschi_Context();
        $context->addTag('tag');

        $sub = $context->createContext('sub');
        $this->assertTrue($sub->hasTag('tag'));
    }

    public function testSelectorSetsTag()
    {
        $context = new Muentschi_Context('foo');
        $context->select('foo')->addTag('bar')->add('content', array('tags' => true));

        $expected = 'bar';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);

    }

    public function testGetContentInformation()
    {
        $context = new Muentschi_Context('foo', 'bar');
        $this->assertEquals('foo', $context->getContent('#name'));
        $this->assertEquals('bar', $context->getContent('#id'));
    }

    public function testCompareSelectors()
    {
        $context = new Muentschi_Context();

        $actual = $context->compareSelector('one', 'two');
        $this->assertEquals(0, $actual);

        $actual = $context->compareSelector('one.two', 'two');
        $this->assertEquals(1, $actual);

        $actual = $context->compareSelector('one', 'two.');
        $this->assertEquals(-1, $actual);

        $actual = $context->compareSelector('table', 'table:empty');
        $this->assertEquals(-1, $actual);
    }
    
    public function testSelectorSorting()
    {
    	$expected = array('one' => 1, 'one.two' => 2, 'one:three' => 3);
    	$selectors = array('one.two' => 2, 'one:three' => 3, 'one' => 1);
    	$context = new Muentschi_Context();
    	uksort($selectors, array($context, 'compareSelector'));
    	
    	$this->assertEquals($expected, $selectors);
    }

	public function testComputedTags()	
	{
		$context = new Muentschi_Context('name');
		
		$context->addComputedTag('first');
		$this->assertTrue($context->applies('name:first'));
	}
	
	public function testEmptySubContent()
	{
	   $context = new Muentschi_Context('name');
	   $context->setContent(array('empty' => array()));
	   $subContext = $context->createContext('sub', 'empty');
	   
	   $this->assertEquals(array(), $subContext->getContent());
	}
	
	public function testSettingEmptyContentIfParamMatchesName()
	{
	   $context = new Muentschi_Context('test');
	   $content = array();
	   
	   $context->setContent('test', $content);
	   $actual = $context->getContent('test');
	   
	   $this->assertEquals(array(), $actual);
	}
	
	public function testIds()
	{
		$context = new Muentschi_Context();
		$context->ids('sub', 'one,two');
		$this->assertEquals('one,two', $context->ids('sub'));
	}
	
	public function testLog()
	{
        
		$context = new Muentschi_Context('dialog');
        $context->add('div', array('class' => 'dialog'));
        $context->add('context', 'title');
        $context->add('context', 'body');

        $context->select('title')->add('div', array('class' => 'title'));
        $context->select('title')->add('content');

        $body = $context->select('body');
        $body->add('div', array('class' => 'body'));
        $body->add('content');

        $content = array('title' => 'My title', 'body' => 'My first contextual view');
        Muentschi_Context::clearLog();
        $context->render($content);
        
        $actual = implode("\n", Muentschi_Context::getLog());
        
        $expected = <<<END
[Render] Rendering dialog#dialog
[Selector] Selector dialog applies
[Decorator] Rendering decorator Context with name body
[Context] Creating context body with id body
[Render] Rendering body#body
[Selector] Selector body applies
[Decorator] Rendering decorator Content with name content
[Decorator] Rendering decorator HtmlTag with name div
[Decorator] Rendering decorator Context with name title
[Context] Creating context title with id title
[Render] Rendering title#title
[Selector] Selector title applies
[Decorator] Rendering decorator Content with name content
[Decorator] Rendering decorator HtmlTag with name div
[Decorator] Rendering decorator HtmlTag with name div
END;

        // Normalize line endings
        $expected = str_replace("\r\n", "\n", $expected);
        $actual = str_replace("\r\n", "\n", $actual);
        
        
        $this->assertEquals($expected, $actual);
		
        
	}
}