<?php
namespace Lekarz\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

use Zend\Validator\Date;

class LekarzFilter extends InputFilter
{
	public function __construct()
	{

        for($i=0; $i<7;$i++){
            $this->add(array(
                'name'     => 'grafik_'.$i,
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    new \Zend\Validator\Regex(array('pattern' => '/^[0-9\.\-;\s]*/'))
                ),
            ));  
        }
        
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