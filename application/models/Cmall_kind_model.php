<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall kind model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Cmall_kind_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'cmall_kind';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'ckd_id'; // 사용되는 테이블의 프라이머리키

    function __construct()
    {
        parent::__construct();
    }


    public function get_all_kind()
    {
        $cachename = 'cmall-kind-all';
        if ( ! $result = $this->cache->get($cachename)) {
            $return = $this->get($primary_value = '', $select = '', $where = '', $limit = '', $offset = 0, $findex = 'ckd_id', $forder = 'asc');
            if ($return) {
                foreach ($return as $key => $value) {
                    $result[$value['ckd_parent']][] = $value;
                }
                $this->cache->save($cachename, $result);
            }
        }
        return $result;
    }


    public function get_kind_info($ckd_id = 0)
    {
        $ckd_id = (int) $ckd_id;
        if (empty($ckd_id) OR $ckd_id < 1) {
            return;
        }
        $cachename = 'cmall-kind-detail-'.$ckd_id;
        if ( ! $result = $this->cache->get($cachename)) {
            $return = $this->get($primary_value = '', $select = '', $where = '', $limit = '', $offset = 0, $findex = 'ckd_order', $forder = 'asc');
            if ($return) {
                foreach ($return as $key => $value) {
                    $result[$value['ckd_id']] = $value;
                }
                $this->cache->save($cachename, $result);
            }
        }
        return isset($result[$ckd_id]) ? $result[$ckd_id] : '';
    }


    public function get_kind($cit_id = 0)
    {
        $cit_id = (int) $cit_id;
        if (empty($cit_id) OR $cit_id < 1) {
            return;
        }

        
        

        $this->db->select('cmall_kind.*');
        $this->db->join('cmall_kind_rel', 'cmall_kind.ckd_id = cmall_kind_rel.ckd_id', 'inner');
        $this->db->where(array('cmall_kind_rel.cit_id' => $cit_id));
        $this->db->order_by('ckd_order', 'asc');
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }
}
