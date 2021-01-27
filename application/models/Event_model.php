<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Event model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Event_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'event';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'eve_id'; // 사용되는 테이블의 프라이머리키

    public $_select = 'eve_id,eve_start_date,eve_end_date,eve_title,eve_datetime,eve_image,eve_content,eve_activated'; // 사용되는 테이블의 프라이머리키

    public $cache_prefix = 'event/event-model-get-'; // 캐시 사용시 프리픽스

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
        // $select = $this->_select;
        $result = $this->_get_list_common($select='', $join = '', $limit, $offset, $where, $like, $findex, $forder, $sfield, $skeyword, $sop);
        return $result;
    }


    public function get_today_list($egr_id)
    {
        $cachename = 'event/event-info-'.$egr_id.'-'. cdate('Y-m-d');
        $data = array();
        if ( ! $data = $this->cache->get($cachename)) {
            // $this->db->select($this->_select);
            $this->db->from($this->_table);
            $this->db->where('eve_activated', 1);
            $this->db->where('egr_id',$egr_id);
            $this->db->group_start();
            $this->db->where(array('eve_start_date <=' => cdate('Y-m-d')));
            $this->db->or_where(array('eve_start_date' => null));
            $this->db->group_end();
            $this->db->group_start();
            $this->db->where('eve_end_date >=', cdate('Y-m-d'));
            $this->db->or_where('eve_end_date', '0000-00-00');
            $this->db->or_where(array('eve_end_date' => ''));
            $this->db->or_where(array('eve_end_date' => null));
            $this->db->group_end();
            $res = $this->db->get();
            $result['list'] = $res->result_array();

            $data['result'] = $result;
            $data['cached'] = '1';

            $this->cache->save($cachename, $data, $this->cache_time);
        }
        return isset($data['result']) ? $data['result'] : false;
    }

    public function get_prev_next_post($post_id = 0, $post_num = 0, $type = '', $where = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {
        $post_id = (int) $post_id;
        if (empty($post_id) OR $post_id < 1) {
            return false;
        }

        $sop = (strtoupper($sop) === 'AND') ? 'AND' : 'OR';
        if (empty($sfield)) {
            $sfield = array('eve_title', 'eve_content');
        }

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

        // $this->db->select($this->_select);
        $this->db->from($this->_table);
        // $this->db->join('member', 'event.mem_id = member.mem_id', 'left');

        if ($type === 'next') {
            $where['eve_id >'] = $post_id;
        } else {
            $where['eve_id <'] = $post_id;
        }

        if ($where) {
            $this->db->where($where);
        }
        
        if ($search_where) {
            $this->db->where($search_where);
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

        $orderby = $type === 'next'
            ? 'eve_id' : 'eve_id desc';

        $this->db->order_by($orderby);
        $this->db->limit(1);
        $qry = $this->db->get();
        $result = $qry->row_array();

        return $result;
    }


    public function delete($primary_value = '', $where = '')
    {
        $result = parent::delete($primary_value, $where);
        $this->cache->delete($this->cache_prefix . $primary_value);

        return $result;
    }


    public function update($primary_value = '', $updatedata = '', $where = '')
    {
        $result = parent::update($primary_value, $updatedata);
        $this->cache->delete($this->cache_prefix . $primary_value);

        return $result;
    }

    public function get_one($primary_value = '', $select = '', $where = '')
    {
        $use_cache = false;
        // if ($primary_value && empty($select) && empty($where)) {
        //     $use_cache = true;
        // }

        if ($use_cache) {
            $cachename = $this->cache_prefix . $primary_value;
            if ( ! $result = $this->cache->get($cachename)) {
                $result = parent::get_one($primary_value);
                $this->cache->save($cachename, $result, $this->cache_time);
            }
        } else {
            $result = parent::get_one($primary_value, $select, $where);
        }
        return $result;
    }

    public function get_event($eve_id = 0)
    {
        $eve_id = (int) $eve_id;
        if (empty($eve_id) OR $eve_id < 1) {
            return;
        }

        $this->db->select('event_rel.*');
        $this->db->join('event_rel', 'event.eve_id = event_rel.eve_id', 'inner');
        $this->db->where(array('event_rel.eve_id' => $eve_id));
        $qry = $this->db->get($this->_table);
        $result = $qry->result_array();

        return $result;
    }

    public function get_item_list($limit = '', $offset = '', $where = '', $like = '', $findex = '', $forder = '', $sfield = '', $skeyword = '', $sop = 'OR')
    {

        $select = 'cmall_item.*,event.*,event_rel.*,board.*';        
        $join[] = array('table' => 'event_rel', 'on' => 'event_rel.eve_id = event.eve_id', 'type' => 'inner');
        $join[] = array('table' => 'cmall_item', 'on' => 'cmall_item.cit_id = event_rel.cit_id', 'type' => 'inner');
        $join[] = array('table' => 'board', 'on' => 'board.brd_id = cmall_item.brd_id', 'type' => 'inner');

        

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
