<?php

namespace Oci8;

class Oci8Field
  {
  private $name;
  private $precision;
  private $rawType;
  private $scale;
  private $size;
  private $type;
  private $value; //TODO REMOVE

  public function __construct($name, $value, $size, $precision, $scale, $type, $rawType)
    {
    $this->name = $name;
    $this->precision = $precision;
    $this->rawType = $rawType;
    $this->scale = $scale;
    $this->size = $size;
    $this->type = $type;
    $this->value = $value;
    }
  }
