<?php
namespace Lekarz\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Digits as Digits;

use Uzytkownik\Controller\Uzytkownik

 class Lekarz  implements InputFilterAwareInterface
 {
     public $id;
     public $tytul_naukowy;
     public $grafik;
     public $os_id;
     public $spec_id;
     
     /**
      * Object Uzytkownik $osoba
      */
     public $osoba = null;
     
     /**
      * Object Specjalnosc $osoba
      */
     public $specjalnosc = null;

     public function exchangeArray($data)
     {
         $this->id     = (!empty($data['id'])) ? $data['id'] : null;
         $this->tytul_naukowy   = (!empty($data['tytul_naukowy'])) ? $data['tytul_naukowy'] : null;
         $this->grafik = (!empty($data['grafik'])) ? $data['grafik'] : null;
         $this->os_id = (!empty($data['os_id'])) ? $data['os_id'] : null;
         $this->spec_id = (!empty($data['spec_id'])) ? $data['spec_id'] : null;
         
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