<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall item model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Cmall_brand_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'cmall_brand';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'cbr_id'; // 사용되는 테이블의 프라이머리키

    

    function __construct()
    {
        parent::__construct();
    }
}
