<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Device model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Device_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'device';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'dev_id'; // 사용되는 테이블의 프라이머리키

    function __construct()
    {
        parent::__construct();
    }

    public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR',$where_in = '')
    {

        $join = array();
        
        $select = 'device.*';        
        $join[] = array('table' => 'member', 'on' => 'member.mem_id = device.mem_id', 'type' => 'inner');
        $join[] = array('table' => 'member_group_member', 'on' => 'member.mem_id = member_group_member.mem_id', 'type' => 'left');

        
        
        $result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop,$where_in);

        return $result;
    }
}
