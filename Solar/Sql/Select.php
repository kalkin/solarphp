<?php

/**
* 
* Class for SQL select generation and results.
* 
* @category Solar
* 
* @package Solar
* 
* @subpackage Solar_Sql
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
* Class for SQL select generation and results.
* 
* Example usage:
* 
* <code>
* $select = Solar::object('Solar_Sql_Select');
* 
* // select these columns
* $select->cols(array(
*   'id',
* 	'n_last',
* 	'n_first',
* 	'adr_street',
* 	'adr_city',
* 	'adr_region AS state',
* 	'adr_postcode AS zip',
* 	'adr_country',
* ));
* 
* // from this table
* $select->from('contacts'); // single or multi
* 
* // on these ANDed conditions
* $select->where('n_last = :lastname');
* $select->where('adr_city = :city');
* 
* // reverse-ordered by first name
* $select->order('n_first DESC')
* 
* // get 50 per page, when we limit by page
* $select->paging(50);
* 
* // bind data into the query.
* // remember :lastname and :city in the setWhere() calls above.
* $data = ('lastname' => 'Jones', 'city' => 'Memphis');
* $select->bind($data);
* 
* // limit by which page of results we want
* $select->limitPage(1);
* 
* // get a Solar_Sql_Result object (the default)
* $result = $select->fetch(); // or fetch('result')
* 
* // alternatively, fetch all rows as an array
* $rows = $select->fetch('all');
* 
* // find out the count of rows, and how many pages there are.
* // this comes back as an array('count' => ?, 'pages' => ?).
* $total = $select->countPages();
* 
* </code>
* 
* @category Solar
* 
* @package Solar
* 
* @subpackage Solar_Sql
* 
*/

class Solar_Sql_Select extends Solar_Base {
	
	
	/**
	* 
	* User-provided configuration.
	* 
	* Keys are:
	* 
	* sql => (string|array) Name of the shared SQL object, or array of (driver,
	* options) to create a standalone SQL object.
	* 
	* paging => (int) Number of rows per page.
	* 
	* @access protected
	* 
	* @var array
	* 
	*/
	
	protected $config = array(
		'sql'    => 'sql',
		'paging' => 10,
	);
	
	
	/**
	* 
	* Data to bind into the query as key => value pairs.
	* 
	* @access protected
	* 
	* @var array
	* 
	*/
	
	protected $bind = array();
	
	
	/**
	* 
	* The component parts of a select statement.
	* 
	* @access protected
	* 
	* @var array
	* 
	*/
	
	protected $parts = array(
		'distinct' => false,
		'cols'     => array(),
		'from'     => array(),
		'join'     => array(),
		'where'    => array(),
		'group'    => array(),
		'having'   => array(),
		'order'    => array(),
		'limit'    => array(
			'count'  => 0,
			'offset' => 0
		),
	);
	
	
	/**
	* 
	* The number of rows per page.
	* 
	* @access protected
	* 
	* @var int
	* 
	*/
	
	protected $paging = 10;
	
	
	/**
	* 
	* Tracks which columns are being select from each table and join.
	* 
	* We use this for automated deconfliction.
	* 
	* @access protected
	* 
	* @var array
	* 
	*/
	
	protected $tbl_cols = array();
	
	
	/**
	* 
	* Constructor.
	* 
	* @access public
	* 
	* @param array $config User-provided config values.
	* 
	*/

	public function __construct($config = null)
	{
		// basic construction
		parent::__construct($config);
		
		// connect to the database
		if (is_string($this->config['sql'])) {
			// use a shared object
			$this->sql = Solar::shared($this->config['sql']);
		} else {
			// use a standalone object
			$this->sql = Solar::object(
				$this->config['sql'][0],
				$this->config['sql'][1]
			);
		}
		
		// set up defaults
		$this->paging($this->config['paging']);
	}
	
	
	/**
	* 
	* Read-only access to properties.
	* 
	* @access public
	* 
	* @return mixed The property value.
	* 
	*/

	public function __get($key)
	{
		return $this->$key;
	}
	
	
	/**
	* 
	* Sets the number of rows per page.
	* 
	* @access public
	* 
	* @param int $rows The number of rows to page at.
	* 
	* @return void
	* 
	*/

