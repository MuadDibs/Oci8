<?php

namespace Oci8;

class Oci8Connection extends Oci8Abstract
	{
	private $connection;
	private $transactionOngoing = false;

	/**
	 * Connection constructor.
	 * @param      $username
	 * @param      $password
	 * @param null $connectionString
	 * @param null $characterSet
	 * @param null $sessionMode
	 * @throws Oci8Exception
	 */
	public function __construct($username, $password, $connectionString = null, $characterSet = 'AL32UTF8', $sessionMode = null)
		{
		$this->connect($username, $password, $connectionString, $characterSet, $sessionMode);
		}

	/**
	 * Connect to the Oracle server using a unique connection
	 *
	 * @param string $username
	 * @param string $password
	 * @param string $connectionString
	 * @param string $characterSet
	 * @param int $sessionMode
	 * @return Oci8Connection
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-new-connect.php
	 */
	public function connect($username, $password, $connectionString = null, $characterSet = 'AL32UTF8', $sessionMode = null): Oci8Connection
		{
		//$sql = "ALTER SESSION SET NLS_DATE_FORMAT='DD.MM.YYYY'";
		//$sql = "ALTER SESSION SET NLS_NUMERIC_CHARACTERS='. '";

		set_error_handler(static::getErrorHandler());
		$this->connection = oci_new_connect($username, $password, $connectionString, $characterSet, $sessionMode);
		restore_error_handler();
		return $this;
		}

	/**
	 * Closes an Oracle connection
	 *
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-close.php
	 */
	public function close(): bool
		{
		$result = @oci_close($this->connection);
		$this->throwExceptionIfFalse($result, $this->connection);
		$this->connection = null;

		return true;
		}

	/**
	 * Synonym for close()
	 * @return bool
	 * @throws Oci8Exception
	 */
	public function disconnect(): bool
		{
		return $this->close();
		}

	/**
	 * Copies large object
	 *
	 * @param \OCI-Lob $lobTo
	 * @param \OCI-Lob $lobFrom
	 * @param int $length
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-lob-copy.php
	 */
	public function copyLob($lobTo, $lobFrom, $length = 0)
		{

		}

	/**
	 * Frees a descriptor
	 *
	 * @param resource $descriptor
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-free-descriptor.php
	 */
	public function freeDescriptor($descriptor)
		{

		}

	/**
	 * Returns the Oracle client library mayor version
	 *
	 * @return int
	 */
	public function getClientMayorVersion()
		{

		}

	/**
	 * Returns the Oracle client library version
	 *
	 * @return string
	 * @see http://php.net/manual/en/function.oci-client-version.php
	 */
	public function getClientVersion()
		{

		}

	/**
	 * Allocates new collection object
	 *
	 * @param string $tdo
	 * @param string $schema
	 * @return \OCI_Collection
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-new-collection.php
	 */
	public function getNewCollection($collectionName, $schema = 'SYS'): \OCI_Collection
		{
		$collection = oci_new_collection($this->connection, $collectionName, $schema);
		$this->throwExceptionIfFalse($collection, $this->connection);
		return $collection;
		}

	/**
	 * Allocates and returns a new cursor (statement handle)
	 *
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-new-cursor.php
	 */
	public function getNewCursor()
		{
		$cursor = oci_new_cursor($this->connection);
		$this->throwExceptionIfFalse($cursor, $this->connection);
		return $cursor;
		}

	/**
	 * Initializes a new empty LOB or FILE descriptor
	 *
	 * @param int $type
	 * @return \OCI_Lob
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-new-descriptor.php
	 */
	public function getNewDescriptor($type = OCI_DTYPE_LOB)
		{

		}

	/**
	 * Returns the Oracle Database version
	 *
	 * @return string
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-server-version.php
	 */
	public function getServerVersion()
		{

		}

	/**
	 * Compares two LOB/FILE locators for equality
	 *
	 * @param \OCI-Lob $lob1
	 * @param \OCI-Lob $lob2
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-lob-is-equal.php
	 */
	public function isLobEqual($lob1, $lob2)
		{

		}

	/**
	 * Prepares an Oracle statement for execution
	 *
	 * @param string $sqlText
	 * @return Oci8Statement
	 * @throws Oci8Exception
	 * @throws \Exception
	 */
	public function parse($sqlText): Oci8Statement
		{
		$resource = oci_parse($this->connection, $sqlText);
		$this->throwExceptionIfFalse($resource, $this->connection);

		return new Oci8Statement($resource,
														 ($this->transactionOngoing) ? OCI_NO_AUTO_COMMIT : OCI_COMMIT_ON_SUCCESS);
		}

	/**
	 * Sets the action name
	 *
	 * @param string $actionName
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-set-action.php
	 */
	public function setAction($actionName)
		{

		}

	/**
	 * Sets the client identifier
	 *
	 * @param string $clientIdentifier
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-set-client-identifier.php
	 */
	public function setClientIdentifier($clientIdentifier)
		{

		}

	/**
	 * Sets the client information
	 *
	 * @param string $clientInfo
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-set-client-info.php
	 */
	//TODO maybe change to module name. Дабы видеть в v$sql
	public function setClientInfo(string $clientInfo): bool
		{
		set_error_handler(static::getErrorHandler());
		$isSuccess = oci_set_client_info($this->connection, $clientInfo);
		restore_error_handler();
		return $isSuccess;
		}

	/**
	 * Sets the database edition
	 *
	 * @param string $edition
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-set-edition.php
	 */
	public static function setEdition($edition)
		{

		}

	/**
	 * Enables or disables internal debug output
	 *
	 * @param bool $onOff
	 * @see http://php.net/manual/en/function.oci-internal-debug.php
	 */
	public function setInternalDebug($onOff)
		{

		}

	/**
	 * Sets the module name
	 *
	 * @param string $moduleName
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-set-module-name.php
	 */
	public function setModuleName($moduleName)
		{

		}

	/**
	 * Starts virtual transaction
	 * All queries launched after that will use OCI_NO_AUTO_COMMIT by default
	 *
	 * @return bool
	 */
	public function transactionStart(): bool
		{
		if ($this->transactionOngoing === false)
			{
			$this->transactionOngoing = true;
			return true;
			}
		else
			{
			return false;
			//TODO add expection?
			}
		}

	/**
	 * Commits the outstanding database transaction
	 *
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-commit.php
	 */
	public function commit(): bool
		{
		$isSuccess = oci_commit($this->connection);
		$this->throwExceptionIfFalse($isSuccess, $this->connection);
		$this->transactionOngoing = false;
		return $isSuccess;
		}

	/**
	 * Rolls back the outstanding database transaction
	 *
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-rollback.php
	 */
	public function rollback(): bool
		{
		$isSuccess = oci_rollback($this->connection);
		$this->throwExceptionIfFalse($isSuccess, $this->connection);
		$this->transactionOngoing = false;
		return $isSuccess;
		}

	/**
	 * @throws Oci8Exception
	 */
	public function __destruct()
		{
		$this->close();
		}
	}
