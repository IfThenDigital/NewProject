<?php

namespace IfThen\Utility\Utility;

class Handy {

  public static function removeNewlines($string) : string {
    return preg_replace('/\s+/', ' ', trim($string));
  }

  public static function jsonEncode($data) : string {
    return json_encode($data, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
  }

  public static function jsonDecode($data_string) : array {
    return json_decode($data_string, TRUE);
  }

  public static function numberToRoman($number) {
    $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
    $returnValue = '';
    while ($number > 0) {
      foreach ($map as $roman => $int) {
        if($number >= $int) {
          $number -= $int;
          $returnValue .= $roman;
          break;
        }
      }
    }
    return $returnValue;
  }

}