<?php
/**
 * Processes an AJAX request and returns a JSON encoded result.
 *
 * Path Info:
 * ----------
 * http://example.com/horde/services/ajax.php/APP/ACTION
 *
 * 'APP' - (string) The application name.
 * 'ACTION' - (string) The AJAX action identifier.
 *
 * Reserved 'ACTION' strings:
 * 'LogOut' - Logs user out of Horde.
 *
 * Copyright 2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Michael Slusarz <slusarz@horde.org>
 * @package Horde
 */

require_once dirname(__FILE__) . '/../lib/Application.php';

list($app, $action) = explode('/', trim(Horde_Util::getPathInfo(), '/'));
if (empty($action)) {
    // This is the only case where we really don't return anything, since
    // the frontend can be presumed not to make this request on purpose.
    // Other missing data cases we return a response of boolean false.
    exit;
}

try {
    Horde_Registry::appInit($app, array('authentication' => 'throw'));
} catch (Horde_Exception $e) {
    /* Handle session timeouts when they come from an AJAX request. */
    if (($e->getCode() == Horde_Registry::AUTH_FAILURE) &&
        ($action != 'LogOut')) {
        $ajax = Horde_Ajax::getInstance($app);
        $notification = Horde_Notification::singleton();

        $notification->push(str_replace('&amp;', '&', Horde_Auth::getLogoutUrl(array('reason' => Horde_Auth::REASON_SESSION))), 'horde.ajaxtimeout', array('content.raw'));
        Horde::sendHTTPResponse(Horde::prepareResponse(null, $ajax->notificationHandler()), $ajax->responseType());
        exit;
    }

    Horde_Auth::authenticateFailure($app, $e);
}

// Open an output buffer to ensure that we catch errors that might break JSON
// encoding.
ob_start();

$ajax = Horde_Ajax::getInstance($app, $action);
try {
    $result = $ajax->doAction();
} catch (Exception $e) {
    $notification->push($e->getMessage(), 'horde.error');
    $result = null;
}

// Clear the output buffer that we started above, and log any unexpected
// output at a DEBUG level.
if (ob_get_length()) {
    Horde::logMessage('Unexpected output (' . $app . '): ' . ob_get_clean(), __FILE__, __LINE__, PEAR_LOG_DEBUG);
}

// Send the final result.
Horde::sendHTTPResponse(Horde::prepareResponse($result, $ajax->notificationHandler()), $ajax->responseType());