	public function paging($rows)
	{
		// force a positive integer
		$rows = (int) $rows;
		if ($rows < 1) {
			$rows = 1;
		}
		$this->paging = $rows;
	}
	
	
	/**
	* 
	* Makes the query SELECT DISTINCT.
	* 
	* @access public
	* 
	* @param bool $flag Whether or not the SELECT is DISTINCT (default
	* true).
	* 
	* @return void
	* 
	*/

	public function distinct($flag = true)
	{
		$this->parts['distinct'] = (bool) $flag;
	}
	
	
	/**
	* 
	* Add un-mapped columns to the query.
	* 
	* @access public
	* 
	* @param string|array The list of columns.
	* 
	* @return void
	* 
	*/

	public function cols($spec)
	{
		// track them as related to no specific table
		$this->tblCols('', $spec);
	}
	
	
	/**
	* 
	* Adds a FROM table and columns to the query.
	* 
	* @access public
	* 
	* @param string|object $spec If a Solar_Sql_Table object, the table
	* to select from; if a string, the table name to select from.
	* 
	* @param array|string $cols The columns to select from this table.
	* 
	* @return void
	* 
	*/

	public function from($spec, $cols = null)
	{
		// the $spec may be a table object, or a string.
		if ($spec instanceof Solar_Sql_Table) {
			
			// get the table name
			$name = $spec->name;
			
			// add all columns?
			if ($cols == '*') {
				$cols = array_keys($spec->col);
			}
			
		} else {
			$name = $spec;
		}
		
		// add the table to the 'from' list
		$this->parts['from'] = array_merge(
			$this->parts['from'],
			(array) $name
		);
		
		// add to the columns from this table
		$this->tblCols($name, $cols);
	}
	
	
	/**
	* 
	* Adds a JOIN table and columns to the query.
	* 
	* @access public
	* 
	* @param string|object $spec If a Solar_Sql_Table object, the table
	* to join to; if a string, the table name to join to.
	* 
	* @param string $cond Join on this condition.
	* 
	* @param array|string $cols The columns to select from the joined table.
	* 
	* @return void
	* 
	*/

	public function join($spec, $cond, $cols = null)
	{
		// the $spec may be a table object, or a string.
		if ($spec instanceof Solar_Sql_Table) {
			
			// get the table name
			$name = $spec->name;
			
			// add all columns?
			if ($cols == '*') {
				$cols = array_keys($spec->col);
			}
			
		} else {
			$name = $spec;
		}
		
		$this->parts['join'][] = array(
			'type' => null,
			'name' => $name,
			'cond' => $cond
		);
		
		// add to the columns from this joined table
		$this->tblCols($name, $cols);
	}
	
	
	/**
	* 
	* Adds a WHERE condition to the query by AND.
	* 
	* If a value is passed as the second param, it will be quoted
	* and replaced into the condition wherever a question-mark
	* appears.
	* 
	* Array values are quoted and comma-separated.
	* 
	* <code>
	* // simplest but non-secure
	* $select->where("id = $id");
	* 
	* // secure
	* $select->where('id = ?', $id);
	* 
	* // equivalent security with named binding
	* $select->where('id = :id');
	* $select->bind('id', $id);
	* </code>
	* 
	* @access public
	* 
	* @param string $cond The WHERE condition.
	* 
	* @param string $val A single value to quote into the condition.
	* 
	* @return void
	* 
	*/

	public function where($cond)
	{
		if (empty($cond)) {
			return;
		}
		
		if (func_num_args() > 1) {
			$cond = $this->quoteInto($cond, func_get_arg(1));
		}
		
		if ($this->parts['where']) {
			$this->parts['where'][] = "AND $cond";
		} else {
			$this->parts['where'][] = $cond;
		}
	}
	
	
	/**
	* 
	* Adds a WHERE condition to the query by OR.
	* 
	* Otherwise identical to where().
	* 
	* @access public
	* 
	* @param string $cond The WHERE condition.
	* 
	* @param string $val A value to quote into the condition.
	* 
	* @return void
	* 
	* @see where()
	* 
	*/

