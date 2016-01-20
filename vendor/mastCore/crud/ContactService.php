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
                if(isset($data['company'])){
                    $contact->setCompany($data['company']);
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
                if (isset($data['exchangeId'])) {
                    $contact->setExchangeId($data['exchangeId']);
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
    
    public function addContact(Contact $contact)
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
                if($contact->getType()->getId() == null || $contact->getType()->getId() == -1){
                    $typeId = $this->typeService->getTypeIdByLabel($contact->getType()->getLabel());
                    if($typeId != -1){
                        $contact->getType()->setId($typeId);
                    }else{
                        $contact->setType(null);
                    }
                }
            }

            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }

            $query = "INSERT INTO CONTACT VALUES(null,:fName, :name, :mail, :phone, :company, :addrId, :typeId, :exchangeId)";
            $request = parent::getBdd()->prepare($query);
            if($contact->getFirstName() != null){
                $request->bindParam(':fName',$contact->getFirstName());
            }else{
                $request->bindValue(':fName',null);
            }
            if($contact->getName() != null){
                $request->bindParam(':name',$contact->getName());
            }else{
                $request->bindValue(':name',null);
            }
            if($contact->getMail() != null){
                $request->bindParam(':mail',$contact->getMail());
            }else{
                $request->bindValue(':mail',null);
            }
            if($contact->getPhone() != null){
                $request->bindParam(':phone',$contact->getPhone());
            }else{
                $request->bindValue(':phone',null);
            }
            if($contact->getCompany() != null){
                $request->bindParam(':company',$contact->getCompany());
            }else{
                $request->bindValue(':company',null);
            }
            if($contact->getAddress() != null  && $contact->getAddress()->getId() != null) {
                $request->bindParam(':addrId', $contact->getAddress()->getId());
            }else{
                $request->bindValue(':addrId',null);
            }
            if($contact->getType() != null  && $contact->getType()->getId() != null){
                $request->bindParam(':typeId', $contact->getType()->getId());
            }else{
                $request->bindValue(':typeId',null);
            }
            if($contact->getExchangeId() != null){
                $request->bindParam(':exchangeId',$contact->getExchangeId());
            }else{
                $request->bindValue(':exchangeId',null);
            }

            $request->execute();
            $id = parent::getBdd()->lastInsertId();
            $request->closeCursor();
            parent::getBdd()->commit();
            return $id;
        }catch(PDOException  $e){
            error_log($e->getMessage());
        }
        return -1;
    }
    
    public function updateContact(Contact $contact){
        if($contact != null && ($contact->getId() == null || $contact->getId() == -1)){
            return false;
        }
        try{
            if($contact->getAddress() != null && $contact->getAddress()->getId != null && $contact->getAddress()->getId() != -1){
                $this->addressService->updateAddress($contact->getAddress());
            }
            if($contact->getType() != null && $contact->getType()->getId() != null && $contact->getType()->getId() != -1){
                $this->typeService->updateType($contact->getType());
            }
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            if($contact->getAddress() != null && $contact->getAddress()->getId != null && $contact->getAddress()->getId() != -1 &&
                $contact->getType() != null && $contact->getType()->getId() != null && $contact->getType()->getId() != -1){
                $query = "UPDATE CONTACT SET firstName = :fName, name = :name, mail = :mail,
                      phone = :phone, company = :company, address = :address, type = :type, exchangeId = :exchangeId
                      WHERE id = :id";
            }elseif($contact->getAddress() != null && $contact->getAddress()->getId != null && $contact->getAddress()->getId() != -1){
                $query = "UPDATE CONTACT SET firstName = :fName, name = :name, mail = :mail,
                      phone = :phone, company = :company, address = :address, null, exchangeId = :exchangeId
                      WHERE id = :id";
            }elseif($contact->getType() != null && $contact->getType()->getId() != null && $contact->getType()->getId() != -1){
                $query = "UPDATE CONTACT SET firstName = :fName, name = :name, mail = :mail,
                      phone = :phone, company = :company, null, type = :type, exchangeId = :exchangeId
                      WHERE id = :id";
            }else{
                $query = "UPDATE CONTACT SET firstName = :fName, name = :name, mail = :mail,
                      phone = :phone, company = :company, exchangeId = :exchangeId
                      WHERE id = :id";
            }
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':id',$contact->getId());
            $request->bindParam(':fName',$contact->getFirstName());
            $request->bindParam(':name',$contact->getName());
            $request->bindParam(':mail',$contact->getMail());
            $request->bindParam(':phone',$contact->getPhone());
            $request->bindParam(':company',$contact->getCompany());
            if($contact->getAddress() != null && $contact->getAddress()->getId != null && $contact->getAddress()->getId() != -1){
                $request->bindParam(':address',$contact->getAddress()->getId());
            }
            if($contact->getType() != null && $contact->getType()->getId() != null && $contact->getType()->getId() != -1){
                $request->bindParam(':type',$contact->getType()->getId());
            }
            $request->bindParam(':exchangeId',$contact->getExchangeId());
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
        if($c != null && ($c->getId() == null || $c->getId() == -1)){
            return false;
        }
        try{
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            $query = "DELETE FROM CONTACT WHERE ID = :id";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':id', $c->getId());
            $request->execute();
            parent::getBdd()->commit();
            $request->closeCursor();
            if($c->getAddress() != null && $c->getAddress()->getId() != null && $c->getAddress()->getId() != -1){
                $this->addressService->deleteAddress($c->getAddress()->getId());
            }
            return true;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return false;
    }


    
}