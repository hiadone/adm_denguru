<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Cron class
 *
 * Copyright (c) CIBoard <www.ciboard.co.kr>
 *
 * @author CIBoard (develop@ciboard.co.kr)
 */

/**
 * 메인 페이지를 담당하는 controller 입니다.
 */
class Cron extends CB_Controller
{

    /**
     * 모델을 로딩합니다
     */
    protected $models = array('Board');

    /**
     * 헬퍼를 로딩합니다
     */
    protected $helpers = array('form', 'array');

    function __construct()
    {
        parent::__construct();

        /**
         * 라이브러리를 로딩합니다
         */
    }
    
}