	public function orWhere($cond)
	{
		if (empty($cond)) {
			return;
		}
		
		if (func_num_args() > 1) {
			$cond = $this->quoteInto($cond, func_get_arg(1));
		}
		
		if ($this->parts['where']) {
			$this->parts['where'][] = "OR $cond";
		} else {
			$this->parts['where'][] = $cond;
		}
	}
	
	
	/**
	* 
	* Adds multiple WHERE conditions to the query.
	* 
	* Otherwise identical to where()/orWhere().
	* 
	* @access public
	* 
	* @param array $list An array of WHERE conditions.
	* 
	* @param string $op How to add the conditions, by 'AND' (the
	* default) or by 'OR'.
	* 
	* @return void
	* 
	* @see where()
	* 
	* @see orWhere()
	* 
	*/

	public function multiWhere($list, $op = 'AND')
	{
		// normally use where() ...
		$method = 'where';
		if ($op == 'OR') {
			// unless it's orWhere().
			$method = 'orWhere';
		}
		
		// add each condition.
		foreach ((array) $list as $key => $val) {
			if (is_int($key)) {
				// integer key means a literal condition
				// and no value to be quoted into it
				$this->$method($val);
			} else {
				// string $key means the key is a condition,
				// and the $val should be quoted into it.
				$this->$method($key, $val);
			}
		}
	}
	
	
	/**
	* 
	* Adds grouping to the query.
	* 
	* @access public
	* 
	* @param string|array $spec The column(s) to group by.
	* 
	* @return void
	* 
	*/

	public function group($spec)
	{
		if (empty($spec)) {
			return;
		}
		
		if (is_string($spec)) {
			$spec = explode(',', $spec);
		} else {
			settype($spec, 'array');
		}
		
		$this->parts['group'] = array_merge($this->parts['group'], $spec);
	}
	
	
	/**
	* 
	* Adds a HAVING condition to the query by AND.
	* 
	* If a value is passed as the second param, it will be quoted
	* and replaced into the condition wherever a question-mark
	* appears.
	* 
	* Array values are quoted and comma-separated.
	* 
	* <code>
	* // simplest but non-secure
	* $select->having("COUNT(id) = $count");
	* 
	* // secure
	* $select->having('COUNT(id) = ?', $count);
	* 
	* // equivalent security with named binding
	* $select->having('COUNT(id) = :count');
	* $select->bind('count', $count);
	* </code>
	* 
	* @access public
	* 
	* @param string $cond The HAVING condition.
	* 
	* @param string $val A single value to quote into the condition.
	* 
	* @return void
	* 
	*/

	public function having($cond)
	{
		if (empty($cond)) {
			return;
		}
		
		if (func_num_args() > 1) {
			$cond = $this->quoteInto($cond, func_get_arg(1));
		}
		
		if ($this->parts['having']) {
			$this->parts['having'][] = "AND $cond";
		} else {
			$this->parts['having'][] = $cond;
		}
	}
	
	
	/**
	* 
	* Adds a HAVING condition to the query by OR.
	* 
	* Otherwise identical to orHaving().
	* 
	* @access public
	* 
	* @param string $cond The HAVING condition.
	* 
	* @param string $val A single value to quote into the condition.
	* 
	* @return void
	* 
	* @see having()
	* 
	*/

	public function orHaving($cond)
	{
		if (empty($cond)) {
			return;
		}
		
		if (func_num_args() > 1) {
			$cond = $this->quoteInto($cond, func_get_arg(1));
		}
		
		if ($this->parts['having']) {
			$this->parts['having'][] = "OR $cond";
		} else {
			$this->parts['having'][] = $cond;
		}
	}
	
	
	/**
	* 
	* Adds multiple HAVING conditions to the query.
	* 
	* Otherwise identical to having()/orHaving().
	* 
	* @access public
	* 
	* @param array $list An array of HAVING conditions.
	* 
	* @param string $op How to add the conditions, by 'AND' (the
	* default) or by 'OR'.
	* 
	* @return void
	* 
	* @see having()
	* 
	* @see orHaving()
	* 
	*/
	
