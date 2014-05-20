<?php

namespace Muentschi;

/**
 * Muentschi\Context test case.
 */
class ContextBootstrapTest extends \PHPUnit_Framework_TestCase
{

    public function testBootstrapTable()
    {
        $context = ContextFactory::fromYaml(__DIR__ . '/../fixtures/bootstrap_table.yaml');

        $apiResults = array(
            array(
                'state' => 'OK',
                'text' => 'All ok',
                'host' => 'www.example.com'
            ),
            array(
                'state' => 'KO',
                'text' => '8 Failures',
                'host' => 'backend.example.com'
            )
        );

        // only display the key 'text'
        $context->ids('column', 'text');

        // set header text to fixed string
        $context->select('header column')->add('content', 'Result');

        // add tag warning to rows with the state set to 'KO'
        $context->select('row[state=KO]')->addTag('warning');

        $actual = $context->render($apiResults);
        $expected = file_get_contents(__DIR__ . '/../fixtures/bootstrap_table.html');

        $this->assertEquals($expected, $actual);
    }

}