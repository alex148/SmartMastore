<?php
/**
 * Created by Alexandre Brosse
 * User: Alex
 * Date: 24/12/2015
 * Time: 12:16
 */
class Address implements \JsonSerializable{

    private $id;

    private $line1;

    private $line2;

    private $zipCode;

    private $city;

    private $latitude;

    private $longitude;


    function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'line1' => $this->getLine1(),
            'line2'=> $this->getLine2(),
            'zipCode' => $this->getZipCode(),
            'city' => $this->getCity(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude()
        ];
    }

    public function __construct(){
        $this->id = -1;
        $this->line1 = null;
        $this->line2 = null;
        $this->city = null;
        $this->zipCode = null;
        $this->latitude = null;
        $this->longitude = null;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLine1()
    {
        return $this->line1;
    }

    /**
     * @param mixed $line1
     */
    public function setLine1($line1)
    {
        $this->line1 = $line1;
    }

    /**
     * @return mixed
     */
    public function getLine2()
    {
        return $this->line2;
    }

    /**
     * @param mixed $line2
     */
    public function setLine2($line2)
    {
        $this->line2 = $line2;
    }

    /**
     * @return mixed
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * @param mixed $zipCode
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param mixed $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param mixed $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

}
?>