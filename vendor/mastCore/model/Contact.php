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

    private $company;

    private $address;

    private $type;

    function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'name' => $this->name,
            'mail'=> $this->mail,
            'phone' => $this->phone,
            'company' => $this->company,
            'address' => $this->address,
            'type' => $this->type
        ];
    }

    function __construct()
    {
        $this->id = 0;
        $this->firstName = null;
        $this->name = null;
        $this->mail = null;
        $this->phone = null;
        $this->company = null;
        $this->address = null;
        $this->type = null;
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


}



?>