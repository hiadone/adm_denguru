<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cmall kind rel model class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

class Other_rel_model extends CB_Model
{

    /**
     * 테이블명
     */
    public $_table = 'other_rel';

    /**
     * 사용되는 테이블의 프라이머리키
     */
    public $primary_key = 'otr_id'; // 사용되는 테이블의 프라이머리키

    function __construct()
    {
        parent::__construct();
    }


    public function save_other($oth_id = 0, $other = '')
    {
        $oth_id = (int) $oth_id;
        if (empty($oth_id) OR $oth_id < 1) {
            return;
        }
        // $deletewhere = array(
        //     'oth_id' => $oth_id,
        // );
        // $this->delete_where($deletewhere);

        if ($other) {
            foreach ($other as $cval) {
                $insertdata = array(
                    'oth_id' => $oth_id,
                    'brd_id' => $cval,
                );

                if(empty($this->count_by($insertdata)))
                    $this->insert($insertdata);
            }
        }
    }

    public function delete_other($oth_id = 0, $other = '')
    {
        $oth_id = (int) $oth_id;
        if (empty($oth_id) OR $oth_id < 1) {
            return;
        }
        // $deletewhere = array(
        //     'oth_id' => $oth_id,
        // );
        

        if ($other) {
            foreach ($other as $cval) {
                $deletewhere = array(
                    'oth_id' => $oth_id,
                    'brd_id' => $cval,
                );
                $this->delete_where($deletewhere);
            }
        }
    }
}
