<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserInputType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', 'text')
            ->add('team', 'entity', array(
                'class'    => 'SEInputBundle:Team',
                'property' => 'name', 
                'multiple' => false,
                'expanded' => false
                ), array('required' => true))
            ->add('shift', 'entity', array(
                'class'    => 'SEInputBundle:Shift',
                'property' => 'identifier', 
                'multiple' => false,
                'expanded' => true
                ), array('required' => true))
            ->add('input_entries', 'collection', array(
                'type'         => new InputEntryType(),
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype' => true,
                'by_reference' => false,
                'prototype_name' => 'entry_name',
                ))
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SE\InputBundle\Entity\UserInput'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'se_inputbundle_userinput';
    }
}
