<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Nom', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('Prenom', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('Email', EmailType::class, array('attr' => array('class' => 'form-control')))
            ->add('Age', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('Nationalite', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('TarifReduit', TextType::class, array('attr' => array('class' => 'form-control')))
            ->add('Prix', TextType::class, array('attr' => array('class' => 'form-control')));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Client'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_client';
    }


}
