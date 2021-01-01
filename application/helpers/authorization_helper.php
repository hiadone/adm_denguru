<?php

class AUTHORIZATION
{
    public static function validateTimestamp($token)
    {
        $CI =& get_instance();
        $token = self::validateToken($token);        

        if ($token != false && !empty($token->timestamp) && (ctimestamp() - $token->timestamp < ($CI->config->item('token_timeout') * 60))) {
            return $token;
        } elseif($token != false){
            return 'expiration';
        }

        return false;
    }

    public static function validateToken($token)
    {
        $CI =& get_instance();
        return JWT::decode($token, $CI->config->item('jwt_key'));
    }

    public static function generateToken($data)
    {   
        
        $CI =& get_instance();
        $CI->load->helpers(array('JWT'));
        
        return JWT::encode($data, $CI->config->item('jwt_key'));
    }

}