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
            if($contact->getAddress() != null && $contact->getAddress()->getId() != null && $contact->getAddress()->getId() != -1){
                $this->addressService->updateAddress($contact->getAddress());
            }

            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            $query = "UPDATE CONTACT SET firstName = :fName, name = :name, mail = :mail,
                      phone = :phone, company = :company, type = :type, exchangeId = :exchangeId
                      WHERE id = :id";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':id',$contact->getId());
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

            if($contact->getType() != null && $contact->getType()->getId() != null && $contact->getType()->getId() != -1){
                $request->bindParam(':type',$contact->getType()->getId());
            }elseif($contact->getType() != null && ($contact->getType()->getId() == null || $contact->getType()->getId() == -1) && $contact->getType()->getLabel() !=null){
                $typeId = $this->typeService->getTypeIdByLabel($contact->getType()->getLabel());
                if($typeId != -1){
                    $request->bindParam(':type',$typeId);
                }else{
                    $request->bindValue(':type',null);
                }
            }else{
                $request->bindValue(':type',null);
            }
            if($contact->getExchangeId() != null){
                $request->bindParam(':exchangeId',$contact->getExchangeId());
            }else{
                $request->bindValue(':exchangeId',null);
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

    public function parseContact($data){
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
        return $contact;
    }

    public function getContactByCompany($company){
        if($company == null){
            return null;
        }
        $list = [];
        try {
            if (!parent::getBdd()->inTransaction()) {
                parent::getBdd()->beginTransaction();
            }
            $query = "SELECT * FROM CONTACT WHERE company like :company";
            $request = parent::getBdd()->prepare($query);
            $company = '%'.$company.'%';
            $request->bindParam(':company', $company);
            $request->execute();
            while ($data = $request->fetch()) {
                $contact = $this->parseContact($data);
                array_push($list, $contact);
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

    public function getContactByTypeId($typeId){
        $list = [];
        try {
            if (!parent::getBdd()->inTransaction()) {
                parent::getBdd()->beginTransaction();
            }
            $query = "SELECT * FROM CONTACT WHERE type = :type";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':type', $typeId);
            $request->execute();
            while ($data = $request->fetch()) {
                $contact = $this->parseContact($data);
                array_push($list, $contact);
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

    public function getContactsbyAddressAndRayon($address, $rayon, $typeId, $company){
        $addressIds = [];
        $list = [];
        try{
            $addresses = $this->addressService->getAddressesByRayon($address,$rayon);
            if($addresses == []){
                return null;
            }
            foreach($addresses as $data){
                array_push($addressIds,$data->getId());
            }
            if (!parent::getBdd()->inTransaction()) {
                parent::getBdd()->beginTransaction();
            }
            $formatedIds = '';
            $i = 0;
            foreach($addressIds as $id){
                if($i < (count($addressIds)-1)){
                    $formatedIds = $formatedIds.$id.', ';
                }else{
                    $formatedIds = $formatedIds.$id;
                }
                $i++;
            }
            $query = "";
            if($typeId == null && $company == null){
                $query = "SELECT * FROM CONTACT where address in (".$formatedIds.")";
            }elseif($typeId != null && $company == null) {
                $query = "SELECT * FROM CONTACT where address in (".$formatedIds.") and type = ".$typeId;
            }elseif($typeId == null && $company != null){
                $query = "SELECT * FROM CONTACT where address in (".$formatedIds.") and company = ".$company;
            }
            echo $query;
            $response = parent::getBdd()->query($query);
            while ($data = $response->fetch()) {
                $contact = $this->parseContact($data);
                array_push($list, $contact);
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

    public function getContactByName($firstName, $name){
        if($name == null){
            return null;
        }
        $list = [];
        try{
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            if($firstName != null){
                $query = "SELECT * FROM CONTACT WHERE firstName like :firstName and name like :name";
            }else{
                $query = "SELECT * FROM CONTACT WHERE name like :name";
            }
            $request = parent::getBdd()->prepare($query);
            if($firstName != null){
                $firstName = '%'.$firstName.'%';
                $request->bindParam(':firstName', $firstName);
            }
            $name = '%'.$name.'%';
            $request->bindParam(':name', $name);
            $request->execute();
            while ($data = $request->fetch()) {
                $contact = $this->parseContact($data);
                array_push($list, $contact);
            }
            $request->closeCursor();
            if ($list == []) {
                return null;
            }
            return $list;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return null;
    }

    public function searchContacts(SearchObject $search){
        if($search == null ){
            return null;
        }
        //search contact by name
        if($search->getName() != null){
            if($search->getFirstName() != null){
                $result = $this->getContactByName($search->getFirstName(), $search->getName());
            }else{
                echo $search->getFirstName();
                $result = $this->getContactByName(null, $search->getName());
            }
            if($result == []){
                return null;
            }
            return $result;
        }

        //get contact by company
        if($search->getCompany() != null){
            $result = $this->getContactByCompany($search->getCompany());
            if($result == []){
                return null;
            }
            return $result;
        }

        //search by Type
        if($search->getTypeName() != null && $search->getAddress() == null){
            $typeId = $this->typeService->getTypeIdByLabel($search->getTypeName());
            if($typeId != null && $typeId != -1){
                $result = $this->getContactByTypeId($typeId);
                if($result == []){
                    return null;
                }
                return $result;
            }
        }

        //search by address + rayon
        if($search->getAddress() != null && $search->getAddress()->getLatitude() != null &&
            $search->getAddress()->getLongitude() != null && $search->getRayon() != null && $search->getTypeName() == null){
            $result = $this->getContactsbyAddressAndRayon($search->getAddress(), $search->getRayon(),null,null);
            if($result == []){
                return null;
            }
            return $result;
        }
        //search by type + address + rayon
        if($search->getAddress() != null && $search->getAddress()->getLatitude() != null &&
            $search->getAddress()->getLongitude() != null && $search->getRayon() != null && $search->getTypeName() != null){
            $typeId = $this->typeService->getTypeIdByLabel($search->getTypeName());
            if($typeId != null && $typeId != -1){
                $result = $this->getContactsbyAddressAndRayon($search->getAddress(), $search->getRayon(),$typeId,null);
                if($result == []){
                    return null;
                }
                return $result;
            }
        }
        //search by company + address + rayon
        if($search->getAddress() != null && $search->getAddress()->getLatitude() != null &&
            $search->getAddress()->getLongitude() != null && $search->getRayon() != null && $search->getCompany() != null){
            $result = $this->getContactsbyAddressAndRayon($search->getAddress(), $search->getRayon(),null, $search->getCompany());
            if($result == []){
                return null;
            }
            return $result;
        }
        return null;
    }


    
}