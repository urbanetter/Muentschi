<?php
/**
 * Creates a new context
 * @author Urban Etter
 */
class Muentschi_Decorator_Context extends Muentschi_Decorator
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