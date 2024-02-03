<?php
/**
 * Created by PhpStorm.
 * User: RASEL
 * Date: 4/10/2018
 * Time: 12:32 PM
 */

class Utils
{
    function __construct()
    {
        # code...
    }

    public function get_hashed_password($password){
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function verify_user_password($password, $hash){
        if (password_verify($password, $hash)){
            return true;
        }else{
            return false;
        }
    }

    function is_session_started()
    {
      if ( php_sapi_name() !== 'cli' ) {
          if ( version_compare(phpversion(), '5.4.0', '>=') ) {
              return session_status() === PHP_SESSION_ACTIVE ? TRUE : FALSE;
          } else {
              return session_id() === '' ? FALSE : TRUE;
          }
      }
      return FALSE;
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
      }

      public function generateLicenseKey($length = 12) {
        $characters = '23456789ABCDEFGHKLMNPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
      }
}
