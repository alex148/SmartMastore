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

            if($exContacts == null){
                return true;
            }
            if($dbContacts == null){
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
                    if(!$this->databaseService->addContact($exContact)){
                        error_log("Contact creation from Exchange to database error");
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
                        error_log("Contact creation from database to Exchange error");
                        $error = true;
                    }
                }
            }
            return !$error;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return false;

    }
}