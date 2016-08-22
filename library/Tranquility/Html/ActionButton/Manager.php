<?php namespace Tranquility\Html\ActionButton;

use Tranquility\Utility;

class Manager {
	
	/**
     * Array to hold the details of the primary action for the button
     *
     * @var array
     */
    protected $_primaryAction;
    
    /**
	 * Array to hold items in dropdown menu for the action button
	 *
	 * @var array
	 */
	protected $_dropdownItems;
    
    /**
     * Name of the view used to render the toolbar
     *
     * @var string
     */
    protected $_viewName;
    
	/**
	 * Constructor
	 */
	public function __construct() {
        $this->_primaryAction = array();
        $this->_dropdownItems = array();
	}
    
    /**
     * Sets the view used to render the toolbar
     *
     * @var $view   string  The name of the view to use
     * @return void
     */ 
    public function setView($view) {
		$this->_viewName = $view;
	}

    /**
     * Sets the text and primary action for the action button
     *
     * An element ID must be specified, in order to allow JavaScript to attach itself
     * to the link event handlers.
     *
     * @param string  $text       Link text displayed to the user
     * @param string  $id         HTML element ID
     * @param string  $url        URL of the link
     * @param string  $class      CSS classes to apply to the link element
     * @param string  $icon       Icon identifier for link (optional)
     * @param boolean $enabled    Sets whether the link is initially enabled
     * @param array   $attributes Array of key => value pairs of additional attributes to add to link
     * @return void
     */
    public function setPrimaryAction($text, $id, $url, $class = null, $icon = null, $enabled = true, $attributes = array()) {
        $this->_primaryAction['text'] = $text;
        $this->_primaryAction['id'] = $id;
        $this->_primaryAction['url'] = $url;
        
        if (!is_null($class)) {
            $this->_primaryAction['class'] = $class;
        }
        if (!is_null($icon)) {
            $this->_primaryAction['icon'] = $icon;
        }
        if (!is_null($enabled)) {
            $this->_primaryAction['enabled'] = (bool)$enabled;
        }
        if (is_array($attributes) && count($attributes) > 0) {
            $this->_primaryAction['attributes'] = $attributes;
        }
    }
	
	/**
     * Adds a new hyperlinked item as a dropdown menu item
     *
     * An element ID must be specified, in order to allow JavaScript to attach 
     * itself to the link event handlers.
     *
     * If the link is only intended to execute using JavaScript, use '#' as the
     * url parameter.
     *
     * @param string  $text      Link text displayed to the user
     * @param string  $id        HTML element ID
     * @param string  $url       URL of the link
     * @param string  $class     CSS classes to apply to the link element
     * @param string  $icon      Icon identifier for link (optional)
     * @param boolean $enabled   Sets whether the link is initially enabled
     * @param array   $attributes Array of key => value pairs of additional attributes to add to link
     * @return void
     */
    public function addLink($text, $id, $url, $class = null, $icon = null, $enabled = true, $attributes = array()) {
        $params = array(
            'type'       => 'link',
            'text'       => $text,
            'id'         => $id,
            'url'        => $url,
            'class'      => $class,
            'icon'       => $icon,
            'enabled'    => $enabled,
            'attributes' => $attributes
        );
		$this->_addElement($params);
    }
    
    /**
     * Add text as a dropdown menu item
     *
     * @param string  $text      Link text displayed to the user
     * @param string  $id        HTML element ID
     * @param string  $class     CSS classes to apply to the link element
     * @param string  $icon      Icon identifier for link (optional)
     * @param array   $attributes Array of key => value pairs of additional attributes to add to link
     * @return void
     */ 
    public function addText($text, $id = null, $class = null, $icon = null, $attributes = array()) {
        $params = array(
            'type'       => 'text',
            'text'       => $text,
            'id'         => $id,
            'class'      => $class,
            'icon'       => $icon,
            'attributes' => $attributes
        );
        $this->_addElement($params);
        return;
    }
    
    /**
     * Adds a divider element as a dropdown menu item
     *
     * @return void
     */
    public function addDivider() {
        $params = array('type' => 'divider');
		$this->_addElement($params);
    }

    /**
     * Returns the list of dropdown menu items
     *
     * @return array
     */
    public function getDropdownMenuItems() {
        return $this->_dropdownItems;
    }
    
    /**
     * Clears all dropdown menu items from the action button
     * 
     * @return boolean
     */
    public function clearDropdownMenuItems() {
        $this->_dropdownItems = array();
        return true;
    }
    
    public function render() {
        // Set data to render
        $data = array();
        $data['primaryAction'] = $this->_primaryAction;
        $data['dropdownMenuItems'] = $this->_dropdownItems;

        // Create a view, if it doesn't already exist
        $view = view($this->_viewName, $data);
        return $view;
    }

    protected function _addElement($params) {
		// Add element to array
        $this->_dropdownItems[] = array(
			'type'       => Utility::extractValue($params, 'type', ''),
            'text'       => Utility::extractValue($params, 'text', ''),
            'id'         => Utility::extractValue($params, 'id', ''),
            'url'        => Utility::extractValue($params, 'url', '#'),
            'items'      => Utility::extractValue($params, 'items', array()),
            'icon'       => Utility::extractValue($params, 'icon', ''),
            'class'      => Utility::extractValue($params, 'class', ''),
            'enabled'    => Utility::extractValue($params, 'enabled', true),
            'attributes' => Utility::extractValue($params, 'attributes', array())
		);
    }
}