<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Theme model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Theme_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'theme';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'the_id'; // 사용되는 테이블의 프라이머리키

    public $cache_time = 86400; // 캐시 저장시간

    function __construct()
    {
        parent::__construct();

        check_cache_dir('theme');
    }


    public function get_theme($type = '', $limit = '')
    {
        
        if (strtolower($type) !== 'order') {
            $type = 'random';
        }

        $cachename = 'theme/theme-' . $type . '-' . cdate('Y-m-d');

        if ( ! $result = $this->cache->get($cachename)) {
            $this->db->from($this->_table);
            $this->db->where('the_activated', 1);
            $this->db->group_start();
            $this->db->where(array('the_start_date <=' => cdate('Y-m-d')));
            $this->db->or_where(array('the_start_date' => null));
            $this->db->group_end();
            $this->db->group_start();
            $this->db->where('the_end_date >=', cdate('Y-m-d'));
            $this->db->or_where('the_end_date', '0000-00-00');
            $this->db->or_where(array('the_end_date' => ''));
            $this->db->or_where(array('the_end_date' => null));
            $this->db->group_end();
            $this->db->order_by('the_order', 'DESC');
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

    public function get_theme_rel($the_id = 0)
    {
        $the_id = (int) $the_id;
        if (empty($the_id) OR $the_id < 1) {
            return;
        }

        $this->db->select('theme_rel.*');
        $this->db->join('theme_rel', 'theme.the_id = theme_rel.the_id', 'inner');
        $this->db->where(array('theme_rel.the_id' => $the_id));
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }

    public function get_item_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {

        $select = '*';        
        $join[] = array('table' => 'theme_rel', 'on' => 'theme_rel.the_id = theme.the_id', 'type' => 'inner');
        $join[] = array('table' => 'board', 'on' => 'board.brd_id = theme_rel.brd_id', 'type' => 'inner');
        

        

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

        

        $this->db->order_by($findex, $forder);
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        $qry = $this->db->get();
        $result['list'] = $qry->result_array();


        $this->db->select('count(*) as rownum');

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
        if ($search_where) {
            $this->db->where($search_where);
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
        if ($this->set_where) {         
            foreach ($this->set_where as $skey => $sval) {
                $this->db->where($skey, $sval,false);               
            }
        }
        
        // if ($category_id) {
        //  $this->db->join('cmall_category_rel', 'cmall_item.cit_id = cmall_category_rel.cit_id', 'inner');
        //  $this->db->where('cca_id', $category_id);
        // }
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
        $qry = $this->db->get();
        $rows = $qry->row_array();
        $result['total_rows'] = $rows['rownum'];

        return $result;

        return $result;

       
    }
}
