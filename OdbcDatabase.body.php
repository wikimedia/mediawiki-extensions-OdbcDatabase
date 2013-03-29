<?php
/**
 * Databse implementation for ODBC databases.
 *
 * @file
 * @ingroup Database
 * @author Roger Cass
 */
class DatabaseOdbc extends DatabaseBase {

	var $mAffectedRows = 0;
	var $mRowNum = 0;

	/**
	 * @return string
	 */
	function getType() {
		return 'odbc';
	}

	/**
	 * @param $sql string
	 * @return resource
	 */
	protected function doQuery( $sql ) {
		$res = odbc_exec( $this->mConn, $sql );
		if ( $res ) {
			$this->mAffectedRows = odbc_num_rows( $res );
			$res = new ResultWrapper( $this, $res );
			$this->mRowNum = 0;
		}
		return $res;
	}

	/**
	 * @param $server string
	 * @param $user string
	 * @param $password string
	 * @param $dbName string
	 * @return bool
	 * @throws DBConnectionError
	 */
	function open( $server, $user, $password, $dbName ) {
		if ( !function_exists( 'odbc_connect' ) ) {
			throw new DBConnectionError( $this, wfMessage( 'odbcdatabase-odbc-missing' ) . "\n" );
		}

		$success = false;

		$this->close();
		$this->mServer = $server;
		$this->mUser = $user;
		$this->mPassword = $password;
		$this->mDBname = $dbName;

		$this->mConn = odbc_connect( $server, $user, $password );

		if ( !$this->mConn ) {
			$error = $this->lastError();
			if ( !$error ) {
				$error = $phpError;
			}
			wfLogDBError( wfMessage( 'odbcdatabase-connection-error', $this->mServer, $error ) . "\n" );
		} else {
			$success = true;
		}

		$this->mOpened = $success;
		return $success;
	}

	/**
	 * @return bool
	 */
	function closeConnection() {
		if ( $this->mConn ) {
			return odbc_close( $this->mConn );
		} else {
			return true;
		}
	}

	/**
	 * @param $res ResultWrapper
	 * @throws DBUnexpectedError
	 */
	function freeResult( $res ) {
		if ( $res instanceof ResultWrapper ) {
			$res->free();
		} else {
			$ok = odbc_free_result( $res );
			if ( !$ok ) {
				throw new DBUnexpectedError( $this, wfMessage( 'odbcdatabase-free-error' ) . "\n" );
			}
		}
		$this->mAffectedRows = 0;
		$this->mRowNum = 0;
	}

	/**
	 * @param $res ResultWrapper
	 * @return object|stdClass
	 * @throws DBUnexpectedError
	 */
	function fetchObject( $res ) {
		if ( $res instanceof ResultWrapper ) {
			$res = $res->result;
		}
		$row = odbc_fetch_object( $res );
		if ( $row ) {
			$this->mRowNum++;
		} else if ( $this->mRowNum <= $this->mAffectedRows ) {
			if( $this->lastErrno() ) {
				throw new DBUnexpectedError( $this, wfMessage( 'odbcdatabase-fetch-object-error', $this->lastErrno(), htmlspecialchars( $this->lastError() ) ) );
			}
		}
		return $row;
	}

	/**
	 * @param $res ResultWrapper
	 * @return array
	 * @throws DBUnexpectedError
	 */
	function fetchRow( $res ) {
		if ( $res instanceof ResultWrapper ) {
			$res = $res->result;
		}

		$array = null;
		$row = odbc_fetch_row( $res );
		if ( $row ) {
			$this->mRowNum++;
			$nCols = odbc_num_fields( $res );
			for ( $i = 0; $i < $nCols; $i++ ) {
				$array[$i] = odbc_result( $res, $i+1 );
			}
		} else if ( $this->mRowNum <= $this->mAffectedRows ) {
			if ( $this->lastErrno() ) {
				throw new DBUnexpectedError( $this, wfMessage( 'odbcdatabase-fetch-row-error', $this->lastErrno(), htmlspecialchars( $this->lastError() ) ) );
			}
		}
		return $array;
	}

	/**
	 * @throws DBUnexpectedError
	 * @param $res ResultWrapper
	 * @return int
	 */
	function numRows( $res ) {
		return $this->mAffectedRows;
	}

	/**
	 * @param $res ResultWrapper
	 * @return int
	 */
	function numFields( $res ) {
		if ( $res instanceof ResultWrapper ) {
			$res = $res->result;
		}
		return odbc_num_fields( $res );
	}

	/**
	 * @param $res ResultWrapper
	 * @param $n string
	 * @return string
	 */
	function fieldName( $res, $n ) {
		if ( $res instanceof ResultWrapper ) {
			$res = $res->result;
		}
		return odbc_field_name( $res, $n );
	}

