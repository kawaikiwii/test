<?php

/**
 * Project:     WCM
 * File:        wcm.nbridge.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * socket_read latency in microseconds
 *
 * The amount of time to sleep between calls to the PHP socket_read
 * function.
 *
 * The PHP socket_read function seems to have issues with network
 * latency. The work-around is to sleep a little between calls.
 *
 */
if (!defined('SOCKET_READ_LATENCY'))
{
    define('SOCKET_READ_LATENCY', 10);
}

/**
 * socket_read chunk size in bytes
 *
 * The maximum number of bytes to read during a call to the PHP
 * socket_read function.
 *
 */
if (!defined('SOCKET_READ_CHUNK_SIZE'))
{
    define('SOCKET_READ_CHUNK_SIZE', 1024);
}

/**
 * Provides a bridge to the Text-Mining Engine Server
 */
class wcmSemanticServer
{
    private static $singleton = null;

    private $socket = null;

    private $host;
    private $port;

    private $lastErrorMsg = null;
    private $lastCommand = null;
    private $lastResult = null;

    /**
     * Retrieves the singleton instance of this class
     *
     * @return wcmSemanticServer The semantic server singleton
     */
    public static function getInstance()
    {
        // Build singleton
        if (!isset(self::$singleton))
        {
            $className = __CLASS__;
            self::$singleton = new $className();
        }

        return self::$singleton;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $config = wcmConfig::getInstance();
        $this->host = $config['tme.server'];
        $this->port = $config['tme.port'];
    }

    /**
     * Returns an array of available methods
     *
     * @string array An assoc array (key is method name, value is '1' is use by default, '0' otherwise
     */
    public function getMethods()
    {
        return array(
            "NConceptExtractor" => 1,
            "NFinder"           => 1,
            "NCategorizer"      => 1,
            "NSummarizer"       => 1,
            "NSentiment"        => 1,
            "NLikeThis_Compare" => 1,
            "NLikeThis_Index"   => 1,
            "NLikeThis_Delete"  => 1
        );
    }

    /**
     * Returns the last error message
     *
     * @return string Last error message (or null)
     */
    public function getErrorMessage()
    {
        return $this->lastErrorMsg;
    }

    /**
     * Returns the last TME command
     *
     * @return string Last TME command in native format (XML)
     */
    public function getLastCommand()
    {
        return $this->lastCommand;
    }

    /**
     * Returns the last TME result
     *
     * @return string Last TME result in native format (XML)
     */
    public function getLastResult()
    {
        return $this->lastResult;
    }
    
    /**
     * Performs an indexing operation for NLikeThis
     *
     * @param bizObject $bizobject The bizobject to mine
     *
     * @return void
     */
    public function indexObject($bizobject)
    {
        $this->mineObject($bizobject, array('NLikeThis_Index'));
    }
    
    /**
     * Performs an de-indexing operation from NLikeThis
     *
     * @param bizObject $bizobject The bizobject to mine
     *
     * @return void
     */
    public function deindexObject($bizobject)
    {
        $this->mineObject($bizobject, array('NLikeThis_Delete'));
    }

    /**
     * Mine a business object and returns the corresponding semantic data
     *
     * @param bizObject $bizobject The bizobject to mine
     * @param array     $methods List of methods to process (or null)
     *
     * @return wcmSemanticData The corresponding semantic data (or null)
     */
    public function mineObject($bizobject, $methods = null)
    {
        if (!$bizobject)
            return null;

        return $this->mineText( $bizobject->getSemanticText(),
                                $bizobject->getClass() . '_' . $bizobject->id,
                                $bizobject->getLanguage(),
                                $methods);
    }


