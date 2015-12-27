<?php
/**
 * Created by Alexandre Brosse
 * User: Alex
 * Date: 27/12/2015
 * Time: 11:00
 */


class GoogleMapService {

    private $url;

    public function __construct(){
        $this->url = "http://maps.google.com/maps/api/geocode/json?";
    }

    public function getLatLong(Address $address){
        $adr ="";
        $formatedAddress = "";
        if($address->getLine1() != null){
            $adr.=$address->getLine1()." , ";
        }
        if($address->getZipCode() != null){
            $adr.=$address->getZipCode()." ";
        }
        if($address->getCity() != null){
            $adr.=$address->getCity();
        }
        $formatedAddress = str_replace(" ","+",$adr);

        if($formatedAddress != "") {
            $result = file_get_contents($this->url . "address=" . $formatedAddress);
            $json = json_decode($result);
            $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};

            return [$lat, $long];

        }
        return [];
    }
}