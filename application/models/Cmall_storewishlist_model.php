<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall storewishlist model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Cmall_storewishlist_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'cmall_storewishlist';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'csi_id'; // 사용되는 테이블의 프라이머리키

    function __construct()
    {
        parent::__construct();
    }


    public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {
        $select = 'cmall_storewishlist.*, member.mem_id, member.mem_userid, member.mem_nickname, member.mem_is_admin,
            member.mem_icon, board.brd_name, board.brd_key, board.brd_image';
        $join[] = array('table' => 'board', 'on' => 'cmall_storewishlist.brd_id = board.brd_id', 'type' => 'inner');
        $join[] = array('table' => 'member', 'on' => 'cmall_storewishlist.mem_id = member.mem_id', 'type' => 'left');
        $result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
        return $result;
    }


    public function get_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {
        $select = 'cmall_storewishlist.*, board.brd_name, board.brd_key, board.brd_image';
        $join[] = array('table' => 'board', 'on' => 'cmall_storewishlist.brd_id = board.brd_id', 'type' => 'inner');
        $result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
        return $result;
    }


    public function get_rank($start_date = '', $end_date = '')
    {
        if (empty($start_date) OR empty($end_date)) {
            return false;
        }

        $this->db->where('left(csi_datetime, 10) >=', $start_date);
        $this->db->where('left(csi_datetime, 10) <=', $end_date);
        $this->db->select('cmall_storewishlist.brd_id, board.brd_name');
        $this->db->join('board', 'cmall_storewishlist.brd_id = board.brd_id', 'inner');
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }
}
