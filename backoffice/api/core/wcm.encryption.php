<?php

/**
 * Project:     WCM
 * File:        wcm.encryption.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

/**
 * Provides encryption/decryption functions using the mcrypt PHP
 * extension module.
 */
class wcmEncryption
{
    /**
     * The singleton instance.
     *
     * @var wcmEncryption
     */
    private static $instance = null;

    /**
     * Gets the singleton instance.
     *
     * @return wcmEncryption The singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new wcmEncryption('ICM.Is.S0.Good!!', '1/07/05?', 'rc2', 'cfb');
        }

        return self::$instance;
    }

    /**
     * The mcrypt module handle.
     *
     * @var resource
     */
    private $mcryptModule;

    /**
     * The cipher key.
     *
     * @var string
     */
    private $key;

    /**
     * The initialization vector.
     *
     * @var string
     */
    private $iv;

    /**
     * The encryption/decryption algorithm.
     *
     * @var string
     */
    private $algorithm;

    /**
     * The mode of operation.
     *
     * @var string
     */
    private $mode;

    /**
     * Constructs an instance given a cipher key and, optionally, an
     * initialization vector, an encryption/decryption algorithm, and
     * a mode of operation.
     *
     * @param string $key       The cipher key
     * @param string $iv        The initialization vector (default is null)
     * @param string $algorithm The encryption/decryption algorithm (default is 'rc2')
     * @param string $mode      The algorithm mode (default is 'ecb')'
     */
    public function __construct($key, $iv = null, $algorithm = 'rc2', $mode = 'ecb')
    {
        $this->key = $key;
        $this->iv = $iv;
        $this->algorithm = $algorithm;
        $this->mode = $mode;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if ($this->mcryptModule)
        {
            // Deinitialize the mcrypt module
            mcrypt_generic_deinit($this->mcryptModule);

            // Close the mcrypt module
            mcrypt_module_close($this->mcryptModule);
        }
    }

    /**
     * Gets the mcrypt module handle.
     *
     * Initializes the mcrypt module if necessary.
     *
     * @return resource The mcrypt module handle, or null on failure
     */
    protected function getMCryptModuleHandle()
    {
        if (!$this->mcryptModule)
        {
            // We need an initialization vector unless the mode of operation is 'ecb'
            if ($this->iv === null && $this->mode != 'ecb')
            {
                return null;
            }

            // Open the mcrypt module for the given algorithm and mode of operation
            $mcryptModule = mcrypt_module_open($this->algorithm, '', $this->mode, '');
            if ($mcryptModule === false)
            {
                return null;
            }

            // Get the cipher key
            $keySize = mcrypt_enc_get_key_size($mcryptModule);
            $this->key = substr($this->key, 0, $keySize);

            // Get the initialization vector
            $ivSize = mcrypt_enc_get_iv_size($mcryptModule);
            if ($this->iv === null)
            {
                // From a random seed - on Windows, use MCRYPT_RAND instead of MCRYPT_DEV_RANDOM
                $this->iv = mcrypt_create_iv($ivSize, strstr(PHP_OS, "WIN") ? MCRYPT_RAND : MCRYPT_DEV_RANDOM);
            }
            else
            {
                // From the given one
                $this->iv = substr($this->iv, 0, $ivSize);
            }

            // Save mcrypt module handle
            $this->mcryptModule = $mcryptModule;
        }

        // Initialize mcrypt for the next call to mcrypt_generic or mdecrypt_generic
        $status = mcrypt_generic_init($this->mcryptModule, $this->key, $this->iv);
        if ($status < 0 || $status === false)
        {
            return null;
        }

        return $this->mcryptModule;
    }

    /**
     * Encrypts a given string.
     *
     * @param string $string The string to encrypt
     *
     * @return string The encrypted string
     */
    public function encrypt($string)
    {
        // Encrypt the string
        $string = mcrypt_generic($this->getMCryptModuleHandle(), $string);

        // Base-64 encode and return the string
        return base64_encode($string);
    }

    /**
     * Decrypts a given string.
     *
     * @param string $string The string to decrypt
     *
     * @return string The decrypted string, or null on failure
     */
    public function decrypt($string)
    {
        // Base-64 decode the string
        $string = base64_decode($string);
        if ($string === false)
        {
            return null; // string not valid base-64 data
        }

        // Decrypt the string
        $string = mdecrypt_generic($this->getMCryptModuleHandle(), $string);

        // Trim and return the decrypted string
        return trim($string);
    }
}

?>