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
            ->add('regularHours', 'integer')
            ->add('otHours', 'integer')
            ->add('otStartTime', 'time', array(
                  'widget' => 'single_text',
                  'required' => false))
            ->add('otEndTime', 'time', array(
                  'widget' => 'single_text',
                  'required' => false))
            ->add('zone', 'entity', array(
                'class'    => 'SEInputBundle:Zone',
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
