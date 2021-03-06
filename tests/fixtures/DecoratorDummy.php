<?php

namespace Muentschi\Decorator;

use Muentschi\Decorator;

class Dummy extends Decorator
{
    protected $_defaultOption = 'name';

    public function render($output = '')
    {
        $placement = $this->getOption('placement', 'replace');
        $result = $this->getName();
        if ($placement == "replace") {
            $result .= '(' . $output . ')';
        } else {
            $result .= '()';
        }

        return $this->handlePlacement($output, $result, $placement);
    }

    public function getName()
    {
        if ($this->hasOption('name')) {
            return $this->getOption('name');
        } else {
            return 'dummy';
        }
    }
}