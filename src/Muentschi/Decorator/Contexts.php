<?php
/**
 * Creates multiple contexts
 * @author Urban Etter
 */
class Muentschi_Decorator_Contexts extends Muentschi_Decorator
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

        if (!is_array($parent->getContent())) {
            throw new Muentschi_Exception('Not possible to create context ' . $name . ' without an array as content');
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