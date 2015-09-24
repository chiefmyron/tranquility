<?php namespace Tranquility\Html\Toolbar;

class Manager {
	
	/**
	 * Array to hold elements of the toolbar
	 *
	 * @var array
	 */
	protected $_elements;
    
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
        $this->_elements = array();
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
     * Adds a new hyperlinked item to the toolbar
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
     * @param boolean $enabled  Sets whether the link is initially enabled
     * @return void
     */
    public function addLink($text, $id, $url, $class = null, $icon = null, $enabled = true ) {
		$this->_addElement('link', $text, $id, $url, array(), $class, $icon, $enabled);
    }
    
    /**
     * Adds a new hyperlink styled to look like a button to the toolbar
     *
     * An element ID must be specified, in order to allow JavaScript to attach 
     * itself to the link event handlers.
     *
     * If the link is only intended to execute using JavaScript, use '#' as the
     * url parameter.
     *
     * @param string $text      Link text displayed to the user
     * @param string $id        HTML element ID
     * @param string $url       URL of the link
     * @param string $class     CSS classes to apply to the link element
     * @param string $icon      Icon identifier for link (optional)
     * @param boolean $enabled  Sets whether the link is initially enabled
     * @return void
     */
    public function addButton($text, $id, $url, $class = null, $icon = null, $enabled = true ) {
        $this->_addElement('button', $text, $id, $url, array(), $class, $icon, $enabled);
    }
    
    public function addMultiItemButton($text, $id, $url, $items = array(), $class = null, $icon = null, $enabled = true) {
        $this->_addElement('multibutton', $text, $id, $url, $items, $class, $enabled);
    }
    
    public function addHeading($text, $class = null, $icon = null) {
		$this->_addElement('header', $text, null, null, array(), $class, $icon, true);
    }
    
    public function addText($text, $id = null, $class = null, $icon = null) {
        $this->_addElement('text', $text, $id, null, array(), $class, $icon, true);
        return;
    }
    
    public function addTextWithLabel($text, $label, $id = null, $class = null, $icon = null) {
		$this->_addElement('textWithLabel', $text, $id, $url, $label, $class, $icon, $enabled);
    }

    /**
     * Adds a divider element to the toolbar
     *
     * @return void
     */
    public function addDivider() {
		$this->_addElement('divider', null);
    }

    /**
     * Returns the list of toolbar links in display order
     *
     * @return array
     */
    public function getElements() {
        return $this->_elements;
    }
    
    /**
     * Clears all elements in the toolbar
     * 
     * @return boolean
     */
    public function clearElements() {
        $this->_elements = array();
        return true;
    }
    
    public function render() {
        // Create a view, if it doesn't already exist
        $view = view($this->_viewName)->with('toolbar', $this->getElements());
        return $view;
    }

	protected function _addElement($type, $text, $id = null, $url = null, $items = array(), $class = null, $icon = null, $enabled = true) {
		// If the URL isn't set, default to an empty anchor
        if (is_null($url) || $url == '') {
            $url = '#';
        }
		
		// TODO: Validate element type
        
		// Add element to array
        $this->_elements[] = array(
			'type'    => $type,
            'text'    => $text, 
            'id'      => $id,
            'url'     => $url,
            'items'   => $items,
            'icon'    => $icon,
            'class'   => $class,
            'enabled' => $enabled
		);
    }
}