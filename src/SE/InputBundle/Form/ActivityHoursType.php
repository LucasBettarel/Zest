<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

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
                'expanded' => false,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                            ->select('a')
                            ->where("a.statusControl = 1")
                            ->orderBy('a.name', 'ASC');
                    }
                ), array('required' => true))
            ->add('regularHours', 'number')
            ->add('otHours', 'number')
            /*->add('zone', 'entity', array(
                'class'    => 'SEInputBundle:Zone',
                'property' => 'name', 
                'multiple' => false,
                'expanded' => false
                ), array('required' => false))*/
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
