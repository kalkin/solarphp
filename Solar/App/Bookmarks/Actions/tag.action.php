<?php
/**
 * 
 * Default controller; for viewing bookmarks by tag intersection.
 * 
 * @category Solar
 * 
 * @package Solar_App
 * 
 * @subpackage Solar_App_Bookmarks
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license LGPL
 * 
 * @version $Id$
 * 
 */

// get standalone objects
$bookmarks = Solar::factory('Solar_Model_Bookmarks');

// allow uri to set the "count" for each page (default 10)
$bookmarks->paging($this->_query('paging', 10));

// the requested owner_handle (none)
$owner_handle = null;

// what tags are we looking for?
$tags = $this->_info('tags');

// the requested ordering of list results
$order = $this->_getOrder();

// what page-number of the results are we looking for?
// (regardless of RSS or HTML)
$page = $this->_query('page', 1);

// get the list of results
$this->list = $bookmarks->fetchList($owner_handle, $tags, $order, $page);

// get the total pages and row-count
$total = $bookmarks->countPages($owner_handle, $tags);

// assign everything else for the view
$this->pages        = $total['pages'];
$this->order        = $order;
$this->page         = $page;
$this->owner_handle = null; // requested owner_handle
$this->tags         = $tags; // the requested tags
$this->tags_in_use  = $bookmarks->fetchTagList($owner_handle); // all tags

// use the 'list' view and site layout
$this->_view = 'list';
$this->_layout['head']['title'] = 'Solar_App_Bookmarks';
$this->_layout['body']['header'] = $this->locale('BOOKMARKS');

// RSS feed link for the page
$link = Solar::factory('Solar_Uri');
$link->setInfo(1, 'tagFeed');
$this->_layout['head']['link']['rss'] = array(
    'rel'   => 'alternate',
    'type'  => 'application/rss+xml',
    'title' => Solar::server('PATH_INFO'),
    'href'  => $link->export(),
);
?>