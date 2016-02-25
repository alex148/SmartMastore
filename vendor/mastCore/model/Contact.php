<?php
/**
 * Created by Alexandre Brosse
 * User: Alex
 * Date: 24/12/2015
 * Time: 12:16
 */
require_once 'Address.php';
require_once 'Type.php';

class Contact implements \JsonSerializable{

    private $id;

    private $firstName;

    private $name;

    private $mail;

    private $phone;

    private $phone2;

    private $phone3;

    private $company;

    private $address;

    private $type;

    private $exchangeId;

    function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'name' => $this->name,
            'mail'=> $this->mail,
            'phone' => $this->phone,
            'phone2' => $this->phone2,
            'phone3' => $this->phone3,
            'company' => $this->company,
            'address' => $this->address,
            'type' => $this->type,
            'exchangeId' =>$this->exchangeId
        ];
    }

    function __construct()
    {
        $this->id = -1;
        $this->firstName = null;
        $this->name = null;
        $this->mail = null;
        $this->phone = null;
        $this->phone2 = null;
        $this->phone3 = null;
        $this->company = null;
        $this->address = null;
        $this->type = null;
        $this->exchangeId = null;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail)
    {
        $this->mail = $mail;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
            return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return null
     */
    public function getExchangeId()
    {
        return $this->exchangeId;
    }

    /**
     * @param null $exchangeId
     */
    public function setExchangeId($exchangeId)
    {
        $this->exchangeId = $exchangeId;
    }

    /**
     * @return mixed
     */
    public function getPhone2()
    {
        return $this->phone2;
    }

    /**
     * @param mixed $phone2
     */
    public function setPhone2($phone2)
    {
        $this->phone2 = $phone2;
    }

    /**
     * @return mixed
     */
    public function getPhone3()
    {
        return $this->phone3;
    }

    /**
     * @param mixed $phone3
     */
    public function setPhone3($phone3)
    {
        $this->phone3 = $phone3;
    }



}



?>