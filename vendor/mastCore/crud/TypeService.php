<?php
/**
 * Created by Alexandre Brosse.
 * User: Alex
 * Date: 24/12/2015
 * Time: 14:19
 */
require_once 'DataBaseConnection.php';

class TypeService extends DataBaseConnection{


    public function __construct(){
        parent::__construct();
    }

    public function getAllTypes(){
        $list = [];
        $hasTransaction = parent::getBdd()->beginTransaction();
        if($hasTransaction){
            $query = "SELECT * FROM TYPE";
            $response = parent::getBdd()->query($query);
            while($data = $response->fetch()){
                $type = new Type($data['id'],$data['label']);
                array_push($list,$type);
            }
            $response->closeCursor();
            if ($list == []){
                return null;
            }
            return $list;
        }
        error_log('Transaction error');
        return null;
    }

    public function getType($id){
        try{
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            $query = "SELECT * FROM TYPE WHERE ID = :id";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':id',$id);
            $request->execute();
            $typeData = $request->fetch();
            $type = new Type($typeData['id'],$typeData['label']);
            $request->closeCursor();
            return $type;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return null;
    }

    public function addType(Type $type){

    }

    public function updateType(Type $type){

    }

    public function deleteType($id){

    }
}