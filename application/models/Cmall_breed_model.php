<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall breed model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Cmall_breed_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'cmall_breed';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'ced_id'; // 사용되는 테이블의 프라이머리키

    function __construct()
    {
        parent::__construct();
    }


    public function get_all_breed()
    {
        $cachename = 'cmall-breed-all';
        if ( ! $result = $this->cache->get($cachename)) {
            $return = $this->get($primary_value = '', $select = '', $where = '', $limit = '', $offset = 0, $findex = 'ced_order', $forder = 'asc');
            if ($return) {
                foreach ($return as $key => $value) {
                    $result[$value['ced_parent']][] = $value;
                }
                $this->cache->save($cachename, $result);
            }
        }
        return $result;
    }


    public function get_breed_info($ced_id = 0)
    {
        $ced_id = (int) $ced_id;
        if (empty($ced_id) OR $ced_id < 1) {
            return;
        }
        $cachename = 'cmall-breed-detail';
        if ( ! $result = $this->cache->get($cachename)) {
            $return = $this->get($primary_value = '', $select = '', $where = '', $limit = '', $offset = 0, $findex = 'ced_order', $forder = 'asc');
            if ($return) {
                foreach ($return as $key => $value) {
                    $result[$value['ced_id']] = $value;
                }
                $this->cache->save($cachename, $result);
            }
        }
        return isset($result[$ced_id]) ? $result[$ced_id] : '';
    }


    public function get_breed($cit_id = 0)
    {
        $cit_id = (int) $cit_id;
        if (empty($cit_id) OR $cit_id < 1) {
            return;
        }

        $this->db->select('cmall_breed.*');
        $this->db->join('cmall_breed_rel', 'cmall_breed.ced_id = cmall_breed_rel.ced_id', 'inner');
        $this->db->where(array('cmall_breed_rel.cit_id' => $cit_id));
        $this->db->order_by('ced_order', 'asc');
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }
}
