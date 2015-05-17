<?php

namespace SE\ZestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AdvertType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('date',      'date')
        ->add('title',     'text')
        ->add('author',    'text')
        ->add('content',   'textarea')
        ->add('image',      new ImageType())
        ->add('categories', 'entity', array(
            'class'    => 'SEZestBundle:Category',
            'property' => 'name', 
            'multiple' => true,
            'expanded' => false
          ))
        ->add('save',      'submit')
        ;

        $builder->addEventListener(
          FormEvents::PRE_SET_DATA,
          function(FormEvent $event) {
            // On récupère notre objet Advert sous-jacent
            $advert = $event->getData();

            if (null === $advert) {
              return;
            }

            if (!$advert->getPublished() || null === $advert->getId()) {
              // Si l'annonce n'est pas publiée, ou si elle n'existe pas encore en base (id est null),
              // alors on ajoute le champ published
              $event->getForm()->add('published', 'checkbox', array('required' => false));
            } else {
              // Sinon, on le supprime
              $event->getForm()->remove('published');
            }
          }
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SE\ZestBundle\Entity\Advert'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'se_zestbundle_advert';
    }
}
