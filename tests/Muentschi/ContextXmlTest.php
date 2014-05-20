<?php

namespace Muentschi;

class ContextXmlTest extends \PHPUnit_Framework_TestCase
{
	
	public function testSimple()
    {
        $context = ContextFactory::fromXML(dirname(__FILE__) . '/../fixtures/simple.xml');

        $context->setContent('hello world');

        $expected = '<h1>hello world</h1>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDialog()
    {
        $context = ContextFactory::fromXML(dirname(__FILE__) . '/../fixtures/dialog.xml');
    	
        $content = array('title' => 'My title', 'body' => 'My first contextual view');
        $actual = $context->render($content);

        $expected = '<div class="dialog"><div class="title">My title</div><div class="body">My first contextual view</div></div>';
        $this->assertEquals($expected, $actual);
    }

    public function testSimpleTable()
    {
        $context = ContextFactory::fromXML(dirname(__FILE__) . '/../fixtures/simpletable.xml');
    	
        $data = array(
            array('name' => 'Peter', 'email' => 'peter@alps.ch'),
            array('name' => 'Heidi', 'email' => 'heidi@alps.ch'),
        );

        $context->setContent($data);

        $expected = '<table><tr><td>Peter</td><td>peter@alps.ch</td></tr><tr><td>Heidi</td><td>heidi@alps.ch</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTableWithActionCol()
    {
        $context = ContextFactory::fromXML(dirname(__FILE__) . '/../fixtures/tableWithActionCol.xml');
    	
        $data = array(
            array('id' => 23, 'name' => 'Peter', 'email' => 'peter@alps.ch'),
            array('id' => 17, 'name' => 'Heidi', 'email' => 'heidi@alps.ch'),
        );

        $context->setContent($data);

        $expected = '<table><tr><td>Peter</td><td>id: 23</td></tr><tr><td>Heidi</td><td>id: 17</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTableWithHeaderRow()
    {
        $context = ContextFactory::fromXML(dirname(__FILE__) . '/../fixtures/tableWithHeader.xml');
    	
        $context->ids('column', 'name,email');
        $context->select('column#name')->setContent('title', 'Name');
        $context->select('column#email')->setContent('title', 'Email');

        $data = array(
            array('id' => 23, 'name' => 'Peter', 'email' => 'peter@alps.ch'),
            array('id' => 17, 'name' => 'Heidi', 'email' => 'heidi@alps.ch'),
        );

        $context->setContent($data);

        $expected = '<table><thead><tr><th>Name</th><th>Email</th></tr></thead><tbody><tr><td>Peter</td><td>peter@alps.ch</td></tr><tr><td>Heidi</td><td>heidi@alps.ch</td></tr></tbody></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTableHilightRow()
    {
        $context = ContextFactory::fromXML(dirname(__FILE__) . '/../fixtures/simpletable.xml');

        $context->ids('column', 'name,email');
        $context->select('row[id=17]')->add('tr', array('class' => 'hilight'));

        $data = array(
            array('id' => 23, 'name' => 'Peter', 'email' => 'peter@alps.ch'),
            array('id' => 17, 'name' => 'Heidi', 'email' => 'heidi@alps.ch'),
        );

        $context->setContent($data);

        $expected = '<table><tr><td>Peter</td><td>peter@alps.ch</td></tr><tr class="hilight"><td>Heidi</td><td>heidi@alps.ch</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTableWithSortedColumn()
    {
        $context = ContextFactory::fromXML(dirname(__FILE__) . '/../fixtures/simpletable.xml');

        $context->ids('column', 'name,email');
    	$context->select('column#name')->add('td', array('class' => 'sorted'));


        $data = array(
            array('id' => 23, 'name' => 'Peter', 'email' => 'peter@alps.ch'),
            array('id' => 17, 'name' => 'Heidi', 'email' => 'heidi@alps.ch'),
        );

        $context->setContent($data);

        $expected = '<table><tr><td class="sorted">Peter</td><td>peter@alps.ch</td></tr><tr><td class="sorted">Heidi</td><td>heidi@alps.ch</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testTableWithSortableColumn()
    {
        $context = ContextFactory::fromXML(dirname(__FILE__) . '/../fixtures/tableWithHeader.xml');

        $context->ids('column', 'name,email');
    	
        $context->select('header column.sortable')->add('content', '<a href="?sort={#id}">{title}</a>');

        $context->select('column#name')->setContent('title', 'Name')->addTag('sortable');
        $context->select('column#email')->setContent('title', 'E-Mail');


        $data = array(
            array('id' => 23, 'name' => 'Peter', 'email' => 'peter@alps.ch'),
            array('id' => 17, 'name' => 'Heidi', 'email' => 'heidi@alps.ch'),
        );

        $context->setContent($data);

        $expected = '<table><thead><tr><th><a href="?sort=name">Name</a></th><th>E-Mail</th></tr></thead><tbody><tr><td>Peter</td><td>peter@alps.ch</td></tr><tr><td>Heidi</td><td>heidi@alps.ch</td></tr></tbody></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }
    
    public function testEmptyTable()
    {
        $context = ContextFactory::fromXML(dirname(__FILE__) . '/../fixtures/simpletable.xml');

        $context->select('table:empty')->insteadOf('row')->add('tr')->add('td')->add('text', 'No content!');

        $data = array();

        $context->setContent($data);

        $expected = '<table><tr><td>No content!</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    
    public function testEmptyColumn()
    {
        $context = ContextFactory::fromXML(dirname(__FILE__) . '/../fixtures/simpletable.xml');

        $context->ids('column', 'name,email');
        $context->select('column:empty')->insteadOf('content')->add('text', 'Empty column!');

        $data = array(
            array('name' => 'Peter', 'email' => 'peter@alps.ch'),
            array('name' => 'Heidi', 'email' => ''),
        );

        $context->setContent($data);

        $expected = '<table><tr><td>Peter</td><td>peter@alps.ch</td></tr><tr><td>Heidi</td><td>Empty column!</td></tr></table>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }
}