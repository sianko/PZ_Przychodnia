<?php
namespace Uzytkownik\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Digits as Digits;

 class Uzytkownik  implements InputFilterAwareInterface
 {
     public $id;
     public $imie;
     public $nazwisko;
     public $pesel;
     public $adres;
     public $telefon;
     public $email;
     public $data_ur;
     public $plec;
     public $poziom;
     public $haslo;
     public $sol;

     public function exchangeArray($data)
     {
         $this->id     = (!empty($data['id'])) ? $data['id'] : null;
         $this->imie   = (!empty($data['imie'])) ? $data['imie'] : null;
         $this->nazwisko = (!empty($data['nazwisko'])) ? $data['nazwisko'] : null;
         $this->pesel = (!empty($data['pesel'])) ? $data['pesel'] : null;
         $this->adres = (!empty($data['adres'])) ? $data['adres'] : null;
         $this->telefon = (!empty($data['telefon'])) ? $data['telefon'] : null;
         $this->email = (!empty($data['email'])) ? $data['email'] : null;
         $this->data_ur = (!empty($data['data_ur'])) ? $data['data_ur'] : null;
         $this->plec = (!empty($data['plec'])) ? $data['plec'] : null;
         $this->poziom = (!empty($data['poziom'])) ? $data['poziom'] : null;
         $this->haslo = (!empty($data['haslo'])) ? $data['haslo'] : null;
         $this->sol = (!empty($data['sol'])) ? $data['sol'] : null;
         
     }
     
     
     
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
	
	
	protected $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
	
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'pesel',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 11,
                            'max'      => 11,
                        ),
                    ),
                    new Digits(),
                ),
            )));

            $inputFilter->add($factory->createInput(array(
                'name'     => 'haslo',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }	
 }