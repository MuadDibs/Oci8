<?php


namespace Oci8;


abstract class Oci8
	{
	//Convert array keys (or object properties) to lowercase/camelCase or leaves as is
	const CASE_UPPER = 'default';
	const CASE_LOWER = 'CASE_LOWER';
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


	public static function camelCaseKeys(array $array, $arrayHolder = []): array
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
	public static function camelCase($string): string
		{
		$stringArray = explode('_', strtolower($string));
		$firstWord   = (isset($stringArray)) ? array_shift($stringArray) : '';
		return $firstWord . implode('', array_map('ucfirst', $stringArray));
		}
	}
