<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InterestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cinema', 'entity', array(
                'mupltiple' => false,
                'expanded' => false,
                'property' => 'title',
                'required' => false,
                'label' => 'form.cinema',
                ), array(
                    'expanded' => false,
                    'mupltiple' => false,
                )
            )
            ->add('favouriteMovies', 'text', array(
                'required' => true,
                'label' => 'form.favouriteMovies',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('favouriteActors', 'text', array(
                'required' => true,
                'label' => 'form.favouriteActors',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('categories', 'collection', array(
                // each item in the array will be an "checkbox" field
                'type'   => 'checkbox',
                // these options are passed to each "checkbox" type
                'options'  => array(
                    'required'  => false,
                    'attr'      => array('class' => 'email-box'),
                ),
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'label' => 'form.categories',
                'required' => true,
            ))
            ->add('interests', 'collection', array(
                // each item in the array will be an "email" field
                'type'   => 'checkbox',
                // these options are passed to each "email" type
                'options'  => array(
                    'required'  => false,
                    'attr'      => array('class' => 'email-box'),
                ),
                'constraints' => new NotBlank(array('message' => 'required_field')),
                'label' => 'form.interests',
                'required' => true,
            ))
            ->add('_submit', 'submit', array('label' => 'form.submit', 'attr' => array('class' => 'button big col3 noMarginR')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\UserBundle\Entity\User',
            'intention'  => 'stenik_user_interest',
            'translation_domain' => 'SonataUserBundle',
        ));
    }

    public function getName()
    {
        return 'stenik_user_interest';
    }
}
