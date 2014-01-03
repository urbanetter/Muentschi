<?php

namespace Muentschi;

/**
 * Decorates the context
 * @author Urban Etter
 */
class Decorator
{

    /**
     * The options
     * @var array
     */
    protected $_options;

    /**
     * The main option name
     * @var string
     */
    protected $_defaultOption = 'default';

    /**
     * the context which is decorated
     * @var Context
     */
    protected $_context;

    /**
     * creates a new decorator
     * @param array $options
     */
    public function __construct($options = null)
    {
        $this->clearOptions();
        $this->_init();
        if ($options !== null) {
            if (!is_array($options)) {
                $this->setDefaultOption($options);
            } else {
                $this->_options = array_merge($this->_options, $options);
            }
        }
    }

    /**
     * This function is called by the constructor to init options.
     * Overload this function to initialize options
     */
    protected function _init()
    {
        // Intenionally left blank override this method to set default options
    }

    /**
     * Returns an option
     *
     * @param string $name
     * @param string $default is returned if the option is not set
     * @throws Exception
     * @return mixed
     */
    public function getOption($name, $default = null)
    {
        if (!$name) {
            throw new Exception('Option name is empty');
        }
        if (isset($this->_options[$name])) {
            $option = $this->_options[$name];
            $option = $this->_replacePlaceholders($option);
            return $option;
        }
        return $default;
    }


    /**
     * Replaces the placeholders in the options
     * @param string $string The option
     * @return string The replaced option
     */
    private function _replacePlaceholders($string)
    {
        // when there is no context we are not rendering yet, so no need to replace placeholders
        if (!$this->_context instanceof Context) {
            return $string;
        }
        $string = preg_replace_callback('/{([^{}\r\n]*)}/', array($this, '_replaceContent'), $string);

        return $string;
    }

    /**
     * Replace content in the syntax {content}
     * @param array $matches Given from preg_replace_callback
     * @return string The content needed for replacing
     */
    private function _replaceContent($matches)
    {
        $name = $matches[1];
        return $this->_context->getContent($name);
    }

    /**
     * Returns a mandatory option
     *
     * @param string $name
     * @param string $error message for display when option is not set
     * @throws Exception
     * @return mixed
     */
    public function getMandatoryOption($name, $error = null)
    {
        if (!isset($this->_options[$name])) {
            $msg = ($error) ? $error : get_class($this) . ' needs option ' .$name;
            throw new Exception($msg);
        }
        return $this->getOption($name);
    }

    /**
     * Sets an option
     *
     * @param string $name
     * @param string $value
     * @return mixed
     */
    public function setOption($name, $value)
    {
        $this->_options[$name] = $value;
    }

    /**
     * Sets the default option
     * @param mixed $value Value to set as default option
     * @return Decorator Fluent interface
     */
    public function setDefaultOption($value)
    {
        $this->_options[$this->_defaultOption] = $value;
        return $this;
    }

    /**
     * Sets the options at once
     *
     * @param array $options
     * @return Decorator Fluent interface
     */
    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    /**
     * Removes an option
     *
     * @param string $name
     * @param string $default is returned if the option is not set
     * @return mixed
     */
    public function removeOption($name, $default = null)
    {
        $value = $this->getOption($name, $default);
        if (isset($this->_options[$name])) {
            unset($this->_options[$name]);
        }
        return $value;
    }

    /**
     * Clears all options
     */
    public function clearOptions()
    {
        $this->_options = array();
    }

    /**
     * Returns true if a option is set
     *
     * @param string $name
     * @return boolean
     */
    public function hasOption($name)
    {
        return (isset($this->_options[$name]));
    }

    /**
     * Returns all options
     * @return array all options
     */
    public function getOptions()
    {
        $private = explode(',', 'placement,separator,conditional');
        $result = array();
        foreach (array_keys($this->_options) as $option) {
            if (!in_array($option, $private)) {
                $result[$option] = $this->getOption($option);
            }
        }
        return $result;
    }

    /**
     * Sets the context of this decorator
     *
     * @param Context $context
     */
    public function context(Context $context = null)
    {
        if ($context == null) {
            return $this->_context;
        } else {
            $this->_context = $context;
            return $this;
        }
    }

    /**
     * Returns the name of the decorator
     * @return string
     */
    public function getName()
    {
        $className = get_class($this);
        $parts = array_slice(explode("\\", $className), -1);
        $name = strtolower($parts[0]);
        return $name;
    }

    /**
     * Handles "placement" of Decorator (prepeand/ appen)
     * @param string $original The original string
     * @param string $result The new string
     * @param string $placement Where to place the new string (append, prepend, wrap)
     * @param string $separator What to put between the original and the new string
     * @return string The resulting string
     */
    public function handlePlacement($original, $result, $placement = null, $separator = null)
    {
        $placement = ($placement) ? $placement : $this->getOption('placement', 'replace');
        $separator = ($separator) ? $separator : $this->getOption('separator', '');
        switch($placement) {
            case "prepend":
                return $result . $separator . $original;
                break;
            case "append":
                return $original . $separator . $result;
                break;
            default:
                return $result;
                break;
        }
    }

    /**
     * Magic getter, user for getting options
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->getOption($name);
    }

    /**
     * Magic setter, user for setting options
     * @param string $name
     * @param $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        $this->setOption($name, $value);
    }

    /**
     * Render the decorator. Subclasses should override this
     *
     * @param string $output
     * @throws Exception
     * @return string
     */
    public function render($output = '')
    {
        $msg = get_class($this) . ': render($output = "") function not implemeted';
        throw new Exception($msg);
    }

    /**
     * Merges this decorator with another one
     * @param Decorator $decorator The decorator to merge
     */
    public function merge(Decorator $decorator)
    {
        $this->_options = array_merge($this->_options, $decorator->_options);
    }
}
