<?php
/**
 * Allows to simple display text
 * @author Urban Etter
 */
class Muentschi_Decorator_Text extends Muentschi_Decorator
{
    /**
     * @var string The default option
     */
    protected $_defaultOption = 'text';

    /**
     * (non-PHPdoc)
     * @see nonwww/Muentschi/Muentschi_Decorator#render()
     */
    public function render($output = '')
    {
        $result = $this->getMandatoryOption('text');
        return $this->handlePlacement($output, $result);
    }

    /**
     * (non-PHPdoc)
     * @see nonwww/Muentschi/Muentschi_Decorator#getName()
     */
    public function getName()
    {
        return $this->getOption('name', 'text');
    }

}