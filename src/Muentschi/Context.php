<?php

namespace Muentschi;

/**
 * Represents a context
 * @author Urban Etter
 */
class Context
{

    /**
     * The name of the context
     * @var string
     */
    protected $_name;

    /**
     * The id of the context
     * @var string
     */
    protected $_id;

    /**
     * The tags of this context, will be inherited
     * @var array
     */
    protected $_tags;
    
    /**
     * The computed tags, will not be inherited
     */
    protected $_computedTags;

    /**
     * The selectors for this context
     * @var array
     */
    protected $_selectors;

    /**
     * The content of the context
     * @var array
     */
    protected $_content;
    
    /**
     * Ids which will be used for creating sub context. Array with context name as key, comma separated ids as value
     * @var array
     */
    protected $_ids;
    
    static private $_log;

    /**
     * Create an context instance
     *
     * @param string $name name of the context instance
     * @param string $id id of the context
     */
    public function __construct($name = 'main', $id = null)
    {
        $this->_name = $name;
        if ($id === null) {
            $id = $name;
        }
        $this->_id = $id;
        $this->_selectors = array();
        $this->_tags = array();
        $this->_content = array();
    }

    /**
     * Get the name of context: name()
     * Set the name of context: name($newName)
     * @param string $name Set this parameter if you want to set the name
     * @return string|Context
     */
    public function name($name = null)
    {
        if ($name === null) {
            return $this->_name;
        } else {
            $this->_name = $name;
            return $this;
        }
    }

    /**
     * Get the id of context: id()
     * Set the id of context: id($newId)
     * @param string $id Set this parameter if you want to set the id
     * @return string|Context
     */
    public function id($id = null)
    {
        if ($id === null) {
            return $this->_id;
        } else {
            $this->_id = $id;
            return $this;
        }
    }

    /**
     * Sets the content
     * @param string|array $name content itself or name of the content
     * @param array $content if a name is given value is the content
     * @return Context
     */
    public function setContent($name, $content = null)
    {
        if ($content === null) {
            $content = $name;
            $name = $this->_name;
        }
        $this->_content[$name] = $content;
        
        // if an empty content is set, we set the computed tag
        if (empty($content)) {
        	$this->addComputedTag('empty');
        }
        return $this;
    }

    /**
     * Returns the content with a certain name
     * @param string $name the name of the content to return
     * @return mixed
     */
    public function getContent($name = null)
    {
        if ($name === null) {
            $name = $this->_name;
        }

        if (substr($name, 0, 1) == '#') {
            $name = substr($name, 1);
            if (method_exists($this, $name)) {
                return $this->$name();
            }
        }

        $parts = explode('.', $name);
        $result = $this->_content;
        foreach ($parts as $part) {
            if (!is_array($result) || !isset($result[$part])) {
                return '';
            }
            $result = $result[$part];
        }
        return $result;
    }

    /**
     * Returns a selector for $what
     * @param string $what what to select
     * @return Selector
     */
    public function select($what)
    {
        if (!isset($this->_selectors[$what])) {
            $this->_selectors[$what] = new Selector();
        }
        return 	$this->_selectors[$what];
    }

    /**
     * Applies the selectors and renders the context
     * @param mixed $content optional content for rendering
     * @throws Exception
     * @return string the rendered context
     */
    public function render($content = null)
    {
        if ($content !== null) {
            $this->setContent($content);
        }
        self::$_log[] = '[Render] Rendering ' . $this->_name . '#' . $this->_id;

        $decorators = array();

        // sort selectors according operators in key: name < id < content < tag
        uksort($this->_selectors, array($this, 'compareSelector'));

        foreach ($this->_selectors as $what => $selector) {
            if ($this->applies($what)) {
		        self::$_log[] = '[Selector] Selector ' . $what . ' applies';
                $decorators = $selector->apply($decorators);
                $this->_tags = array_merge($this->_tags, $selector->tags);
                $this->_content = array_merge($this->_content, $selector->content);
            }
        }

        // throw exception if there is no decorator applying
        if (count($decorators) == 0) {
            throw new Exception('No decorator applies for ' . $this->name());
        }

        // decorators are upside down now
        $decorators = array_reverse($decorators);

        $result = '';
        foreach ($decorators as $decorator) {
            // only work on clone, the original object may be used for other
            // contexts as well
            $decorator = clone ($decorator);
            $decorator->context($this);
            $class = substr(get_class($decorator), 20);
            self::$_log[] = '[Decorator] Rendering decorator ' . $class . ' with name ' . $decorator->getName();
            $result = $decorator->render($result);
        }

        return $result;
    }

