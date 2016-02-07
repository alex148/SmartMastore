<?php
/**
 * Created by Alexandre Brosse
 * User: Alex
 * Date: 24/12/2015
 * Time: 12:16
 */
require 'vendor/autoload.php';
require_once 'vendor/mastCore/model/Address.php';
require_once 'vendor/mastCore/model/Contact.php';
require_once 'vendor/mastCore/model/Type.php';
require_once 'vendor/mastCore/model/SearchObject.php';
require_once 'vendor/mastCore/crud/AddressService.php';
require_once 'vendor/mastCore/crud/ContactService.php';
require_once 'vendor/mastCore/crud/TypeService.php';
require_once 'vendor/mastCore/crud/GoogleMapService.php';
require_once 'vendor/ExchangeService.php';
require_once 'vendor/MastCoreService.php';

// Start PHP session
session_cache_limiter(false);
session_start();

$app = new \Slim\App();
$app->config('debug', true);

$key = 'M@st0r_Key_Secured';
$_SERVER['key'] = '72c5e00cb6c620fa3a8d4277cb84d83c58dea23be4b931dfad9eeff59d5bc6918ac42db511c7856c3b859c8c440924ef';


$app->get('/synchronization', function (\Slim\Http\Request $request,\Slim\Http\Response $response, $args) use ($app) {
    try{
        if(!has_access($request)){
            return access_denied($response);
        }

        $mastCoreService = new MastCoreService();
        if($mastCoreService->synchronization()){
            return $response->write(json_encode('Synchronized with success',true));
        }else{
            return $response->write(json_encode('Synchronized with errors',true));
        }
    }catch(Exception $e){
        error_log($e->getMessage());
    }
    return error($response, 'SYNCHRONISATION ERROR');

});

$app->get('/getAllTypes', function (\Slim\Http\Request $request,\Slim\Http\Response $response, $args) use ($app) {
    try{
        if(!has_access($request)){
            return access_denied($response);
        }
        $typeServices = new TypeService();
        $types = $typeServices->getAllTypes();
        if($types != []){
            return $response->write(json_encode($types,true));
        }
    }catch(Exception $e){
        error_log($e->getMessage());
    }
    return error($response, 'GETALLTYPES ERROR');
});

$app->get('/getAllContacts', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app) {
    try{

        if(!has_access($request)){
            return access_denied($response);
        }

        $contactService = new ContactService();
        $contactList = $contactService->getAllContacts();

        if($contactList == null || $contactList == []){
            return error($response, 'NO CONTACT IN DATABASE');
        }

        return $response->write(json_encode($contactList,true));
    }catch(Exception $e){
        error_log($e->getMessage());
    }
    return error($response, 'GET ALL CONTACTS UNKNOWN ERROR');

});

$app->post('/addContact', function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app) {
    try{
        if(!has_access($request)){
            return access_denied($response);
        }

        $requestData = $request->getBody()->getContents();
        $data = json_decode($requestData,true);
        $contact = contactParser($data);
        $contactService = new ContactService();
        $id = $contactService->addContact($contact);
        if($id != -1){
            $contact->setId($id);
        }else{
            return error($response, 'ADD CONTACT IN DATABASE ERROR');
        }
        $exchangeService = new ExchangeService();
        if($exchangeService->addContact($contact)){
            return $response->write(json_encode($contact,true));

        }else{
            return error($response, 'ADD CONTACT IN EXCHANGE ERROR');
        }
    }catch(Exception $e){
        error_log($e->getMessage());
    }
    return error($response, 'ADD CONTACT UNKNOWN ERROR');
});

$app->put('/updateContact',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app){
    try{
        if(!has_access($request)){
            return access_denied($response);
        }
        $requestData = $request->getBody()->getContents();
        $data = json_decode($requestData,true);
        $contact = contactParser($data);
        if($contact->getId() == null || $contact->getId() == -1){
            return error($response, 'UPDATE A CONTACT WITH NO ID IS NOT POSSIBLE');
        }
        if($contact->getExchangeId() == null || $contact->getExchangeId() == -1 || $contact->getExchangeId() == ""){
            return error($response, 'UPDATE A CONTACT WITH NO EXCHANGE ID IS NOT POSSIBLE');
        }
        $contactService = new ContactService();
        $exchangeService = new ExchangeService();

        $deletedFromExchange = $exchangeService->deleteContact($contact);
        if(!$deletedFromExchange){
            return error($response, 'ERROR DURING UPDATING IN EXCHANGE');
        }
        if(!$exchangeService->addContact($contact)){
            return error($response, 'ERROR DURING UPDATING IN EXCHANGE');
        }

        if(!$contactService->updateContact($contact)){
            return error($response, 'ERROR DURING UPDATING IN DATABASE');
        }
        return $response->write(json_encode("ok",true));
    }catch(Exception $e){
        error_log($e->getMessage());
    }
    return error($response, ' UPDATE CONTACT UNKNOWN ERROR');
});

$app->post('/deleteContact',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app){
    try{
        if(!has_access($request)){
            return access_denied($response);
        }
        $requestData = $request->getBody()->getContents();
        $data = json_decode($requestData,true);
        $contact = contactParser($data);
        if($contact->getId() == null || $contact->getId() == -1){
            return error($response, 'DELETE A CONTACT WITH NO ID IS NOT POSSIBLE');
        }
        if($contact->getExchangeId() == null || $contact->getExchangeId() == -1 || $contact->getExchangeId() == ""){
            return error($response, 'DELETE A CONTACT WITH NO EXCHANGE ID IS NOT POSSIBLE');
        }
        $contactService = new ContactService();
        $exchangeService = new ExchangeService();

        if(!$contactService->deleteContact($contact)){
            return error($response, 'ERROR DURING DELETING FROM DATABASE');
        }
        if(!$exchangeService->deleteContact($contact)){
            return error($response, 'ERROR DURING DELETING FROM EXCHANGE');
        }

        return $response->write(json_encode('Delete with success',true));
    }catch(Exception $e){
        error_log($e->getMessage());
    }
    return error($response, ' DELETE CONTACT UNKNOWN ERROR');
});

