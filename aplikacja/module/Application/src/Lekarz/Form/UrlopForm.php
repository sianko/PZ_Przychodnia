<?php
namespace Lekarz\Form;

use Zend\Form\Form;

class UrlopForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('registration');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-inline');

       
        
        for($i=1; $i<=31; $i++)
        {
            $dni[$i] = $i;
        }
        
        for($i=1; $i<=12; $i++)
        {
            $msc[$i] = $i;
        }
        
        for($i=1900; $i<=date('Y')+5; $i++)
        {
            $rok[$i] = $i;
        }
        
        for($i=1; $i<=59; $i++)
        {
            $min[$i] = $i;
        }
        
        for($i=1; $i<=24; $i++)
        {
            $godz[$i] = $i;
        }
        


        $this->add(array(
            'name' => 'data_od_0',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $dni,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 75px;',
                'value' => date("d")
            )
        ));

        $this->add(array(
            'name' => 'data_od_1',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => $msc,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 75px;',
                'value' => date("m")
            )
        ));
        
        $this->add(array(
            'name' => 'data_od_2',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => $rok,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 100px;',
                'value' => date("Y")
            )
        ));
        
        $this->add(array(
            'name' => 'data_od_3',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => $godz,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 75px;',
                'value' => date("H")
            )
        ));        

         $this->add(array(
            'name' => 'data_od_4',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => $min
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 75px;',
                'value' => date("i")
            )
        ));

        $this->add(array(
            'name' => 'data_do_0',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $dni,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 75px;',
                'value' => date("d")
            )
        ));

        $this->add(array(
            'name' => 'data_do_1',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => $msc,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 75px;',
                'value' => date("m")
            )
        ));
        
        $this->add(array(
            'name' => 'data_do_2',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => $rok,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 100px;',
                'value' => date("Y")
            )
        ));
        
        $this->add(array(
            'name' => 'data_do_3',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => $godz,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 75px;',
                'value' => date("H")
            )
        ));        

         $this->add(array(
            'name' => 'data_do_4',
			'type' => 'Zend\Form\Element\Select',
            'options' => array(
				'value_options' => $min,
            ),
            'attributes' => array(
                'class' => 'form-control',
                'style' => 'width: 75px;',
                'value' => date("i")
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