    /**
     * Compares selectors. The more specific the heavier they are. This function
     * is used for sorting selectors before applying them to the context.
     * @param string $a first selector
     * @param string $b second selector
     * @return int 0 if both are the same, 1 if $a > $b, -1 if $a < $b
     */
    public function compareSelector($a, $b)
    {
        $weightA = (substr_count($a, '#') * 1) + (substr_count($a, ' ') * 2) + (substr_count($a, '.') * 4) + (substr_count($a, ':') * 8);
        $weightB = (substr_count($b, '#') * 1) + (substr_count($b, ' ') * 2) + (substr_count($b, '.') * 4) + (substr_count($b, ':') * 8);
        return ($weightA == $weightB) ? 0 : (($weightA > $weightB) ? 1 : -1);
    }

    /**
     * Determines if the given selector applies to this context.
     * Possible selectors:
     *  * Named content: '[name=value]' applies when a certain content is set
     *  * Content: '[value]' applies when the actual context has value as content
     *  * Tags: '.tag' applies when the context has the tag
     *  * Id: '#id' applies when the context has the id
     *  * Nested name: 'parent child' applies when the parent context and the child context have the given name
     *  * Name: 'name' applies when the context has the given name
     * @param string $what the selector to test
     * @return boolean true if the selector applies, false otherwise
     */
    public function applies($what)
    {
        // content
        $matches = array();
        $contentPattern = '/\[([^\]]*)\]/';
        if (preg_match_all($contentPattern, $what, $matches)) {
            foreach ($matches[1] as $content) {
                $what = str_replace("[$content]", '', $what);
                $value = $this->getContent();
                if (strpos($content, '=') !== false) {
                    list ($name, $content) = explode('=', $content);
                    $value = @$value[$name];
                }
                if ($content != $value) {
                    return false;
                }
            }
        }

        // tag
        $matches = array();
        $tagPattern = '/\.([^#:\.[]*)/';
        if (preg_match_all($tagPattern, $what, $matches)) {
            foreach ($matches[1] as $tag) {
                $what = str_replace(".$tag", '', $what);
                if (!$this->hasTag($tag)) {
                    return false;
                }
            }
        }

        // computed tag
        $matches = array();
        $tagPattern = '/:([^#:\.[]*)/';
        if (preg_match_all($tagPattern, $what, $matches)) {
            foreach ($matches[1] as $tag) {
                $what = str_replace(":$tag", '', $what);
                if (!$this->hasComputedTag($tag)) {
                    return false;
                }
            }
        }

        // id
        $matches = array();
        $idPattern = '/#([^#:\.[]*)/';
        if (preg_match($idPattern, $what, $matches)) {
            $id = $matches[1];
            if ($id != $this->_id) {
                return false;
            }
            $what = preg_replace($idPattern, '', $what);
            if ($what == '') {
                // only id was specified
                return true;
            }
        }

        return ($what == $this->_name);
    }

    /**
     * Decorate the main context
     * @param mixed $decorator the decorator as string or as object
     * @param array $options
     * @return Context fluent interface
     */
    public function add($decorator, $options = null)
    {
        $selector = $this->select($this->name());
        $selector->add($decorator, $options);
        return $this;
    }

