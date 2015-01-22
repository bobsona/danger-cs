<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class RegistrationType extends AbstractType
{
    private $genderChoices = array(
        'm' => 'Мъж',
        'f' => 'Жена',
    );

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', array(
                'required' => true,
                'label' => 'form.firstname',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('lastname', 'text', array(
                'required' => true,
                'label' => 'form.lastname',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('email', 'email', array(
                'required' => true,
                'label' => 'form.email',
                'constraints' => array(
                    new NotBlank(array('message' => 'required_field')),
                    new Email(),
                ),
            ))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'SonataUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_again'),
                'invalid_message' => 'form.password_mismatch',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('gender', 'choice', array(
                'choices' => $this->genderChoices,
                'required' => true,
                'label' => 'form.gender',
            ), array('expanded' => true,
                'mupltiple' => false, ))
            ->add('date_of_birth', 'birthday', array(
                'required' => true,
                'label' => 'form.dateOfBirth',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('city', 'text', array(
                'required' => true,
                'label' => 'form.city',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('cinema', null, array('label' => 'form.cinema'))
            ->add('terms', 'checkbox', array('mapped' => false, 'required' => true, 'label' => 'form.terms'))
            ->add('_submit', 'submit', array('label' => 'form.submit', 'attr' => array('class' => 'button big col3 noMarginR')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\UserBundle\Entity\User',
            'intention'  => 'stenik_user_registration',
            'constraints' => array(
                new UniqueEntity(array(
                    'message' => 'email_exists',
                    'fields' => array('email'),
                )),
            ),
            'translation_domain' => 'SonataUserBundle',
        ));
    }

    public function getName()
    {
        return 'stenik_user_registration';
    }
}
