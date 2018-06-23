<?php

use PHPUnit\Framework\TestCase;
use SimpleRules\SimpleRules;

/**
 * Class - Simple Rules Test
 */
class SimpleRulesTest extends TestCase
{
    /**
     * Setup the static data.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Test - rule test
     *
     * Tests too many of a product, basket overfilled
     *
     * @group simple-rules
     *
     * @return void
     */
    public function testRule0TooManyOf()
    {
        $simpleRules = new SimpleRules;

        $simpleRules->strRules = file_get_contents('./tests/test-rules/rule-0.srule');

        $simpleRules->memory['fruit.orange.count'] = 3;
        $simpleRules->memory['fruit.grapes.count'] = 1;

        $simpleRules->parse();

        $this->assertEquals(count($simpleRules->memory['messages']), 2);

        $this->assertContains(
            'basket overfilled',
            $simpleRules->memory['messages']
        );

        $this->assertContains(
            'too many of fruit.orange',
            $simpleRules->memory['messages']
        );
    }

    /**
     * Test - rule test
     *
     * Tests under-orderd product, basket underfilled
     *
     * @group simple-rules
     *
     * @return void
     */
    public function testRule0UnderOrderedProduct()
    {
        $simpleRules = new SimpleRules;

        $simpleRules->strRules = file_get_contents('tests/test-rules/rule-0.srule');

        $simpleRules->memory['fruit.orange.count'] = 2;
        $simpleRules->memory['fruit.grapes.count'] = 0;

        $simpleRules->parse();

        $this->assertEquals(count($simpleRules->memory['messages']), 2);


        $this->assertContains(
            'basket underfilled',
            $simpleRules->memory['messages']
        );

        $this->assertContains(
            'under ordered fruit.grapes',
            $simpleRules->memory['messages']
        );
    }
}
