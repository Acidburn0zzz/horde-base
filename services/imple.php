<?php
/**
 * Handle Horde_Ajax_Imple:: requests.
 *
 * Mandatory components:
 * 'imple'
 *
 * Optional components:
 * 'impleApp'
 * 'sessionWrite'
 * 'post' - name of POST variable that contains any values required to be sent
 *          by POST. Format is the same as imple (/var1=value/var2=value)
 *
 * Copyright 2005-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Michael Slusarz <slusarz@horde.org>
 * @package Horde
 */

/* Load core first because we need access to Horde_Util::. */
require_once dirname(__FILE__) . '/../lib/core.php';

if (!($path = Horde_Util::getFormData('imple'))) {
    exit;
}

$path = explode('/', ltrim($path, '/'));
$impleName = array_shift($path);

$args = array();
$post = array();

foreach ($path as $pair) {
    if (strpos($pair, '=') === false) {
        $args[$pair] = true;
    } else {
        list($name, $val) = explode('=', $pair);
        $args[$name] = $val;
    }
}

/* See if any variables required a POST */
if (!empty($args['post'])) {
    $posts = explode('/', Horde_Util::getPost($args['post']));
    unset($args['post']);

    /* Populate from POST */
    foreach ($posts as $pair) {
        if (strpos($pair, '=') === false) {
            $post[$pair] = true;
        } else {
            list($name, $val) = explode('=', $pair);
            $post[$name] = $val;
        }
    }
}

Horde_Registry::appInit('horde', array('nologintasks' => true, 'session_control' => empty($args['sessionWrite']) ? 'readonly' : null));

$impleargs = $impleName;
if (isset($args['impleApp'])) {
    $registry = Horde_Registry::singleton();
    $registry->pushApp($args['impleApp']);
    $impleargs = array($args['impleApp'], $impleName);
}

$imple = Horde_Ajax_Imple::factory($impleargs);
$result = $imple->handle($args, $post);

$ct = empty($_SERVER['Content-Type'])
    ? (is_string($result) ? 'plain' : 'json')
    : $_SERVER['Content-Type'];

Horde::sendHTTPResponse($result, $ct);
