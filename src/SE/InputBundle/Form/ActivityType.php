<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActivityType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',   'text')
            ->add('default_target', 'number', array('required' => false))
            ->add('teams', 'entity', array(
                'class'    => 'SEInputBundle:Team',
                'property' => 'name', 
                'multiple' => true,
                'expanded' => true
                ))
            ->add('activity_zones', 'collection', array(
                'type'         => new ActivityZoneType(),
                'allow_add'    => true,
                'allow_delete' => true
                ), array('required' => false))
            ->add('workstations', 'collection', array(
                'type'         => new WorkstationType(),
                'allow_add'    => true,
                'allow_delete' => true
                ), array('required' => false))
            ->add('save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SE\InputBundle\Entity\Activity'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'se_inputbundle_activity';
    }
}
