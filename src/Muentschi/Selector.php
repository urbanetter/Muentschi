<?php

namespace Muentschi;

/**
 * Represents settings (decorators, content, tags) for a context
 * @author Urban Etter
 */
class Selector
{
    /**
     * @var array The decorators for this selector
     */
    public $decorators = array();

    /**
     * @var array The content
     */
    public $content = array();

    /**
     * @var array The tags
     */
    public $tags = array();

    /**
     * @var string Which strategy to use when applying this selector
     */
    protected $_strategy = 'merge';

    /**
     * @var mixed Parameter for the strategy
     */
    protected $_strategyParam;

    /**
     * @var string Which decorator to use when the decorator is not found
     */
    protected static $_defaultDecorator = 'htmlTag';

    /**
     * Add decorator for this selector
     * @param Decorator|string $decorator Instance or class name of decorator
     * @param mixed $options Options for the decorator
     * @return Selector
     */
    public function add($decorator, $options = null)
    {
        if (is_string($decorator)) {
            $class = 'Muentschi\\Decorator\\' . ucfirst($decorator);
            if (!class_exists($class)) {
                $class = 'Muentschi\\Decorator\\' . ucfirst(self::$_defaultDecorator);
                $defaultOption = $decorator;
                $decorator = new $class($options);
                $decorator->setDefaultOption($defaultOption);
            } else {
                $decorator = new $class($options);
            }
        }
        if ($decorator instanceof Decorator) {
            $name = $decorator->getName();
            $this->decorators[$name] = $decorator;
        }
        return $this;
    }

    /**
     * Sets the content
     * @param $name
     * @param string|array $content content itself or name of the content
     * @internal param array $value if a name is given value is the content
     * @return Selector
     */
    public function setContent($name, $content)
    {
        $this->content[$name] = $content;
        return $this;
    }

    /**
     * Sets the default decorator
     * @param string $defaultDecorator The default decorator
     */
    public static function setDefaultDecorator($defaultDecorator)
    {
        self::$_defaultDecorator = $defaultDecorator;
    }

    /**
     * Sets a strategy for applying selector
     * @param string $function
     * @param array $params
     * @return Selector
     * @throws Exception When there is no function implementing the strategy
     */
    public function __call($function, $params)
    {
        $this->_strategy = $function;
        $function = '_' . $function;
        if (!method_exists($this, $function)) {
            throw new Exception('Unknown function: ' . substr($function, 1));
        }
        if (count($params) > 0) {
            $this->_strategyParam = array_shift($params);
        } else {
            $this->_strategyParam = '';
        }

        return $this;
    }

    /**
     * Applies the decorators according to the set strategy
     * @param  $decorators
     * @return mixed
     */
    public function apply($decorators)
    {
        return call_user_func(array($this, '_' . $this->_strategy), $decorators);
    }

    /**
     * Returns the name of the strategy
     * @return string
     */
    public function getStrategy()
    {
        return $this->_strategy;
    }

    /**
     * Adds a Tag to the selector
     * @param string $tag The tag to add
     * @return Selector Fluent interface
     */
    public function addTag($tag)
    {
        $this->tags[$tag] = $tag;
        return $this;
    }

    /**
     * Removes a tag
     * @param string $tag Tag to remove
     * @return Selector Fluent interface
     */
    public function removeTag($tag)
    {
        if ($this->hasTag($tag)) {
            unset( $this->tags[$tag] );
        }
        return $this;
    }

    /**
     * Checks if selector has a certain tag
     * @param string $tag Tag to test
     * @return boolean True if context has the tag, false otherwise
     */
    public function hasTag($tag)
    {
        return isset($this->tags[$tag]);
    }

    /**
     * Implements the merge strategy
     * @param array $decorators the decorators to merge
     * @return array merged decorators
     */
    protected function _merge($decorators)
    {
        foreach ($this->decorators as $decorator) {
            $name = $decorator->getName();
            if (isset($decorators[$name])) {
                $decorators[$name]->merge($decorator);
            } else {
                // because the used decorator can be changed,
                // we need to clone the original to keep it in the original state
                $decorators[$name] = clone( $decorator );
            }
        }

        return $decorators;
    }

    /**
     * Implements the replace strategy
     * @param array $decorators the decorators to merge
     * @return array merged decorators
     */
    protected function _replace($decorators)
    {
        return $this->decorators;
    }

    /**
     * Implements the insteadOf strategy
     * @param array $decorators the decorators to merge
     * @throws Exception
     * @return array merged decorators
     */
    protected function _insteadOf($decorators)
    {
        if (!$this->_strategyParam) {
            throw new Exception('Strategy insteadOf needs param!');
        }
        if (!isset($decorators[$this->_strategyParam])) {
            return $decorators + $this->decorators;
        }
        $offset = array_search($this->_strategyParam, array_keys($decorators));
        $before = array_slice($decorators, 0, $offset, true);
        $after = array_slice($decorators, $offset + 1, count($decorators), true);
        return $before + $this->decorators + $after;
    }

    /**
     * Implements the after strategy
     * @param array $decorators the decorators to merge
     * @return array merged decorators
     */
    protected function _after($decorators)
    {

        if (!$this->_strategyParam || !isset($decorators[$this->_strategyParam])) {
            return $decorators + $this->decorators;
        }
        $offset = array_search($this->_strategyParam, array_keys($decorators));
        $before = array_slice($decorators, 0, $offset, true);
        $after = array_slice($decorators, $offset, count($decorators), true);
           return $before + $this->decorators + $after;
    }

    /**
     * Implements the before strategy
     * @param array $decorators the decorators to merge
     * @return array merged decorators
     */
    protected function _before($decorators)
    {

        if (!$this->_strategyParam || !isset($decorators[$this->_strategyParam])) {
            return $this->decorators + $decorators;
        }
        $offset = array_search($this->_strategyParam, array_keys($decorators));
        $offset = ($offset < 2) ? 0 : $offset - 1;
        $before = array_slice($decorators, 0, $offset, true);
        $after = array_slice($decorators, $offset, count($decorators), true);
           return $before + $this->decorators + $after;
    }

    /**
     * Implements the remove strategy
     * @param array $decorators the decorators to merge
     * @return array merged decorators
     */
    protected function _remove($decorators)
    {
        if ($this->_strategyParam && isset($decorators[$this->_strategyParam])) {
            unset ($decorators[$this->_strategyParam]);
        }
        return $decorators;
    }

}