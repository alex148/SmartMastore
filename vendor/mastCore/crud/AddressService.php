<?php

/**
 * Created by Alexandre Brosse.
 * User: Alex
 * Date: 24/12/2015
 * Time: 14:20
 */
require_once 'DataBaseConnection.php';
require_once 'GoogleMapService.php';


class AddressService extends DataBaseConnection {

    private $googleMapService;

    public function __construct(){
        parent::__construct();
        $this->googleMapService = new GoogleMapService();
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
        try{
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            $query = "SELECT * FROM ADDRESS WHERE ID = :id";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':id',$id);
            $request->execute();
            $adrData = $request->fetch();
            $address = new Address();
            $address->setId($adrData['id']);
            $address->setLine1($adrData['line1']);
            $address->setLine2($adrData['line2']);
            $address->setZipCode($adrData['zipcode']);
            $address->setCity($adrData['city']);
            $address->setLatitude($adrData['latitude']);
            $address->setLongitude($adrData['longitude']);
            $request->closeCursor();
            return $address;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return null;

    }

    public function addAddress(Address $address){
        try{
            if($address != null){
                $latLong = [];
                if(($address->getLatitude() == null && $address->getLongitude() == null) ||
                    ($address->getLatitude() == 0.0 && $address->getLongitude() == 0.0)){
                    $latLong = $this->googleMapService->getLatLong($address);
                    if(sizeof($latLong) == 2){
                        $address->setLatitude($latLong[0]);
                        $address->setLongitude($latLong[1]);
                    }
                }
                if(!parent::getBdd()->inTransaction()){
                    parent::getBdd()->beginTransaction();
                }
                $query = "INSERT INTO ADDRESS VALUES (NULL,:line1, :line2, :zipcode, :city, :lat, :long)";
                $request = parent::getBdd()->prepare($query);
                $request->bindParam(':line1',$address->getLine1());
                $request->bindParam(':line2',$address->getLine2());
                $request->bindParam(':zipcode',$address->getZipCode());
                $request->bindParam(':city',$address->getCity());
                $request->bindParam(':lat', $address->getLatitude());
                $request->bindParam(':long', $address->getLongitude());
                $request->execute();
                $id = parent::getBdd()->lastInsertId();
                $request->closeCursor();
                parent::getBdd()->commit();
                return $id;
             }
        }catch(Exception $e){
            error_log($e->getMessage());

        }
        return -1;
    }

    public function updateAddress(Address $address){
        try{
            if($address != null && ($address->getId() == null || $address->getId() == -1)){
                return false;
            }
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            $query = "UPDATE ADDRESS SET line1 = :line1, line2 = :line2
                      , zipcode = :zip, city = :city, latitude = :lat, longitude = :long
                      WHERE id = :id";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':id',$address->getId());
            if($address->getLine1() != null){
                $request->bindParam(':line1',$address->getLine1());
            }else{
                $request->bindValue(':line1',null);
            }
            if($address->getLine2() != null){
                $request->bindParam(':line2',$address->getLine2());
            }else{
                $request->bindValue(':line2',null);
            }
            if($address->getZipCode() != null){
                $request->bindParam(':zip',$address->getZipCode());
            }else{
                $request->bindValue(':zip',null);
            }
            if($address->getCity() != null){
                $request->bindParam(':city',$address->getCity());
            }else{
                $request->bindValue(':city',null);
            }
            $latLong = $this->googleMapService->getLatLong($address);
            if(sizeof($latLong) == 2){
                $request->bindParam(':lat', $latLong[0]);
                $request->bindParam(':long', $latLong[1]);

            }else{
                $request->bindValue(':lat', null);
                $request->bindValue(':long', null);
            }
            $request->execute();
            parent::getBdd()->commit();
            $request->closeCursor();
            return true;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return false;
    }

    public function deleteAddress($id){
        try{
            if($id == null || $id == -1){
                return false;
            }
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            $query = "DELETE FROM ADDRESS WHERE id = :id";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':id',$id);
            $request->execute();
            parent::getBdd()->commit();
            $request->closeCursor();
            return true;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return false;
    }


    public function getAddressesByRayon($address, $rayon){
        $list = [];
        try {
            if (!parent::getBdd()->inTransaction()) {
                parent::getBdd()->beginTransaction();
            }
            $query = "SELECT *, ( 6371 * acos( cos( radians(:latitude) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(:longitude) )
              + sin( radians(:latitude) ) * sin( radians( latitude ) ) ) ) AS distance FROM address HAVING distance < :rayon ORDER BY distance LIMIT 0 , 150;";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':latitude', $address->getLatitude());
            $request->bindParam(':longitude', $address->getLongitude());
            $request->bindParam(':rayon', $rayon);
            $request->execute();

            while ($data = $request->fetch()) {
                $address = new Address();
                $address->setId($data['id']);
                $address->setLine1($data['line1']);
                $address->setLine2($data['line2']);
                $address->setZipCode($data['zipcode']);
                $address->setCity($data['city']);
                $address->setLatitude($data['latitude']);
                $address->setLongitude($data['longitude']);
                array_push($list,$address);
            }
            if($list == []){
                return null;
            }
            return $list;

        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return null;
    }
    
}