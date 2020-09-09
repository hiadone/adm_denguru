<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Board model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Board_model extends CB_Model
{

	/**
	 * 테이블명
	 */
	public $_table = 'board';

	/**
	 * 사용되는 테이블의 프라이머리키
	 */
	public $primary_key = 'brd_id'; // 사용되는 테이블의 프라이머리키

	public $cache_prefix = 'board/board-model-get-'; // 캐시 사용시 프리픽스

	public $cache_time = 86400; // 캐시 저장시간

	public $allow_order = array();

	public $_join = array();

	function __construct()
	{
		parent::__construct();

		check_cache_dir('board');
	}


	public function get_board_list($where = '')
	{
		$result = $this->get('', '', $where, '', 0, 'brd_order', 'ASC');
		return $result;
	}


	public function get_one($primary_value = '', $select = '', $where = '')
	{
		$use_cache = false;
		if ($primary_value && empty($select) && empty($where)) {
			$use_cache = true;
		}

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


	public function delete($primary_value = '', $where = '')
	{
		if (empty($primary_value)) {
			return false;
		}
		$result = parent::delete($primary_value);
		$this->cache->delete($this->cache_prefix . $primary_value);
		return $result;
	}


	public function update($primary_value = '', $updatedata = '', $where = '')
	{
		if (empty($primary_value)) {
			return false;
		}

		$result = parent::update($primary_value, $updatedata);
		if ($result) {
			$this->cache->delete($this->cache_prefix . $primary_value);
		}
		return $result;
	}


	public function get_group_select($bgr_id = 0)
	{
		$bgr_id = (int) $bgr_id;

		$option = '<option value="0">그룹선택</option>';
		$this->db->order_by('bgr_order', 'ASC');
		$this->db->select('bgr_id, bgr_name');
		$qry = $this->db->get('board_group');
		foreach ($qry->result_array() as $row) {
			$option .= '<option value="' . $row['bgr_id'] . '"';
			if ((int) $row['bgr_id'] === $bgr_id) {
				$option .= ' selected="selected" ';
			}
			$option .= '>' . $row['bgr_name'] . '</option>';
		}
		return $option;
	}

	public function get_crawl_list($where=array())
	{
		$this->db->select('*');
		$this->db->from($this->_table);
		$this->db->join('board_crawl', 'board.brd_id = board_crawl.brd_id', 'inner');

		if ($where) {
			$this->db->where($where);
		}
		

		
		$qry = $this->db->get();
		$result['list'] = $qry->result_array();

		$this->db->select('count(*) as rownum');
		$this->db->from($this->_table);
		$this->db->join('board_crawl', 'board.brd_id = board_crawl.brd_id', 'inner');
		if ($where) {
			$this->db->where($where);
		}
		
		$qry = $this->db->get();
		$rows = $qry->row_array();
		$result['total_rows'] = $rows['rownum'];

		return $result;
	}

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
						$swordarray = explode(' ', $skeyword);
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
					$swordarray = explode(' ', $skeyword);
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

		$this->db->select('board.brd_name,cmall_item.*');
		$this->db->from($this->_table);
		$this->db->join('cmall_item','board.brd_id = cmall_item.brd_id ','inner');
		// $this->db->join('cmall_brand','cmall_item.cbr_id = cmall_brand.cbr_id ','inner');


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
		$this->db->join('cmall_item','board.brd_id = cmall_item.brd_id ','inner');
		// $this->db->join('cmall_brand','cmall_item.cbr_id = cmall_brand.cbr_id ','inner');
		
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
}


