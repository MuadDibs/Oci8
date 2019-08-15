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
    $error = error_get_last();
    return $error;
    }
  
  protected function throwExceptionIfFalse($result, $resource = null)
    {
    if (false === $result || $result === null)
      {
      $error = $this->getError($resource);
      throw new Oci8Exception($error);
      }
    }
  
  /*
  protected function throwExceptionIfFalse($result, $handle = null)
    {
    if (false === $result || $result === null)
      {
      $error = $this->getError($handle);
      throw new Oci8Exception($error);
      }
    
    return $this;
    }
  */
  /*
    public function getError()
      {
      set_error_handler($this->getErrorHandler());
      $error = oci_error($this->resource);
      restore_error_handler();
      return $error;
      }*/
  
  /**
   * @return callable
   */
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
