<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Kinditem model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Kinditem_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'kinditem';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'kdi_id'; // 사용되는 테이블의 프라이머리키

    function __construct()
    {
        parent::__construct();
    }


    public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {
        $select = 'kinditem.*, cmall_item.*';
        $join[] = array('table' => 'cmall_item', 'on' => 'kinditem.cit_id = cmall_item.cit_id', 'type' => 'inner');
        $result = $this->_get_list_common($select, $join, $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
        return $result;
    }
}
