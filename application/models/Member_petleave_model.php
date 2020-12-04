<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Member_petleave model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Member_petleave_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'member_petleave';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'pet_id'; // 사용되는 테이블의 프라이머리키

    public $search_sfield = '';

    function __construct()
    {
        parent::__construct();
    }



    public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR',$where_in = '')
    {

        $join = array();
        
        $select = 'member_petleave.*,member.mem_nickname,member.mem_userid';
        $join[] = array('table' => 'member', 'on' => 'member_petleave.mem_id = member.mem_id', 'type' => 'left');
        
        $result = $this->_get_list_common($select = '', $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop,$where_in);

        return $result;
    }


    public function get_leave_count($type = 'd', $start_date = '', $end_date = '', $orderby = 'asc')
    {
        if (empty($start_date) OR empty($end_date)) {
            return false;
        }
        $left = ($type === 'y') ? 4 : ($type === 'm' ? 7 : 10);
        if (strtolower($orderby) !== 'desc') $orderby = 'asc';

        $this->db->select('count(*) as cnt, left(pet_leave_datetime, ' . $left . ') as day ', false);
        $this->db->where('left(pet_leave_datetime, 10) >=', $start_date);
        $this->db->where('left(pet_leave_datetime, 10) <=', $end_date);
        // $this->db->where('mem_denied', 0);
        $this->db->group_by('day');
        $this->db->order_by('pet_leave_datetime', $orderby);
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }
}
