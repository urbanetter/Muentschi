<?php

namespace Muentschi\Decorator;

use Muentschi\Decorator;

/**
 * Allows to simple display text
 * @author Urban Etter
 */
class Text extends Decorator
{
    /**
     * @var string The default option
     */
    protected $_defaultOption = 'text';

    public function render($output = '')
    {
        $result = $this->getMandatoryOption('text');
        return $this->handlePlacement($output, $result);
    }

    public function getName()
    {
        return $this->getOption('name', 'text');
    }

}