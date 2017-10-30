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
	'url' => 'https://www.mediawiki.org/wiki/Extension:OdbcDatabase',
	'descriptionmsg' => 'odbcdatabase-desc',
	'version' => '1.2.0 alpha 2',
);

$wgMessagesDirs['OdbcDatabase'] = __DIR__ . '/i18n';
$wgExtensionMessagesFiles['OdbcDatabaseAlias'] = __DIR__ . '/OdbcDatabase.alias.php';
$wgAutoloadClasses['DatabaseOdbc']
	= $wgAutoloadClasses['OdbcField']
	= __DIR__ . '/OdbcDatabase.body.php';
