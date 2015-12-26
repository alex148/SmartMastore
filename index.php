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
require_once 'vendor/ExchangeService.php';
require_once 'vendor/MastCoreService.php';

// Start PHP session
session_start();

$app = new \Slim\App();
$app->config('debug', true);

$app->get('/synchronization', function ($request, $response, $args) {
    $mastCoreService = new MastCoreService();
    if($mastCoreService->synchronization()){
        return $response->write(json_encode('Synchronized with success',true));

    }else{
        return $response->write(json_encode('Synchronized with errors',true));
    }
});

$app->get('/getAllContacts', function ($request, $response, $args) {

    $contactService = new ContactService();
    $contactList = $contactService->getAllContacts();

    return $response->write(json_encode($contactList,true));


});


$app->run();


?>