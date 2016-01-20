<?php
/**
 * Created by Alexandre Brosse.
 * User: Alex
 * Date: 24/12/2015
 * Time: 15:23
 */
require_once 'php-ews/ExchangeWebServices.php';
abstract class ExchangeConnection {

    private $ews;

    public function __construct(){
        try {
              //$this->ews = new ExchangeWebServices("ex.mail.ovh.net", "contact@sitalia.fr", "Nv412glk");
            $this->ews = new ExchangeWebServices("ex.mail.ovh.net", "Toto@sitalia.fr", "063214577");
        }catch(Exception $e){
            error_log($e->getMessage());
        }
    }

    /**
     * @return ExchangeWebServices
     */
    public function getEws()
    {
        return $this->ews;
    }

    /**
     * @param ExchangeWebServices $ews
     */
    public function setEws($ews)
    {
        $this->ews = $ews;
    }



}