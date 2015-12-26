<?php
/**
 * Created by Alexandre Brosse
 * User: Alex
 * Date: 25/12/2015
 * Time: 10:58
 */

require_once 'ExchangeConnection.php';
require_once 'php-ews/EWSType/FindItemType.php';
require_once 'php-ews/EWSType/ItemResponseShapeType.php';
require_once 'php-ews/EWSType/DefaultShapeNamesType.php';
require_once 'php-ews/EWSType/ContactsViewType.php';
require_once 'php-ews/EWSType/NonEmptyArrayOfBaseFolderIdsType.php';
require_once 'php-ews/EWSType/DistinguishedFolderIdType.php';
require_once 'php-ews/EWSType/DistinguishedFolderIdNameType.php';
require_once 'php-ews/EWSType/ItemQueryTraversalType.php';
require_once 'php-ews/NTLMSoapClient/Exchange.php';

class ExchangeService extends ExchangeConnection{


    public function __construct(){
        parent::__construct();
    }

    /**
     * Used to get all the contacts from Exchange
     * @return array of contact
     */
    public function getAllContacts(){
        try{
            $contactList = [];
            $request = new EWSType_FindItemType();

            $request->ItemShape = new EWSType_ItemResponseShapeType();
            $request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ALL_PROPERTIES;

            $request->ContactsView = new EWSType_ContactsViewType();
            $request->ContactsView->InitialName = 'a';
            $request->ContactsView->FinalName = 'z';

            $request->ParentFolderIds = new EWSType_NonEmptyArrayOfBaseFolderIdsType();
            $request->ParentFolderIds->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType();
            $request->ParentFolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::CONTACTS;

            $request->Traversal = EWSType_ItemQueryTraversalType::SHALLOW;

            $response = parent::getEws()->FindItem($request);
            $stdContacts = $response->ResponseMessages->FindItemResponseMessage->RootFolder->Items->Contact;

            foreach ($stdContacts as $c){
                $contact = new Contact();
                if(isset($c->CompleteName)){
                    if(isset($c->CompleteName->FirstName)){
                        $contact->setFirstName($c->CompleteName->FirstName);
                    }
                    if(isset($c->CompleteName->LastName)){
                        $contact->setName($c->CompleteName->LastName);
                    }
                }
                if(isset($c->PhoneNumbers->Entry)){
                    if( is_array($c->PhoneNumbers->Entry)){ //todo gerer multi phone
                        $contact->setPhone($c->PhoneNumbers->Entry[0]->_);
                    }else{
                        $contact->setPhone($c->PhoneNumbers->Entry->_);
                    }
                }
                if(isset($c->EmailAddresses->Entry)){
                    if( is_array($c->EmailAddresses->Entry)){   //todo gerer multi mail
                        $contact->setMail($c->EmailAddresses->Entry[0]->_);
                    }else{
                        $contact->setMail($c->EmailAddresses->Entry->_);
                    }
                }
                if(isset($c->CompanyName)){
                    $contact->setCompany($c->CompanyName);
                }
                if(isset($c->PhysicalAddresses->Entry)){
                    $address = new Address();
                    $stdAddress = $c->PhysicalAddresses->Entry;
                    if(is_array($stdAddress)){ //todo gerer multi adresse
                        $address->setName($stdAddress[0]->Key);
                        $address->setLine1($stdAddress[0]->Street);
                        $address->setZipCode($stdAddress[0]->PostalCode);
                        $address->setCity($stdAddress[0]->City);
                        //     $address->setLongitude($stdAddress[0]->); //todo gerer lat long
                        //     $address->setLatitude($stdAddress[0]->);
                    }else{
                        $address->setName($stdAddress->Key);
                        $address->setLine1($stdAddress->Street);
                        $address->setZipCode($stdAddress->PostalCode);
                        $address->setCity($stdAddress->City);
                        //    $address->setLongitude($stdAddress->);    //todo gerer lat long
                        //    $address->setLatitude($stdAddress->);
                    }
                    $contact->setAddress($address);
                }
                $contact->setType(null);    //todo gerer type
                array_push($contactList,$contact);
            }
            if($contactList == []){
                $contactList = null;
            }
            return $contactList;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return null;
    }

    public function addContact(Contact $contact){   //todo
        return true;
    }
}