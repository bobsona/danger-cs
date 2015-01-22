<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class PersonalInformationType extends AbstractType
{
    private $genderChoices = array(
        'm' => 'Мъж',
        'f' => 'Жена',
    );

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('gender', 'choice', array(
                'choices' => $this->genderChoices,
                'required' => true,
                'label' => 'form.gender',
            ), array('expanded' => true,
                'mupltiple' => false, ))
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
                'read_only' => true,
                'disabled' => true,
                'constraints' => array(
                    new NotBlank(array('message' => 'required_field')),
                    new Email(),
                ),
            ))
            ->add('phone', 'phone', array(
                'required' => true,
                'label' => 'form.phone',
                'constraints' => array(
                    new NotBlank(array('message' => 'required_field')),
                ),
            ))
            ->add('city', 'text', array(
                'required' => true,
                'label' => 'form.city',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('post_code', 'number', array(
                'required' => true,
                'label' => 'form.postCode',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('address', 'text', array(
                'required' => true,
                'label' => 'form.address',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('date_of_birth', null, array(
                'required' => true,
                'label' => 'form.dateOfBirth',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('education', 'text', array(
                'required' => true,
                'label' => 'form.education',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('workingPosition', 'text', array(
                'required' => true,
                'label' => 'form.workingPosition',
                'constraints' => new NotBlank(array('message' => 'required_field')),
            ))
            ->add('_submit', 'submit', array('label' => 'form.submit', 'attr' => array('class' => 'button big col3 noMarginR')))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Application\Sonata\UserBundle\Entity\User',
            'intention'  => 'stenik_user_personal_information',
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
        return 'stenik_user_personal_information';
    }
}
