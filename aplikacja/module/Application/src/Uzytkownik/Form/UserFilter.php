<?php
namespace Uzytkownik\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

use Zend\Validator\Date;

class UserFilter extends InputFilter
{
	public function __construct()
	{
		// self::__construct(); // parnt::__construct(); - throws an error
		$this->add(array(
			'name'     => 'imie',
			'required' => true,
			'filters'  => array(
				array('name' => 'StripTags'),
				array('name' => 'StringTrim'),//odetnie biale znaki na pocz i na koncu
			),
			'validators' => array(
				array(
					'name'    => 'StringLength', //typ walidatora, spr dlugosc stringa
					'options' => array(
						'encoding' => 'UTF-8',
						'min'      => 2,
						'max'      => 20,
					),
				),
			),
		));
        
		$this->add(array(
			'name'     => 'nazwisko',
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
						'min'      => 2,
						'max'      => 40,
					),
				),
			),
		)); 

		$this->add(array(
			'name'     => 'adres',
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
						'min'      => 2,
						'max'      => 200,
					),
				)
			),
		));    
        
              

		$this->add(array(
			'name'     => 'telefon',
			'required' => false,
			'validators' => array(
				array(
					'name'    => 'Digits',
                    'options' => array(
						'min'      => 1,
						'max'      => 11,
                        'message' => 'Dopuszczalne jest %min% do %max% znaków',
					),
				),
			),
		));	 
        

        $this->add(array(
            'name'       => 'email',
            'required'   => true,
            'validators' => array(
                array(
                    'name' => 'EmailAddress'
                ),
            ),
        ));
		
        
        
		$this->add(array(
			'name'     => 'pesel',
			'required' => true,
			'validators' => array(
				array(
					'name'    => 'Digits',
				),
                array(
					'name'    => 'StringLength',
					'options' => array(
						'min'      => 11,
						'max'      => 11,
                        'message' => 'PESEL musi mieć %max% znaków',
					),
                    
				)
			),
		));	

        $this->add(array(
			'name'     => 'haslo',
            'required' => false,
			'validators' => array(
				    array(
					'name'    => 'StringLength',
					'options' => array(
						'min'      => 3,
						'max'      => 15,
                        'message' => 'Hasło musi mieć od %min% do %max% znaków',
					),
                 
				)
			),
		));	
	
	}
}