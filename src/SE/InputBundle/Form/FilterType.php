<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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
            ->add('save',      'submit')
            ;
    }
    
    /*
     * @param OptionsResolverInterface $resolver
     *
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SE\InputBundle\Entity\ActivityHours'
        ));
    }
    */
    
    /**
     * @return string
     */
    public function getName()
    {
        return 'se_inputbundle_filter';
    }
}
