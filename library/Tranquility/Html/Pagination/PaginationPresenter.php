<?php namespace Tranquility\Html\Pagination;

use Illuminate\Support\HtmlString;
use Illuminate\Pagination\BootstrapThreePresenter;

class PaginationPresenter extends BootstrapThreePresenter {

    /**
     * Name of the view used to render the pagination block
     *
     * @var string
     */
    protected $_viewName;

    /**
     * Sets the view used to render the pagination block
     *
     * @var $view   string  The name of the view to use
     * @return void
     */ 
    public function setView($view) {
		$this->_viewName = $view;
	}

    /**
     * Convert the URL window into Bootstrap HTML.
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function render() {
        if ($this->hasPages()) {
            // Set data to render
            $data = array();
            $data['links'] = $this->getPaginationLinks();
            $data['currentPage'] = $this->paginator->currentPage();
            $data['totalPages'] = $this->paginator->lastPage();
            $data['firstItem'] = $this->paginator->firstItem();
            $data['lastItem'] = $this->paginator->lastItem();
            $data['totalItems'] = $this->paginator->total();

            // Create a view, if it doesn't already exist
            $view = view($this->_viewName, $data);
            return new HtmlString($view);
        }

        return '';
    }

    /**
     * Render the actual link slider.
     *
     * @return array
     */
    protected function getPaginationLinks() {
        // Show 'previous' button
        $links = $this->getPreviousPageLink();

        // Always show first page
        if (is_array($this->window['first'])) {
            $links = array_merge($links, $this->getPageNumberLinks($this->window['first']));
        }

        // Show some or more of the middle page links (the 'slider')
        if (is_array($this->window['slider'])) {
            $links = array_merge($links, $this->getDots(), $this->getPageNumberLinks($this->window['slider']));
        }

        // Always the last page
        if (is_array($this->window['last'])) {
            $links = array_merge($links, $this->getDots(), $this->getPageNumberLinks($this->window['last']));
        }

        // Show 'next' button
        $links = array_merge($links, $this->getNextPageLink());

        return $links;
    }

    /**
     * Get the links for the URLs in the given array.
     *
     * @param  array  $urls
     * @return array
     */
    function getPageNumberLinks(array $urls) {
        $links = array();
        
        foreach ($urls as $page => $url) {
            // Set details for link
            $item = array(
                'type' => 'page',
                'pageNumber' => $page,
                'url' => $url,
                'active' => true
            );

            // If this link is for the current page, make the link inactive
            if ($this->paginator->currentPage() == $page) {
                $item['active'] = false;
            }

            $links[] = $item;
        }

        return $links;
    }

    /**
     * Get a pagination "dot" element.
     *
     * @return string
     */
    function getDots() {
        $links = array();
        $links[] = array('type' => 'dots');
        return $links;
    }

    /**
     * Get the previous page pagination element.
     *
     * @return array
     */
    function getPreviousPageLink() {
        $links = array();

        // Set details for link
        $item = array('type' => 'previous');

        // Check if we are already on the first page
        if ($this->paginator->currentPage() <= 1) {
            // Disable link
            $item['pageNumber'] = -1;
            $item['url'] = null;
            $item['active'] = false;
        } else {
            // Set previous page number
            $pageNum = $this->paginator->currentPage() - 1;

            // Set link details
            $item['pageNumber'] = $pageNum;
            $item['url'] = $this->paginator->url($pageNum);
            $item['active'] = true;
        }

        $links[] = $item;
        return $links;
    }

    /**
     * Get the next page pagination element.
     *
     * @return array
     */
    function getNextPageLink() {
        $links = array();

        // Set details for link
        $item = array('type' => 'next');

        // Check if we are already on the last page
        if (!$this->paginator->hasMorePages()) {
            // Disable link
            $item['pageNumber'] = -1;
            $item['url'] = null;
            $item['active'] = false;
        } else {
            // Set next page number
            $pageNum = $this->paginator->currentPage() + 1;

            // Set link details
            $item['pageNumber'] = $pageNum;
            $item['url'] = $this->paginator->url($pageNum);
            $item['active'] = true;
        }

        $links[] = $item;
        return $links;
    }
}
