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


// Start PHP session
session_start();

$app = new \Slim\App();


$app->get('/getAllContacts', function ($request, $response, $args) {
    $contact = new Contact();
    $contact->setId(1);
    $contact->setFirstName('jean');
    $contact->setName('test');
    $contact->setCompany('Sitalia');
    $contact->setMail('test@test.fr');
    $contact->setPhone('0651272726');

    $address = new Address();
    $address->setId(1);
    $address->setName("L'adresse");
    $address->setLine1('rue du test');
    $address->setLine2(null);
    $address->setCity('Lyon');
    $address->setZipCode('69100');
    $address->setLatitude(1.5);
    $address->setLongitude(1.6);

    $type = new Type(1,'plombier');

    $contact->setType($type);
    $contact->setAddress($address);



    return $response->write(json_encode($contact));


});


$app->run();

?>