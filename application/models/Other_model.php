<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Other model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Other_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'other';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'oth_id'; // 사용되는 테이블의 프라이머리키

    public $cache_time = 86400; // 캐시 저장시간

    function __construct()
    {
        parent::__construct();

        check_cache_dir('other');
    }


    public function get_other($type = '', $limit = '')
    {
        
        if (strtolower($type) !== 'order') {
            $type = 'random';
        }

        $cachename = 'other/other-' . $type . '-' . cdate('Y-m-d');

        if ( ! $result = $this->cache->get($cachename)) {
            $this->db->from($this->_table);
            $this->db->where('oth_activated', 1);
            $this->db->group_start();
            $this->db->where(array('oth_start_date <=' => cdate('Y-m-d')));
            $this->db->or_where(array('oth_start_date' => null));
            $this->db->group_end();
            $this->db->group_start();
            $this->db->where('oth_end_date >=', cdate('Y-m-d'));
            $this->db->or_where('oth_end_date', '0000-00-00');
            $this->db->or_where(array('oth_end_date' => ''));
            $this->db->or_where(array('oth_end_date' => null));
            $this->db->group_end();
            $this->db->order_by('oth_order', 'DESC');
            $res = $this->db->get();
            $result = $res->result_array();

            $this->cache->save($cachename, $result, $this->cache_time);
        }

        if ($type === 'random') {
            shuffle($result);
        }
        if ($limit) {
            $result = array_slice($result, 0, $limit);
        }
        return $result;
    }

    public function get_other_rel($oth_id = 0)
    {
        $oth_id = (int) $oth_id;
        if (empty($oth_id) OR $oth_id < 1) {
            return;
        }

        $this->db->select('other_rel.*');
        $this->db->join('other_rel', 'other.oth_id = other_rel.oth_id', 'inner');
        $this->db->where(array('other_rel.oth_id' => $oth_id));
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }
}
