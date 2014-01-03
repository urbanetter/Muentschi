<?php

namespace Muentschi\Decorator;

use Muentschi\Decorator;

/**
 * Represents the actual content
 * @author Urban Etter
 */
class Content extends Decorator
{
    /**
     * @var string The default option
     */
    protected $_defaultOption = 'format';

    /**
     * (non-PHPdoc)
     * @see nonwww/Muentschi/Muentschi_Decorator#render()
     */
    public function render($output = '')
    {
        $context = $this->context();
        if ($this->hasOption('format')) {
            $result = $this->getOption('format');
        } elseif ($this->hasOption('name')) {
            $name = $this->getOption('name');
            $result = $context->getContent($name);
        } elseif ($this->hasOption('id')) {
            $result = $context->id();
        } elseif ($this->hasOption('tags')) {
            $result = implode(',', $context->getTags());
        } elseif ($this->hasOption('computedTags')) {
            $result = implode(',', $context->getComputedTags());
        } else {
            $result = $context->getContent();
        }

        return $this->handlePlacement($output, $result);
    }

    /**
     * (non-PHPdoc)
     * @see nonwww/Muentschi/Muentschi_Decorator#getName()
     */
    public function getName()
    {
        return $this->getOption('name', 'content');
    }

}