<?php
/**
 * Copyright 1999-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author Chuck Hagenbuch <chuck@horde.org>
 */

require_once dirname(__FILE__) . '/../lib/Application.php';
Horde_Registry::appInit('horde', array('admin' => true));

$title = _("PHP Shell");
Horde::addScriptFile('stripe.js', 'horde');
require HORDE_TEMPLATES . '/common-header.inc';
require HORDE_TEMPLATES . '/admin/menu.inc';

$apps_tmp = $registry->listApps();
$apps = array();
foreach ($apps_tmp as $app) {
    // Make sure the app is installed.
    if (!file_exists($registry->get('fileroot', $app))) {
        continue;
    }

    $apps[$app] = $registry->get('name', $app) . ' (' . $app . ')';
}
asort($apps);
$application = Horde_Util::getFormData('app', 'horde');

$command = trim(Horde_Util::getFormData('php'))

?>
<div style="padding:10px">
<form action="phpshell.php" method="post">
<?php Horde_Util::pformInput() ?>

<h1 class="header"><?php echo _("PHP Shell") ?></h1>
<br />
<label for="app"><?php echo _("Application Context: ") ?></label>
<select id="app" name="app">
<?php foreach ($apps as $app => $name): ?>
 <option value="<?php echo $app ?>"<?php if ($application == $app) echo ' selected="selected"' ?>><?php echo $name ?></option>
<?php endforeach; ?>
</select><br /><br />

<label for="php" class="hidden"><?php echo _("PHP") ?></label>
<textarea class="fixed" id="php" name="php" rows="10" style="width:100%; padding:0;">
<?php if (!empty($command)) echo htmlspecialchars($command) ?></textarea>
<br />
<input type="submit" class="button" value="<?php echo _("Execute") ?>" />
<?php echo Horde_Help::link('admin', 'admin-phpshell') ?>
</form><br />

<?php

if ($command) {
    if (file_exists($registry->get('fileroot', $application) . '/lib/base.php')) {
        include $registry->get('fileroot', $application) . '/lib/base.php';
    } else {
        $registry->pushApp($application);
    }

    $part = new Horde_Mime_Part();
    $part->setContents($command);
    $part->setType('application/x-httpd-phps');
    $part->buildMimeIds();
    $viewer = Horde_Mime_Viewer::factory($part);
    $pretty = $viewer->render('inline');
    echo '<h1 class="header">' . _("PHP Code") . '</h1>';
    echo $pretty[1]['data'];
    echo '<br />';

    echo '<h1 class="header">' . _("Results") . '</h1>';
    echo '<pre class="text">';
    eval($command);
    echo '</pre>';
}
?>

</div>
<?php

require HORDE_TEMPLATES . '/common-footer.inc';
