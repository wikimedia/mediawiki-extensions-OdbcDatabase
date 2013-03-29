<?php

/**
 * OdbcDatabase extension
 *
 * @author Roger Cass
 */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'OdbcDatabase',
	'author' => 'Roger Cass',
	'url' => 'http://www.mediawiki.org/wiki/Extension:OdbcDatabase',
	'descriptionmsg' => 'odbcdatabase-desc',
	'version' => '1.0 alpha 1',
);

$wgExtensionMessagesFiles['OdbcDatabase'] = __DIR__ . '/OdbcDatabase.i18n.php';
$wgExtensionAliasesFiles['OdbcDatabase'] = __DIR__ . '/OdbcDatabase.aliases.php';
$wgAutoloadClasses['DatabaseOdbc']
	= $wgAutoloadClasses['OdbcField']
	= __DIR__ . '/OdbcDatabase.body.php';

