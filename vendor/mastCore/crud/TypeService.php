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
        try{
            $list = [];
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
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
        }catch (Exception $e){
            error_log($e->getMessage());
        }
        return [];
    }

    public function getTypeIdByLabel($label){
        try{
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }

            $label = '%'.$label.'%';
            $query = "SELECT * FROM TYPE WHERE LABEL LIKE :label";
            $request = parent::getBdd()->prepare($query);
            $request->bindValue(":label",$label, PDO::PARAM_STR);
            $request->execute();

            $typeData = $request->fetch();
            if(isset($typeData['id'])){
                return $typeData['id'];
            }
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return -1;
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
        try{
            if($type == null){
                return false;
            }
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            $query = "INSERT INTO TYPE VALUES(NULL,:label)";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':label', $type->getLabel());
            $request->execute();
            $id = parent::getBdd()->lastInsertId();
            $request->closeCursor();
            parent::getBdd()->commit();
            return $id;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return -1;
    }

    public function updateType(Type $type){
        try{
            if ($type != null && ($type->getId() == null) || $type == -1){
                return false;
            }
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            $query = "UPDATE TYPE SET label = :label WHERE id = :id";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':id',$type->getId());
            $request->bindParam(':label', $type->getLabel());
            $request->execute();
            parent::getBdd()->commit();
            $request->closeCursor();
            return true;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return false;
    }

    public function deleteType($id){
        try{
            if($id == null || $id == -1){
                return false;
            }
            if(!parent::getBdd()->inTransaction()){
                parent::getBdd()->beginTransaction();
            }
            $query = "DELETE FROM TYPE WHERE ID = :id";
            $request = parent::getBdd()->prepare($query);
            $request->bindParam(':id', $id);
            $request->execute();
            parent::getBdd()->commit();
            $request->closeCursor();
            return true;
        }catch(Exception $e){
            error_log($e->getMessage());
        }
        return false;
    }
}