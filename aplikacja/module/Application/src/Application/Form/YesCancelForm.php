<?php
namespace Application\Form;

use Zend\Form\Form;

class YesCancelForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('registration');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

        $this->add(array(
            'name' => 'submity',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Tak',
                'class' => 'btn btn-default'
            ),
        )); 
        
        $this->add(array(
            'name' => 'submitc',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Anuluj',
                'class' => 'btn btn-default'
            ),
        )); 
    }
}