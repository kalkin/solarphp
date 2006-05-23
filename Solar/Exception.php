<?php
/**
 * 
 * Exception class.
 * 
 * @category Solar
 * 
 * @package Solar_Exception
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id$
 * 
 */

/**
 * 
 * Exception class.
 * 
 * @category Solar
 * 
 * @package Solar_Exception
 * 
 */
class Solar_Exception extends Exception {
    
    /**
     * 
     * User-defined information array.
     * 
     * @var array
     * 
     */
    protected $_info = array();
    
    /**
     * 
     * Class where the exception originated.
     * 
     * @var array
     * 
     */
    protected $_class;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config User-defined configuration with keys
     * for 'class', 'code', 'text', and 'info'.
     * 
     */
    public function __construct($config = null)
    {
        $default = array(
            'class' => '',
            'code'  => '',
            'text'  => '',
            'info'  => array(),
        );
        $config = array_merge($default, (array) $config);
        extract($config);
        parent::__construct($text);
        $this->code = $code;
        $this->_class = $class;
        $this->_info = (array) $info;
    }
    
    /**
     * 
     * Returnes the exception as a string.
     * 
     * @return void
     * 
     */
    public function __toString()
    {
        return "exception '" . get_class($this) . "'\n"
             . "class::code '" . $this->_class . "::" . $this->code . "' \n"
             . "with message '" . $this->message . "' \n"
             . "information " . var_export($this->_info, true) . " \n"
             . "Stack trace:\n"
             . "  " . str_replace("\n", "\n  ", $this->getTraceAsString());
    }
    
    /**
     * 
     * Returns user-defined information.
     * 
     * @return array
     * 
     */
    final public function getInfo()
    {
        return $this->_info;
    }
    
    /**
     * 
     * Returns the class name that threw the exception.
     * 
     * @return string
     * 
     */
    final public function getClass()
    {
        return $this->_class;
    }
    
    /**
     * 
     * Returns the class name and code together.
     * 
     * @return string
     * 
     */
    final public function getClassCode()
    {
        return $this->_class . '::' . $this->code;
    }
    
    
}
?>