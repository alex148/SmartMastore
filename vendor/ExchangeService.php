<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 25/12/2015
 * Time: 10:58
 */

require_once '../../vendor/ExchangeConnection.php';

class ExchangeService extends ExchangeConnection{

    public function __construct(){
        parent::__construct();
    }
}