<?php

if (!defined('ABSPATH')) {
  exit;
}


class Utils
{
  public static function getOption($name)
  {
    $options = get_option('enviamais_partner_api_option_name');

    if (!isset($options[$name])) {
      return null;
    }

    return $options[$name];
  }

  public static function printf_array($format, $arr)
  {
    return call_user_func_array('printf', array_merge((array)$format, $arr));
  }

  public static function getUser()
  {
    return get_option('enviamais_partner_api_user');
  }
  
  public static function setUser($user)
  {
    update_option('enviamais_partner_api_user', $user);
  }
}
