<?php

namespace Instudies\AdminBundle\Form\Management\User\Edit;

use
    Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilder,
    Symfony\Component\Form\CallbackValidator
;

class ManagementUserEditType extends AbstractType
{

    public function buildForm(FormBuilder $builder, array $options)
    {

        $builder
            ->add('firstname', 'text', array('required' => false, 'label' => 'Имя:'))
            ->add('lastname', 'text', array('required' => false, 'label' => 'Фамилия:'))
            ->add('email', 'email', array('required' => false, 'label' => 'Email:'))
            ->add('emailActivated', 'checkbox', array('required' => true, 'label' => 'Email активирован:'))
            ->add('filledAllInformation', 'checkbox', array('required' => true, 'label' => 'Информация заполнена:'))   
            ->add('password', 'text', array('required' => false, 'label' => 'Пароль:'))
        ;

    }

    public function getDefaultOptions(array $options) 
    {
        return array('data_class' => 'Instudies\SiteBundle\Entity\User');
    }
    
    public function getName()
    {
        return 'management_user_edittype';
    }

}

