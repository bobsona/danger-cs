<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordChangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'SonataUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_again'),
                'invalid_message' => 'form.password_mismatch',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('_submit', 'submit', array('label' => 'form.submit', 'attr' => array('class' => 'button big col3 noMarginR')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\UserBundle\Entity\User',
            'intention'  => 'stenik_user_password_change',
            'translation_domain' => 'SonataUserBundle',
        ));
    }

    public function getName()
    {
        return 'stenik_user_password_change';
    }
}
