<?php
/**
 * Parent test.
 */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'Adapter.php';

/**
 * 
 * Adapter class test.
 * 
 */
class Test_Solar_Auth_Adapter_Sql extends Test_Solar_Auth_Adapter {
    
    /**
     * 
     * Configuration values.
     * 
     * @var array
     * 
     */
    protected $_Test_Solar_Auth_Adapter_Sql = array(
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
        $this->todo('need adapter-specific config');
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
        $obj = Solar::factory('Solar_Auth_Adapter_Sql');
        $this->assertInstance($obj, 'Solar_Auth_Adapter_Sql');
    }
    
    /**
     * 
     * Test -- Retrieves a "read-once" session value for Solar_Auth.
     * 
     */
    public function testGetFlash()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Tells if the current page load appears to be the result of an attempt to log in.
     * 
     */
    public function testIsLoginRequest()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Tells if the current page load appears to be the result of an attempt to log out.
     * 
     */
    public function testIsLogoutRequest()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Tells whether the current authentication is valid.
     * 
     */
    public function testIsValid()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Processes login attempts and sets user credentials.
     * 
     */
    public function testProcessLogin()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Processes logout attempts.
     * 
     */
    public function testProcessLogout()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Resets any authentication data in the session.
     * 
     */
    public function testReset()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Starts a session with authentication.
     * 
     */
    public function testStart()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Updates idle and expire times, invalidating authentication if they are exceeded.
     * 
     */
    public function testUpdateIdleExpire()
    {
        $this->todo('stub');
    }


}