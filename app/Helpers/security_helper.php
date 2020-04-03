<?php

if (! function_exists('safe_b64encode'))
{
  function safe_b64encode($string) {
    $data = base64_encode($string);
    $data = str_replace(array('+','/','='),array('-','_',''),$data);
    return $data;
  }
}

if (! function_exists('safe_b64decode'))
{
   function safe_b64decode($string) {
      $data = str_replace(array('-','_'),array('+','/'),$string);
      $mod4 = strlen($data) % 4;
      if ($mod4) {
          $data .= substr('====', $mod4);
      }
      return base64_decode($data);
  }
}

if (! function_exists('encode'))
{
  function encode($value){
    if(!$value){return false;}
    $skey = "sygaTechnology";
    $text = $value;
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $skey, $text, MCRYPT_MODE_ECB, $iv);
    return trim(safe_b64encode($crypttext));
  }
}

if (! function_exists('decode'))
{
  function decode($value){
    if(!$value){return false;}
    $skey = "sygaTechnology";
    $crypttext = safe_b64decode($value);
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $skey, $crypttext, MCRYPT_MODE_ECB, $iv);
    return trim($decrypttext);
  }
}
