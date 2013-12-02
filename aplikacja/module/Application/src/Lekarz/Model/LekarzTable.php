<?php
namespace Lekarz\Model;

 use Zend\Db\TableGateway\TableGateway;

 class LekarzTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select();
         return $resultSet;
     }

     public function getLekarz($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Nie można znaleźć #$id");
         }
         return $row;
     }

     public function saveLekarz(Lekarz $lekarz)
     {
         $data = array(
             'id' => $lekarz->id,
             'tytul_naukowy' => $lekarz->tytul_naukowy,
             'grafik' => $lekarz->grafik,
             
             'os_id' => $lekarz->os_id,
             'spec_id' => $lekarz->spec_id
         );

         $id = (int) $lekarz->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getLekarz($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Lekarz id does not exist');
             }
         }
     }

     public function deleteLekarz($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
     
     
     
     
 }