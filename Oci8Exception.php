<?php

namespace Oci8;

class Oci8Exception extends \ErrorException implements \JsonSerializable
	{
	protected $sqlText;
	protected $sqlOffset;
	protected $sqlParams;
	protected $sqlTrace;

	/**
	 * @param array $error массив, возвращаемый функцией oci_error
	 * @param array $params параметры sql-запроса
	 *
	 * @see http://php.net/manual/ru/function.oci-error.php
	 */
	public function __construct(array $error, array $params = [])
		{
		//print_r($error);
		$error += ['code'    => 0,
							 'message' => 'Unknown Oracle error',
							 'sqltext' => 'No SQL query',
							 'offset'  => 0];

		$this->sqlParams = $params;
		$this->sqlText   = $error['sqltext'];
		$this->sqlOffset = $error['offset'];

		# Преобразовываем оракловую трассировку в   массив
		$this->sqlTrace = explode("\n", $error['message']);

		# Заполняем стандартные поля класса Exception
		$code = $error['code'];

		$message = 'Error Code    : ' . $code . PHP_EOL .
							 'Error Message : ' . $error['message'] . PHP_EOL .
							 'Position      : ' . $this->sqlOffset . PHP_EOL .
							 'Statement     : ' . $this->sqlText . PHP_EOL;
		/*
			         'Bindings      : ['. print_r($params) . ']' . PHP_EOL .
			         'Trace         : ' . print_r($this->sqlTrace);
*/

		parent::__construct($message, $code);
		}

	public function getSqlText()
		{
		return trim($this->sqlText);
		}

	public function getSqlOffset()
		{
		return $this->sqlOffset;
		}

	public function getSqlParams()
		{
		return $this->sqlParams;
		}

	public function getSqlTrace()
		{
		return $this->sqlTrace;
		}

	public function jsonSerialize()
		{
		$json = ['code'      => $this->code,
						 'sqlParams' => $this->sqlParams,
						 'sqlTrace'  => $this->sqlTrace,
						 'sqlText'   => $this->sqlText,
						 'sqlOffset' => $this->sqlOffset];
		return $json;
		}
	}
