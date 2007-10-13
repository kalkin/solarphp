<?php
/**
 * 
 * Represents the characteristics of a relationship where a native model
 * "has one" of a foreign model.
 * 
 * @category Solar
 * 
 * @package Solar_Sql_Model
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Exception.php 2440 2007-04-21 14:33:44Z pmjones $
 * 
 */

/**
 * 
 * Represents the characteristics of a relationship where a native model
 * "has one" of a foreign model.
 * 
 * @category Solar
 * 
 * @package Solar_Sql_Model
 * 
 */
class Solar_Sql_Model_Related_HasOne extends Solar_Sql_Model_Related {
    
    /**
     * 
     * Sets the relationship type.
     * 
     * @return void
     * 
     */
    protected function _setType()
    {
        $this->type = 'has_one';
    }
    
    /**
     * 
     * Fixes the related column names in the user-defined options **in place**.
     * 
     * The foreign key is stored in the **foreign** model.
     * 
     * @param array $opts The user-defined relationship options.
     * 
     * @return void
     * 
     */
    protected function _fixRelatedCol(&$opts)
    {
        $opts['foreign_col'] = $opts['foreign_key'];
    }
    
    /**
     * 
     * A support method for _fixRelated() to handle has-one relationships.
     * 
     * @param array &$opts The relationship options; these are modified in-
     * place.
     * 
     * @param StdClass $foreign The catalog entry for the foreign model.
     * 
     * @return void
     * 
     */
    protected function _setRelated($opts)
    {
        // the foreign column
        if (empty($opts['foreign_col'])) {
            // named by native table's suggested foreign_col name
            $this->foreign_col = $this->_native_model->foreign_col;
        } else {
            $this->foreign_col = $opts['foreign_col'];
        }
        
        // the native column
        if (empty($opts['native_col'])) {
            // named by native primary key
            $this->native_col = $this->_native_model->primary_col;
        } else {
            $this->native_col = $opts['native_col'];
        }
        
        // the fetch type
        if (empty($opts['fetch'])) {
            $this->fetch = 'one';
        } else {
            $this->fetch = $opts['fetch'];
        }
    }
}