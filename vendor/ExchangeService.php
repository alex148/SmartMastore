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
require_once 'php-ews/EWSType/CreateItemType.php';
require_once 'php-ews/EWSType/ContactItemType.php';
require_once 'php-ews/EWSType/EmailAddressDictionaryEntryType.php';
require_once 'php-ews/EWSType/EmailAddressKeyType.php';
require_once 'php-ews/EWSType/EmailAddressDictionaryType.php';
require_once 'php-ews/EWSType/PhysicalAddressDictionaryEntryType.php';
require_once 'php-ews/EWSType/PhysicalAddressKeyType.php';
require_once 'php-ews/EWSType/PhysicalAddressDictionaryType.php';
require_once 'php-ews/EWSType/PhoneNumberDictionaryEntryType.php';
require_once 'php-ews/EWSType/PhoneNumberKeyType.php';
require_once 'php-ews/EWSType/FileAsMappingType.php';
require_once 'php-ews/EWSType/PhoneNumberDictionaryType.php';
require_once 'php-ews/NTLMSoapClient/Exchange.php';
require_once 'mastCore/model/Address.php';
require_once 'mastCore/model/Type.php';

class ExchangeService extends ExchangeConnection{


    public function __construct(){
        parent::__construct();
    }

    /**
     * Used to get all the contacts from Exchange
     * @return array of contact
     */
    public function getAllContacts(){   //todo gerer type
        try{
            $contactList = [];
            $request = new EWSType_FindItemType();

            $request->ItemShape = new EWSType_ItemResponseShapeType();
            $request->ItemShape->BaseShape = EWSType_DefaultShapeNamesType::ALL_PROPERTIES;

            $request->ContactsView = new EWSType_ContactsViewType();

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
                    }else{
                        $address->setName($stdAddress->Key);
                        if(isset($stdAddress->Street)){
                            $address->setLine1($stdAddress->Street);
                        }
                        if(isset($stdAddress->PostalCode)){
                            $address->setZipCode($stdAddress->PostalCode);
                        }
                        if(isset($stdAddress->City)){
                            $address->setCity($stdAddress->City);
                        }
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

    public function addContact(Contact $c){   //todo add type
        try{
            $request = new EWSType_CreateItemType();

            $contact = new EWSType_ContactItemType();
            if($c->getFirstName() != null){
                $contact->GivenName = $c->getFirstName();
            }
            if($c->getName()){
                $contact->Surname = $c->getName();
            }
            if($c->getMail() != null){
                $email = new EWSType_EmailAddressDictionaryEntryType();
                $email->Key = new EWSType_EmailAddressKeyType();
                $email->Key->_ = EWSType_EmailAddressKeyType::EMAIL_ADDRESS_1;
                $email->_ = $c->getMail();
                $contact->EmailAddresses = new EWSType_EmailAddressDictionaryType();
                $contact->EmailAddresses->Entry[] = $email;
            }
            $addr = $c->getAddress();
            if($addr != null){
                $address = new EWSType_PhysicalAddressDictionaryEntryType();
                $address->Key = new EWSType_PhysicalAddressKeyType();
                $address->Key->_ = EWSType_PhysicalAddressKeyType::BUSINESS;
                if($addr->getLine1() != null){
                    $address->Street = $addr->getLine1();
                }
                if($addr->getCity() != null){
                    $address->City = $addr->getCity();

                }
                if($addr->getZipCode() != null){
                    $address->PostalCode = $addr->getZipCode();
                }
                $contact->PhysicalAddresses = new EWSType_PhysicalAddressDictionaryType();
                $contact->PhysicalAddresses->Entry[] = $address;
            }


            if($c->getPhone() != null){
                $phone = new EWSType_PhoneNumberDictionaryEntryType();
                $phone->Key = new EWSType_PhoneNumberKeyType();
                $phone->Key->_ = EWSType_PhoneNumberKeyType::BUSINESS_PHONE;
                $phone->_ = $c->getPhone();
                $contact->PhoneNumbers = new EWSType_PhoneNumberDictionaryType();
                $contact->PhoneNumbers->Entry[] = $phone;
            }

            $contact->FileAsMapping = new EWSType_FileAsMappingType();  //?
            $contact->FileAsMapping->_ = EWSType_FileAsMappingType::FIRST_SPACE_LAST;

            $request->Items->Contact[] = $contact;

            $result = parent::getEws()->CreateItem($request);
            return true;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return false;
    }
}