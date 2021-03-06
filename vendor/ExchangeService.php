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
require_once 'php-ews/EWSType/DeleteItemType.php';
require_once 'php-ews/EWSType/DisposalType.php';
require_once 'php-ews/EWSType/CalendarItemCreateOrDeleteOperationType.php';
require_once 'php-ews/EWSType/NonEmptyArrayOfBaseItemIdsType.php';
require_once 'php-ews/EWSType/ItemIdType.php';
require_once 'mastCore/model/Address.php';
require_once 'mastCore/model/Type.php';
require_once 'mastCore/crud/TypeService.php';

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

            $request->ParentFolderIds = new EWSType_NonEmptyArrayOfBaseFolderIdsType();
            $request->ParentFolderIds->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType();
            $request->ParentFolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::CONTACTS;

            $request->Traversal = EWSType_ItemQueryTraversalType::SHALLOW;

            $response = parent::getEws()->FindItem($request);
            if(isset($response->ResponseMessages->FindItemResponseMessage->RootFolder->Items->Contact)){
               $stdContacts = $response->ResponseMessages->FindItemResponseMessage->RootFolder->Items->Contact;
            }else{
               return null;
            }
            foreach ($stdContacts as $c){
                $contact = new Contact();
                if(isset($c->ItemId->Id)){
                    $contact->setExchangeId($c->ItemId->Id);
                }
                if(isset($c->CompleteName)){
                    if(isset($c->CompleteName->FirstName)){
                        $contact->setFirstName($c->CompleteName->FirstName);
                    }
                    if(isset($c->CompleteName->LastName)){
                        if (strpos($c->CompleteName->LastName,'--') !== false) {
                            $nameAndType = $this->getTypeFromName($c->CompleteName->LastName);
                            if(sizeof($nameAndType) == 2){
                                $contact->setName($nameAndType[0]);
                                $typeService = new TypeService();
                                $type = $typeService->getType($nameAndType[1]);
                                if($type != null){
                                    $contact->setType($type);
                                }else{
                                    $contact->setType(null);
                                }
                            }else{
                                $contact->setName($c->CompleteName->LastName);
                            }
                        }else{
                            $contact->setName($c->CompleteName->LastName);
                        }
                    }
                }
                if(isset($c->PhoneNumbers->Entry)){
                    if( is_array($c->PhoneNumbers->Entry)){
                        $contact->setPhone($c->PhoneNumbers->Entry[0]->_);
                        if(sizeof($c->PhoneNumbers->Entry) > 1) {
                            if (isset($c->PhoneNumbers->Entry[1])) {
                                $contact->setPhone2($c->PhoneNumbers->Entry[1]->_);
                            }
                        }
                        if(sizeof($c->PhoneNumbers->Entry) > 2) {
                            if(isset($c->PhoneNumbers->Entry[2])){
                                $contact->setPhone3($c->PhoneNumbers->Entry[2]->_);
                            }
                        }
                    }else{
                        $contact->setPhone($c->PhoneNumbers->Entry->_);
                    }
                }
                if(isset($c->EmailAddresses->Entry)){
                    if( is_array($c->EmailAddresses->Entry)){
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
                    if(is_array($stdAddress)){
                        $address->setLine1($stdAddress[0]->Street);
                        $address->setZipCode($stdAddress[0]->PostalCode);
                        $address->setCity($stdAddress[0]->City);
                    }else{
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

    function splitn($string, $needle, $offset)
    {
        $newString = $string;
        $totalPos = 0;
        $length = strlen($needle);
        for($i = 0; $i < $offset; $i++)
        {
            $pos = strpos($newString, $needle);
            if($pos === false)
                return false;
            $newString = substr($newString, $pos+$length);
            $totalPos += $pos+$length;
        }
        return array(substr($string, 0, $totalPos-$length),substr($string, $totalPos));
    }


    public function getTypeFromName($name){
        $typeService = new TypeService();
        $nameAndType = $this->splitn($name,'--',sizeof($name));
        if($nameAndType != false && sizeof($nameAndType) >=2){
           $typeId = $typeService->getTypeIdByLabel($nameAndType[1]);
            if($typeId != -1){
                return [$nameAndType[0], $typeId];
            }
        }
        return [];
    }

    public function addContact(Contact $c){
        try{
            $request = new EWSType_CreateItemType();

            $contact = new EWSType_ContactItemType();
            if($c->getFirstName() != null){
                    $contact->GivenName = $c->getFirstName();
            }
            if($c->getType() != null){
                $contact->Surname = $c->getName().' -- '.$c->getType()->getLabel();

            }else{
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
            if($c->getCompany() != null){
                $contact->CompanyName = $c->getCompany();
            }
            $addr = $c->getAddress();
            if($addr != null){
                $address = new EWSType_PhysicalAddressDictionaryEntryType();
                $address->Key = new EWSType_PhysicalAddressKeyType();
                $address->Key->_ = EWSType_PhysicalAddressKeyType::BUSINESS;
                if($addr->getLine1() != null){
                    if($addr->getLine2() != null){
                        $address->Street = $addr->getLine1().' '.$addr->getLine2();
                    }else{
                        $address->Street = $addr->getLine1();
                    }
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

                $contact->PhoneNumbers->Entry[] = $phone;
            }
            if($c->getPhone2() != null){
                $phone = new EWSType_PhoneNumberDictionaryEntryType();
                $phone->Key = new EWSType_PhoneNumberKeyType();
                $phone->Key->_ = EWSType_PhoneNumberKeyType::BUSINESS_PHONE_2;
                $phone->_ = $c->getPhone2();
                if($contact->PhoneNumbers == null){
                    $contact->PhoneNumbers = new EWSType_PhoneNumberDictionaryType();
                }
                $contact->PhoneNumbers->Entry[] = $phone;
            }
            if($c->getPhone3() != null){
                $phone = new EWSType_PhoneNumberDictionaryEntryType();
                $phone->Key = new EWSType_PhoneNumberKeyType();
                $phone->Key->_ = EWSType_PhoneNumberKeyType::OTHER_PHONE;
                $phone->_ = $c->getPhone3();
                if($contact->PhoneNumbers == null){
                    $contact->PhoneNumbers = new EWSType_PhoneNumberDictionaryType();
                }
                $contact->PhoneNumbers->Entry[] = $phone;
            }

            $contact->FileAsMapping = new EWSType_FileAsMappingType();  //?
            $contact->FileAsMapping->_ = EWSType_FileAsMappingType::FIRST_SPACE_LAST;

            $request->Items->Contact[] = $contact;

            $result = parent::getEws()->CreateItem($request);
            if($result->ResponseMessages->CreateItemResponseMessage->Items->Contact->ItemId->Id != null){
                $c->setExchangeId($result->ResponseMessages->CreateItemResponseMessage->Items->Contact->ItemId->Id);
            }
            return true;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return false;
    }

    public function deleteContact(Contact $c){
        try{
            if($c->getExchangeId() == null || $c->getExchangeId() == -1 || $c->getExchangeId() == ""){
                return false;
            }

            $event_id = $c->getExchangeId();

            $request = new EWSType_DeleteItemType();
            $request->DeleteType = EWSType_DisposalType::HARD_DELETE;
            $request->SendMeetingCancellations = EWSType_CalendarItemCreateOrDeleteOperationType::SEND_TO_NONE;

            $item = new EWSType_ItemIdType();
            $item->Id = $event_id;

            $items = new EWSType_NonEmptyArrayOfBaseItemIdsType();
            $items->ItemId = $item;
            $request->ItemIds = $items;

            $response = $this->getEws()->DeleteItem($request);
            print_r($response);
            if(strcmp($response->ResponseMessages->DeleteItemResponseMessage->ResponseClass,"Success") == 0){
                return true;
            }else{
                return false;
            }
        }catch (Exception $e){
            error_log($e->getMessage());
        }
    }
}