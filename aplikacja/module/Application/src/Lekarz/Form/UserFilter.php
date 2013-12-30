<?php
namespace Lekarz\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

use Zend\Validator\Date;

class UserFilter extends InputFilter
{
	public function __construct()
	{
		// self::__construct(); // parnt::__construct(); - trows and error
		$this->add(array(
			'name'     => 'imie',
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
						'max'      => 20,
					),
				),
                new \Zend\Validator\Regex(array('pattern' => '/^[A-Za-zęóąśłżźćńĘÓĄŚŁŻŹĆŃ\-\s]*$/'))
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
                new \Zend\Validator\Regex(array('pattern' => '/^[A-Za-zęóąśłżźćńĘÓĄŚŁŻŹĆŃ\-\s]*$/'))
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
        
        for($i=0; $i<7;$i++){
            $this->add(array(
                'name'     => 'grafik_'.$i,
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    new \Zend\Validator\Regex(array('pattern' => '/^[0-9\:\-;\s]*$/'))
                ),
            ));  
        }
        

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
			'name'     => 'czas_na_pacjenta',
			'required' => true,
			'validators' => array(
				array(
					'name'    => 'Digits',
				),
                array(
					'name'    => 'StringLength',
					'options' => array(
						'min'      => 1,
						'max'      => 4,
                        'message' => 'Wymagane jest od %min% do %max% znaków',
					),
				)               
			),
		));	
	
	}
}