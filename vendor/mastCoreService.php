<?php
/**
 * Created by Alexandre Brosse
 * User: Alex
 * Date: 25/12/2015
 * Time: 21:52
 */

require_once 'mastCore/crud/ContactService.php';
require_once 'ExchangeService.php';
require_once 'mastCore/model/Contact.php';

class MastCoreService {

    private $databaseService;
    private $exchangeService;

    public function __construct(){
        $this->databaseService = new ContactService();
        $this->exchangeService = new ExchangeService();
    }

    public function synchronization(){
        try{
            $error = false;
            $dbContacts = $this->databaseService->getAllContacts();
            $exContacts = $this->exchangeService->getAllContacts();

            if($exContacts == null && $dbContacts != null){
                foreach($dbContacts as $contact){
                    $this->exchangeService->addContact($contact);
                }
                return true;
            }
            if($dbContacts == null && $exContacts != null){
                foreach($exContacts as $contact){
                    $this->databaseService->addContact($contact);
                }
                return true;
            }
            //check that all Exchange contacts are in database
            foreach($exContacts as $exContact){

                $existAlready = false;
                foreach($dbContacts as $dbContact){
                    if(strcasecmp($dbContact->getMail(),$exContact->getMail()) == 0 && strcasecmp($dbContact->getPhone(),$exContact->getPhone()) == 0){
                        $existAlready = true;
                        break;
                    }
                }
                if(!$existAlready){
                    if($this->databaseService->addContact($exContact) == -1){
                        error_log("Contact creation from Exchange to database failed. Contact [".$exContact->getFirstName()." ".$exContact->getName()." ]" );
                        $error = true;
                    }
                }
            }
            //check that all database contacts are in Exchange
            foreach($dbContacts as $dbContact){
                $existAlready = false;
                foreach($exContacts as $exContact){
                    if(strcasecmp($dbContact->getMail(),$exContact->getMail()) == 0 && strcasecmp($dbContact->getPhone(),$exContact->getPhone()) == 0){
                        $existAlready = true;
                        break;
                    }
                }
                if(!$existAlready){
                    if(!$this->exchangeService->addContact($dbContact)){
                        error_log("Contact creation from database to Exchange error. Contact [".$dbContact->getFirstName()." ".$dbContact->getName()." ]");
                        $error = true;
                    }else{
                        if($dbContact->getExchangeId() != null){
                            $this->databaseService->updateContact($dbContact);
                        }
                    }
                }
            }
            return !$error;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return true;

    }
}