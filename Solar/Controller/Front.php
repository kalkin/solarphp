<?php
/**
 * 
 * Front-controller class for Solar.
 * 
 * @category Solar
 * 
 * @package Solar_Controller
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
 * Front-controller class for Solar.
 * 
 * An example front-controller "index.php" for your web root:
 *
 * <code>
 * require_once 'Solar.php';
 * Solar::start();
 * $front = Solar::factory('Solar_Controller_Front');
 * $front->display();
 * Solar::stop();
 * </code>
 * 
 * @category Solar
 * 
 * @package Solar_Controller
 * 
 * @todo How to get data back from the app to use in the layout (e.g., html title?)
 * 
 */
class Solar_Controller_Front extends Solar_Base {

    /**
     * 
     * User-defined configuration array.
     * 
     * @var array
     * 
     */
    protected $_config = array(
        'app_class'   => array(
            'hello'     => 'Solar_App_HelloWorld',
            'bookmarks' => 'Solar_App_Bookmarks',
        ),
        'app_default' => 'bookmarks',
        'layout_dir'  => '',
        'layout_tpl'  => '',
        'layout_var'  => 'solar_app_output',
    );

    /**
     * 
     * The default short-name when none is specified.
     * 
     * @var array
     * 
     */
    protected $_app_default;

    /**
     * 
     * Map of app names to classes.
     * 
     * @var array
     * 
     */
    protected $_app_class;
    
    protected $_layout_tpl; // the layout template name
    
    protected $_layout_dir; // the directory where the layout templates are
    
    protected $_layout_var; // name of the app-output var in the layout template
    
    /**
     * 
     * Constructor.
     * 
     * @var array
     * 
     */
    public function __construct($config)
    {
        // set the layout directory and name
        $this->_config['layout_dir'] = dirname(dirname(__FILE__)) . '/Layout';
        $this->_config['layout_tpl'] = 'default';
        
        // now do "real" construction
        parent::__construct($config);
        $this->_app_default = $this->_config['app_default'];
        $this->_app_class   = $this->_config['app_class'];
        $this->_layout_dir  = $this->_config['layout_dir'];
        $this->_layout_tpl  = $this->_config['layout_tpl'];
        $this->_layout_var  = $this->_config['layout_var'];
    }

    /**
     * 
     * Fetches the output of an app/action/info specification URI.
     * 
     * @param string $spec A app/action/info spec for the front
     * controller. E.g., 'bookmarks/user/pmjones/php+blog?page=2'.
     * 
     * @return string The output of the application action.
     * 
     */
    public function fetch($spec = null)
    {
        // default to current URI
        $uri = Solar::factory('Solar_Uri');
        
        // override current URI with user spec
        if (is_string($spec)) {
            $uri->importAction($spec);
        }
        
        // pull the app name off the top of the path_info.
        $name = array_shift($uri->info);
        if (trim($name) == '') {
            // no app specified, use the default.
            $name = $this->_app_default;
        }
        
        /** @todo Add real 404 support. */
        // is it a known app name?
        if (! array_key_exists($name, $this->_app_class)) {
            return htmlspecialchars("404: Page '$name' unknown.");
        }
        
        // instantiate the app class and fetch its content.
        $class   = $this->_app_class[$name];
        $app     = Solar::factory($class);
        $content = $app->fetch($uri);
        
        // did the app set any data for the layout?
        $layout = $app->getLayout();
        if ($layout === false) {
            // the app explicitly does not want to use
            // the layout, so fall back to a one-step view
            // and just return the app content.  typically
            // this is the case in things like RSS feeds.
            return $content;
        } else {
            // set up the layout template for a two-step view.
            $tpl = Solar::factory('Solar_Template');
            
            // step 1:
            // assign the app's layout data, then assign the app content
            // (so that the content overrides any related app data).
            $tpl->assign($layout);
            $tpl->assign($this->_layout_var, $content);
            
            // step 2:
            // fetch the layout with the content and vars.
            $tpl->setPath('template', $this->_layout_dir);
            return $tpl->fetch($this->_layout_tpl . '.layout.php');
        }
    }

    /**
     * 
     * Displays the output of an app/action/info specification URI.
     * 
     * @param string $spec A app/action/info spec for the front
     * controller. E.g., 'bookmarks/user/pmjones/php+blog?page=2'.
     * 
     * @return string The output of the application.
     * 
     */
    public function display($spec = null)
    {
        echo $this->fetch($spec);
    }
}
?>