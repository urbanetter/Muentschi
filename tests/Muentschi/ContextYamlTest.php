<?php

namespace Muentschi;

use Symfony\Component\Yaml\Parser;

class ContextYamlTest extends \PHPUnit_Framework_TestCase
{

    public function testSimple()
    {
        $context = Context::fromYaml(dirname(__FILE__) . '/../fixtures/simple.yaml');

        $context->setContent('hello world');

        $expected = '<h1>hello world</h1>';
        $actual = $context->render();

        $this->assertEquals($expected, $actual);
    }

    public function testDialog()
    {
        $context = Context::fromYaml(dirname(__FILE__) . '/../fixtures/dialog.yaml');

        $content = array('title' => 'My title', 'body' => 'My first contextual view');
        $actual = $context->render($content);

        $expected = '<div class="dialog"><div class="title">My title</div><div class="body">My first contextual view</div></div>';
        $this->assertEquals($expected, $actual);
    }

    public function testSimpleTable()
    {
        $context = Context::fromYaml(dirname(__FILE__) . '/../fixtures/simpletable.yaml');

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
        $context = Context::fromYaml(dirname(__FILE__) . '/../fixtures/tableWithActionCol.yaml');

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
        $context = Context::fromYaml(dirname(__FILE__) . '/../fixtures/tableWithHeader.yaml', true);

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
        $context = Context::fromYaml(dirname(__FILE__) . '/../fixtures/simpletable.yaml');

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
}