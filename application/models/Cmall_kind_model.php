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

    public function get_kinditme($ckd_id = 0)
    {
        $ckd_id = (int) $ckd_id;
        if (empty($ckd_id) OR $ckd_id < 1) {
            return;
        }

        
        

        $this->db->select('kinditem_rel.*');
        $this->db->join('kinditem_rel', 'cmall_kind.ckd_id = kinditem_rel.ckd_id', 'inner');
        $this->db->where(array('kinditem_rel.ckd_id' => $ckd_id));
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }

    public function get_admin_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {

        $select = 'cmall_kind.*,sum(IF(cb_kinditem_rel.kir_id, 1, 0)) as kinditem_count';
        $join[] = array('table' => 'cb_kinditem_rel', 'on' => 'cb_kinditem_rel.ckd_id = cmall_kind.ckd_id', 'type' => 'left');

        

        $forder = (strtoupper($forder) === 'ASC') ? 'ASC' : 'DESC';
        $sop = (strtoupper($sop) === 'AND') ? 'AND' : 'OR';

        $count_by_where = array();
        $search_where = array();
        $search_like = array();
        $search_or_like = array();
        if ($sfield && is_array($sfield)) {
            foreach ($sfield as $skey => $sval) {
                $ssf = $sval;
                if ($skeyword && $ssf && in_array($ssf, $this->allow_search_field)) {
                    if (in_array($ssf, $this->search_field_equal)) {
                        $search_where[$ssf] = $skeyword;
                    } else {
                        $swordarray = explode('abcdef', $skeyword);
                        
                        foreach ($swordarray as $str) {
                            if (empty($ssf)) {
                                continue;
                            }
                            if ($sop === 'AND') {
                                $search_like[] = array($ssf => $str);
                            } else {
                                $search_or_like[] = array($ssf => $str);
                            }
                        }
                    }
                }
            }
        } else {
            $ssf = $sfield;
            if ($skeyword && $ssf && in_array($ssf, $this->allow_search_field)) {
                if (in_array($ssf, $this->search_field_equal)) {
                    $search_where[$ssf] = $skeyword;
                } else {
                    $swordarray = explode('abcdef', $skeyword);
                    
                    foreach ($swordarray as $str) {
                        if (empty($ssf)) {
                            continue;
                        }
                        if ($sop === 'AND') {
                            $search_like[] = array($ssf => $str);
                        } else {
                            $search_or_like[] = array($ssf => $str);
                        }
                    }
                }
            }
        }

        if ($select) {
            $this->db->select($select);
        }
        $this->db->from($this->_table);
        if ( ! empty($join['table']) && ! empty($join['on'])) {
            if (empty($join['type'])) {
                $join['type'] = 'left';
            }
            $this->db->join($join['table'], $join['on'], $join['type']);
        } elseif (is_array($join)) {
            foreach ($join as $jkey => $jval) {
                if ( ! empty($jval['table']) && ! empty($jval['on'])) {
                    if (empty($jval['type'])) {
                        $jval['type'] = 'left';
                    }
                    $this->db->join($jval['table'], $jval['on'], $jval['type']);
                }
            }
        }

        if ($where) {
            $this->db->where($where);
        }
        
        if($this->or_where){
            $this->db->group_start();
                    
            foreach ($this->or_where as $skey => $sval) {
                $this->db->or_where($skey, $sval);
            }
            
            $this->db->group_end();
        }

        if ($this->where_in) {
            foreach($this->where_in as $wval){
                $this->db->where_in(key($wval),$wval[key($wval)]);  
            }
            
        }
        if ($search_where) {
            $this->db->where($search_where);
        }
        if ($like) {
            $this->db->like($like);
        }
        if ($search_like) {
            foreach ($search_like as $item) {
                foreach ($item as $skey => $sval) {
                    $this->db->like($skey, $sval);
                }
            }
        }
        if ($search_or_like) {
            $this->db->group_start();
            foreach ($search_or_like as $item) {
                foreach ($item as $skey => $sval) {
                    $this->db->or_like($skey, $sval);
                }
            }
            $this->db->group_end();
        }
        if ($count_by_where) {
            $this->db->where($count_by_where);
        }

        $this->db->group_by('cmall_kind.ckd_id');

        $this->db->order_by($findex, $forder);
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        $qry = $this->db->get();
        $result['list'] = $qry->result_array();


        

        return $result;

       
    }
}
