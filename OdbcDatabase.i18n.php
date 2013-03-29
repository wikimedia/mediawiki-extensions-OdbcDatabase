<?php

/*
 * Locale strings for OdbcDatabase extension
 */

$messages = array();

/** English
 * @author Roger Cass
 */
$messages['en'] = array(
	'odbcdatabase' => 'OdbcDatabase',
	'odbcdatabase-desc' => 'Driver that uses the PHP odbc_* library of commands ' .
		'to support the External Data extension.',
	'odbcdatabase-odbc-missing' => 'ODBC functions missing. Ensure PHP:ODBC is installed.',
	'odbcdatabase-connection-error' => 'Error connecting to server [{{1}}]: {{2}}',
	'odbcdatabase-free-error' => 'Unable to free ODBC result',
	'odbcdatacase-fetch-object-error' => 'Error while fetching object: [{{1}}] {{2}}',
	'odbcdatacase-fetch-row-error' => 'Error while fetching row: [{{1}}] {{2}}',
	'odbcdatabase-insert-id-unsupported' => 'Function insertId() is unsupported.',
	'odbcdatabase-unknown-server-version' => 'Unknown ODBC server version.',
);

/** Message Descriptions
 * @author Roger Cass
 */
$messages['qqq'] = array(
	'odbcdatabase-desc' => '{{desc}}',
	'odbcdatabase-odbc-missing' => 'The ODBC functions are not available in this PHP ' .
		'installation.',
	'odbcdatabase-connection-error' => 'There was an error connecting to the ' .
	'database server. {{1}} is the name of the server. {{2}} is the text of any ' .
		'error we could recover.',
	'odbcdatabase-free-error' => 'There was an error freeing an ODBC result.',
	'odbcdatacase-fetch-object-error' => 'There was an error while fetching an object ' .
		'using odbc_fetch_object. {{1}} is the error number from odbc_error. ' .
		'{{2}} is the error text.',
	'odbcdatacase-fetch-row-error' => 'There was an error while fetching a row ' .
		'using odbc_fetch_row. {{1}} is the error number from odbc_error. ' .
		'{{2}} is the error text.',
	'odbcdatabase-insert-id-unsupported' => 'Report that this function is not supported.',
	'odbcdatabase-unknown-server-version' => 'The ODBC server version is not known. '.
		'A default version message.',
);

