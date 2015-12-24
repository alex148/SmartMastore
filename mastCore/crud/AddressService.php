<?php

/**
 * Created by Alexandre Brosse.
 * User: Alex
 * Date: 24/12/2015
 * Time: 14:20
 */
require_once'DataBaseConnection.php';




class AddressService extends DataBaseConnection {

    public function __construct(){
        parent::__construct();
    }
    
    public function getAllAddresses(){
        $list = [];
        $hasTransaction = parent::getBdd()->beginTransaction();
        if($hasTransaction){
            $query = "SELECT * FROM ADDRESS";
            $response = parent::getBdd()->query($query);
            while($data = $response->fetch()){
                $address = new Address();
                $address->setId($data['id']);
                $address->setName($data['name']);
                $address->setLine1($data['line1']);
                $address->setLine2($data['line2']);
                $address->setZipCode($data['zipcode']);
                $address->setCity($data['city']);
                $address->setLatitude($data['latitude']);
                $address->setLongitude($data['longitude']);
                array_push($list,$address);
            }
            $response->closeCursor();
            if ($list == []){
                return null;
            }
            return $list;
        }
        error_log('Transaction error');
       return null;
    }

    public function getAddress($id){

    }

    public function addAddress(Address $address){
        $address->getCity();
    }

    public function updateAddress(Address $address){

    }

    public function deleteAddress($id){

    }
    
}