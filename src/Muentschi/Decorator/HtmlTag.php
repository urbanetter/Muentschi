<?php

namespace Muentschi\Decorator;

use Muentschi\Decorator;

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