<?php

namespace modules\Oci8;

abstract class Oci8Abstract
  {
  protected static $errorHandler;
  
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
   * @throws Oci8Exception
   */
  protected function throwExceptionIfFalse($result, $resource = null)
    {
    if (false === $result || $result === null)
      {
      $error = $this->getError($resource);
      throw new Oci8Exception($error);
      }
    }
  
  //TODO remove me!
  protected static function getErrorHandler()
    {
    if (!static::$errorHandler)
      {
      static::$errorHandler = function ($severity, $message, $file = '', $line = 0) {
        restore_error_handler();
        $code = 0;
        
        $patterns = array('/ORA-(\d+)/', '/OCI-(\d+)/');
        foreach ($patterns as $pattern)
          {
          preg_match($pattern, $message, $matches);
          if (is_array($matches) && array_key_exists(1, $matches))
            {
            $code = (int)$matches[1];
            }
          }
        throw new Oci8Exception($message);
      };
      }
    
    return static::$errorHandler;
    }
  }
