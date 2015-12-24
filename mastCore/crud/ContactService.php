<?php
/**
 * Created by Alexandre Brosse.
 * User: Alex
 * Date: 24/12/2015
 * Time: 14:19
 */
require_once'DataBaseConnection.php';

class ContactService extends DataBaseConnection {

    public function __construct(){
        parent::__construct();
    }

    public function getAllContacts(){
        $list = [];
        $hasTransaction = parent::getBdd()->beginTransaction();
        if($hasTransaction){
            $query = "SELECT * FROM CONTACT";
            $response = parent::getBdd()->query($query);
            while($data = $response->fetch()){
                $contact = new Contact();
                $contact->setId($data['id']);
                $contact->setFirstName($data['firstName']);
                $contact->setName($data['name']);
                $contact->setMail($data['mail']);
                $contact->setPhone($data['phone']);

                //get address
                $query = "SELECT * FROM ADDRESS WHERE ID = :id";
                $request = parent::getBdd()->prepare($query);
                $request->bindParam(':id',$data['address']);
                $request->execute();
                $adrData = $request->fetch();
                $address = new Address();
                $address->setId($adrData['id']);
                $address->setName($adrData['name']);
                $address->setLine1($adrData['line1']);
                $address->setLine2($adrData['line2']);
                $address->setZipCode($adrData['zipcode']);
                $address->setCity($adrData['city']);
                $address->setLatitude($adrData['latitude']);
                $address->setLongitude($adrData['longitude']);
                $request->closeCursor();
                $contact->setAddress($address);

                //getType
                $query = "SELECT * FROM TYPE WHERE ID = :id";
                $request = parent::getBdd()->prepare($query);
                $request->bindParam(':id',$data['type']);
                $request->execute();
                $typeData = $request->fetch();
                $type = new Type($typeData['id'],$typeData['label']);
                $request->closeCursor();
                $contact->setType($type);
                array_push($list,$contact);
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
    
    public function getContact($id){
        
    }
    
    public function addContact(Contact $contact){
        
    }
    
    public function updateContact(Contact $contact){

    }

    public function deleteContact($id){

    }
    
}