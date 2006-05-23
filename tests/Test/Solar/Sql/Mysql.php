<?php

require_once realpath(dirname(__FILE__) . '/../Sql.php');

class Test_Solar_Sql_Mysql extends Test_Solar_Sql {
    
    protected $_config = array(
        'driver' => 'Solar_Sql_Driver_Mysql',
        'name'   => 'test',
        'user'   => null,
        'pass'   => null,
        'host'   => '127.0.0.1',
    );
    
    protected $_quote_expect = "'\\\"foo\\\" bar \\'baz\\''";
    
    protected $_quote_array_expect = "'\\\"foo\\\"', 'bar', '\'baz\''";
    
    protected $_quote_into_expect = "foo = '\'bar\''";
    
    protected $_quote_multi_expect = "id = 1 AND foo = 'bar' AND zim IN('dib', 'gir', 'baz')";
}
?>