    /**
     * Mine a text and returns the corresponding semantic data
     *
     * @param string $text The text to mine
     * @param string $id A unique id for the text
     * @param string $language The text language
     * @param array     $methods List of methods to process (or null)
     *
     * @return wcmSemanticData The corresponding semantic data (or null)
     */
    public function mineText($text, $id, $language, $methods = null)
    {
        $logger = wcmProject::getInstance()->logger;
        $this->lastErrorMsg = null;

        // Select methods?
        if ($methods == null)
        {
            $methods = array();
            foreach($this->getMethods() as $method => $enabled)
            {
                if ($enabled) $methods[] = $method;
            }
        }

        $dom = new DomDocument;
        $dom->Load(WCM_DIR . '/xml/tme/methods.xml');

        // Build and execute command
        $xmlCommand  = '<?xml version="1.0" encoding="utf-8"?>';
        $xmlCommand .= '<Command>';
        $xmlCommand .= '<TextID>' . $id . '</TextID>';
        if ($text)
        {
            $xmlCommand .= '<Text>' . wcmXML::xmlEncode(getRawText($text)) . '</Text>';
        }

        // Translate language id
        if ($language == 'fr') $languageID = 'FRENCH';
        elseif ($language == 'sp') $languageID = 'SPANISH';
        else $languageID = 'ENGLISH';

        $xmlCommand .= '<LanguageID>' . $languageID . '</LanguageID>';
        $xmlCommand .= '<Methods>';
        foreach ($methods as $method)
        {
            $xmlCommand .= wcmXML::getOuterXml($dom->getElementsByTagName($method)->item(0));
        }
        $xmlCommand .= '</Methods>';
        $xmlCommand .= '</Command>';

        // Substitute special variables
        $config = wcmConfig::getInstance();
        $xmlCommand = str_replace('#PROJECT.GUID#', $config['wcm.project.guid'], $xmlCommand);


        // Generate the corresponding native TME command and result
        try
        {
            $this->lastCommand = wcmXML::processXSLT($xmlCommand, WCM_DIR . '/xml/tme/methods.xsl');
            $this->lastResult = $this->executeQuery($this->lastCommand);
        }
        catch(Exception $e)
        {
            $logger->logError('TME processing failed: ' . $e->getMessage());
            return null;
        }

        // Process result to simplify management of semantic data
        try
        {
             return new wcmSemanticData(wcmXML::processXSLT($this->lastResult, WCM_DIR . '/xml/tme/methods.xsl'));
        }
        catch (Exception $e)
        {
            $logger->logError('TME processing failed: ' . $e->getMessage());
            return null;    
        }
    }

    /**
     * Opens a connection to TME (if needed) a
     * the result, closes the connection, and returns the result.
     *
     * Updates $lastErrorMsg on failure.
     *
     * @param string $query The native TME query to execute
     *
     * @return string The XML result, or null on failure
     */
    public function executeQuery($query)
    {
        try
        {
            $this->lastErrorMsg = null;
            return $this->process($query);
        }
        catch(Exception $e)
        {
            $this->lastErrorMsg = $e->getMessage();
        }
        return null;
    }

    /**
     * Opens connection to TME
     *
     * Updates $lastErrorMsg on failure
     *
     * @return boolean True on success, false on failure
     */
    private function connect()
    {
        // Close previous connection
        if ($this->socket) $this->close();

        $this->socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($this->socket === false)
        {
            $this->saveSocketError('Could not create socket (', ')');
            $this->socket = null;
            return false;
        }

        if (@socket_connect($this->socket, $this->host, $this->port) === false)
        {
            $this->saveSocketError('Could not connect socket to '.$this->host.':'.$this->port.' (', ')');
            $this->socket = null;
            return false;
        }

        return true;
    }

    /**
     * Closes connection to TME
     */
    private function close()
    {
        // Safe disconnection
        if ($this->socket)
        {
            @socket_close($this->socket);
        }
        $this->socket = null;
    }

    /**
     * Sends data over the connection to NServer.
     *
     * Updates $lastErrorMsg on failure.
     *
     * @param string data The data to send
     *
     * @return boolean True on success, false on failure
     */
    private function send($data)
    {
        // Pack the data length in 'Little Endian' binary format
        $binaryLength = pack("V", mb_strlen($data, 'ISO-8859-1'));

        // Send the binary data length to NServer
        if (@socket_write($this->socket, $binaryLength) === false)
        {
            $this->saveSocketError('Could not write data length to socket (', ')');
            return false;
        }

        // Send the data to NServer
        if (@socket_write($this->socket, $data) === false)
        {
            $this->saveSocketError('Could not write data to socket (', ')');
            return false;
        }

        return true;
    }

