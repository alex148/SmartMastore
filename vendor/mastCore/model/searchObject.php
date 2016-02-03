<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 27/01/2016
 * Time: 12:11
 */

class SearchObject implements \JsonSerializable{

    private $firstName;

    private $name;

    private $company;

    private $address;

    private $typeName;

    private $rayon;

    function jsonSerialize()
    {
        return [
            'firstName' => $this->firstName,
            'name' => $this->name,
            'company' => $this->company,
            'address' => $this->address,
            'typeName' => $this->typeName,
            'rayon' => $this->rayon
        ];
    }

    function __construct()
    {
        $this->firstName = null;
        $this->name = null;
        $this->company = null;
        $this->address = null;
        $this->typeName = null;
        $this->rayon = null;
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
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * @param mixed $typeName
     */
    public function setTypeName($typeName)
    {
        $this->typeName = $typeName;
    }

    /**
     * @return mixed
     */
    public function getRayon()
    {
        return $this->rayon;
    }

    /**
     * @param mixed $rayon
     */
    public function setRayon($rayon)
    {
        $this->rayon = $rayon;
    }




}