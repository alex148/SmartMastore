<?php
/**
 * Created by Alexandre Brosse.
 * User: Alex
 * Date: 24/12/2015
 * Time: 13:03
 */
class DataBaseConnection {


    private $bdd;

    public function __construct(){
        $bdd = new PDO('mysql:host=localhost;dbname=smartmastore;charset=utf8', 'root', '');
    }
}