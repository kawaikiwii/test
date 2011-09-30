<?php

/**
 * Project:     WCM
 * File:        testSuite.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

require_once dirname(__FILE__).'/testCase.php';

/**
 * Test Suite
 * 
 * A test suite consists of one or more test cases in a given directory.
 */
class testSuite
{
    /**
     * Runs the test cases in a given directory.
     * 
     * A test case file name is assumed to end in "TestCase.php".
     *
     * @param string $testCaseDirname The directory name
     */
    public static function run($testCaseDirname)
    {
        $testSuite = new testSuite($testCaseDirname);

        $testCaseFilenames = glob($testCaseDirname.'/*TestCase.php');
        if ($testCaseFilenames)
        {
            foreach ($testCaseFilenames as $testCaseFilename)
            {
                $testCaseClassName = basename($testCaseFilename, '.php');
                $testSuite->addTestCase($testCaseClassName);
            }
        }

        $testSuite->runTestCases();
    }

    /**
     * Test case directory name.
     *
     * @var string
     */
    private $testCaseDirname;
    
    /**
     * Names of test cases to run
     *
     * @var string[]
     */
    private $testCaseNames = array();

    /**
     * Constructs a new instance, initializing the test case directory
     * name.
     * 
     * If a directory name is not given, uses the same directory as this
     * file.
     *
     * @param string $testCaseDirname The directory name
     */
    public function __construct($testCaseDirname = null)
    {
        if (!$testCaseDirname)
        {
            $testCaseDirname = dirname(__FILE__);
        }
        $this->testCaseDirname = $testCaseDirname;
    }

    /**
     * Adds the name of a test case to the list of test case names
     *
     * @param string $testCase The test case name to add
     */
    public function addTestCase($testCaseName)
    {
        array_push($this->testCaseNames, $testCaseName);
    }

    /**
     * Runs the test cases in this suite.
     */
    public function runTestCases()
    {
        echo '--- running test suite in '.$this->testCaseDirname.'...<br/>';

        if ($this->testCaseNames)
        {
            foreach ($this->testCaseNames as $testCaseName)
            {
                testCase::run($testCaseName, $this->testCaseDirname);
            }
        }
        else
        {
            echo '------- no test cases to run<br/>';
        }

        echo '--- finished running test suite<br/><br/>';
    }
}

?>