	public function multiHaving($list, $op = 'AND')
	{
		$method = 'having';
		if (strtoupper($op) == 'OR') {
			$method = 'orHaving';
		}
		
		foreach ((array) $list as $key => $val) {
			if (is_int($key)) {
				// integer key means a literal condition
				// and no value to be quoted into it
				$this->$method($val);
			} else {
				// string $key means the key is a condition,
				// and the $val should be quoted into it.
				$this->$method($key, $val);
			}
		}
	}
	
	
	/**
	* 
	* Adds a row order to the query.
	* 
	* @access public
	* 
	* @param string|array $spec The column(s) and direction to order by.
	* 
	* @return void
	* 
	*/

	public function order($spec)
	{
		if (empty($spec)) {
			return;
		}
		
		if (is_string($spec)) {
			$spec = explode(',', $spec);
		} else {
			settype($spec, 'array');
		}
		
		// force 'ASC' or 'DESC' on each order spec, default is ASC.
		foreach ($spec as $key => $val) {
			$asc  = (strtoupper(substr($val, -4)) == ' ASC');
			$desc = (strtoupper(substr($val, -5)) == ' DESC');
			if (! $asc && ! $desc) {
				$spec[$key] .= ' ASC';
			}
		}
		
		// merge them into the current order set
		$this->parts['order'] = array_merge($this->parts['order'], $spec);
	}
	
	
	/**
	* 
	* Sets a limit count and offset to the query.
	* 
	* @access public
	* 
	* @param int $count The number of rows to return.
	* 
	* @param int $offset Start returning after this many rows.
	* 
	* @return void
	* 
	*/

	public function limit($count = null, $offset = null)
	{
		$this->parts['limit']['count']  = (int) $count;
		$this->parts['limit']['offset'] = (int) $offset;
	}
	
	
	/**
	* 
	* Sets the limit and count by page number.
	* 
	* @access public
	* 
	* @param int $page Limit results to this page number.
	* 
	* @return void
	* 
	*/

	public function limitPage($page = null)
	{
		// reset the count and offset
		$this->parts['limit']['count']  = 0;
		$this->parts['limit']['offset'] = 0;
		
		// determine the count and offset from the page number
		$page = (int) $page;
		if ($page > 0) {
			$this->parts['limit']['count']  = $this->paging;
			$this->parts['limit']['offset'] = $this->paging * ($page - 1);
		}
	}
	
	
	/**
	* 
	* Clears query properties.
	* 
	* @access public
	* 
	* @param string $key The property to clear; if empty, clears all
	* query properties.
	* 
	* @return void
	* 
	*/

	public function clear($key = null)
	{
		$list = array_keys($this->parts);
		
		if (empty($key)) {
			// clear all
			foreach ($list as $key) {
				$this->parts[$key] = array();
			}
		} elseif (in_array($key, $list)) {
			// clear some
			$this->parts[$key] = array();
		}
		
		// make sure limit has a count and offset
		if (empty($this->parts['limit'])) {
			$this->parts['limit'] = array(
				'count' => 0,
				'offset' => 0
			);
		}
	}
	
	
	/**
	* 
	* Adds data to bind into the query.
	* 
	* @access public
	* 
	* @param mixed $key The replacement key in the query.  If this is an
	* array or object, the $val parameter is ignored, and all the
	* key-value pairs in the array (or all properties of the object) are
	* added to the bind.
	* 
	* @param mixed $val The value to use for the replacement key.
	* 
	* @return void
	* 
	*/

	public function bind($key, $val = null)
	{
		if (is_array($key)) {
			$this->bind = array_merge($this->bind, $key);
		} elseif (is_object($key)) {
			$this->bind = array_merge((array) $this->bind, $key);
		} else {
			$this->bind[$key] = $val;
		}
	}
	
	
	/**
	* 
	* Unsets bound data.
	* 
	* @access public
	* 
	* @param mixed $spec The key to unset.  If a string, unsets that one
	* bound value; if an array, unsets the list of values; if empty, unsets
	* all bound values (the default).
	* 
	* @return void
	* 
	*/

