<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Board_post class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 게시판 목록과 게시물 열람 페이지에 관한 controller 입니다.
 */
class Make_cache extends CB_Controller
{

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Board');

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array', 'number');

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
        
        $this->load->library(array('pagination','pagination_sub', 'querystring', 'accesslevel', 'videoplayer', 'point'));
    }


    /**
     * 게시판 목록입니다.
     */
    

    public function lists()
    {   
        $this->make_brd_tags_cache();
        $this->make_brd_attr_cache();

        $config = array(
            'cit_type1' => '1',
            'limit' => '5',
            'cache_minute' => 86400,
            // 'select' => $select,
        );
        $this->make_cit_latest_cache($config);

        $config = array(
            'cit_type2' => '2',
            'limit' => '20',
            'cache_minute' => 86400,
            // 'select' => $select,
        );
        $this->make_cit_latest_cache($config);

        $config = array(
            'cit_type1' => '3',
            'limit' => '30',
            'cache_minute' => 86400
        );
        $this->make_cit_latest_cache($config);
        
    }

    public function make_brd_tags_cache()
    {   
        
        

        $where['brd_blind'] = 0;
        // $where['cit_status'] = 1;
        
        

        $result['list'] = $this->Board_model->get_board_list($where);

        if (element('list', $result)) {         
            foreach (element('list', $result) as $key => $val) {                
                $cachename = 'latest/get_popular_brd_tags' . element('brd_id',$val) . '_10';
                $cachetime = 86400;
                $data = array();

                $this->load->model(array('Cmall_item_model'));
                $result = $this->Cmall_item_model->get_popular_tags(element('brd_id',$val), 10);

                $data['result'] = $result;
                $data['cached'] = '1';
                check_cache_dir('latest');
                $this->cache->save($cachename, $data, $cachetime);
                
            }
        }

        
    }


    public function make_brd_attr_cache()
    {   
        
        

        $where['brd_blind'] = 0;
        // $where['cit_status'] = 1;
        
        

        $result['list'] = $this->Board_model->get_board_list($where);

        if (element('list', $result)) {         
            foreach (element('list', $result) as $key => $val) {                
                $cachename = 'latest/get_popular_brd_attr' . element('brd_id',$val) . '_10';
                $cachetime = 86400;
                $data = array();

                $this->load->model(array('Cmall_item_model'));
                $result = $this->Cmall_item_model->get_popular_attr(element('brd_id',$val), 10);

                $data['result'] = $result;
                $data['cached'] = '1';
                check_cache_dir('latest');
                $this->cache->save($cachename, $data, $cachetime);

                $cachename = 'latest/get_popular_brd_attr' . element('brd_id',$val) . '_8';
                $cachetime = 86400;
                $data = array();

                $this->load->model(array('Cmall_item_model'));
                $result = $this->Cmall_item_model->get_popular_attr(element('brd_id',$val), 8);

                $data['result'] = $result;
                $data['cached'] = '1';
                check_cache_dir('latest');
                $this->cache->save($cachename, $data, $cachetime);
                
            }
        }

        
    }


    public function make_cit_latest_cache($config)
    {   

        $this->load->model('Board_model');
            
        $cache_minute = element('cache_minute', $config);
        $where['cit_status'] = 1;
        $where['cit_is_del'] = 0;
        if (element('cit_type1', $config)) {
            $where['cit_type1'] = 1;
            $cit_order = '(0.1/cit_order1)';
        }
        if (element('cit_type2', $config)) {
            $where['cit_type2'] = 1;
            $cit_order = '(0.1/cit_order2)';
        }
        if (element('cit_type3', $config)) {
            $where['cit_type3'] = 1;
            $cit_order = '(0.1/cit_order3)';
        }
        if (element('cit_type4', $config)) {
            $where['cit_type4'] = 1;
            $cit_order = '(0.1/cit_order4)';
        }
        $limit = element('limit', $config) ? element('limit', $config) : 4;

        $cachename = 'latest/cit-order-' . element('cit_type1', $config).element('cit_type2', $config).element('cit_type3', $config).element('cit_type4', $config) . '-' . $limit;

        
        
        $this->db->join('cmall_item', 'board.brd_id = cmall_item.brd_id', 'inner');
        $this->db->join('cmall_brand', 'cmall_item.cbr_id = cmall_brand.cbr_id', 'inner');
        $this->db->where($where);
        $this->db->limit($limit);
        $this->db->order_by($cit_order, 'desc');
        $this->db->order_by('(0.1/cit_order)', 'desc');
        $qry = $this->db->get('board');
        $result = $qry->result_array();
        check_cache_dir('cmall');
        $this->cache->save($cachename, $result, $cache_minute);
        
        return $result;
    }
    
}
