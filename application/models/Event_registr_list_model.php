<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Event registr list model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Event_registr_list_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'event_registr_list';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'erl_id'; // 사용되는 테이블의 프라이머리키

    public $_select = 'erl_id,eve_id,mem_id,erl_mobileno,erl_datetime,erl_ip,erl_status,erl_event_result'; // 사용되는 테이블의 프라이머리키

    public $cache_prefix = 'event/event-registr-list-model-get-'; // 캐시 사용시 프리픽스

    public $cache_time = 86400; // 캐시 저장시간

    function __construct()
    {
        parent::__construct();

        check_cache_dir('event');
    }


    public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {
        $result = $this->_get_list_common($select = '', $join = '', $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
        return $result;
    }


    public function get_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {   
        $select = $this->_select;
        $result = $this->_get_list_common($select, $join = '', $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
        return $result;
    }


    
}
