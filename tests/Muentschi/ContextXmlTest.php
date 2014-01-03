<?php

namespace Muentschi;

class ContextXmlTest extends \PHPUnit_Framework_TestCase
{
	
	public function testSimple()
    {
        $context = Context::fromXML(dirname(__FILE__) . '/../fixtures/simple.xml');

        $context->setContent('hello world');

        $expected = '<h1>hello world</h1>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDialog()
    {
        $context = Context::fromXML(dirname(__FILE__) . '/../fixtures/dialog.xml');
    	
        $content = array('title' => 'My title', 'body' => 'My first contextual view');
        $actual = $context->render($content);

        $expected = '<div class="dialog"><div class="title">My title</div><div class="body">My first contextual view</div></div>';
        $this->assertEquals($expected, $actual);
    }

    public function testSimpleTable()
    {
        $context = Context::fromXML(dirname(__FILE__) . '/../fixtures/simpletable.xml');
    	
        $data = array(
            array('name' => 'Blop', 'email' => 'blop@nothing.ch'),
            array('name' => 'Spin', 'email' => 'spin@nothing.ch'),
        );

        $context->setContent($data);

        $expected = '<table><tr><td>Blop</td><td>blop@nothing.ch</td></tr><tr><td>Spin</td><td>spin@nothing.ch</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTableWithActionCol()
    {
        $context = Context::fromXML(dirname(__FILE__) . '/../fixtures/tableWithActionCol.xml');
    	
        $data = array(
            array('id' => 23, 'name' => 'Blop', 'email' => 'blop@nothing.ch'),
            array('id' => 17, 'name' => 'Spin', 'email' => 'spin@nothing.ch'),
        );

        $context->setContent($data);

        $expected = '<table><tr><td>Blop</td><td>id: 23</td></tr><tr><td>Spin</td><td>id: 17</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTableWithHeaderRow()
    {
        $context = Context::fromXML(dirname(__FILE__) . '/../fixtures/tableWithHeader.xml');
    	
        $context->ids('column', 'name,email');
        $context->select('column#name')->setContent('title', 'Name');
        $context->select('column#email')->setContent('title', 'Email');

        $data = array(
            array('id' => 23, 'name' => 'Blop', 'email' => 'blop@nothing.ch'),
            array('id' => 17, 'name' => 'Spin', 'email' => 'spin@nothing.ch'),
        );

        $context->setContent($data);

        $expected = '<table><thead><tr><th>Name</th><th>Email</th></tr></thead><tbody><tr><td>Blop</td><td>blop@nothing.ch</td></tr><tr><td>Spin</td><td>spin@nothing.ch</td></tr></tbody></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTableHilightRow()
    {
        $context = Context::fromXML(dirname(__FILE__) . '/../fixtures/simpletable.xml');

        $context->ids('column', 'name,email');
        $context->select('row[id=17]')->add('tr', array('class' => 'hilight'));

        $data = array(
            array('id' => 23, 'name' => 'Blop', 'email' => 'blop@nothing.ch'),
            array('id' => 17, 'name' => 'Spin', 'email' => 'spin@nothing.ch'),
        );

        $context->setContent($data);

        $expected = '<table><tr><td>Blop</td><td>blop@nothing.ch</td></tr><tr class="hilight"><td>Spin</td><td>spin@nothing.ch</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTableWithSortedColumn()
    {
        $context = Context::fromXML(dirname(__FILE__) . '/../fixtures/simpletable.xml');

        $context->ids('column', 'name,email');
    	$context->select('column#name')->add('td', array('class' => 'sorted'));


        $data = array(
            array('id' => 23, 'name' => 'Blop', 'email' => 'blop@nothing.ch'),
            array('id' => 17, 'name' => 'Spin', 'email' => 'spin@nothing.ch'),
        );

        $context->setContent($data);

        $expected = '<table><tr><td class="sorted">Blop</td><td>blop@nothing.ch</td></tr><tr><td class="sorted">Spin</td><td>spin@nothing.ch</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTableWithSortableColumn()
    {
        $context = Context::fromXML(dirname(__FILE__) . '/../fixtures/tableWithHeader.xml');

        $context->ids('column', 'name,email');
    	
        $context->select('header column.sortable')->add('content', '<a href="?sort={#id}">{title}</a>');

        $context->select('column#name')->setContent('title', 'Name')->addTag('sortable');
        $context->select('column#email')->setContent('title', 'E-Mail');


        $data = array(
            array('id' => 23, 'name' => 'Blop', 'email' => 'blop@nothing.ch'),
            array('id' => 17, 'name' => 'Spin', 'email' => 'spin@nothing.ch'),
        );

        $context->setContent($data);

        $expected = '<table><thead><tr><th><a href="?sort=name">Name</a></th><th>E-Mail</th></tr></thead><tbody><tr><td>Blop</td><td>blop@nothing.ch</td></tr><tr><td>Spin</td><td>spin@nothing.ch</td></tr></tbody></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }
    
    public function testEmptyTable()
    {
        $context = Context::fromXML(dirname(__FILE__) . '/../fixtures/simpletable.xml');

        $context->select('table:empty')->insteadOf('row')->add('tr')->add('td')->add('text', 'No content!');

        $data = array();

        $context->setContent($data);

        $expected = '<table><tr><td>No content!</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    
    public function testEmptyColumn()
    {
        $context = Context::fromXML(dirname(__FILE__) . '/../fixtures/simpletable.xml');

        $context->ids('column', 'name,email');
        $context->select('column:empty')->insteadOf('content')->add('text', 'Empty column!');

        $data = array(
            array('name' => 'Blop', 'email' => 'blop@nothing.ch'),
            array('name' => 'Spin', 'email' => ''),
        );

        $context->setContent($data);

        $expected = '<table><tr><td>Blop</td><td>blop@nothing.ch</td></tr><tr><td>Spin</td><td>Empty column!</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }
}