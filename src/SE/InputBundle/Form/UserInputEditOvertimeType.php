<?php

namespace SE\InputBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class UserInputEditOvertimeType extends AbstractType
{

    /**
     * @var \DateTime
     */
    private $date;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          ->remove('user')
          ->remove('team')
          ->remove('shift')
          ->remove('dateInput')
          ->remove('input_entries')
          ->add('process', 'checkbox', 
              array('data' => true,
              'required' => false))        
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'se_inputbundle_userinput_editovertime';
    }

    public function getParent()
    {
      return new UserInputType();
    }
}
