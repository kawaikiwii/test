<?php

/**
 * Project:     WCM
 * File:        testCase.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

/**
 * Test Failure Exception
 * 
 * Thrown when a test fails.
 */
class testFailureException extends exception
{
    /**
     * Constructs a new instance with an optional message.
     *
     * @param string $message The optional message
     */
    public function __construct($message = null)
    {
        parent::__construct($message, 0);
    }
}

/**
 * A test case à la JUnit.
 */
abstract class testCase
{
    /**
     * Runs a test case given its class name.
     *
     * @param string $testCaseClassName The class name of the test case to run
     */
    public static function run($testCaseClassName, $testCaseDirname = null)
    {
        if (!$testCaseDirname)
        {
            $testCaseDirname = dirname(__FILE__);
        }
        require_once $testCaseDirname.'/'.$testCaseClassName.'.php';

        $testCase = new $testCaseClassName();
        $testCase->runTests();        
    }

    /**
     * Runs the tests of this test case, ie. all methods with names
     * starting with "test".
     */
    private function runTests()
    {
        $prefix = 'test';
        $prefixLen = strlen($prefix);

        $tests = array();
        foreach (get_class_methods($this) as $method)
        {
            if (substr($method, 0, $prefixLen) == $prefix)
            {
                array_push($tests, substr($method, $prefixLen));
            }
        }

        echo '------- running test case '.get_class($this).'...<br/>';

        if ($tests)
        {
            foreach ($tests as $test)
            {
                $this->runTest($test);
            }
        }
        else
        {
            echo '----------- no tests to run<br/>';
        }

        echo '------- finished running test case<br/><br/>';
    }

    /**
     * Runs a given test, ie. the method with name "test$testName".
     *
     * @param string $testName The name of the test to run
     */
    private function runTest($testName)
    {
        $ranTearDown = false;
        try
        {
            echo '----------- running test '.$testName.'...<br/>';

            $methodName = 'test'.$testName;
            $this->setup($testName);
            $this->$methodName();    
            $this->tearDown($testName);

            $ranTearDown = true;

            echo 'SUCCESS';
        }
        catch (Exception $e)
        {
            try
            {
                if (!$ranTearDown)
                {
                    $this->tearDown($testName);
                }
            }
            catch (Exception $e) { }

            echo 'FAILURE in '.$e->getFile().':'.$e->getLine().'<br/>';
            echo $e->getMessage();
        }
        echo '<br/><br/>';
    }

    /**
     * Called before running a test.
     *
     * @param string $testName The name of the test
     */
    protected function setup($testName)
    {
    }

    /**
     * Called after running a test.
     *
     * @param string $testName The name of the test
     */
    protected function tearDown($testName)
    {
    }

    /**
     * Fails the current test with an optional message, ie. throws a test
     * failure exception.
     *
     * @param string $message The optional message
     */
    protected function fail($message = null)
    {
        throw new testFailureException($message);
    }

    /**
     * Asserts that a boolean value is "true".
     * 
     * If the assertion fails, fails the current test with fail().
     *
     * @param bool $boolValue The boolean value
     */
    protected function assert($boolValue)
    {
        if (!$boolValue)
        {
            $this->fail($boolValue.' is not true');
        }
    }

    /**
     * Asserts that an actual value is equal (==) to an expected value.
     * 
     * If the assertion fails, fails the current test with fail().
     *
     * @param mixed $expectedValue The expected value
     * @param mixed $actualValue   The actual value
     */
    protected function assertEqual($expectedValue, $actualValue)
    {
        if ($expectedValue != $actualValue)
        {
            $this->fail($actualValue.' != '.$expectedValue);
        }
    }

    /**
     * Asserts that an actual value is strictly equal (===)
     * to an expected value.
     * 
     * If the assertion fails, fails the current test with fail().
     *
     * @param mixed $expectedValue The expected value
     * @param mixed $actualValue   The actual value
     */
    protected function assertStrictlyEqual($expectedValue, $actualValue)
    {
        if ($expectedValue !== $actualValue)
        {
            $this->fail($actualValue.' !== '.$expectedValue);
        }
    }
}

?>