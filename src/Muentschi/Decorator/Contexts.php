<?php

namespace Muentschi\Decorator;

use Muentschi\Decorator;

/**
 * Creates multiple contexts
 * @author Urban Etter
 */
class Contexts extends Decorator
{
    protected $_defaultOption = 'name';

    protected function _init()
    {
        $this->setOption('placement', 'prepend');
    }

    public function getName()
    {
        return $this->getOption('name');
    }

    public function render($output = '')
    {
        $name = $this->getMandatoryOption('name');
        $parent = $this->context();

        if (!is_array($parent->getContent())) {
            throw new Exception('Not possible to create context ' . $name . ' without an array as content');
        }

        // find out which context to create
        if ($this->hasOption('ids')) {
            $ids = explode(',', $this->getOption('ids'));
            $ids = array_map('trim', $ids);
        } elseif ($parent->ids($name)) {
        	$ids = explode(',', $parent->ids($name));
        } else {
            $ids = array_keys($parent->getContent());
        }
        $results = array();
        $count = 0;
        foreach ($ids as $id) {
            $context = $parent->createContext($name, $id);
            // compute tags
            if ($count == 0) {
                $context->addComputedTag('first');
            }
            if ($count == count($ids) - 1) {
                $context->addComputedTag('last');
            }
            if ($count % 2 == 0) {
                $context->addComputedTag('even');
            }
            if ($count % 2 == 1) {
                $context->addComputedTag('odd');
            }
            $count++;
            $results[] = $context->render();
        }

        $separator = $this->getOption('separator', '');
        $result = implode($separator, $results);
        return $this->handlePlacement($output, $result);
    }
}