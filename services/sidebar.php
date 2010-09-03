<?php
/**
 * Horde sidebar generation.
 *
 * Copyright 1999-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author   Michael Pawlowsky <mikep@clearskymedia.ca>
 * @author   Chuck Hagenbuch <chuck@horde.org>
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @package  Horde
 */

require_once dirname(__FILE__) . '/../lib/Application.php';
Horde_Registry::appInit('horde', array('authentication' => 'none'));

/* We may not be in global scope since this file can be included from other
 * scripts. */
global $conf, $injector, $language, $prefs, $registry;

if (!Horde_Util::getFormData('ajaxui') &&
    ($conf['menu']['always'] ||
     ($registry->getAuth() && $prefs->getValue('show_sidebar')))) {
    $tree = $injector->getInstance('Horde_Core_Sidebar')->getTree();

    Horde::addScriptFile('sidebar.js', 'horde');

    $ajax_url = Horde::getServiceLink('ajax', 'horde');
    $ajax_url->pathInfo = 'sidebarUpdate';

    $charset = $registry->getCharset();
    $rtl = intval(isset($registry->nlsconfig['rtl'][$language]));
    $show_sidebar = !isset($_COOKIE['horde_sidebar_expanded']) || $_COOKIE['horde_sidebar_expanded'];
    $width = intval($prefs->getValue('sidebar_width'));

    Horde::addInlineJsVars(array(
        'HordeSidebar.domain' => $conf['cookie']['domain'],
        'HordeSidebar.path' => $conf['cookie']['path'],
        '-HordeSidebar.refresh' => intval($prefs->getValue('menu_refresh_time')),
        'HordeSidebar.url' => strval($ajax_url),
        '-HordeSidebar.width' => $width
    ));

    require $registry->get('templates', 'horde') . '/sidebar/sidebar.inc';

    if ($show_sidebar) {
        $style = $rtl
            ? 'margin-right:' . $width . 'px'
            : 'margin-left:' . $width . 'px';
    } else {
        /* Default to 18px. If local theme changes alter this value, it will
         * automatically be determined by javascript at load time. */
        $style = $rtl
            ? 'margin-right:18px'
            : 'margin-left:18px';
    }

    echo '<div id="horde_body" class="body" style="' . $style . '">';
} else {
    echo '<div class="body" id="horde_body">';
}

$GLOBALS['sidebarLoaded'] = true;
