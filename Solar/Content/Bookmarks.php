<?php
/**
 * 
 * Content class for bookmark nodes.
 * 
 * @category Solar
 * 
 * @package Solar_Content
 * 
 * @subpackage Solar_Content_Bookmarks
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license LGPL
 * 
 * @version $Id$
 * 
 */

/**
 * Abstract content master node.
 */
require_once 'Solar/Content/Abstract.php';

/**
 * 
 * Content class for bookmark nodes.
 * 
 * @category Solar
 * 
 * @package Solar_Content
 * 
 * @subpackage Solar_Content_Bookmarks
 * 
 */
class Solar_Content_Bookmarks extends Solar_Content_Abstract {
    
    protected $_type = 'bookmark';
    
    protected $_form = array('id', 'uri', 'subj', 'summ', 'tags', 'rank');
    
    /**
     * 
     * Fetches a list of bookmarks from the content store.
     * 
     * You can specify an owner_handle (username) and/or a list of
     * tags to filter the list.
     * 
     * @param string|array $tags Fetch bookmarks with all these
     * tags; if empty, fetches for all tags.
     * 
     * @param string $handle The owner_handle (username) to fetch
     * bookmarks for; if empty, fetches for all owners.
     * 
     * @param string|array $order Order in this fashion; if empty,
     * orders by creation-timestamp descending (most-recent first).
     * 
     * @param int $page Which page-number of results to fetch.
     * 
     * @return array The list of bookmarks.
     * 
     */
    public function fetchList($tags = null, $handle = null, $order = null,
        $page = null)
    {
        $where = array();
        if ($handle) {
            $where['nodes.owner_handle = ?'] = $handle;
        }
        
        if (empty($order)) {
            $order = 'nodes.created DESC';
        }
        
        return parent::fetchList($tags, $where, $order, $page);
    }
    
    /**
     * 
     * Fetches a total count and pages of bookmarks in the content store.
     * 
     * You can specify an owner_handle (username) and/or a list of
     * tags to limit the count.
     * 
     * @param string|array $tags Count bookmarks with all these
     * tags; if empty, counts for all tags.
     * 
     * @param string $handle The owner_handle (username) to count
     * bookmarks for; if empty, counts for all owners.
     * 
     * @return array A array with keys 'count' (total number of 
     * bookmarks) and 'pages' (number of pages).
     * 
     */
    public function countPages($tags = null, $handle = null)
    {
        $where = array();
        if ($handle) {
            $where['nodes.owner_handle = ?'] = $handle;
        }
        
        return parent::countPages($tags, $where);
    }
    
    /**
     * 
     * Fetches a list of all bookmark tags.
     * 
     * You can specify an owner_handle (username) to limit the list.
     * 
     * @param string $handle The owner_handle (username) to list
     * bookmark tags for; if empty, lists for all owners.
     * 
     * @return array An array where the key is the tag name and
     * the value is the number of times that tag appears.
     * 
     */
    public function fetchTags($handle = null)
    {
        $where = array();
        if ($handle) {
            $where['nodes.owner_handle = ?'] = $handle;
        }
        return parent::fetchTags($where);
    }
    
    /**
     * 
     * Fetches a bookmark by owner_handle (username) and URI.
     * 
     * Useful for seeing if an owner has already bookmarked a URI.
     * 
     * @param string $handle The owner_handle (username).
     * 
     * @param string $uri The URI to look form
     * 
     * @return array The node data.
     * 
     */
    public function fetchOwnerUri($handle, $uri)
    {
        $tags = null;
        $where = array();
        $where['nodes.owner_handle = ?'] = $handle;
        $where['nodes.uri = ?']          = $uri;
        return parent::fetchItem($tags, $where);    
    }
    
    /**
     * 
     * Returns a bookmark data-entry form processor.
     * 
     * @return Solar_Form
     * 
     */
    public function form($data = null)
    {
        $form = parent::form($data);
        $form->elements['bookmark[uri]']['attribs']['size']  = 48;
        $form->elements['bookmark[subj]']['attribs']['size'] = 48;
        $form->elements['bookmark[summ]']['attribs']['size'] = 48;
        $form->elements['bookmark[tags]']['attribs']['size'] = 48;
        $form->elements['bookmark[rank]']['attribs']['size'] = 5;
        return $form;
    }
}
?>