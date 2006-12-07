<?php
/**
 * 
 * Authenticate against an SQL database table.
 * 
 * @category Solar
 * 
 * @package Solar_Auth
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id$
 * 
 */

/**
 * Authentication adapter class.
 */
Solar::loadClass('Solar_Auth_Adapter');

/**
 * 
 * Authenticate against an SQL database table.
 * 
 * @category Solar
 * 
 * @package Solar_Auth
 * 
 * @todo add support for email, moniker, uri retrieval
 * 
 */
class Solar_Auth_Adapter_Sql extends Solar_Auth_Adapter {
    
    /**
     * 
     * User-supplied configuration values.
     * 
     * Keys are ...
     * 
     * `sql`
     * : (string|array) How to get the SQL object.  If a string, is
     *   treated as a [[Solar::registry()]] object name.  If array, treated as
     *   config for a standalone Solar_Sql object.
     * 
     * `table`
     * : (string) Name of the table holding authentication data.
     * 
     * `handle_col`
     * : (string) Name of the column with the user handle ("username").
     * 
     * `passwd_col`
     * : (string) Name of the column with the MD5-hashed passwd.
     * 
     * `email_col`
     * : (string) Name of the column with the email address.
     * 
     * `moniker_col`
     * : (string) Name of the column with the display name (moniker).
     * 
     * `uri_col`
     * : (string) Name of the column with the website URI.
     * 
     * `uid_col`
     * : (string) Name of the column with the numeric user ID ("user_id").
     * 
     * `salt`
     * : (string) A salt prefix to make cracking passwords harder.
     * 
     * `where`
     * : (string|array) Additional _multiWhere() conditions to use
     *   when selecting rows for authentication.
     * 
     * @var array
     * 
     */
    protected $_Solar_Auth_Adapter_Sql = array(
        'sql'         => 'sql',
        'table'       => 'members',
        'handle_col'  => 'handle',
        'passwd_col'  => 'passwd',
        'email_col'   => null,
        'moniker_col' => null,
        'uri_col'     => null,
        'uid_col'     => null,
        'salt'        => null,
        'where'       => array(),
    );
    
    /**
     * 
     * Verifies a username handle and password.
     * 
     * @return mixed An array of verified user information, or boolean false
     * if verification failed.
     * 
     * 
     */
    protected function _processLogin()
    {
        // get the dependency object of class Solar_Sql
        $obj = Solar::dependency('Solar_Sql', $this->_config['sql']);
        
        // get a selection tool using the dependency object
        $select = Solar::factory(
            'Solar_Sql_Select',
            array('sql' => $obj)
        );
        
        // list of optional columns as (property => field)
        $optional = array(
            'email'   => 'email_col',
            'moniker' => 'moniker_col',
            'uri'     => 'uri_col',
            'uid'     => 'uid_col',
        );
        
        // always get the user handle
        $cols = array($this->_config['handle_col']);
        
        // get optional columns
        foreach ($optional as $key => $val) {
            if ($this->_config[$val]) {
                $cols[] = $this->_config[$val];
            }
        }
        
        // salt and hash the password
        $md5 = md5($this->_config['salt'] . $this->_passwd);
        
        // build the select
        $select->from($this->_config['table'], $cols)
               ->where("{$this->_config['handle_col']} = ?", $this->_handle)
               ->where("{$this->_config['passwd_col']} = ?", $md5)
               ->multiWhere($this->_config['where']);
               
        // get the results
        $rows = $select->fetch('all');
        
        // if we get back exactly 1 row, the user is authenticated;
        // otherwise, it's more or less than exactly 1 row.
        if (count($rows) == 1) {
            
            // set base info
            $info = array('handle' => $this->_handle);
            
            // set optional info from optional cols
            $row = $rows->current();
            foreach ($optional as $key => $val) {
                if ($this->_config[$val]) {
                    $info[$key] = $row[$this->_config[$val]];
                }
            }
            
            // done
            return $info;
            
        } else {
            return false;
        }
    }
}
