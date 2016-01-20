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
        $receivedKey = null;

       // if(!has_access($request)){
       //     return access_denied($response);
       // }

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

}); //todo improvable

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
            return error($response, 'UPADTE A CONTACT WITH NO ID IS NOT POSSIBLE');
        }
        $contactService = new ContactService();
        if($contactService->updateContact($contact)){
            return $response->write(json_encode('Update with success',true));
        }else{
            return error($response, 'ERROR DURING UPDATING IN DATABASE');
        }
        //todo update contact in exchange
    }catch(Exception $e){
        error_log($e->getMessage());
    }
    return error($response, ' UPDATE CONTACT UNKNOWN ERROR');
});

$app->delete('/deleteContact',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app){
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
        $contactService = new ContactService();

        if($contactService->deleteContact($contact)){
            return $response->write(json_encode('Delete with success',true));
        }else{
            return error($response, 'ERROR DURING DELETING FROM DATABASE');
        }


        //todo delete contact from Exchange

    }catch(Exception $e){
        error_log($e->getMessage());
    }
    return error($response, ' DELETE CONTACT UNKNOWN ERROR');
});

$app->search('/searchContacts',function (\Slim\Http\Request $request, \Slim\Http\Response $response, $args) use ($app){
    try{
         if(!has_access($request)){
            return access_denied($response);
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

    $ex = new ExchangeService();
    $ex->getTypeFromName('jean -- plombier');

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
        ->write('ACCESS DENIED !');
}

function error(\Slim\Http\Response $response, $message){
    return $response->withStatus(503)
        ->withHeader('Content-Type', 'text/html')
        ->write('SERVER ERROR. PLEASE CONTACT YOUR ADMINISTRATOR. ERROR TYPE : '.$message);
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
        $mapService = new GoogleMapService();
        $latlng = $mapService->getLatLong($address);
        if($latlng != [] && sizeof($latlng) == 2){
            $address->setLatitude($latlng[0]);
            $address->setLongitude($latlng[1]);
        }
        $contact->setAddress($address);
    }
    if(isset($data['type'])){
        if(isset($data['type']['name'])){
            $type = new Type(0,$data['type']['name']);
            $contact->setType($type);
        }
    }
    if(isset($data['exchangeId'])){
        $contact->setExchangeId($data['exchangeId']);
    }
    return $contact;
}

$app->run();



?>