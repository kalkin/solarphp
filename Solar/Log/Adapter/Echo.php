<?php
/**
 * 
 * Log adapter to echo messages directly.
 * 
 * @category Solar
 * 
 * @package Solar_Log
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id$
 * 
 */

/**
 * Solar_Log_Adapter
 */
Solar::loadClass('Solar_Log_Adapter');

/**
 * 
 * Log adapter to echo messages directly.
 * 
 * @category Solar
 * 
 * @package Solar_Cache
 * 
 */
class Solar_Log_Adapter_Echo extends Solar_Log_Adapter {
    
    /**
     * 
     * User-defined configuration values.
     * 
     * Keys are:
     * 
     * : \\events\\ : (string|array) The event types this instance
     *   should recognize; a comma-separated string of events, or
     *   a sequential array.  Default is all events ('*').
     * 
     * : \\format\\ : (string) The line format for each saved event.
     *   Use '%t' for the timestamp, '%e' for the event type, '%m' for
     *   the event description, and '%%' for a literal percent.  Default
     *   is '%t %e %m'.
     * 
     * : \\output\\ : (string) Output mode.  Set to 'html' for HTML; 
     *   or 'text' for plain text.  Default autodetects by SAPI version.
     * 
     * @var array
     * 
     */
    protected $_config = array(
        'events' => '*',
        'format' => '%t %e %m', // time, event, message
        'output' => null,
    );

    /**
     * 
     * Constructor.  Detect output mode by SAPI if none is specified.
     * 
     * @param array $config User-defined configuration.
     * 
     */
    public function __construct($config = null)
    {
        parent::__construct($config);
        if (empty($this->_config['output'])) {
            $mode = (PHP_SAPI == 'cli') ? 'text' 
                                        : 'html';
            $this->_config['output'] = $mode;
        }
    }

    /**
     * 
     * Echos the log message.
     * 
     * @param string $event The event type (e.g. 'info' or 'debug').
     * 
     * @param string $descr A description of the event. 
     * 
     * @return mixed Boolean false if the event was not saved (usually
     * because it was not recognized), or a non-empty value if it was
     * saved.
     * 
     */
    protected function _save($event, $descr)
    {
        $text = str_replace(
            array('%t', '%e', '%m', '%%'),
            array($this->_getTime(), $event, $descr, '%'),
            $this->_config['format']
        );

        if (strtolower($this->_config['output']) == 'html') {
            $text = htmlspecialchars($text) . '<br />';
        } else {
            $text .= PHP_EOL;
        }
    
        echo $text;
        return true;
    }
}
?>