<?php

namespace Oci8;

class Oci8Statement extends Oci8Abstract
	{
	private $connection;
	private $statement;
	private $defaultExecutionMode = OCI_COMMIT_ON_SUCCESS;
	//
	private $params = [];
	private $cursor = null;
	//
	private $executed  = false;
	private $described = false;
	//TODO remove
	const EXECUTE_AUTO_COMMIT = 0x02;
	const EXECUTE_DESCRIBE = 0x01;
	const EXECUTE_NO_AUTO_COMMIT = 0x03;
	//
	const RETURN_LOBS_AS_STRING = 0x02;
	const RETURN_NULLS = 0x01;

	/**
	 * Oci8Statement constructor.
	 * @param $statement
	 * @param $defaultExecutionMode
	 * @throws \Exception
	 */
	public function __construct($statement, $defaultExecutionMode = OCI_COMMIT_ON_SUCCESS, Oci8Connection $connection = null)
		{
		if (!is_resource($statement) || get_resource_type($statement) !== 'oci8 statement')
			{
			throw new \Exception('resource is not an oci8 statement');
			}
		$this->statement = $statement;
		$this->defaultExecutionMode = $defaultExecutionMode;
		if ($connection !== null) $this->connection = $connection;
		}

	/**
	 * Binds a PHP array to an Oracle PL/SQL array parameter
	 *
	 * @param string $name
	 * @param array $varArray
	 * @param int $maxTableLength
	 * @param int $maxItemLength
	 * @param int $type
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-bind-array-by-name.php
	 */
	public function bindArrayByName($name, &$varArray, $maxTableLength, $maxItemLength = -1, $type = SQLT_AFC): Oci8Statement
		{
		$isSuccess = oci_bind_array_by_name($this->statement, $name, $varArray, $maxTableLength, $maxItemLength, $type);
		$this->throwExceptionIfFalse($isSuccess, $this->statement);

		//return $isSuccess;
		return $this;
		}

	/**
	 * Binds a PHP variable to an Oracle placeholder
	 *
	 * @param string $bvName
	 * @param mixed $variable
	 * @param int $maxLength
	 * @param int $type
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-bind-by-name.php
	 */
	public function bindByName($bvName, $variable, $maxLength = -1, $type = SQLT_CHR): Oci8Statement
		{
		$isSuccess = oci_bind_by_name($this->statement, $bvName, $variable, $maxLength, $type);
		$this->throwExceptionIfFalse($isSuccess, $this->statement);

		//return $isSuccess;
		return $this;
		}

	public function bind(array $params): Oci8Statement
		{
		//TODO implement universal bind
		return $this;
		}

	/**
	 * Cancels reading from cursor
	 *
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-cancel.php
	 */
	public function cancel()
		{

		}

	/**
	 * Associates a PHP variable with a column for query fetches
	 *
	 * @param string $columnName
	 * @param mixed $variable
	 * @param int $type
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-define-by-name.php
	 */
	public function defineByName($columnName, &$variable, $type = SQLT_CHR)
		{

		}

	/**
	 * Executes a statement
	 *
	 * @param int $mode
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-execute.php
	 */
	public function execute($mode = null)
		{
		if (!$mode)
			{
			$mode = $this->defaultExecutionMode;
			}
		$isSuccess = @oci_execute($this->statement, $mode);
		$this->throwExceptionIfFalse($isSuccess, $this->statement);

		if ($this->cursor)
			{
			oci_execute($this->cursor, OCI_DEFAULT);
			}


		return $isSuccess;
		}

	public function describe()
		{

		}

	/**
	 * Fetches multiple rows from a query into a two-dimensional array
	 *
	 * @param array $output
	 * @param int $skip
	 * @param int $maxRows
	 * @param int $flags
	 * @return int
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-fetch-all.php
	 */
	public function fetchAll(&$output, $skip = 0, $maxRows = -1, $flags = 0)
		{
		if (empty($flags))
			{
			$flags = OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC;
			}

		$numRows = @oci_fetch_all($this->statement, $output, $skip, $maxRows, $flags);
		$this->throwExceptionIfFalse($numRows, $this->statement);

		return $numRows;
		}

	/**
	 * Returns the next row from a query as an associative or numeric array
	 *
	 * @param int $mode
	 * @return array
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-fetch-array.php
	 */
	public function fetchArray($mode = OCI_BOTH)
		{
		$row = oci_fetch_array($this->statement, $mode);
		$this->throwExceptionIfFalse($row, $this->statement);

		return $row;
		}

	/**
	 * Returns the next row from a query as an associative array
	 *
	 * @return array
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-fetch-assoc.php
	 */
	public function fetchAssoc()
		{
		$row = oci_fetch_assoc($this->statement);
		$this->throwExceptionIfFalse($row, $this->statement);

		return $row;
		}

	/**
	 * Returns the next row from a query as an object
	 *
	 * @return object
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-fetch-object.php
	 */
	public function fetchObject()
		{
		$row = oci_fetch_object($this->statement);
		$this->throwExceptionIfFalse($row, $this->statement);

		return $row;
		}

	/**
	 * Returns the next row from a query as a numeric array
	 *
	 * @return array
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-fetch-row.php
	 */
	public function fetchRow()
		{
		$row = oci_fetch_row($this->statement);
		$this->throwExceptionIfFalse($row, $this->statement);

		return $row;
		}

	/**
	 * Fetches the next row from a query into internal buffers
	 *
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-fetch.php
	 */
	public function fetch()
		{
		$isSuccess = oci_fetch($this->statement);
		$this->throwExceptionIfFalse($isSuccess, $this->statement);

		return $isSuccess;
		}

	/**
	 * Returns an Oci8Field instance
	 *
	 * @param mixed $field
	 * @throws Oci8Exception
	 */
	public function getField($field)
		{

		}

	/**
	 * Frees all resources associated with statement or cursor
	 *
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-free-statement.php
	 */
	public function free()
		{

		}

	/**
	 * Returns the next child statement resource from a parent statement resource that has
	 * Oracle Database 12c Implicit Result Sets
	 *
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-get-implicit-resultset.php
	 */
	public function getImplicitResultset()
		{

		}

	/**
	 * Returns the number of result columns in a statement
	 *
	 * @return int
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-num-fields.php
	 */
	public function getNumFields()
		{
		$numFields = oci_num_fields($this->statement);
		$this->throwExceptionIfFalse($numFields, $this->statement);

		return $numFields;
		}

	/**
	 * Returns number of rows affected during statement execution
	 *
	 * @return int
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-num-rows.php
	 */
	public function getNumRows()
		{
		$numRows = oci_num_rows($this->statement);
		$this->throwExceptionIfFalse($numRows, $this->statement);

		return $numRows;
		}

	/**
	 * Sets number of rows to be prefetched by queries
	 *
	 * @param int $rows
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-set-prefetch.php
	 */
	public function setPrefetch($rows)
		{

		}

	/**
	 * Returns the type of a statement
	 *
	 * @return string
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-statement-type.php
	 */
	public function getType()
		{

		}

	/**
	 * Bind new cursor to param name
	 * @param $paramName
	 * @return bool
	 * @throws Oci8Exception
	 */
	public function setCursor($paramName): Oci8Statement
		{
		$this->cursor = $this->connection->getNewCursor();
		$this->bindByName($paramName, $this->cursor, -1, OCI_B_CURSOR);
		//return true;
		return $this;
		}
	}
