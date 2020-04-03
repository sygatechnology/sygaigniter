<?php

if (! function_exists('set_password'))
{
  function set_password($password) {
    return password_hash($password, PASSWORD_BCRYPT);
  }
}

if (! function_exists('is_valid_password'))
{
   function is_valid_password($password, $hashed_password) {
     return password_verify($password, $hashed_password);
  }
}
