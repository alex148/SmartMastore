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
                $contact->setId($data['id']);
                $contact->setFirstName($data['firstName']);
                $contact->setName($data['name']);
                $contact->setMail($data['mail']);
                $contact->setPhone($data['phone']);
                if ($data['address'] != null) {
                    $contact->setAddress($this->addressService->getAddress($data['address']));
                }
                if ($data['type'] != null) {
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
                //todo gerer type

            }
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            if($contact->getAddress()->getId() != null){
                $query = "INSERT INTO CONTACT VALUES(null,:fName, :name, :mail, :phone, :company, :addrId, null)"; //todo add type
            }else{
                $query = "INSERT INTO CONTACT VALUES(null,:fName, :name, :mail, :phone, :company, null, null)"; //todo add type

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
            //$request->bindParam(':typeId',null); //todo
            $request->execute();
            $id = parent::getBdd()->lastInsertId();
            $request->closeCursor();
            parent::getBdd()->commit();
            $contact->setId($id);
            return true;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return false;
    }
    
    public function updateContact(Contact $contact){

    }

    public function deleteContact($id){

    }
    
}