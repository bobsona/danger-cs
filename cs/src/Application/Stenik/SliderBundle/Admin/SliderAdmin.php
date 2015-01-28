<?php

namespace Application\Stenik\SliderBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;

use  Stenik\SliderBundle\Admin\SliderAdmin as BaseSliderAdmin;
/**
* 
*/
class SliderAdmin extends BaseSliderAdmin
{
    private $choices = array(
        '_self' => 'В същия прозорец',
        '_blank' => 'В нов прозорец'
    );

     protected $datagridValues = array(
         '_page' => 1,
         '_sort_order' => 'ASC',
         '_sort_by' => 'rank',
     );

	/**
     * Configure the form
     *
     * @param FormMapper $formMapper formMapper
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'title' => array(
                            'field_type' => 'text',
                            'label' => 'form.title',
                            'required' => false
                        ),
                       'description' => array(
                            'field_type' => 'textarea',
                            'required' => false,
                            'label' => 'form.description',
                        ),
                        'file' => array( 
                            'field_type' => 'sonata_media_type', 
                            'provider' => 'sonata.media.provider.image',
                            'context'  => 'stenik_slider',
                            'data_class'   =>  'Application\Sonata\MediaBundle\Entity\Media',
                            'required' => false,
                            'label' => 'form.file',

                            'empty_on_new'  => true,
                            'new_on_update' => false,
                        ),
                        'alt' => array(
                            'required' => false,
                            'label' => 'form.alt'
                        ),

                    ),
                        'label' => 'form.translations',
                        'translation_domain' => 'StenikSliderBundle',
                        'exclude_fields' => array('url', 'target')
                ))
                ->add('is_hidden', 'checkbox', array('required'=>false, 'label' => 'form.is_hidden', 'translation_domain' => 'StenikSliderBundle'))
            ->remove('url')
            ->remove('target')
            ->end();
    }
}