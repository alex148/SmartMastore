<?php
/**
 * Created by Alexandre Brosse
 * User: Alex
 * Date: 24/12/2015
 * Time: 12:16
 */
require 'vendor/autoload.php';
require_once 'mastCore/model/Address.php';
require_once 'mastCore/model/Contact.php';
require_once 'mastCore/model/Type.php';
require_once 'mastCore/crud/AddressService.php';
require_once 'mastCore/crud/ContactService.php';
require_once 'mastCore/crud/TypeService.php';
require_once 'vendor/ExchangeService.php';


// Start PHP session
session_start();

$app = new \Slim\App();
$app->config('debug', true);

$app->get('/getAllContacts', function ($request, $response, $args) {
    $addrServ = new ContactService();
    $list = $addrServ->getAllContacts();
    $ExchangeService = new ExchangeService();
    $listExchange = $ExchangeService->getAllContacts();
    echo ' ';
    echo ' ';
    return $response->write(json_encode($listExchange,true));


});


$app->run();


?>