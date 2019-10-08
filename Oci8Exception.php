<?php

namespace Oci8;

class Oci8Exception extends \ErrorException
  {
  protected $sqlText;
  protected $sqlOffset;
  protected $sqlParams;
  protected $sqlTrace;

  /**
   * @param array  $error массив, возвращаемый функцией oci_error
   * @param array  $params параметры sql-запроса
   * @see http://php.net/manual/ru/function.oci-error.php
   */
  public function __construct(array $error, array $params = [])
    {
    //print_r($error);
    $error += ['code' => 0,
               'message' => 'Unknown Oracle error',
               'sqltext' => 'No SQL query',
               'offset' => 0];

    $this->sqlParams = $params;
    $this->sqlText = $error['sqltext'];
    $this->sqlOffset = $error['offset'];

    # Преобразовываем оракловую трассировку в   массив
    $this->sqlTrace = explode("\n", $error['message']);

    # Заполняем стандартные поля класса Exception
    $code = $error['code'];

    $message=$error['message'].PHP_EOL.
             'SQL: '.$this->sqlText;
    /*
    $message = $this->sqlTrace
      ? str_replace("ORA-{$error['code']}:", '', $this->sqlTrace[0])
      : 'Unknown Oracle error';
    */
    //FIXME finish message/params/code/filename stuff
    parent::__construct($message, $code, 1, 'sql', null);
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
  }
