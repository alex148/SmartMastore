<?php
/**
 * Created by Alexandre Brosse.
 * User: Alex
 * Date: 24/12/2015
 * Time: 15:23
 */

class ExchangeConnection {

    private $ews;

    public function __construct(){
        $pathInPieces = explode(DIRECTORY_SEPARATOR , __FILE__);
        echo $pathInPieces[3].DIRECTORY_SEPARATOR;
       // $this->ews = new ExchangeWebServices("ex.mail.ovh.net","contact@sitalia.fr","Nv412glk");
    }

}