	public function unbind($spec = null)
	{
		if (empty($spec)) {
			$this->bind = array();
		} else {
			settype($spec, 'array');
			foreach ($spec as $key) {
				unset($this->bind[$key]);
			}
		}
	}
	
	
	/**
	* 
	* Fetch the results based on the current query properties.
	* 
	* @access public
	* 
	* @param string $type The type of fetch to perform (all, one, row, etc).
	* 
	* @return mixed The query results.
	* 
	*/

	public function fetch($type = 'result')
	{
		// build up the $parts['cols'] from scratch.
		$this->parts['cols'] = array();
		
		// how many tables/joins to select from?
		$count = count(array_keys($this->tbl_cols));
		
		// add table/join column names with deconfliction
		foreach ($this->tbl_cols as $tbl => $cols) {
			
			// set up a table/join prefix.
			// is the table/join aliased?
			$pos = stripos($tbl, ' AS ');
			if ($pos) {
				// yes, use the alias portion as the prefix
				$pre = trim(substr($tbl, $pos + 4));
			} else {
				// no, just use the table/join name as the prefix.
				$pre = trim($tbl);
			}
			
			// add each of the columns, deconflicting as we go
			foreach ($cols as $col) {
				// is the column aliased?
				$pos = stripos($col, ' AS ');
				if ($pos || $pre == '') {
					// use the column as aliased
					$this->parts['cols'][] = $col;
				} elseif ($count == 1) {
					// only one table with columns: minimal deconfliction
					$this->parts['cols'][] = "{$pre}.$col AS $col";
				} else {
					// more than one table: full deconfliction
					$this->parts['cols'][] = "{$pre}.$col AS {$pre}__$col";
				}
			}
		}
		
		// perform the select query and return the results
		return $this->sql->select($type, $this->parts, $this->bind);
	}
	
	
	/**
	* 
	* Get the count of rows and number of pages for the current query.
	* 
	* @access public
	* 
	* @param string $col The column to COUNT() on.  Default is 'id'.
	* 
	* @return array An associative array with keys 'count' (the total number
	* of rows) and 'pages' (the number of pages based on $this->paging).
	* 
	*/

	public function countPages($col = 'id')
	{
		// make a self-cloned copy so that all settings are identical
		$select = clone($this);
		
		// clear out all columns (note that this works because we are
		// already in a Select class; this wouldn't work externally
		// because $cols is protected) ...
		$select->tbl_cols = array();
		
		// ... then add a single COUNT column (no need for a table name
		// in this case)
		$select->cols("COUNT($col)");
		
		// clear any limits
		$select->clear('limit');
		
		// select the count of rows and free the cloned copy
		$result = $select->fetch('one');
		unset($select);
		
		// was there an error?
		if (Solar::isError($result)) {
			return $result;
		}
		
		// $result is the row-count; how many pages does it convert to?
		$pages = 0;
		if ($result > 0) {
			$pages = ceil($result / $this->paging);
		}
		
		// done!
		return array(
			'count' => $result,
			'pages' => $pages
		);
	}
	
	
	/**
	* 
	* Adds to the internal table-to-column mapping array.
	* 
	* @access protected
	* 
	* @param string $tbl The table/join the columns come from.
	* 
	* @param string|array $cols The list of columns; preferably as
	* an array, but possibly as a comma-separated string.
	* 
	* @return void
	* 
	*/

	protected function tblCols($tbl, $cols)
	{
		if (is_string($cols)) {
			$cols = explode(',', $cols);
		} else {
			settype($cols, 'array');
		}
		
		// only add columns if non-blank
		if (! empty($cols)) {
			
			// trim everything up ...
			array_walk($cols, 'trim');
			
			// ... and merge them into the tbl_cols mapping.
			if (empty($this->tbl_cols[$tbl])) {
				// this table/join not previously used
				$this->tbl_cols[$tbl] = $cols;
			} else {
				// merge with existing columns for this table/join
				$this->tbl_cols[$tbl] = array_merge(
					$this->tbl_cols[$tbl],
					$cols
				);
			}
		}
	}
}
?>