    /**
     * Receives data over the connection from NServer.
     *
     * Updates $lastErrorMsg on failure.
     *
     * @return string The received data, or null on failure
     */
    private function receive()
    {
        // Receive the binary data length from NServer
        $binaryLength = socket_read($this->socket, 4, PHP_BINARY_READ);

        // Check for error
        if ($binaryLength === false)
        {
            $this->saveSocketError('Could not read data length from socket (', ')');
            return null;
        }

        // Unpack the binary data length assumed to be in 'Little Endian' binary format
        $unpackedBinaryLength = unpack("V", $binaryLength);
        $dataLength = (int) $unpackedBinaryLength[1];

        // Recover the result as a string
        $result = '';
        $chunk  = '';

        // Sleep a little to work around network latency problems
        while ($dataLength > 0)
        {
            // We will read large responses in chunks
            $bytesToRead = ($dataLength > SOCKET_READ_CHUNK_SIZE
                            ? SOCKET_READ_CHUNK_SIZE
                            : $dataLength);
            // Read chunk
            usleep(SOCKET_READ_LATENCY);
            $chunk = socket_read($this->socket, $bytesToRead, PHP_BINARY_READ);
            if ($chunk === false)
            {
                // Error
                $dataLength = 0;
            }
            else
            {
                $result .= (string) $chunk;
                $dataLength -= $bytesToRead;
            }
        }

        // Check for error
        if ($chunk === false)
        {
            $this->saveSocketError('Could not read data from socket (', ')');
            return null;
        }

        return $result;
    }

    /**
     * Saves the last socket error in $lastErrorMsg, bracketing it
     * with given message prefix and suffix, and then clears the
     * socket error.
     *
     * @param string $msgPrefix The message prefix (default: '')
     * @param string $msgSuffix The message suffix (default: '')
     */
    private function saveSocketError($msgPrefix = '', $msgSuffix)
    {
        $errCode = socket_last_error();
        $errMesg = socket_strerror($errCode);

        $this->lastErrorMsg = $msgPrefix.$errCode.': '.$errMesg.$msgSuffix;
        wcmProject::getInstance()->logger->logError('TME execution failed: ' . $this->lastErrorMsg);
        socket_clear_error();
    }

    /**
     * Process a native query and return a native result
     *
     * @param string $request TME native query
     *
     * @return string TME native result
     */
    public function process($request)
    {
        if (!isset($request) || empty($request))
            return null;

        $socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false)
            throw new Exception(socket_strerror(socket_last_error()));

        try
        {
            $res = @socket_connect($socket, $this->host, $this->port);
            if ($res === false)
              throw new Exception(socket_strerror(socket_last_error()));

            // Header (4 bytes little-endian data length)
            $header = pack("V", strlen($request));

            $res = @socket_write($socket, $header);
            if ($res === false)
              throw new Exception(socket_strerror(socket_last_error()));

            $res = @socket_write($socket, $request);
            if ($res === false)
              throw new Exception(socket_strerror(socket_last_error()));

            // Retrieve response length
            $header = @socket_read($socket, 4);
            if (empty($header))
              throw new Exception("Invalid response from TME.");

            $response = null;
            $a = unpack("V", $header);
            $length = $a[1];
            for ($pos = 0, $size = 0; $length > 0; $pos += $size, $length -= $size)
            {
                $data = @socket_read($socket, $length);
                if (empty($data))
                  throw new Exception("Invalid response from TME.");

                $response .= $data;
                $size = strlen($data);
            }
            @socket_close($socket);

            // Get response encoding
            $doc = new DOMDocument();
            $doc->loadXML($request);
            $node = $doc->getElementsByTagName('ResultEncoding')->item(0);
            $encoding = $node ? $node->nodeValue : 'UTF-8';
            if ($encoding != 'UTF-8')
              $response = iconv($encoding, 'UTF-8', $response);

            return $response;
        }
        catch (Exception $e)
        {
            @socket_close($socket);
            throw $e;
        }
        return null;
    }
}
