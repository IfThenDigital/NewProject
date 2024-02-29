<?php


namespace IfThen\Utility\Utility;

/**
 * A helpful trait to implement a serialization function.
 */
trait JsonSerializer {

  public function jsonSerialize() {
    return get_object_vars($this);
  }

}