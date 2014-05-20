<?php

namespace Muentschi;


use Symfony\Component\Yaml\Parser;

class ContextFactory
{

    /**
     * Creates an instance with decorators specified in a XML file
     * @param string $path The path to the file
     * @return Context
     * @throws Exception when the given file is not readable
     */
    static public function fromXML($path)
    {
        if (!is_file($path)) {
            throw new Exception('File ' . $path . ' not found');
        }

        $xml = simplexml_load_file($path);

        $name = (string) $xml['name'];
        $name = ($name) ? $name : pathinfo($path, PATHINFO_FILENAME);


        // extends
        $extends = (string) $xml['extends'];
        if ($extends) {
            $file = dirname($path) . '/' . $extends . '.xml';
            if (!is_file($file)) {
                throw new Exception('Cannot extend file ' . $file);
            }
            $result = ContextFactory::fromXML($file);
            $result->setName($name);
        } else {
            $result = new Context($name);
        }

        foreach ($xml->children() as $child) {
            if ($child->getName() == 'decorators') {
                $for = (string) $child['for'];
                if ($for) {
                    $selector = $result->select($for);
                } else {
                    $selector = $result->select($name);
                }
                foreach ($child->children() as $decorator) {
                    $options = array();
                    foreach ($decorator->attributes() as $name => $value) {
                        $options[$name] = (string) $value;
                    }
                    $selector->add($decorator->getName(), $options);
                }
            }
        }

        return $result;
    }

    /**
     * Creates an instance with decorators specified in a YAML file
     * @param string $path The path to the file
     * @return Context
     * @throws Exception when the given file is not readable
     */
    static public function fromYaml($path)
    {
        if (!is_file($path)) {
            throw new Exception('File ' . $path . ' not found');
        }

        $parser = new Parser();
        $contexts = $parser->parse(file_get_contents($path));

        reset($contexts);
        $mainContextName = key($contexts);
        $mainContextDecorators = array_shift($contexts);
        if (is_array($mainContextDecorators)) {
            $mainContext = new Context($mainContextName);
            $mainSelector = $mainContext->select($mainContextName);
            $mainSelector->addMultiple($mainContextDecorators);
        } else {
            $file = dirname($path) . '/' . $mainContextDecorators . '.yaml';
            if (!is_file($file)) {
                throw new Exception('Cannot extend file ' . $file);
            }
            $mainContext = ContextFactory::fromYaml($file);
        }

        foreach ($contexts as $selector => $decorators) {
            $selector = $mainContext->select($selector);
            $selector->addMultiple($decorators);
        }

        return $mainContext;

    }

} 