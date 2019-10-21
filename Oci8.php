<?php

namespace Oci8;

abstract class Oci8
	{
	const CASE_UPPER = 1;
	const CASE_LOWER = 0;
	const CASE_CAMEL = 'CASE_CAMEL';

	//
	const FETCH_BOTH   = OCI_BOTH;
	const FETCH_ASSOC  = OCI_ASSOC;
	const FETCH_NUM    = OCI_NUM;
	const FETCH_COLUMN = OCI_FETCHSTATEMENT_BY_COLUMN;
	const FETCH_OBJ    = 'FETCH_OBJ';
	const FETCH_CLASS  = 'FETCH_OBJ';

	//
	const NULL_TO_STRING    = '';
	const NULL_EMPTY_STRING = '';

	//

	public function getError($handle = null): array
		{
		$error = is_resource($handle) ? oci_error($handle) : oci_error();
		if (!$error)
			{
			$error = error_get_last();
			}
		return $error;
		}

	/**
	 * @param      $result
	 * @param null $resource
	 *
	 * @throws Oci8Exception
	 */
	protected function throwExceptionIfFalse($result, $resource = null)
		{
		if (false === $result || $result === null)
			{
			$error = $this->getError($resource);
			throw new Oci8Exception($error); //add params
			}
		}

	//utility

	protected static function formatKeys(array $rs, $caseConvert)
		{
		if ($caseConvert === Oci8::CASE_CAMEL)
			{
			$rs = oci8::camelCaseKeys($rs);
			}
		elseif ($caseConvert === Oci8::CASE_LOWER)
			{
			$rs = array_change_key_case($rs);
			}
		return $rs;
		}

	protected static function camelCaseKeys(array $array, $arrayHolder = []): array
		{
		$camelCaseArray = !empty($arrayHolder) ? $arrayHolder : [];
		foreach ($array as $key => $val)
			{
			$newKey = $newKey = self::camelCase($key);;
			$camelCaseArray[$newKey] = $val;
			}
		return $camelCaseArray;
		}

	/**
	 * Format string to camelCase (each words first letter uppercase, except first word)
	 *
	 * @param String $string
	 *
	 * @return String
	 */
	protected static function camelCase($string): string
		{
		$stringArray = explode('_', strtolower($string));
		$firstWord   = (isset($stringArray)) ? array_shift($stringArray) : '';
		return $firstWord . implode('', array_map('ucfirst', $stringArray));
		}
	}
