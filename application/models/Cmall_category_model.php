<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall category model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Cmall_category_model extends CB_Model
{

	/**
	 * 테이블명
	 */
	public $_table = 'cmall_category';

	/**
	 * 사용되는 테이블의 프라이머리키
	 */
	public $primary_key = 'cca_id'; // 사용되는 테이블의 프라이머리키

	function __construct()
	{
		parent::__construct();
	}


	public function get_all_category()
	{
		$cachename = 'cmall-category-all';
		if ( ! $result = $this->cache->get($cachename)) {
			$return = $this->get($primary_value = '', $select = '', $where = '', $limit = '', $offset = 0, $findex = 'cca_order', $forder = 'asc');
			if ($return) {
				foreach ($return as $key => $value) {
					$result[$value['cca_parent']][] = $value;
				}
				$this->cache->save($cachename, $result);
			}
		}
		return $result;
	}


	public function get_category_info($cca_id = 0)
	{
		$cca_id = (int) $cca_id;
		if (empty($cca_id) OR $cca_id < 1) {
			return;
		}
		$cachename = 'cmall-category-detail';
		if ( ! $result = $this->cache->get($cachename)) {
			$return = $this->get($primary_value = '', $select = '', $where = '', $limit = '', $offset = 0, $findex = 'cca_order', $forder = 'asc');
			if ($return) {
				foreach ($return as $key => $value) {
					$result[$value['cca_id']] = $value;
				}
				$this->cache->save($cachename, $result);
			}
		}
		return isset($result[$cca_id]) ? $result[$cca_id] : '';
	}


	public function get_category($cit_id = 0)
	{
		$cit_id = (int) $cit_id;
		if (empty($cit_id) OR $cit_id < 1) {
			return;
		}

		$this->db->select('cmall_category.*');
		$this->db->join('cmall_category_rel', 'cmall_category.cca_id = cmall_category_rel.cca_id', 'inner');
		$this->db->where(array('cmall_category_rel.cit_id' => $cit_id));
		$this->db->order_by('cca_order', 'asc');
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();

		return $result;
	}

	public function get_brdcategory($where = array(),$group_by = 'cmall_item.brd_id',$select = 'count(DISTINCT cb_cmall_category_rel.cit_id) as cnt,brd_id,cca_parent')
	{
		// $post_id = (int) $post_id;
		// if (empty($post_id) OR $post_id < 1) {
		// 	return;
		// }

		
		// if (!empty($brd_id)) 
		// 	$this->db->select('count(*) as cnt,cmall_category_rel.cit_id,post_id,cca_value,cca_parent');
		// else
		// 	$this->db->select('count(DISTINCT cb_cmall_category_rel.cit_id) as cnt,brd_id,cca_parent');
		
		$this->db->select($select);

		$this->db->join('cmall_category_rel', 'cmall_category.cca_id = cmall_category_rel.cca_id', 'inner');
		$this->db->join('cmall_item', 'cmall_item.cit_id = cmall_category_rel.cit_id', 'inner');
		// if (!empty($post_id)) 
		// 	$this->db->where(array('cmall_item.post_id' => $post_id));

		if (!empty($where)) 
			$this->db->where($where);

		$this->db->where(array('cit_is_del' =>0));				
		$this->db->where(array('cit_status' =>1));				
		
		// $this->db->where(array('cmall_category.cca_parent' => 0));
		// $this->db->where_in('cmall_category_rel.cca_id' , array(6,7,8,9,10,11,12,13));
		
		$this->db->order_by('cca_order', 'asc');
		// $this->db->order_by('cmall_category.cca_id', 'desc');

		// if (!empty($brd_id)) 
		// 	$this->db->group_by('cmall_category_rel.cca_id,cmall_category_rel.cit_id');
		// else
		// 	$this->db->group_by('cmall_item.brd_id');
		$this->db->group_by($group_by);
			
		$qry = $this->db->get($this->_table);
		$result = $qry->result_array();

		return $result;
	}

	
}



