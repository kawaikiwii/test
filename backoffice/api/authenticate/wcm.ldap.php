<?php
/**
 * Project:     WCM
 * File:        ldapAuthenticate.php
 *
 * @copyright   (c)2008 Nstein Technologies
 * @version     4.x
 *
 */

class wcmLdap
{
    private $host;
    private $port;
    private $basedn;
    private $attributes = array();

    public function __construct($host, $port=369, $basedn=null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->basedn = $basedn;

        /**
         * LDAP attributes needed to create a wcm user. Much more are available but we just retreive those ones.
         *
         * display = full name
         * mail = email
         */
        $this->attributes = array('cn','mail');
    }

    /**
     *  This method authenticate the user using a login/password using LDAP
     *
     *  @param String $username     Username of the user
     *  @param String $password     Password of the user
     *
     *  @return array               false on failed
     */
    public function login($username, $password)
    {
        //$project = wcmProject::getInstance();

        $err = '';

        $conn = @ldap_connect($this->host);
        if ($conn)
        {
            ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);

            if (@ldap_bind($conn, $username.'@'.$this->host, $password))
            {
                $ressource = @ldap_search($conn, $this->basedn, '(samaccountname='.$username.')', $this->attributes);
                if ($ressource)
                {
                    $result = @ldap_get_entries($conn, $ressource);
                    if ($result[0])
                    {
                        ldap_close($conn);
                        return array(true, array('name' => $result[0]['cn'][0], 'email' => $result[0]['mail'][0]));
                    }
                    else
                        $err = 'wcmLdap: entries empty!  username: '.$username;
                }
                else
                    $err = 'wcmLdap: search failed! basedn: '.$this->basedn.' username: '.$username.' (samaccountname='.$username.')' ;
            }
            else
                $err = 'wcmLdap: unable to bind! username: '.$username.'@'.$this->host;

            ldap_close($conn);
        }
        else
            $err = 'wcmLdap: connection to ldap server failed! host:'.$this->host.' port: '.$this->port;

        //$project->logger->logWarning($err);
        return array(false, $err);
    }
}