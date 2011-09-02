<?php
/**
 * Represents a HTML tag
 * @author Urban Etter
 */
class Muentschi_Decorator_HtmlTag extends Muentschi_Decorator
{
    /**
     * @var string The default option
     */
    protected $_defaultOption = 'tag';

    /**
     * (non-PHPdoc)
     * @see nonwww/Muentschi/Muentschi_Decorator#_init()
     */
    protected function _init()
    {
        $this->setOption('tag', 'div');
    }

    /**
     * (non-PHPdoc)
     * @see nonwww/Muentschi/Muentschi_Decorator#render()
     */
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

    /**
     * (non-PHPdoc)
     * @see nonwww/Muentschi/Muentschi_Decorator#merge($decorator)
     */
    public function merge(Muentschi_Decorator $decorator)
    {
        if ($this->hasOption('class') && $decorator->hasOption('class')) {
            $class = $this->getOption('class') . ' ' . $decorator->getOption('class');
            $decorator->setOption('class', $class);
        }
        parent::merge($decorator);
    }

    /**
     * (non-PHPdoc)
     * @see nonwww/Muentschi/Muentschi_Decorator#getName()
     */
    public function getName()
    {
        return $this->getMandatoryOption('tag');
    }

}