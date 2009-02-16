<?php
/**
 * 
 * Concrete class test.
 * 
 */
class Test_Solar_Sql_Model_Record extends Solar_Test {
    
    /**
     * 
     * Configuration values.
     * 
     * @var array
     * 
     */
    protected $_Test_Solar_Sql_Model_Record = array(
    );
    
    protected $_sql;
    
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
    public function __construct($config = null)
    {
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
        $this->_sql = Solar::factory('Solar_Sql');
        $this->_sql->setProfiling(true);
        
        // populate everything
        $this->_populateAll();
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
    
    protected function _newModel($name)
    {
        $class = "Solar_Example_Model_" . ucfirst($name);
        $model = Solar::factory($class, array('sql' => $this->_sql));
        return $model;
    }
    
    protected function _populateAll()
    {
        $this->_populateUsers();
        $this->_populateAreas();
        $this->_populateNodes();
        $this->_populateMetas();
        $this->_populateTags();
        $this->_populateTaggings();
    }
    
    protected function _populateUsers()
    {
        $users = $this->_newModel('users');
        $handles = array('zim', 'dib', 'gir');
        foreach ($handles as $key => $val) {
            $user = $users->fetchNew();
            $user->handle = $val;
            $user->save();
        }
        
        // recover memory
        $users->free();
        unset($users);
    }
    
    protected function _populateAreas()
    {
        $areas = $this->_newModel('areas');
        $names = array('Irk', 'Earth');
        foreach ($names as $key => $val) {
            $area = $areas->fetchNew();
            $area->user_id = $key + 1;
            $area->name = $val;
            $area->save();
        }   
        
        // recover memory
        $areas->free();
        unset($areas);
    }
    
    protected function _populateNodes()
    {
        // create some nodes, some for area 1 and some for 2,
        // and some for user 1 and some for user 2.
        // five nodes for each area.
        $nodes = $this->_newModel('nodes');
        for ($i = 1; $i <= 10; $i++) {
            $node = $nodes->fetchNew();
            $node->subj = "Subject Line $i: " . substr(md5($i), 0, 5);
            $node->body = "Body for $i ... " . md5($i);
            $node->area_id = $i % 2 + 1; // sometimes 1, sometimes 2
            $node->user_id = ($i + 1) % 2 + 1; // sometimes 2, sometimes 1
            $node->save();
        }
        
        // recover memory
        $nodes->free();
        unset($nodes);
    }
    
    protected function _populateMetas()
    {
        // one meta for each node
        $nodes = $this->_newModel('nodes');
        $metas = $this->_newModel('metas');
        $collection = $nodes->fetchAll();
        foreach ($collection as $node) {
            $meta = $metas->fetchNew();
            $meta->node_id = $node->id;
            $meta->save();
        }
        
        // recover memory
        $nodes->free();
        unset($nodes);
        
        // recover memory
        $metas->free();
        unset($metas);
    }
    
    protected function _populateTags()
    {
        // some generic tags
        $list = array('foo', 'bar', 'baz', 'zab', 'rab', 'oof');
        
        // save them
        $tags = $this->_newModel('tags');
        foreach ($list as $name) {
            $tag = $tags->fetchNew();
            $tag->name = $name;
            $tag->save();
        }
        
        // recover memory
        $tags->free();
        unset($tags);
    }
    
    protected function _populateTaggings()
    {
        $tags = $this->_newModel('tags');
        $nodes = $this->_newModel('nodes');
        $taggings = $this->_newModel('taggings');
        
        $tag_coll = $tags->fetchAll();
        $tag_last = count($tag_coll) - 1;
        
        $node_coll = $nodes->fetchAll();
        
        // add some tags on each node through taggings
        foreach ($node_coll as $node) {
            
            // add 2-5 tags on this node
            $tags_to_add = rand(2,5);
            
            // which tags have we used already?
            $tags_used = array();
            
            // add each of the tags
            for ($i = 0; $i < $tags_to_add; $i ++) {
                
                // pick a random tag that has not been used yet
                do {
                    $tagno = rand(0, $tag_last);
                } while (in_array($tagno, $tags_used));
                
                // mark it as used
                $tags_used[] = $tagno;
                
                // get the tag from the collection
                $tag = $tag_coll[$tagno];
                
                // match the node to the tag with a tagging
                $tagging = $taggings->fetchNew();
                $tagging->node_id = $node->id;
                $tagging->tag_id = $tag->id;
                $tagging->save();
            }
        }
        
        // recover memory
        $tags->free();
        unset($tags);
        
        // recover memory
        $nodes->free();
        unset($nodes);
        
        // recover memory
        $taggings->free();
        unset($taggings);
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
        $obj = Solar::factory('Solar_Sql_Model_Record');
        $this->assertInstance($obj, 'Solar_Sql_Model_Record');
    }
    
    /**
     * 
     * Test -- Magic getter for record properties; automatically calls __getColName() methods when they exist.
     * 
     */
    public function test__get()
    {
        $this->todo('stub');
    }
    
    public function test__get_related_eagerBelongsTo()
    {
        $this->todo('stub');
    }
    
    public function test__get_related_eagerHasMany()
    {
        // the "before" count includes creating the tables and inserting
        // all the records.
        $before = count($this->_sql->getProfile());
        $this->diag("before: $before");
        
        // get areas and nodes
        $areas = $this->_newModel('areas');
        $list = $areas->fetchAll(array(
            'eager' => 'nodes',
        ));
        
        // all fetches should be *done* by now
        $expect = count($this->_sql->getProfile()) - $before;
        $this->diag("expect: $expect");
        
        foreach ($list as $area) {
            foreach ($area->nodes as $node) {
                $this->diag("{$node->id}: {$node->subj}");
                $this->assertTrue($node->subj != '');
            }
        }
        
        // should have been *no more fetches*
        $actual = count($this->_sql->getProfile()) - $before;
        $this->assertSame($actual, $expect);
    }
    
    public function test__get_related_eagerHasManyThrough()
    {
        // the "before" count includes creating the tables and inserting
        // all the records.
        $before = count($this->_sql->getProfile());
        $this->diag("before: $before");
        
        // get nodes and tags
        $nodes = $this->_newModel('nodes');
        $list = $nodes->fetchAll(array(
            'eager' => 'tags',
        ));
        
        // all fetches should be *done* by now
        $expect = count($this->_sql->getProfile()) - $before;
        $this->diag("expect: $expect");
        
        foreach ($list as $node) {
            foreach ($node->tags as $tag) {
                $this->diag("{$node->id}: {$tag->name}");
                $this->assertTrue($tag->name != '');
            }
        }
        
        // should have been *no more fetches*
        $actual = count($this->_sql->getProfile()) - $before;
        $this->assertSame($actual, $expect);
    }
    
    public function test__get_related_eagerHasOne()
    {
        $this->todo('stub');
    }
    
    public function test__get_related_eagerHasMany_empty()
    {
        // get rid of all the nodes
        $nodes = $this->_newModel('nodes');
        $nodes->delete('id > 0');
        
        // the "before" count includes creating the tables, inserting
        // all the records, and deleting the nodes.
        $before = count($this->_sql->getProfile());
        $this->diag("before: $before");
        
        // get areas and nodes
        $areas = $this->_newModel('areas');
        $list = $areas->fetchAll(array(
            'eager' => 'nodes',
        ));
        
        // all fetches should be *done* by now
        $expect = count($this->_sql->getProfile()) - $before;
        $this->diag("expect: $expect");
        
        foreach ($list as $k => $area) {
            foreach ($area->nodes as $node) {
                $this->diag("{$node->id}: {$node->subj}");
                $this->assertTrue($node->subj != '');
            }
        }
        
        // should have been *no more fetches*, even though there were no
        // nodes pulled (because they didn't exist)
        $actual = count($this->_sql->getProfile()) - $before;
        $this->assertSame($actual, $expect);
    }
    
    public function test__get_related_lazyBelongsTo()
    {
        $this->todo('stub');
    }
    
    public function test__get_related_lazyHasMany()
    {
        $this->todo('stub');
    }
    
    public function test__get_related_lazyHasManyThrough()
    {
        $this->todo('stub');
    }
    
    public function test__get_related_lazyHasOne()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Checks if a data key is set.
     * 
     */
    public function test__isset()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Magic setter for record properties; automatically calls __setColName() methods when they exist.
     * 
     */
    public function test__set()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Sets a key in the data to null.
     * 
     */
    public function test__unset()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds a column filter to this record instance.
     * 
     */
    public function testAddFilter()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Countable: how many keys are there?
     * 
     */
    public function testCount()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Iterator: get the current value for the array pointer.
     * 
     */
    public function testCurrent()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Deletes this record from the database.
     * 
     */
    public function testDelete()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Filter the data.
     * 
     */
    public function testFilter()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns a Solar_Form object pre-populated with column properties, values, and filters ready for processing (all based on the model for this record).
     * 
     */
    public function testForm()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Gets a list of all changed table columns.
     * 
     */
    public function testGetChanged()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns the validation failure message for one or more properties.
     * 
     */
    public function testGetInvalid()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns the model from which the data originates.
     * 
     */
    public function testGetModel()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Gets the name of the primary-key column.
     * 
     */
    public function testGetPrimaryCol()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Gets the value of the primary-key column.
     * 
     */
    public function testGetPrimaryVal()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns the exception (if any) generated by the most-recent call to the save() method.
     * 
     */
    public function testGetSaveException()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Returns the status of this record.
     * 
     */
    public function testGetStatus()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Increments the value of a column **immediately at the database** and retains the incremented value in the record.
     * 
     */
    public function testIncrement()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Tells if a particular table-column has changed.
     * 
     */
    public function testIsChanged()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Iterator: get the current key for the array pointer.
     * 
     */
    public function testKey()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Loads the struct with data from an array or another struct.
     * 
     */
    public function testLoad()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Iterator: move to the next position.
     * 
     */
    public function testNext()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- ArrayAccess: does the requested key exist?
     * 
     */
    public function testOffsetExists()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- ArrayAccess: get a key value.
     * 
     */
    public function testOffsetGet()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- ArrayAccess: set a key value.
     * 
     */
    public function testOffsetSet()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- ArrayAccess: unset a key.
     * 
     */
    public function testOffsetUnset()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Refreshes data for this record from the database.
     * 
     */
    public function testRefresh()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Iterator: move to the first position.
     * 
     */
    public function testRewind()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Saves this record and all related records to the database, inserting or updating as needed.
     * 
     */
    public function testSave()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Perform a save() within a transaction, with automatic commit and rollback.
     * 
     */
    public function testSaveInTransaction()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Forces one property to be "invalid" and sets a validation failure message for it.
     * 
     */
    public function testSetInvalid()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Forces multiple properties to be "invalid" and sets validation failure message for them.
     * 
     */
    public function testSetInvalids()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Injects the model from which the data originates.
     * 
     */
    public function testSetModel()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Forces the status of this record.
     * 
     */
    public function testSetStatus()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Converts the properties of this model Record or Collection to an array, including related models stored in properties.
     * 
     */
    public function testToArray()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Iterator: is the current position valid?
     * 
     */
    public function testValid()
    {
        $this->todo('stub');
    }
}
