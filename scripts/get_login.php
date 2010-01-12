<?php
/**
 * Copyright 2004-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author Joel Vandal <joel@scopserv.com>
 */

require_once dirname(__FILE__) . '/../lib/Application.php';
new Horde_Application(array('authentication' => 'none'));

$auth = Horde_Auth::singleton($conf['auth']['driver']);

// Check for GET auth.
if (empty($_GET['user']) ||
    !$auth->authenticate($_GET['user'], array('password' => $_GET['pass']))) {
    Horde::authenticationFailureRedirect();
}

require HORDE_BASE . '/index.php';