    /**
     * Creates a sub context of the given name
     * @param string $name the context name
     * @param string $id the id of the new context
     * @return Context The created context
     */
    public function createContext($name, $id = null)
    {
        $context = new Context($name, $id);
        $id = $context->id();

        // add selectors
        foreach ($this->_selectors as $key => $selector) {
            $parts = explode(' ', $key);
            // handle nested selectors
            $first = array_shift($parts);
            if (count($parts) > 0 && $context->applies($first)) {
                $key = implode(' ', $parts);
            }
            $context->_selectors[$key] = $selector;
        }

        // add tags and ids
        $context->_tags = $this->_tags;
        $context->_ids = $this->_ids;

        // add content
        $context->_content = $this->_content;
        $content = $this->getContent();
        if (is_array($content) && isset($content[$id])) {
            $content = $content[$id];
        }
        $context->setContent($name, $content);
        
        self::$_log[] = '[Context] Creating context ' . $name . ' with id ' . $id;
        
        return $context;
    }

    /**
     * Adds a Tag to the context
     * @param string $tag The tag to add
     * @return Context Fluent interface
     */
    public function addTag($tag)
    {
        $this->_tags[$tag] = $tag;
        return $this;
    }

    /**
     * Removes a Tag
     * @param string $tag Tag to remove
     * @return Context Fluent interface
     */
    public function removeTag($tag)
    {
        if ($this->hasTag($tag)) {
            unset( $this->_tags[$tag] );
        }
        return $this;
    }

    /**
     * Checks if context has a certain Tag
     * @param string $tag Tag to test
     * @return boolean True if context has the tag, false otherwise
     */
    public function hasTag($tag)
    {
        return isset($this->_tags[$tag]);
    }

    /**
     * Returns all the tags as array
     * @return array The tags
     */
    public function getTags()
    {
        return $this->_tags;
    }
    
    /**
     * Adds a computed tag
     *
     * @param string $name 
     * @return Context Fluent interface
     */
    public function addComputedTag($name)
    {
        $this->_computedTags[$name] = $name;
        return $this;
    }

    /**
     * Checks if context has a certain computed tag
     * @param string $tag Tag to test
     * @return boolean True if context has the tag, false otherwise
     */
    public function hasComputedTag($tag)
    {
        return isset($this->_computedTags[$tag]);
    }

    /**
     * Removes a computed tag
     * @param string $tag Tag to remove
     * @return Context Fluent interface
     */
    public function removeComputedTag($tag)
    {
        if ($this->hasComputedTag($tag)) {
            unset( $this->_computedTags[$tag] );
        }
        return $this;
    }

    /**
     * Returns all the computed tags as array
     * @return array The computed tags
     */
    public function getComputedTags()
    {
        return $this->_computedTags;
    }
    
    /**
     * Returns the name of the context
     * @return String The name of the context
     */
    public function getName()
    {
    	return $this->_name;
    }

    /**
     * Sets the name of the context
     * @param string $name Name of the context
     * @return $this
     */
    public function setName($name)
    {
    	$this->_name = $name;
    	return $this;
    }
    
    /**
     * Get or set which contexts should be created with a certain context. Ids are comma seperated or an array.
     * Get: ids($name)
     * Set: ids($name, $ids)
     * @param string $name Set this parameter if you want to set the id
     * @param string|array Array of ids or comma separated list  of ids
     * @return string|Context
     */
    public function ids($name, $ids = null)
    {
    	if ($ids === null) {
    		return isset($this->_ids[$name]) ? $this->_ids[$name] : '';
    	}
    	if (is_array($ids)) {
    	    $ids = implode(",", $ids);
    	}
    	$this->_ids[$name] = $ids;
    	return $this;
    }
    
    /**
     * Clears the log
     */
    static public function clearLog()
    {
    	self::$_log = array();
    }

    /**
     * Returns the log messages
     * @param string $interest Specify render, selector or decorator if you're only intersted in this topic
     * @return array
     */
    static public function getLog($interest = null) {
    	$return = self::$_log;
    	if ($interest !== null) {
    		$return = array_filter($return, create_function('$s', 'return (strpos($s, "[' . $interest . ']") === 0);'));
    	}
    	return $return;
    }

 }
