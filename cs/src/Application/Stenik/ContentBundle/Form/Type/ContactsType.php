<?php 
namespace Application\Stenik\ContentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->add('name', 'text', array(
               'attr' => array('placeholder' => 'name'),
                'translation_domain' => 'StenikContentBundle',
                'required' => true,
                ))
            ->add('email', 'email', array(
               'attr' => array('placeholder' => 'email'),
               'translation_domain' => 'StenikContentBundle',
               'required' => true,
               ))
            ->add('subject', 'text', array(
               'attr' => array('placeholder' => 'subject'),
               'translation_domain' => 'StenikContentBundle',
               'required' => true,
               ))
            ->add('message', 'textarea', array(
               'attr' => array('placeholder' => 'message'),
               'translation_domain' => 'StenikContentBundle',
               'required' => true,
               ))
            ->add('captcha', 'captcha', array(
                'translation_domain' => 'StenikContentBundle',
                'attr' => array('id' => 'captcha_input'),
                'quality' => 90,
                'as_url' => true,
                'reload' => true,
                'width' => 120,
                'height' => 40,
                'length' => 6,
            ))
            ->add('submit', 'submit');
    }

    public function getName()
    {
        return 'contacts';
    }
}

?>