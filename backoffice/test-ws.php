<?php
/*
 *Test web-service
 */
DEFINE('WCM_URL', 'http://localhost:8080/wcm4/');

    /**
     * Open a SOAP client connexion to a webservice
     *
     * @param string $service Name of the service
     *
     * @return SoapClient SOAP client to the web service
     */
    function openWS($service)
    {
        $url = WCM_URL . 'webservices/service.php?class=wcm' . $service . 'WebService&wsdl';
        return new SoapClient($url);
    }

    /**
     * Execute a method of a web-service and echo the result
     *
     * @param string $service Name of the web-service (e.g 'UserAuthentication')
     * @param string $method Name of the method (e.g. 'Login')
     * @param array $params Optional parameters of the method (e.g. array('login', 'password'))
     *
     * @return mixed method result of null on exception
     */
    function wsMethod($service, $method, $params = null)
    {
        echo '<p>' . $service . '->' . $method . '(';
        if (is_array($params))
        {
            $first = 1;
            foreach($params as $param)
            {
                if (!$first) echo ', ';
                $first = false;
                if ($param === null)
                    echo 'null';
                elseif (is_bool($param))
                    echo ($param) ? 'true' : 'false';
                elseif (is_int($param))
                    echo $param;
                else 
                    echo '"'.strval($param).'"';
            }
        }
        echo ')</p>';
        echo '<ul>';
        try
        {
            $ws = openWS($service);
            $result = call_user_func_array(array($ws, $method), $params);
            echo '<li> => ' . $result . '</li>';
        }
        catch(Exception $e)
        {
            echo '<li style="color:red"> => ' . $e->getMessage() . '</li>';
            $result = null;
        }
        echo '</ul>';
        
        return $result;
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title> WCM web-services testing </title>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="skins/main.css" />
</head>
<body>
<div id="wrapper">
    <div id="header">
        <div id="banner">
            <h1><span>Nstein WCM</span></h1>
            <h2>Powering Online Publishing</h2>
        </div>
        <div id="navMenu"></div>
    </div>
    <div id="content-wrapper">
<?php
    echo '<h1> UserAuthentication </h1>';

    //login
    $wsToken = wsMethod('UserAuthentication', 'Login', array('admin', 'admin'));

    echo '<h1> ObjectManagement </h1>';

    // lock an existing object
    wsMethod('ObjectManagement', 'lockObject', array($wsToken, 'article', 1));

    // lock same object
    wsMethod('ObjectManagement', 'lockObject', array($wsToken, 'article', 1));

    // unlock an existing object
    wsMethod('ObjectManagement', 'unlockObject', array($wsToken, 'article', 1));



    echo '<h1> ContentGeneration </h1>';

    // generation of an existing object
    wsMethod('ContentGeneration', 'generateObjectContent', array($wsToken, 'article', 1, false, false));

    // generation of a non existing object
    wsMethod('ContentGeneration', 'generateObjectContent', array($wsToken, 'foo', 1, false, false));

    // generation of a non existing object
    wsMethod('ContentGeneration', 'generateObjectContent', array($wsToken, 'article', -123, false, false));

    // generation of a non existing object
    wsMethod('ContentGeneration', 'generateTemplateContent', array($wsToken, 'test.tpl', null));

    echo '<h1> UserAuthentication </h1>';

    //logout
    wsMethod('UserAuthentication', 'Logout', array($wsToken));

?>
    </div>
</body>
</html>