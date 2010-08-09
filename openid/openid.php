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
  public $task = 'login';

  // Register actions
  function init()
  {
    // If REMOTE_USER is not set, mod-auth-openid is not active
    // Relying on it allow to have several virtualhosts with different ways to authenticate
    if (isset($_SERVER['REMOTE_USER'])) {
      $this->add_hook('startup', array($this, 'startup'));
      $this->add_hook('authenticate', array($this, 'authenticate'));
      $this->add_hook('create_user', array($this, 'create_user'));
    }
  }

  // Overlap creation of new accounts so as to avoid having
  // default identities following the pattern user*masteruser@domain.
  // Instead use user@domain
  function create_user($profile)
  {
    $profile['user'] = substr($profile['user'], 0, strpos($profile['user'], '*'));
    $profile['user_name'] = $profile['user'];

    $rcmail = rcmail::get_instance();
    $mail_domain = $rcmail->config->get('mail_domain');

    if ($mail_domain)
      {
        $profile['user_email'] = $profile['user'] . '@' . $mail_domain;
      }

    return $profile;
  }

  // Change action to Login if not yet authenticated
  function startup($args)
  {
    if (empty($args['action']) && 
	empty($_SESSION['user_id']) &&
	!empty($_SERVER['REMOTE_USER']))
      {
	$args['action'] = 'login';
      }
    $args['cookiecheck'] = false;
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
  
  // extract the trigram from an openid url - last part of the identity
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
