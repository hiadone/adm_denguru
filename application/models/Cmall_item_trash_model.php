<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall item trash model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Cmall_item_trash_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'Cmall_item_trash';

    public $_join = array();

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'cit_id'; // 사용되는 테이블의 프라이머리키

    public $allow_order = array('cit_order asc', 'cit_datetime desc', 'cit_datetime asc', 'cit_hit desc', 'cit_hit asc', 'cit_review_count desc',
        'cit_review_count asc', 'cit_review_average desc', 'cit_review_average asc', 'cit_price desc', 'cit_price asc', 'cit_sell_count desc','cit_order asc ,cit_id desc');

    function __construct()
    {
        parent::__construct();
    }


    
}
