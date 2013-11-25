<?php
namespace Uzytkownik\Model;

 use Zend\Db\TableGateway\TableGateway;

 class UzytkownikTable
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

     public function getUzytkownik($id)
     {
         $id  = (int) $id;
         $rowset = $this->tableGateway->select(array('id' => $id));
         $row = $rowset->current();
         if (!$row) {
             throw new \Exception("Nie można znaleźć #$id");
         }
         return $row;
     }

     public function saveUzytkownik(Uzytkownik $uzytkownik)
     {
         $data = array(
             'id' => $uzytkownik->id,
             'imie' => $uzytkownik->imie,
             'nazwisko' => $uzytkownik->nazwisko,
             'pesel' => $uzytkownik->pesel,
             'adres' => $uzytkownik->adres,
             'telefon' => $uzytkownik->telefon,
             'email' => $uzytkownik->email,
             'data_ur' => $uzytkownik->data_ur,
             'plec' => $uzytkownik->plec,
             'poziom' => $uzytkownik->poziom,
             'haslo' => $uzytkownik->haslo,
             'sol' => $uzytkownik->sol
         );

         $id = (int) $uzytkownik->id;
         if ($id == 0) {
             $this->tableGateway->insert($data);
         } else {
             if ($this->getUzytkownik($id)) {
                 $this->tableGateway->update($data, array('id' => $id));
             } else {
                 throw new \Exception('Uzytkownik id does not exist');
             }
         }
     }

     public function deleteUzytkownik($id)
     {
         $this->tableGateway->delete(array('id' => (int) $id));
     }
 }