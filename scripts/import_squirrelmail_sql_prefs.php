#!/usr/bin/php
<?php
/**
 * This script imports SquirrelMail database preferences into Horde.
 *
 * The first argument must be a DSN to the database containing the "userprefs"
 * table, e.g.: "mysql://root:password@localhost/squirrelmail".
 *
 * Copyright 2008-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author Ben Chavet <ben@horde.org>
 * @author Jan Schneider <jan@horde.org>
 */

// Do CLI checks and environment setup first.
require_once dirname(__FILE__) . '/../lib/Application.php';
Horde_Registry::appInit('horde', array('authentication' => 'none', 'cli' => true));

// Read command line parameters.
$cli = Horde_Cli::singleton();
if ($argc != 2) {
    $cli->message('Too many or too few parameters.', 'cli.error');
    $cli->writeln('Usage: import_squirrelmail_prefs.php DSN');
    exit;
}
$dsn = $argv[1];

require_once dirname(__FILE__) . '/import_squirrelmail_prefs.php';

// Connect to database.
$db = DB::connect($dsn);
if (is_a($db, 'PEAR_Error')) {
    $cli->fatal($db->toString());
}

// Loop through SquirrelMail address books.
$handle = $db->query('SELECT user, prefkey, prefval FROM userprefs ORDER BY user');
if (is_a($handle, 'PEAR_Error')) {
    $cli->fatal($handle->toString());
}
$user = null;
$prefs_cache = array();
while ($row = $handle->fetchRow(DB_FETCHMODE_ASSOC)) {
    if (is_null($user)) {
        $user = $row['user'];
    }
    if ($row['user'] != $user) {
        importPrefs();
        $prefs_cache = array();
        $user = $row['user'];
    }

    $prefs_cache[$row['prefkey']] = $row['prefval'];
}
importPrefs();

function importPrefs()
{
    global $cli, $conf, $user, $prefs_cache;

    Horde_Auth::setAuth($user, array());
    $cli->message('Importing ' . $user . '\'s preferences');
    $prefs = Horde_Prefs::factory($conf['prefs']['driver'], 'horde', $user, null, null, false);
    savePrefs($user, null, $prefs_cache);
}

function getSignature($basename, $number = 'nature')
{
    global $prefs_cache;

    $key = '___sig' . $number . '___';
    return isset($prefs_cache[$key]) ? $prefs_cache[$key] : '';
}
