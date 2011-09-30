<?php
/**
 * Project:  WCM
 * File:        wcm.openId.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version  4.x
 *
 */

/**
 * Some dependant library use Auth/... to require some other libraries
 * that's why we need to add the Auth path into the include_path.
 */
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.dirname(dirname(dirname(__FILE__))).'/includes/php-openid/');

define('Auth_OpenID_RAND_SOURCE', __FILE__);

require_once('Auth/OpenID/Consumer.php');
require_once('Auth/OpenID/FileStore.php');
require_once('Auth/OpenID/SReg.php');
require_once('Auth/OpenID/PAPE.php');

/**
 * Class used to manage OpenID authentcation.
 */
class wcmOpenId
{
    /**
     * OpenId consumer object
     */
    private $consumer;

    /**
     * Initialise the class with an OpenId consumer object.
     */
    public function __construct()
    {
        $this->consumer = $this->getConsumer();
    }

    /**
     *  This method authenticate the user using a login/password using LDAP
     *
     *  @param String $openIdUrl    The openID url to connect to
     *
     *  @return array               false on failed
     */
    public function login($openIdUrl, $action)
    {
        if ($action == 'completed')
            return $this->analyseResponse();
        else
            return $this->authenticate($openIdUrl);
    }

    /**
     * Analysing the openId authentication response
     *
     * @return array        [0] boolean (true success, false error) / [1] array of user informations or error string
     */
    private function analyseResponse()
    {
        // OpenID authentication is completed, let's analyse the response
        $response = $this->consumer->complete($this->getReturnTo());

        switch ($response->status)
        {
            case Auth_OpenID_CANCEL:
                // This means the authentication was cancelled.
                $error = 'Verification cancelled.';
                break;
            case Auth_OpenID_FAILURE:
                // Authentication failed; display the error message.
                $error = 'OpenID authentication failed: ' . $response->message;
                break;
            case Auth_OpenID_SUCCESS:
                // This means the authentication succeeded; extract the
                // identity URL and Simple Registration data (if it was
                // returned).
                $identity = $response->getDisplayIdentifier();

                $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
                $info = $sreg_resp->contents();
                return array(true, array('username' => $info['nickname'], 'name' => @$info['fullname'], 'email' => @$info['email']));
                break;
            default:
                $error = 'OpenID Unknown response status';
                break;
        }

        return array(false, $error);
    }

    /**
     * Begin the OpenID authentication process.
     *
     * @return array/print html     return an array on error / display an html form (auto submit with javascript) on success.
     */
    private function authenticate($openIdUrl)
    {
        // Begin the OpenID authentication process.
        $auth_request = $this->consumer->begin($openIdUrl);

        // No auth request means we can't begin OpenID.
        if (!$auth_request)
            return array(false, 'Unable to authenticate on OpenID server: '.$openIdUrl);

        // nickname array is required.
        // fullname and email array is optional.
        $sreg_request = Auth_OpenID_SRegRequest::build(array('nickname'), array('fullname', 'email'));
        if ($sreg_request)
            $auth_request->addExtension($sreg_request);

        // Redirect the user to the OpenID server for authentication.
        // Store the token for this authentication so we can verify the
        // response.

        // For OpenID 2, use a Javascript form to send a POST request to the server.
        // Generate form markup and render it.
        $form_id = 'openid_message';
        $form_html = $auth_request->htmlMarkup($this->getTrustRoot(), $this->getReturnTo(), false, array('id' => $form_id));

        // Display an error if the form markup couldn't be generated;
        // otherwise, render the HTML.
        if (Auth_OpenID::isFailure($form_html))
            return array(false, 'OpenID call Can not be done.');
        else
            print $form_html;
    }

    /**
     * Create a consumer object using the store object created earlier.
     *
     * @return object       Auth_OpenID_Consumer
     */
    public function getConsumer()
    {
        $store = $this->getStore();
        $consumer = new Auth_OpenID_Consumer($store);
        return $consumer;
    }

    /**
     * The way we store the OpenID information.
     * actually it's store into a files, this can be change to use sql.
     *
     * @return object       Auth_OpenID_FileStore
     */
    private function getStore()
    {
        /**
         * This is where OpenID information will be store.
         * You can change this path if you want to store information elsewhere.
         */
        $config = wcmConfig::getInstance();
        $store_path = $config['wcm.cache.path'] . 'openId/';

        if (!file_exists($store_path) && !mkdir($store_path))
        {
            print "Could not create the FileStore directory '$store_path'. "." Please check the effective permissions.";
            exit(0);
        }

        return new Auth_OpenID_FileStore($store_path);
    }

    /**
     * Get the url where the user will be redirect to after the openId login process.
     *
     * @return String       url to get the user back after the openID login process.
     */
    public function getReturnTo()
    {
        return sprintf("%s://%s:%s%s/index.php?_wcmAction=login&submit=1&protocol=openid&action=completed", $this->getScheme(), $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'], dirname($_SERVER['PHP_SELF']));
    }

    /**
     * get the Scheme.  If we use http or https.
     *
     * @return String       http or https
     */
    private function getScheme()
    {
        $scheme = 'http';
        if (isset($_SERVER['HTTPS']) and $_SERVER['HTTPS'] == 'on')
            $scheme .= 's';

        return $scheme;
    }

    /**
     *
     * @return String       full_url
     */
    public function getTrustRoot()
    {
        return sprintf("%s://%s:%s%s/", $this->getScheme(), $_SERVER['SERVER_NAME'], $_SERVER['SERVER_PORT'], dirname($_SERVER['PHP_SELF']));
    }

}
?>
