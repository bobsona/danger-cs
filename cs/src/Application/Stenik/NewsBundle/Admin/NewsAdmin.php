<?php

namespace Application\Stenik\NewsBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use  Stenik\NewsBundle\Admin\NewsAdmin as BaseNewsAdmin;
/**
* 
*/
class NewsAdmin extends BaseNewsAdmin
{
     /**
     * Configure the list
     *
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $list list
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('title', null, array('label' => 'list.title'))
            ->add('is_homepage', null, array('label' => 'list.is_homepage', 'editable' => true))
            ->add('is_hidden', null, array('label' => 'list.is_hidden', 'editable' => true))
            ->add('created_at', null, array('label' => 'list.created_at'))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        'history' => array('template' => 'StenikCoreBundle:Admin:list_action_history.html.twig'),
                    ), 'label' => 'actions'
                ))
            ;
    }

	public function configureFormFields(FormMapper $formMapper)
    {
        $trans = $this->getTranslator();
        $choices = array(
            1 => $trans->trans('food_recipes', array(), 'StenikNewsBundle'),
            2 => $trans->trans('music', array(), 'StenikNewsBundle'),
            3 => $trans->trans('interesting_facts', array(), 'StenikNewsBundle'),
            4 => $trans->trans('drinks', array(), 'StenikNewsBundle'),
            5 => $trans->trans('american_culture', array(), 'StenikNewsBundle'),
        );

        $formMapper
            ->with('General')
                ->add('published_date', 'datetime', array('label'=>'form.published_date', 'format' => 'YY-M-dd HH:mm:ss' ,'widget' => 'single_text', 'required' => true,  'attr' => array('class' => 'datepicker')))
                ->add('category', 'choice', array('label'=>'form.category', 'choices' => $choices, 'required' => true))
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'slug' => array(
                            'field_type' => 'text',
                            'required' => false,
                            'label' => 'form.slug'
                        ),
                        'title' => array(
                            'field_type' => 'text',
                            'label' => 'form.title'
                        ),
                        'simple_description' => array(
                            'required' => false,
                            'label' => 'form.simple_description'
                        ),
                        'description' => array(
                            'field_type' => 'textarea',
                            'required' => false,
                            'attr' => array(
                                'class' => 'tinymce',
                                'data-theme' => 'bbcode'
                            ),
                            'label' => 'form.description'
                        ),
                        'meta_title' => array(
                            'required' => false,
                            'label' => 'form.meta_title'
                        ),
                        'meta_description' => array(
                            'required' => false,
                            'label' => 'form.meta_description'
                        ),
                        'meta_keywords' => array(
                            'required' => false,
                            'label' => 'form.meta_keywords'
                        ),
                    ),
                    'translation_domain' => 'StenikNewsBundle',
                    'label' => 'form.translations'
                ))
                ->add('image', 'sonata_type_model_list', array('required' => false, 'label' => 'form.image'), array(
                    'link_parameters' => array(
                        'context' => 'stenik_news'
                    )
                ))
                ->add('is_homepage', 'checkbox', array('required' => false, 'label' => 'form.is_homepage'))
                ->add('is_hidden', 'checkbox', array('required' => false, 'label' => 'form.is_hidden'))
            ->end();
    }
}