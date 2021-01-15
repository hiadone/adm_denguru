<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall item model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Cmall_item_model extends CB_Model
{

	/**
	 * 테이블명
	 */
	public $_table = 'cmall_item';

	

	/**
	 * 사용되는 테이블의 프라이머리키
	 */
	public $primary_key = 'cit_id'; // 사용되는 테이블의 프라이머리키

	public $allow_order = array('cit_order asc', 'cit_datetime desc', 'cit_datetime asc', 'cit_hit desc', 'cit_hit asc', 'cit_review_count desc',
		'cit_review_count asc', 'cit_review_average desc', 'cit_review_average asc', 'cit_price desc', 'cit_price asc', 'cit_sell_count desc','cit_order asc ,cit_id desc');

	function __construct()
	{
		parent::__construct();
	}


	public function get_latest($config)
	{
		$where['cit_status'] = 1;
		$where['cit_is_del'] = 0;
		if (element('cit_type1', $config)) {
			$where['cit_type1'] = 1;
		}
		if (element('cit_type2', $config)) {
			$where['cit_type2'] = 1;
		}
		if (element('cit_type3', $config)) {
			$where['cit_type3'] = 1;
		}
		if (element('cit_type4', $config)) {
			$where['cit_type4'] = 1;
		}
		$limit = element('limit', $config) ? element('limit', $config) : 4;

		$this->db->select('cmall_item.*');
		$this->db->where($where);
		$this->db->limit($limit);
		$this->db->order_by('(0.1/cit_order)', 'desc');
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();

		return $result;
	}


	/**
	 * List 페이지 커스테마이징 함수
	 */
	public function get_item_list($limit = '', $offset = '', $where = '', $category_id = 0, $orderby = '', $sfield = '', $skeyword = '', $sop = 'OR')
	{

		if ( ! in_array(strtolower($orderby), $this->allow_order)) {
			$orderby = 'cit_id desc';
		}

		$sop = (strtoupper($sop) === 'AND') ? 'AND' : 'OR';
		if (empty($sfield)) {
			$sfield = array('cit_name', 'cit_content');
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

		$this->db->select('cmall_item.*');
		$this->db->from($this->_table);


		if ($this->_join) {
			foreach($this->_join as $jval){
				$this->db->join(element(0,$jval),element(1,$jval),element(2,$jval));	
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

		$category_id = (int) $category_id;
		if ($category_id) {
			$this->db->join('cmall_category_rel', 'cmall_item.cit_id = cmall_category_rel.cit_id', 'inner');
			$this->db->where('cca_id', $category_id);
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

		$this->db->order_by($orderby);
		if ($limit) {
			$this->db->limit($limit, $offset);
		}
		$qry = $this->db->get();
		$result['list'] = $qry->result_array();

		$this->db->select('count(*) as rownum');
		$this->db->from($this->_table);
		if ($this->_join) {
			foreach($this->_join as $jval){
				$this->db->join(element(0,$jval),element(1,$jval),element(2,$jval));	
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
		
		if ($category_id) {
			$this->db->join('cmall_category_rel', 'cmall_item.cit_id = cmall_category_rel.cit_id', 'inner');
			$this->db->where('cca_id', $category_id);
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
		$qry = $this->db->get();
		$rows = $qry->row_array();
		$result['total_rows'] = $rows['rownum'];

		return $result;
	}


	public function update_hit($primary_value = '')
	{
		if (empty($primary_value)) {
			return false;
		}

		$this->db->where($this->primary_key, $primary_value);
		$this->db->set('cit_hit', 'cit_hit+1', false);
		$result = $this->db->update($this->_table);
		return $result;
	}

	public function total_count_by($where = '', $group_by = 'brd_id',$set_where = '')
	{
		
		

		if ($where) {			
			$this->db->where($where);			
		} 
		$this->db->group_by($group_by);
		$this->db->select($group_by.',count(*) as rownum');
		
		$this->db->where(array('cit_is_del' =>0));
		$this->db->where(array('cit_status' =>1));
		// if ($or_where) {
		// 	$this->db->group_start();
		// 	$this->db->or_where($or_where);
		// 	$this->db->group_end();
		// }

		if ($set_where) {			
			
				$this->db->where($set_where, '',false);				
			
		}
		
		$this->db->from($this->_table);
		$qry = $this->db->get();
		$result = $qry->result_array();
		return $result;
	}


	
	public function get_popular_tags($brd_id = 0, $limit = '')
    {
        $this->db->select('count(*) as cnt, cta_tag ', false);
        $this->db->from('cmall_item');
        $this->db->join('crawl_tag', 'crawl_tag.cit_id = cmall_item.cit_id', 'inner');
        // $this->db->where('left(crawl_datetime, 10) >=', $start_date);
        if($brd_id)
            $this->db->where('cmall_item.brd_id', $brd_id);
        $this->db->where('cit_status', 1);
        $this->db->where('cit_is_del', 0);
        $this->db->group_by('cta_tag');
        $this->db->order_by('cnt', 'desc');
        if ($limit) {
            $this->db->limit($limit);
        }
        $qry = $this->db->get();
        $result = $qry->result_array();

        return $result;
    }
	
	public function get_popular_attr($brd_id = 0, $limit = '')
    {
        $this->db->select('count(*) as cnt, cat_value,cmall_attr.cat_id ', false);
        $this->db->from('cmall_item');
        $this->db->join('cmall_attr_rel', 'cmall_attr_rel.cit_id = cmall_item.cit_id', 'inner');
        $this->db->join('cmall_attr', 'cmall_attr.cat_id = cmall_attr_rel.cat_id', 'inner');                

        // $this->db->where('left(crawl_datetime, 10) >=', $start_date);
        if($brd_id)
            $this->db->where('cmall_item.brd_id', $brd_id);
        $this->db->where('cit_status', 1);
        $this->db->where('cit_is_del', 0);
        $this->db->where('cat_parent >', 0);        
        $this->db->where_in('cmall_attr.cat_id',array(4,5,6) );        
        $this->db->group_by('cat_value');        
        $this->db->order_by('cnt', 'desc');
        if ($limit) {
            $this->db->limit(3);
        }
        $qry = $this->db->get();
        $result = $qry->result_array();


        $this->db->select('count(*) as cnt, cca_value,cmall_category.cca_id ', false);
        $this->db->from('cmall_item');        
        $this->db->join('cmall_category_rel', 'cmall_category_rel.cit_id = cmall_item.cit_id', 'inner');
        $this->db->join('cmall_category', 'cmall_category.cca_id = cmall_category_rel.cca_id', 'inner');        
        

        // $this->db->where('left(crawl_datetime, 10) >=', $start_date);
        if($brd_id)
            $this->db->where('cmall_item.brd_id', $brd_id);
        $this->db->where('cit_status', 1);
        $this->db->where('cit_is_del', 0);
        $this->db->where('cca_parent >', 0);        
        $this->db->group_by('cca_value');        
        $this->db->order_by('cnt', 'desc');
        if ($limit) {
            $this->db->limit(3);
        }
        $qry = $this->db->get();
        $result_ = $qry->result_array();

        foreach($result_ as $val){
            array_push($result,$val);
        }
        
        $this->db->select('count(*) as cnt, ckd_value_kr as ckd_value,cmall_kind.ckd_id ', false);
        $this->db->from('cmall_item');
        $this->db->join('cmall_kind_rel', 'cmall_kind_rel.cit_id = cmall_item.cit_id', 'inner');
        $this->db->join('cmall_kind', 'cmall_kind.ckd_id = cmall_kind_rel.ckd_id', 'inner');        

        // $this->db->where('left(crawl_datetime, 10) >=', $start_date);
        if($brd_id)
            $this->db->where('cmall_item.brd_id', $brd_id);
        $this->db->where('cit_status', 1);
        $this->db->where('cit_is_del', 0);
        $this->db->where('ckd_parent', 0);        
        $this->db->group_by('ckd_value_kr');
        
        $this->db->order_by('cnt', 'desc');
        if ($limit) {
            $this->db->limit(3);
        }
        $qry = $this->db->get();
        $result_ = $qry->result_array();

        foreach($result_ as $val){
            array_push($result,$val);
        }

        return $result;
    }
}
