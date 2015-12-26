
<?php
/**
 * Created by Alexandre Brosse
 * User: Alex
 * Date: 24/12/2015
 * Time: 12:16
 */
class Type implements \JsonSerializable {


    private $id;

    private $label;

    function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getLabel(),
        ];
    }

    public function __construct($id,$label){
        $this->id = $id;
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
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
}