<?php

/**
 * Project:     WCM
 * File:        wcmO.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 */

require_once dirname(__FILE__) . '/../wcmWebServiceTestCase.php';

/**
 * WCM Object Management Web Service Test Case
 *
 */
class wcmObjectManagementWebServiceTestCase extends wcmWebServiceTestCase
{
    public function testGetObject()
    {
        try
        {
            $sessionId = $this->soapClient->login('admin', 'admin');

            $obj = $this->soapClient->getObject($sessionId, 'site', 1);
            $this->assert($obj != null);

            $doc = new DOMDocument();
            $doc->loadXML($obj);

            $xpath = new DOMXPath($doc);
            $this->assertEqual('site', $xpath->query('/*')->item(0)->nodeName);
            $this->assertEqual('1', $xpath->query('/site/id')->item(0)->nodeValue);
            
            $this->soapClient->logout($sessionId);
        }
        catch (exception $e)
        {
            $this->fail($e->getMessage());            
        }
    }

    public function testGetObjectInvalidSessionId()
    {
        try
        {
            $this->soapClient->getObject('klkdlkdfkdl', 'site', 1);
            $this->fail('expected exception');
        }
        catch (exception $e)
        {
            $this->assertEqual("Invalid session identifier 'klkdlkdfkdl'.", $e->getMessage());            
        }
    }

    public function testGetObjectInvalidObjectClass()
    {
        try
        {
            $sessionId = $this->soapClient->login('admin', 'admin');

            $this->soapClient->getObject($sessionId, 'foobar', 1);
            $this->fail('expected exception');
            
            $this->soapClient->logout($sessionId);
        }
        catch (exception $e)
        {
            $this->assertEqual("Invalid object class name 'foobar'.", $e->getMessage());            
        }
    }

    public function testGetObjectInvalidObjectId()
    {
        try
        {
            $sessionId = $this->soapClient->login('admin', 'admin');

            $this->soapClient->getObject($sessionId, 'site', 0); 
            $this->fail('expected exception');
            
            $this->soapClient->logout($sessionId);
        }
        catch (exception $e)
        {
            $this->assertEqual("Invalid object identifier 0.", $e->getMessage());            
        }
    }

    public function testCheckoutObject()
    {
        try
        {
            $sessionId = $this->soapClient->login('admin', 'admin');

            $obj = $this->soapClient->checkOutObject($sessionId, 'site', 1);
            $this->assert($obj != null);

            $doc = new DOMDocument();
            $doc->loadXML($obj);

            $xpath = new DOMXPath($doc);
            $this->assertEqual('site', $xpath->query('/*')->item(0)->nodeName);
            $this->assertEqual('1', $xpath->query('/site/id')->item(0)->nodeValue);
            
            $this->soapClient->undoCheckOutObject($sessionId, 'site', 1);
            $this->soapClient->logout($sessionId);
        }
        catch (exception $e)
        {
            $this->fail($e->getMessage());            
        }
    }

    public function testCheckoutObjectAlreadyCheckedOut()
    {
        try
        {
            $sessionId = $this->soapClient->login('admin', 'admin');

            $this->soapClient->checkOutObject($sessionId, 'site', 1);
            $obj = $this->soapClient->checkOutObject($sessionId, 'site', 1);
            
            $this->assert($obj != null);

            $doc = new DOMDocument();
            $doc->loadXML($obj);

            $xpath = new DOMXPath($doc);
            $this->assertEqual('site', $xpath->query('/*')->item(0)->nodeName);
            $this->assertEqual('1', $xpath->query('/site/id')->item(0)->nodeValue);
            
            $this->soapClient->undoCheckOutObject($sessionId, 'site', 1);
            $this->soapClient->logout($sessionId);
        }
        catch (exception $e)
        {
            $this->fail($e->getMessage());            
        }
    }

    public function testCheckoutObjectAlreadyCheckedOutByOther()
    {
        try
        {
            $sessionId = $this->soapClient->login('guest', 'guest');
            $this->soapClient->checkOutObject($sessionId, 'site', 1);
            $this->soapClient->logout($sessionId);

            $sessionId = $this->soapClient->login('admin', 'admin');
            $this->soapClient->checkOutObject($sessionId, 'site', 1);
            $this->fail('expected exception');
            
            $this->soapClient->logout($sessionId);
        }
        catch (exception $e)
        {
            $this->fail($e->getMessage());            
        }
    }

    public function testUndoCheckOutObject()
    {
        try
        {
            $sessionId = $this->soapClient->login('admin', 'admin');

            $obj = $this->soapClient->checkOutObject($sessionId, 'site', 1);
            $this->soapClient->undoCheckOutObject($sessionId, 'site', 1);

            $this->soapClient->logout($sessionId);
        }
        catch (exception $e)
        {
            $this->fail($e->getMessage());            
        }
    }

    public function testUndoCheckOutObjectWithoutCheckOut()
    {
        try
        {
            $sessionId = $this->soapClient->login('admin', 'admin');

            $this->soapClient->undoCheckOutObject($sessionId, 'site', 1);
            $this->fail('expected exception');

            $this->soapClient->logout($sessionId);
        }
        catch (exception $e)
        {
            $this->assertEqual("Undo check out object failed for 'site' object 1.", $e->getMessage());            
        }
    }
    
    public function testDeleteObject()
    {

    }
}

?>