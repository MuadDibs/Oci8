<?php

namespace Oci8;

class Oci8Statement extends Oci8Abstract
	{
	private $connection;
	private $statement;
	private $defaultExecutionMode = OCI_COMMIT_ON_SUCCESS;
	//
	private $params  = [];
	private $cursors = [];
	//
	private $executed  = false;
	private $described = false;
	//TODO remove
	const EXECUTE_AUTO_COMMIT    = 0x02;
	const EXECUTE_DESCRIBE       = 0x01;
	const EXECUTE_NO_AUTO_COMMIT = 0x03;
	//
	const RETURN_LOBS_AS_STRING = 0x02;
	const RETURN_NULLS          = 0x01;

	/**
	 * Oci8Statement constructor.
	 *
	 * @param $statement
	 * @param $defaultExecutionMode
	 *
	 * @throws \Exception
	 */
	public function __construct($statement, $defaultExecutionMode = OCI_COMMIT_ON_SUCCESS, Oci8Connection $connection = null)
		{
		if (!is_resource($statement) || get_resource_type($statement) !== 'oci8 statement')
			{
			throw new \Exception('resource is not an oci8 statement');
			}
		$this->statement            = $statement;
		$this->defaultExecutionMode = $defaultExecutionMode;
		if ($connection !== null) $this->connection = $connection;
		}

	/**
	 * Binds a PHP array to an Oracle PL/SQL array parameter
	 *
	 * @param string $name
	 * @param array  $varArray
	 * @param int    $maxTableLength
	 * @param int    $maxItemLength
	 * @param int    $type
	 *
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
	 * @param mixed  $variable
	 * @param int    $maxLength
	 * @param int    $type
	 *
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-bind-by-name.php
	 */
	public function bindByName($bvName, $variable, $maxLength = -1, $type = SQLT_CHR): Oci8Statement
		{
		if (substr($bvName, 0, 1) !== ':') $bvName = ':' . $bvName;

		$isSuccess = oci_bind_by_name($this->statement, $bvName, $variable, $maxLength, $type);
		$this->throwExceptionIfFalse($isSuccess, $this->statement);

		$this->params[] = [$bvName => $variable];
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
	 * @param mixed  $variable
	 * @param int    $type
	 *
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
	 *
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

		foreach ($this->cursors as &$cursor)
			{
			oci_execute($cursor, OCI_DEFAULT);
			}

		$this->executed = true;
		return $isSuccess;
		}

	public function describe()
		{

		}

	/**
	 * Fetches multiple rows from a query into a two-dimensional array
	 *
	 * @param array $output
	 * @param int   $skip
	 * @param int   $maxRows
	 * @param int   $flags
	 *
	 * @return int
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-fetch-all.php
	 */
	public function fetchAll(&$output, $skip = 0, $maxRows = -1, $flags = 0): ?int
		{
		if (empty($flags))
			{
			$flags = OCI_FETCHSTATEMENT_BY_ROW + OCI_ASSOC;
			}

		$numRows = $this->executeFetchAll($this->statement, $output, $skip, $maxRows, $flags);

		foreach ($this->cursors as $cursorName => $cursor)
			{
			$numRows = $this->executeFetchAll($cursor, $output[$cursorName], null, null, $flags);
			}

		if ((sizeof($output) < 2) && !empty($output[$cursorName]))
			{
			$output = $output[$cursorName];
			}
		return $numRows;
		}

	/**
	 * @param     $output
	 * @param int $skip
	 * @param int $maxRows
	 * @param int $flags
	 *
	 * @throws Oci8Exception
	 */
	private function executeFetchAll($statement, &$output, $skip = 0, $maxRows = -1, $flags = 0)
		{
		/*
		while ($row = $this->executeFetch($statement))
			{
			print_r($row);
			}
		*/

		$numRows = @oci_fetch_all($statement, $output, $skip, $maxRows, $flags);
		/*
		$numRows=0;
		echo 'fetch'.$statement.PHP_EOL;
		while (@oci_fetch($statement))
			{
			echo oci_result($statement, 'ID') . "<br>\n";
			$numRows++;
			}
		*/
		//$this->throwExceptionIfFalse($numRows, $this->statement);
		//return $numRows;
		}

	public function executeFetch($cursor, $fetchMode = null)
		{
		// If not fetchMode was specified, used the default value of or the mode
		// set by the last call to setFetchMode()
		if ($fetchMode === null)
			{
			$fetchMode = 'FETCH_OBJ';
			}

		// Convert array keys (or object properties) to lowercase
		$toLowercase = true;
		// Convert null value to empty string
		$nullToString = false;
		// Convert empty string to null
		$nullEmptyString = false;

		// Determine the fetch mode
		switch ($fetchMode)
			{
			case 'FETCH_OBJ':
				$rs = @oci_fetch_assoc($cursor);
				if ($rs === false)
					{
					return false;
					}
				if ($toLowercase)
					{
					$rs = array_change_key_case($rs);
					}

				if (false)
					{
					if (is_object($this->fetchIntoObject))
						{
						$object = $this->fetchIntoObject;
						}
					else
						{
						// Object to set into has not been set
						return false;
						}
					}
				else
					{
					if ($fetchMode === 'FETCH_OBJ')
						{
						$className = '\stdClass';
						$ctorargs  = [];
						}
					else
						{
						$className = $this->fetchClassName;
						$ctorargs  = $this->fetchCtorArgs;
						}

					if ($ctorargs)
						{
						$reflectionClass = new \ReflectionClass($className);
						$object          = $reflectionClass->newInstanceArgs($ctorargs);
						}
					else
						{
						$object = new $className();
						}
					}

				// Format recordsets values depending on options
				foreach ($rs as $field => $value)
					{
					// convert null to empty string
					if (is_null($value) && $nullToString)
						{
						$rs[$field] = '';
						}

					// convert empty string to null
					if (empty($rs[$field]) && $nullEmptyString)
						{
						$rs[$field] = null;
						}

					// convert LOB to string
					if (false)
						{
						$ociFieldIndex = is_int($field) ? $field : array_search($field, array_keys($rs));
						// oci field type index is base 1.
						if (oci_field_type($this->sth, $ociFieldIndex + 1) == 'ROWID')
							{
							throw new PDOException('ROWID output is not yet supported. Please use ROWIDTOCHAR(ROWID) function as workaround.');
							}
						else
							{
							$object->$field = $this->loadLob($value);
							}
						}
					else
						{
						$object->$field = $value;
						}
					}

				return $object;
			}

		return false;
		}

	/**
	 * Returns the next row from a query as an associative or numeric array
	 *
	 * @param int $mode
	 *
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
	 *
	 * @throws Oci8Exception
	 */
	public function getField($field)
		{

		}

	/**
	 * Frees all resources associated with statement and cursors
	 *
	 * @return bool
	 */
	public function free(): bool
		{
		if ($this->statement === null) return false;

		$isSuccess = $this->freeCursor($this->statement);
		foreach ($this->cursors as $cursorName => $cursor)
			{
			$isSuccess = $this->freeCursor($cursor) && $isSuccess;
			unset($this->cursors[$cursorName]);
			}

		$this->statement = null;

		return $isSuccess;
		}

	/**
	 * Frees all resources associated with statement or cursor
	 *
	 * @return bool
	 * @see http://php.net/manual/en/function.oci-free-statement.php
	 */
	private function freeCursor($cursor): bool
		{
		$isSuccess = @oci_free_statement($cursor);
		//TODO maybe add error handler
		return $isSuccess;
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
	 *
	 * @return bool
	 * @throws Oci8Exception
	 * @see http://php.net/manual/en/function.oci-set-prefetch.php
	 */
	public function setPrefetch($rowsCount): Oci8Statement
		{
		$result = oci_set_prefetch($this->statement, $rowsCount);
		$this->throwExceptionIfFalse($result);
		return $this;
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
	 *
	 * @param $paramName
	 *
	 * @return bool
	 * @throws Oci8Exception
	 */
	public function setCursor($paramName): Oci8Statement
		{
		$cursor                    = $this->connection->getNewCursor();
		$this->cursors[$paramName] = $cursor;
		$this->bindByName($paramName, $cursor, -1, SQLT_RSET); //OCI_B_CURSOR

		//return true;
		return $this;
		}
	}
