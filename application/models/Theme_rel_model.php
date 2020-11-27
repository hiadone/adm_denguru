<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall kind rel model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Theme_rel_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'theme_rel';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'thr_id'; // 사용되는 테이블의 프라이머리키

    function __construct()
    {
        parent::__construct();
    }


    public function save_theme($the_id = 0, $theme = '')
    {
        $the_id = (int) $the_id;
        if (empty($the_id) OR $the_id < 1) {
            return;
        }
        // $deletewhere = array(
        //     'the_id' => $the_id,
        // );
        // $this->delete_where($deletewhere);

        if ($theme) {
            foreach ($theme as $cval) {
                $insertdata = array(
                    'the_id' => $the_id,
                    'brd_id' => $cval,
                );
                if(empty($this->count_by($insertdata)))
                    $this->insert($insertdata);
            }
        }
    }

    public function delete_theme($the_id = 0, $theme = '')
    {
        $the_id = (int) $the_id;
        if (empty($the_id) OR $the_id < 1) {
            return;
        }
        // $deletewhere = array(
        //     'the_id' => $the_id,
        // );
        

        if ($theme) {
            foreach ($theme as $cval) {
                $deletewhere = array(
                    'the_id' => $the_id,
                    'brd_id' => $cval,
                );
                $this->delete_where($deletewhere);
            }
        }
    }
}
