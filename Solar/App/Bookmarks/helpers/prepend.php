<?php

/**
* 
* Sets up the environment for all controller action scripts.
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

/**
* 
* Sets up the environment for all controller action scripts.
* 
* @category Solar
* 
* @package Solar_App
* 
* @subpackage Solar_App_Bookmarks
* 
*/

// get the shared user object
$user = Solar::shared('user');

// get the shared template object and add the path for local templates
$tpl = Solar::shared('template');
$tpl->addPath('template', $this->dir['views']);

// add any additional template paths (for theming)
$tpl->addPath(
	'template',
	Solar::config('Solar_App_Bookmarks', 'template_path', '')
);

// get standalone objects
$bookmarks = Solar::object('Solar_Cell_Bookmarks');
$form = Solar::object('Solar_Form');

?>