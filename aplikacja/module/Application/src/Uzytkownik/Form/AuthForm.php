<?php
namespace Uzytkownik\Form;

use Zend\Form\Form;

class AuthForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('auth');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        
        $this->add(array(
            'name' => 'pesel',
            'attributes' => array(
                'type'  => 'text',
                'class' => 'form-control',
                'id'    => 'inputLogin3',
                'placeholder' => 'Numer PESEL'
            ),
            'options' => array(
                'label' => 'PESEL',
            ),
        ));
        $this->add(array(
            'name' => 'haslo',
            'attributes' => array(
                'type'  => 'password',
                'class' => 'form-control',
                'id'    => 'inputPassword3',
                'placeholder' => 'Hasło'
            ),
            'options' => array(
                'label' => 'Hasło',
            ),
        ));
        $this->add(array(
            'name' => 'rememberme',
			'type' => 'checkbox', // 'Zend\Form\Element\Checkbox',			
//            'attributes' => array( // Is not working this way
//                'type'  => '\Zend\Form\Element\Checkbox',
//            ),
            'options' => array(
                'label' => 'Pamiętaj mnie',
//				'checked_value' => 'true', without value here will be 1
//				'unchecked_value' => 'false', // witll be 1
            ),
        ));			
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Zaloguj',
                'id' => 'submitbutton',
                'class' => 'btn btn-default',
            ),
        )); 
    }
}