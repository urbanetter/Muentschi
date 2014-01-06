<?php

namespace Muentschi\Decorator;

use Muentschi\Decorator;
use Muentschi\Exception;

/**
 * Represents a HTML tag
 * @author Urban Etter
 */
class HtmlTag extends Decorator
{
    /**
     * @var string The default option
     */
    protected $_defaultOption = 'tag';

    protected function _init()
    {
        $this->setOption('tag', 'div');
    }

    public function render($output = '')
    {
        $tag = $this->getMandatoryOption('tag');
        if (is_array($output)) {
            $context = $this->context();
            throw new Exception("Multiple data in HTML tag $tag in decorator " . $context->name() . ". (Use decorators instead of decorator in parent context)");
        }

        $this->removeOption('tag');
        $placement = $this->getOption('placement', 'replace');

        $attributes = array();
        foreach ($this->getOptions() as $name => $value) {
            $attributes[] = $name . '="' . $value . '"';
        }
        $attributes = (count($attributes)) ? ' ' . implode(' ', $attributes) : '';

        $result = '<' . $tag . $attributes;
        if ($placement == "replace") {
            $result .= '>' . $output . '</' . $tag . '>';
        } else {
            $result .= '/>';
        }

        return $this->handlePlacement($output, $result, $placement);
    }

    public function merge(Decorator $decorator)
    {
        if ($this->hasOption('class') && $decorator->hasOption('class')) {
            $class = $this->getOption('class') . ' ' . $decorator->getOption('class');
            $decorator->setOption('class', $class);
        }
        parent::merge($decorator);
    }

    public function getName()
    {
        return $this->getMandatoryOption('tag');
    }

}