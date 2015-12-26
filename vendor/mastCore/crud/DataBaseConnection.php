<?php
/**
 * Created by Alexandre Brosse.
 * User: Alex
 * Date: 24/12/2015
 * Time: 13:03
 */
abstract class DataBaseConnection {


    private $bdd;

    public function __construct(){
        try {
            $this->bdd = new PDO('mysql:host=localhost;dbname=smartmastore;charset=utf8', 'root', '');
            $this->bdd->exec("SET CHARACTER SET utf8");
        }catch(Exception $e){
            error_log($e->getMessage());
            $bdd=null;
        }
    }

    /**
     * @return PDO
     */
    public function getBdd()
    {
        return $this->bdd;
    }

    /**
     * @param PDO $bdd
     */
    public function setBdd($bdd)
    {
        $this->bdd = $bdd;
    }



}