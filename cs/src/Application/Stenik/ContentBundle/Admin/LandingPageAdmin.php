<?php

namespace Application\Stenik\ContentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
/**
* 
*/
class LandingPageAdmin extends Admin
{
	protected $datagridValues = array(
        '_page' => 1,
        '_sort_order' => 'ASC',
    );

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);
        $trans = $this->getTranslator();
        if($action == 'history') {
            $id = $this->getRequest()->get('id');
            $menu->addChild(
                $trans->trans("menu.item", array(), 'StenikContentBundle'),
                array('uri' => $this->generateUrl('history', array('id' => $id)))
            );

            $locales = $this->getConfigurationPool()->getContainer()->getParameter('locales');

            foreach ($locales as $value) {
                 $menu->addChild(
                    strtoupper($value),
                    array('uri' => $this->generateUrl('history', array('id' => $id, 'locale' => $value)))
                );
            }
        }
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('history', $this->getRouterIdParameter().'/history');
        $collection->add('history_view_revision', $this->getRouterIdParameter().'/preview/{revision}');
        $collection->add('history_revert_to_revision', $this->getRouterIdParameter().'/revert/{revision}');
        $collection->add('order', 'order');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title', null, array('label' => 'form.title'))
            ->add('isHidden', null, array('label' => 'form.is_hidden'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('title', null, array('label' => 'form.title'))
            ->add('isHidden', null, array('label' => 'form.is_hidden', 'editable' => true))
            ->add('createdAt', null, array('label' => 'form.created_at'))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'edit' => array(),
                    'history' => array('template' => 'StenikCoreBundle:Admin:list_action_history.html.twig')
                ), 'label' => 'table.label_actions'
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
            $trans = $this->getConfigurationPool()->getContainer()->get('translator');
            $formMapper
            ->with('form.general', array(
                    'class' => 'col-md-12', 
                    'label' => 'form.general', 
                    'translation_domain' => 'StenikContentBundle'
                )
            )
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'title' => array(
                            'field_type' => 'text',
                            'label' => $trans->trans('form.title', array(), 'StenikContentBundle')
                        ),
                        'description' => array(
                            'field_type' => 'textarea',
                            'label' => 'form.description',
                            'translation_domain' => 'StenikContentBundle',
                            'required' => false,
                            'attr' => array(
                                'class' => 'tinymce',
                                'data-theme' => 'bbcode'
                            )
                        ),
                    ),
                    'label' => 'form.translations',
                    'translation_domain' => 'StenikContentBundle',
                    'exclude_fields' => array('url', 'title'),
                ))
                ->add('is_hidden', 'checkbox', array('label' => 'form.is_hidden', 'required' => false))
            ->end();
       
    }

    public function postPersist($item)
    {
        $this->hideObjectsExcepCurrent($item);
    }

    public function postUpdate($item)
    {
        $this->hideObjectsExcepCurrent($item);
    }

    public function hideObjectsExcepCurrent($item)
    {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $qb = $this->createQuery(
            'SELECT * FROM ApplicationStenikContentBundle:LangindPage'
        );
        
        $query = $qb->getQuery();
        $objects = $query->getResult();

        foreach ($objects as $object) {
            if ($object->getId() !== $item->getId()) {
                $object->setIsHidden(1);
            }
        }
        $em->flush();
    }
}