	/**
	 * @return int
	 */
	function insertId() {
		throw new DBUnexpectedError( $this, wfMessage( 'odbcdatabase-inert-id-unsupported' ) );
		return 0;
	}

	/**
	 * @param $res ResultWrapper
	 * @param $row
	 * @return bool
	 */
	function dataSeek( $res, $row ) {
		if ( $res instanceof ResultWrapper ) {
			$res = $res->result;
		}
		return odbc_fetch_row( $res, $row );
	}

	/**
	 * @return int
	 */
	function lastErrno() {
		if ( $this->mConn ) {
			return odbc_error( $this->mConn );
		} else {
			return odbc_error();
		}
	}

	/**
	 * @return string
	 */
	function lastError() {
		if ( $this->mConn ) {
			$error = odbc_errormsg( $this->mConn );
		} else {
			$error = odbc_errormsg();
		}
		if( $error ) {
			$error .= ' (' . $this->mServer . ')';
		}
		return $error;
	}

	/**
	 * @param $table string
	 * @param $field string
	 * @return bool|MySQLField
	 */
	function fieldInfo( $table, $field ) {
		$table = $this->tableName( $table );
		$res = $this->query( "SELECT * FROM $table LIMIT 1", __METHOD__, true );
		if ( !$res ) {
			return false;
		} else if ( $res instanceof ResultWrapper ) {
			$res = $res->result;
		}
		$n = odbc_num_fields( $res );
		for( $i = 0; $i < $n; $i++ ) {
			$name = odbc_field_name( $res, $i );
			if( $field == $name ) {
				return new OdbcField( $table, $res, $i );
			}
		}
		return false;
	}

	/**
	 * Get information about an index into an object
	 * Returns false if the index does not exist
	 *
	 * @param $table string
	 * @param $index string
	 * @param $fname string
	 * @return false|array
	 */
	function indexInfo( $table, $index, $fname = 'DatabaseMysql::indexInfo' ) {
		/*
		 * For now, always return false. Not sure how
		 * to generically find this info using no
		 * knowledge of the underlying DB.
		 */
		return false;
	}

	/**
	 * @return int
	 */
	function affectedRows() {
		return $this->mAffectedRows;
	}

	/**
	 * @param $s string
	 *
	 * @return string
	 */
	function strencode( $s ) { # Should not be called by us
                return str_replace( "'", "''", $s );
        }

        /**
         * If it's a string, adds quotes and backslashes
         * Otherwise returns as-is
         *
         * @param $s string
         *
         * @return string
         */
        function addQuotes( $s ) {
                if ( $s instanceof Blob ) {
                        return "'" . $s->fetch( $s ) . "'";
                } else {
                        return parent::addQuotes( $s );
                }
        }

	/**
	 * @return string
	 */
	function getServerVersion() {
		$ver = wfMessage( 'odbddatabase-unknown-server-version' );
		$result = odbc_data_source( $this->mConn, SQL_FETCH_FIRST );
		while($result)
		{
			if (strtolower($this->mServer) == strtolower($result['server'])) {
				$ver = $result['description'];
				break;
			}
			else
				$result = odbc_data_source( $this->mConn, SQL_FETCH_NEXT );
		}
		return $ver;
	}

	/**
	 * @return string
	 */
	public static function getSoftwareLink() {
		return '[http://php.net/manual/en/book.uodbc.php]';
	}

	/**
	 * End a transaction
	 *
	 * @param $fname string
	 */
	function doCommit( $fname = 'DatabaseBase::commit' ) {
		if ( $this->mTrxLevel ) {
			//$this->query( 'COMMIT TRAN', $fname );
			$this->mTrxLevel = 0;
		}
	}

	/**
	 * Begin a transaction, committing any previously open transaction
     * @param $fname string
	 */
	function doBegin( $fname = 'DatabaseBase::begin' ) {
		//$this->query( 'BEGIN', $fname );
		$this->mTrxLevel = 1;
	}
}

/**
 * Utility class.
 * @ingroup Database
 */
class OdbcField implements Field {
	private $name, $tableName, $type;

	function __construct ( $tableName, $res, $n ) {
		$this->name = odbc_field_name( $res, $n );
		$this->tableName = $tableName;
		$this->type = odbc_field_type( $res, $n);
	}

	/**
	 * @return string
	 */
	function name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	function tableName() {
		return $this->tableName;
	}

	/**
	 * @return string
	 */
	function type() {
		return $this->type;
	}

	/**
	 * @return bool
	 */
	function isNullable() {
		return $this->false;
	}
}
