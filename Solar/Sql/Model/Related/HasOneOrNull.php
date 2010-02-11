<?php
/**
 * 
 * Represents the characteristics of a relationship where a native model
 * "has one or none" of a foreign model; the difference from "has one" is
 * is that when there is no related at the database, no placeholder record
 * will be returned.
 * 
 * @category Solar
 * 
 * @package Solar_Sql_Model
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: HasOne.php 3835 2009-06-12 20:05:36Z pmjones $
 * 
 */
class Solar_Sql_Model_Related_HasOneOrNull extends Solar_Sql_Model_Related_HasOne
{
    /**
     * 
     * Returns a null when there is no related data.
     * 
     * @return null
     * 
     */
    public function fetchEmpty()
    {
        return null;
    }
}