<?php
namespace Uzytkownik\Form;

use Zend\Form\Form;

class UserForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('rejestracja');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline'); //form-inline jest z bootsratapa
        
              
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
            'name' => 'data_ur_dd',
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
            'name' => 'data_ur_mm',
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
            'name' => 'data_ur_rr',
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
		
		$this->add(array(
            'name' => 'haslo',
            'attributes' => array(
                'type'  => 'password',
                'class' => 'form-control',
              ),
        ));
		
		$this->add(array(
            'name' => 'haslo_powt',
            'attributes' => array(
                'type'  => 'password',
                'class' => 'form-control',
              ),
        ));
        
        $this->add(array(
            'name' => 'zmien_haslo',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
                'id' => 'switch_zmien_haslo'
              ),
            'options' => array(
                     'checked_value' => 'tak',
                     'unchecked_value' => 'nie'
             )
        ));
        
    }
}