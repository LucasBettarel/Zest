<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InputEntryType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('employee', 'entity', array(
                'class'    => 'SEInputBundle:Employee',
                'property' => 'name', 
                'multiple' => false,
                'expanded' => false
                ), array('required' => true))
            ->add('sesa', 'text', array('required' => false))
            ->add('present', 'checkbox', 
                    array('data' => true,
                          'required' => false))
            ->add('absence_reason', 'entity', array(
                'class'    => 'SEInputBundle:AbsenceReason',
                'property' => 'absenceSelect', 
                'multiple' => false,
                'expanded' => false
                ), array('required' => false))
            ->add('activity_hours', 'collection', array(
                'type'         => new ActivityHoursType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'prototype_name' => 'activity_name',
                ), array('required' => false))
            ->add('comments', 'textarea', array('required' => false))
            ->add('totalTo', 'integer', array('required' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SE\InputBundle\Entity\InputEntry'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'se_inputbundle_inputentry';
    }
}
