<?php

namespace Application\Stenik\ContentBundle\Admin;

use Sonata\AdminBundle\Form\FormMapper;

use  Stenik\ContentBundle\Admin\ContentAdmin as BaseContentAdmin;
/**
* 
*/
class ContentAdmin extends BaseContentAdmin
{
	public function configureFormFields(FormMapper $formMapper)
    {
 		$stenikAdmin = $this->getConfigurationPool()->getContainer()->get('security.context')->isGranted('ROLE_STENIK_ADMIN');
        $object = $this->getSubject();

        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $repo = $em->getRepository($this->getClass());
        $query = $repo->createQueryBuilder('c')
            ->select('c.id')
            ->where('c.lft > :lft and c.rgt < :rgt and c.root = :root')
            ->setParameters(array(
                'lft' => $object->getLft(), 
                'rgt' => $object->getRgt(),
                'root'=> $object->getRoot(),
            ))
            ->getQuery();
        $allChildrenIds = $query->getArrayResult();
        $disabled_ids = array_map(function($obj) {
            return $obj['id'];
        }, $allChildrenIds);

        $a2lixFields = array('fields' => array(
                    'slug' => array(
                        'field_type' => 'text',
                        'label' => 'form.slug',
                        'required' => false
                    ),
                    'title' => array(
                        'field_type' => 'text',
                        'label' => 'form.title'
                    ),
                    'description' => array(
                        'field_type' => 'textarea',
                        'label' => 'form.description',
                        'required' => false,
                        'attr' => array(
                            'class' => 'tinymce',
                            'data-theme' => 'bbcode'
                        )
                    ),
                    'image' => array( 
			            'field_type' => 'sonata_media_type', 
			            'provider' => 'sonata.media.provider.image',
			            'context'  => 'stenik_content',
			            'data_class'   =>  'Application\Sonata\MediaBundle\Entity\Media',
			            'required' => false,
			            'label' => 'form.image',

			            'empty_on_new'  => true,
			            'new_on_update' => false,
			        ),
                ),
                'translation_domain' => 'StenikContentBundle',
                'label' => 'form.translations'
        );
        if (!$object->isNew() && $object->getIsSystem()) {    
            $a2lixFields['fields']['slug']['display'] = false;
        }

        $formMapper
            ->with('General', array('collapsed' => true, 'class' => 'col-md-9'))
                ->add('parent', 'stenik_tree', array('required' => false, 'label' => 'form.parent',
                    'class' => 'Application\Stenik\ContentBundle\Entity\Content',
                    'orderFields' => array('root', 'lft'),
                    'treeLevelField' => 'lvl',
                    'max_level' => 1,
                    'disabled_ids' => $disabled_ids
                ))
                 ->add('gallery', 'sonata_type_model_list', array('required' => false, 'label' => 'form.gallery'), array(
                    'link_parameters' => array(
                        'context' => 'stenik_content'
                    ), 
                ))
                ->add('translations', 'a2lix_translations', $a2lixFields)
            ->end()
        ->end()

        ->with('More', array('collapsed' => true, 'class' => 'col-md-3'));
            if ($stenikAdmin)
                $formMapper->add('is_system', 'checkbox', array('required'=>false, 'label' => 'form.is_system'));
            
        $formMapper->end();
    }
}