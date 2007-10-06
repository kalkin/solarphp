<?php
/**
 * 
 * Abstract class test.
 * 
 */
class Test_Solar_Access_Adapter extends Solar_Test {
    
    /**
     * 
     * Configuration values.
     * 
     * @var array
     * 
     */
    protected $_Test_Solar_Access_Adapter = array(
    );
    
    // -----------------------------------------------------------------
    // 
    // Support methods.
    // 
    // -----------------------------------------------------------------
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config User-defined configuration parameters.
     * 
     */
    public function __construct($config)
    {
        $this->skip('abstract class');
        parent::__construct($config);
    }
    
    /**
     * 
     * Destructor; runs after all methods are complete.
     * 
     * @param array $config User-defined configuration parameters.
     * 
     */
    public function __destruct()
    {
        parent::__destruct();
    }
    
    /**
     * 
     * Setup; runs before each test method.
     * 
     */
    public function setup()
    {
        parent::setup();
    }
    
    /**
     * 
     * Setup; runs after each test method.
     * 
     */
    public function teardown()
    {
        parent::teardown();
    }
    
    // -----------------------------------------------------------------
    // 
    // Test methods.
    // 
    // -----------------------------------------------------------------
    
    /**
     * 
     * Test -- Constructor.
     * 
     */
    public function test__construct()
    {
        $obj = Solar::factory('Solar_Access_Adapter');
        $this->assertInstance($obj, 'Solar_Access_Adapter');
    }
    
    /**
     * 
     * Test -- Fetch access privileges for a user handle and roles.
     * 
     */
    public function testFetch()
    {
        $this->skip('abstract method');
    }
    
    /**
     * 
     * Test -- Tells whether or not to allow access to a class/action/process combination.
     * 
     */
    public function testIsAllowed()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Fetches the access list from the adapter into $this->list.
     * 
     */
    public function testLoad()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Resets the current access controls to a blank array.
     * 
     */
    public function testReset()
    {
        $this->todo('stub');
    }


}