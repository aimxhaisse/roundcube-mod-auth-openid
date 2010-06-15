<?php

/**
 * OpenID Basic Authentication
 *
 * Make use of the username provided by mod-auth-openid of Apache for
 * imap, assuming it has a MasterUsers feature.
 *
 * @version 1.0
 * @author Sebastien Rannou
 */

// username of the MasterUser account
define('MASTER_USER_LOGIN', 'xxx');

// password of the MasterUser account
define('MASTER_USER_PASS', 'xxx');

class openid extends rcube_plugin
{
  // Register actions
  function init()
  {
    $this->add_hook('startup', array($this, 'startup'));
    $this->add_hook('authenticate', array($this, 'authenticate'));
  }

  // Change action to Login if not yet authenticated
  function startup($args)
  {
    if ($args['task'] == 'mail' && empty($args['action']) && 
	empty($_SESSION['user_id']) && !empty($_SERVER['REMOTE_USER']))
      {
	$args['action'] = 'login';
      }

    return $args;
  }

  // Check for the availability of the openid identity, extract the username
  // from it, and authenticates with it using the MasterUser password.
  function authenticate($args)
  {
    if (!empty($_SERVER['REMOTE_USER']))
      {
	$username = $this->extractLoginFromOpenid($_SERVER['REMOTE_USER']);
	if ($username)
	  {
	    // http://wiki.dovecot.org/Authentication/MasterUsers
	    $args['user'] = $username . '*' . MASTER_USER_LOGIN;
	    $args['pass'] = MASTER_USER_PASS;
	  }
      }
    return $args;
  }
  
  // extract the trigram from an openid url (makina-corpus related) - last part of the identity
  function extractLoginFromOpenid($openid_url)
  {
    $matches = array();
    // catch the "login" part of a provider of the following form : http://provider.tld/xxx/yyy/LOGIN
    if (preg_match('#(?:[^/]*/?)*/([^/]+)#', $openid_url, $matches) && strlen($matches[1]) > 0)
      {
	return $matches[1];
      }    
    return false;
  }
}
