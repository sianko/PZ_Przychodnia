<?php
namespace Lekarz\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('registration');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');
        
        $this->add(array(
            'name' => 'tytul_naukowy',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => array(
					'prof.' => 'profesor',
					'prof. dr hab.' => 'profesor doktor habilitowany',
                    'dr' => 'doktor',
					'dr hab.' => 'doktor habilitowany',
					'mgr' => 'magister',
				),
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 250px;',
            ),
        ));
        
        $this->add(array(
            'name' => 'imie',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
        ));
        
        $this->add(array(
            'name' => 'nazwisko',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
        ));   
        
        $this->add(array(
            'name' => 'pesel',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
                'style' => 'width: 250px;',
            ),
        ));         
       
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'email',
                'class' => 'form-control',
            ),
        ));	
        
        $this->add(array(
            'name' => 'telefon',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
        ));   
        
        $this->add(array(
            'name' => 'adres',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
            ),
        ));
        
        for($i=1; $i<=31; $i++)
        {
            $dni[$i] = $i;
        }
        
        for($i=1; $i<=12; $i++)
        {
            $msc[$i] = $i;
        }
        
        for($i=1900; $i<=date('Y'); $i++)
        {
            $rok[$i] = $i;
        }
        
        $this->add(array(
            'name' => 'data_ur_0',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => $dni,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 75px;'
            )
        ));
        
        $this->add(array(
            'name' => 'data_ur_1',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => $msc,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 75px;'
            )
        ));
        
        $this->add(array(
            'name' => 'data_ur_2',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => $rok,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 100px;'
            )
        ));

        $this->add(array(
            'name' => 'lid',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

        $this->add(array(
            'name' => 'poziom',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));  
        
        $this->add(array(
            'name' => 'czas_na_pacjenta',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
                'style' => 'width: 70px;',
            ),
        ));  
        
        for($i = 0; $i < 7; $i++){
            $this->add(array(
                'name' => 'grafik_'.$i,
                'attributes' => array(
                    'type'  => 'text',
                    'class' => 'form-control',
                    'placeholder'=>'np. 8.00-14.00;15.30-16.00',
                ),
            ));  
        }

        $this->add(array(
            'name' => 'specjalnosc',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => array(
					'0' => ''
				),
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 250px;',
            )
        ));
        
        $this->add(array(
            'name' => 'plec',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => array(
					'M' => 'mężczyzna',
                    'K' => 'kobieta'
				),
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 250px;',
            )
        ));
		
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Zapisz',
                'class' => 'btn btn-primary',
                'id' => 'submitbutton',
            ),
        )); 
    }
}