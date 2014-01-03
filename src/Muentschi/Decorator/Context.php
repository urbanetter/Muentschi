<?php

namespace Muentschi\Decorator;

use Muentschi\Decorator;

/**
 * Creates a new context
 * @author Urban Etter
 */
class Context extends Decorator
{
    /**
     * @var string The default option
     */
    protected $_defaultOption = 'name';

    /**
     * (non-PHPdoc)
     * @see nonwww/Muentschi/Muentschi_Decorator#_init()
     */
    protected function _init()
    {
        $this->setOption('placement', 'prepend');
    }

    /**
     * (non-PHPdoc)
     * @see nonwww/Muentschi/Muentschi_Decorator#getName()
     */
    public function getName()
    {
        return $this->getOption('name');
    }

    /**
     * (non-PHPdoc)
     * @see nonwww/Muentschi/Muentschi_Decorator#render()
     */
    public function render($output = '')
    {
        $name = $this->getMandatoryOption('name');
        $parent = $this->context();

        $context = $parent->createContext($name);
        $result = $context->render();

        return $this->handlePlacement($output, $result);
    }
}