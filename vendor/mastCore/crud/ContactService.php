<?php
/**
 * Created by Alexandre Brosse.
 * User: Alex
 * Date: 24/12/2015
 * Time: 14:19
 */
require_once 'DataBaseConnection.php';
require_once 'AddressService.php';
require_once 'TypeService.php';

class ContactService extends DataBaseConnection {

    private $addressService;
    private $typeService;
    public function __construct(){
        parent::__construct();
        $this->addressService = new AddressService();
        $this->typeService = new TypeService();
    }

    public function getAllContacts(){
        try {
            $list = [];
            if (!parent::getBdd()->inTransaction()) {
                parent::getBdd()->beginTransaction();
            }
            $query = "SELECT * FROM CONTACT";
            $response = parent::getBdd()->query($query);
            while ($data = $response->fetch()) {
                $contact = new Contact();
                if(isset($data['id'])){
                    $contact->setId($data['id']);
                }
                if(isset($data['firstName'])){
                    $contact->setFirstName($data['firstName']);
                }
                if(isset($data['name'])){
                    $contact->setName($data['name']);
                }
                if(isset($data['mail'])){
                    $contact->setMail($data['mail']);
                }
                if(isset($data['phone'])){
                    $contact->setPhone($data['phone']);
                }
                if (isset($data['address'])) {
                    $contact->setAddress($this->addressService->getAddress($data['address']));
                }
                if (isset($data['type'])) {
                    $contact->setType($this->typeService->getType($data['type']));
                }
                array_push($list, $contact);
            }
            $response->closeCursor();
            if ($list == []) {
                return null;
            }
            return $list;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return [];
    }
    
    public function getContact($id){
        
    }
    
    public function addContact(Contact $contact)  //todo gerer type
    {
        try{
            if($contact->getAddress() != null){
                $adrId = $this->addressService->addAddress($contact->getAddress());
                if($adrId != -1){
                    $contact->getAddress()->setId($adrId);
                }else{
                    $contact->getAddress()->setId(null);
                }
            }
            if($contact->getType() != null){
                $typeId = $this->typeService->getTypeByLabel($contact->getType()->getLabel());
                if($typeId != -1){
                    $contact->getType()->setId($typeId);
                }else{
                    $contact->getType()->setId(null);
                }
            }
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            if($contact->getAddress()->getId() != null){
                if($contact->getType()->getId() != null){
                    $query = "INSERT INTO CONTACT VALUES(null,:fName, :name, :mail, :phone, :company, :addrId, :typeId)";

                }else{
                    $query = "INSERT INTO CONTACT VALUES(null,:fName, :name, :mail, :phone, :company, :addrId, null)";
                }
            }else{
                if($contact->getType()->getId() != null){
                    $query = "INSERT INTO CONTACT VALUES(null,:fName, :name, :mail, :phone, :company, null, :typeId)";

                }else{
                    $query = "INSERT INTO CONTACT VALUES(null,:fName, :name, :mail, :phone, :company, null, null)";

                }
            }
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':fName',$contact->getFirstName());
            $request->bindParam(':name',$contact->getName());
            $request->bindParam(':mail',$contact->getMail());
            $request->bindParam(':phone',$contact->getPhone());
            $request->bindParam(':company',$contact->getCompany());
            if($contact->getAddress()->getId() != null) {
                $request->bindParam(':addrId', $contact->getAddress()->getId());
            }
            if($contact->getType()->getId() != null){
                $request->bindParam(':typeId', $contact->getType()->getId());
            }
            $request->execute();
            $id = parent::getBdd()->lastInsertId();
            $request->closeCursor();
            parent::getBdd()->commit();
            return $id;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return -1;
    }
    
    public function updateContact(Contact $contact){
        if($contact != null && ($contact->getId() == null || $contact->getId() == -1)){
            return false;
        }
        try{
            if($contact->getAddress() != null && $contact->getAddress()->getId != null){
                $this->addressService->updateAddress($contact->getAddress());
            }
            if($contact->getType() != null && $contact->getType()->getId() != null){
                $this->typeService->updateType($contact->getType());
            }
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            $query = "UPDATE CONTACT SET firstName = :fName, name = :name, mail = :mail,
                      phone = :phone, company = :company
                      WHERE id = :id";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':id',$contact->getId());
            $request->bindParam(':fName',$contact->getFirstName());
            $request->bindParam(':name',$contact->getName());
            $request->bindParam(':mail',$contact->getMail());
            $request->bindParam(':phone',$contact->getPhone());
            $request->bindParam(':company',$contact->getCompany());
            $request->execute();
            parent::getBdd()->commit();
            $request->closeCursor();
            return true;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return false;
    }

    public function deleteContact(Contact $c){
        if($c != null ($c->getId() == null || $c->getId() == -1)){
            return false;
        }
        try{
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            $query = "DELETE FROM CONTACT WHERE ID = :id";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':id', $id);
            $request->execute();
            parent::getBdd()->commit();
            $request->closeCursor();
            if($c->getAddress() != null && $c->getAddress()->getId() != null){
                $this->addressService->deleteAddress($c->getAddress()->getId());
            }
            if($c->getType() != null && $c->getType()->getId() != null){
                $this->typeService->deleteType($c->getType()->getId());
            }
            return true;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return false;
    }


    
}