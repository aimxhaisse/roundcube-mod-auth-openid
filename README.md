RoundCube - mod-auth-openid
===

## Introduction

This plugin can be coupled with mod-auth-openid to provide an automatic authentication of the user 
on an IMAP server supporting master password (see Dovecot).

## Download

The project can be checked out from its repository :

$ git clone git://github.com/aimxhaisse/roundcube-mod-auth-openid.git

## Installation

  - Enable master users in your IMAP server configuration, for Dovecot, see http://wiki.dovecot.org/Authentication/MasterUsers|MasterUsers/Password, with * as separator.
  - Ensure you can login as anyone with a master user login and password
  - Ensure RoundCube is protected by mod-auth-ldap, and that your IMAP users have an OpenID identity ending with their username (ex: domain.tld/username).
  - Move the previously downloaded openid directory to the plugin directory of your RoundCube installation.
  - Open the file plugins/openid/openid.php.
  - Edit the define MASTER_USER_LOGIN to your IMAP master user login.
  - Edit the define MASTER_USER_PASSWORD to your IMAP master user password.
  - Open the file config/main.inc.php file.
  - Activates the plugin by adding "openid" to plugins: $rcmail_config['plugins'] = array('openid');
  - Save files, it should be good.

## How it works

The apache module mod-auth-openid provides an environment variable of the OpenID identity of the authenticated user (quite the same behavior as 
mod-auth-ldap). Because OpenID does not provide a password (fortunately), you need a way to authenticate on IMAP with only the login of the user, 
this is possible with the MasterPasswords feature of some IMAP servers.

## Security note

You must define one trusted OpenID provider with mod-auth-openid, or anyone will be able to
read mailboxes of others.

## See also

  - More documentation: http://aimxhaisse.com/wiki/doku.php?id=roundcube_mod-auth-openid
  - Dovecot and Master users: http://wiki.dovecot.org/Authentication/MasterUsers
  - mod-auth-openid: 