$app->post('/searchContacts',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app){
    try{
         if(!has_access($request)){
            return access_denied($response);
        }
        $requestData = $request->getBody()->getContents();
        $data = json_decode($requestData,true);
        $searchObject = searchObjectParser($data);
        $contactService = new ContactService();
        $result = $contactService->searchContacts($searchObject);
        if($result != null){
            return $response->write(json_encode($result,true));
        }
    }catch(Exception $e){
        error_log($e->getMessage());
    }
    return error($response, ' SEARCH CONTACT UNKNOWN ERROR');
});

$app->get('/test',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app){
    $contact = new Contact();
    $contact->setFirstName('tt');
    $contact->setName('yyy');
    $contact->setPhone('0658745230');
    $contact->setMail('tr@hotmail.fr');
    $contact->setExchangeId("AAAPAFRvdG9Ac2l0YWxpYS5mcgBGAAAAAADUeCkQ3EMJTKBA3gJNfi4uBwAxDqrT2rrGSrf1tQt3lJQmAAAAAAEOAAAxDqrT2rrGSrf1tQt3lJQmAAAAABMsAAA=");
    $ex = new ExchangeService();
    $cc = $ex->deleteContact($contact);


});

function has_access(\Slim\Http\Request $request){
    try{
        $receivedKey = $request->getHeader('key');
    }catch(Exception $e){
        error_log($e->getMessage());
    }
    if($receivedKey != null && strcmp($receivedKey[0],$_SERVER['key']) == 0){
        return true;
    }
    return false;
}

function access_denied(\Slim\Http\Response $response){
    return $response->withStatus(403)
        ->withHeader('Content-Type', 'text/html')
        ->write('{ error: "ACCESS DENIED !"}');
}

function error(\Slim\Http\Response $response, $message){
    return $response->withStatus(503)
        ->withHeader('Content-Type', 'text/html')
        ->write('{ error : "SERVER ERROR. PLEASE CONTACT YOUR ADMINISTRATOR. ERROR TYPE : '.$message.'"}');
}


function searchObjectParser($data){
    $searchObject = new SearchObject();
    if(isset($data['firstName'])){
        $searchObject->setFirstName($data['firstName']);
    }
    if(isset($data['name'])){
        $searchObject->setName($data['name']);
    }
    if(isset($data['company'])){
        $searchObject->setCompany($data['company']);
    }

    if(isset($data['address'])) {
        $dataAddress = $data['address'];
        $address = new Address();
        if (isset($dataAddress['line1'])) {
            $address->setLine1($dataAddress['line1']);
        }
        if (isset($dataAddress['line2'])) {
            $address->setLine2($dataAddress['line2']);
        }
        if (isset($dataAddress['zipCode'])) {
            $address->setZipCode($dataAddress['zipCode']);
        }
        if (isset($dataAddress['city'])) {
            $address->setCity($dataAddress['city']);
        }
        if (isset($dataAddress['latitude']) && isset($dataAddress['longitude'])) {
            $address->setLatitude($dataAddress['latitude']);
            $address->setLongitude($dataAddress['longitude']);
        } else {
            $mapService = new GoogleMapService();
            $latlng = $mapService->getLatLong($address);
            if ($latlng != [] && sizeof($latlng) == 2) {
                $address->setLatitude($latlng[0]);
                $address->setLongitude($latlng[1]);
            }
        }
        $searchObject->setAddress($address);
    }
    if(isset($data['typeName'])){
        $searchObject->setTypeName($data['typeName']);
    }

    if(isset($data['rayon'])){
        $searchObject->setRayon($data['rayon']);
    }
    return $searchObject;
}

function contactParser($data){
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
    if(isset($data['company'])){
        $contact->setCompany($data['company']);
    }
    if(isset($data['address'])){
        $dataAddress = $data['address'];
        $address = new Address();
        if(isset($dataAddress['id'])){
            $address->setId($dataAddress['id']);
        }
        if(isset($dataAddress['line1'])){
            $address->setLine1($dataAddress['line1']);
        }
        if(isset($dataAddress['line2'])){
            $address->setLine2($dataAddress['line2']);
        }
        if(isset($dataAddress['zipCode'])){
            $address->setZipCode($dataAddress['zipCode']);
        }
        if(isset($dataAddress['city'])){
            $address->setCity($dataAddress['city']);
        }
        if(isset($dataAddress['latitude']) && isset($dataAddress['longitude'])) {
            $address->setLatitude($dataAddress['latitude']);
            $address->setLongitude($dataAddress['longitude']);
        }else{
            $mapService = new GoogleMapService();
            $latlng = $mapService->getLatLong($address);
            if($latlng != [] && sizeof($latlng) == 2){
                $address->setLatitude($latlng[0]);
                $address->setLongitude($latlng[1]);
            }
        }

        $contact->setAddress($address);
    }
    if(isset($data['type'])){
        if(isset($data['type']['id']) && isset($data['type']['name'])){
            $type = new Type($data['type']['id'],$data['type']['name']);
        }elseif(isset($data['type']['name'])){
            $type = new Type(null,$data['type']['name']);
        }else{
            $type = null;
        }
            $contact->setType($type);
    }
    if(isset($data['exchangeId'])){
        $contact->setExchangeId($data['exchangeId']);
    }
    return $contact;
}

$app->run();



?>