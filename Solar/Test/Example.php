<?php
/**
 * 
 * Example for testing Solar class-to-file hierarchy, locales, and exceptions.
 * 
 * @category Solar
 * 
 * @package Solar_Test
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
 * Example for testing Solar class-to-file hierarchy, locales, and exceptions.
 * 
 * @category Solar
 * 
 * @package Solar_Test
 * 
 */
class Solar_Test_Example extends Solar_Base {
    
    protected $_config = array(
        'foo' => 'bar',
        'baz' => 'dib',
        'zim' => 'gir',
    );
    
    public function classSpecificException()
    {
        throw $this->_exception('ERR_CUSTOM_CONDITION');
    }
    
    public function solarSpecificException()
    {
        throw $this->_exception('ERR_FILE_NOT_FOUND');
    }
    
    public function classGenericException()
    {
        throw $this->_exception('ERR_GENERIC_CONDITION');
    }
    
    public function solarGenericException()
    {
        throw $this->_exception('ERR_NO_SUCH_CONDITION');
    }
    
    public function exceptionFromCode($code) {
        throw $this->_exception($code);
    }
}
?>