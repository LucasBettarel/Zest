<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActivityHoursType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('activity', 'entity', array(
                'class'    => 'SEInputBundle:Activity',
                'property' => 'name', 
                'multiple' => false,
                'expanded' => false
                ), array('required' => true))
            ->add('regularHours', 'number')
            ->add('otHours', 'number')
            ->add('otStartTime', 'time')
            ->add('otEndTime', 'time')
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
                'expanded' => false
                ), array('required' => true))
            ->add('zone', 'entity', array(
                'class'    => 'SEInputBundle:Zone',
                'property' => 'name', 
                'multiple' => false,
                'expanded' => false
                ), array('required' => false))
            ->add('workstation', 'entity', array(
                'class'    => 'SEInputBundle:Workstation',
                'property' => 'name', 
                'multiple' => false,
                'expanded' => false
                ), array('required' => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SE\InputBundle\Entity\ActivityHours'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'se_inputbundle_activityhours';
    }
}
