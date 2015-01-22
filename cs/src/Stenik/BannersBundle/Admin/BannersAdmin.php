<?php
/**
 * This file is part of the StenikBannersBundle.
 *
 * (c) Georgi Gyurov <georgi@stenik.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Stenik\BannersBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
/**
 *  Admin class for Banners
 *
 * @package StenikBannersBundle
 * @author Georgi Gyurov <georgi@stenik.bg>
 */
class BannersAdmin extends Admin
{
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
         '_page' => 1,
         '_sort_order' => 'ASC',
         '_sort_by' => 'id',
    );

    /**
     * Where should the link goes (new window or same window)
     * @var array
     */
    private $choices = array(
        '_self' => 'В същия прозорец',
        '_blank' => 'В нов прозорец',
    );

    /**
     * {@inheritdoc}
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        $actions['hide'] = [
            'label'            => $this->trans('action_hide', array(), 'StenikCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        ];
        $actions['show'] = [
            'label'            => $this->trans('action_show', array(), 'StenikCoreBundle'),
            'ask_confirmation' => true, // If true, a confirmation will be asked before performing the action
        ];

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        parent::configureTabMenu($menu, $action, $childAdmin);

        if ($action == 'history') {
            $id = $this->getRequest()->get('id');
            $menu->addChild(
                "General",
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

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('history', $this->getRouterIdParameter().'/history');
        $collection->add('history_view_revision', $this->getRouterIdParameter().'/preview/{revision}');
        $collection->add('history_revert_to_revision', $this->getRouterIdParameter().'/revert/{revision}');
        $collection->add('order', 'order');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title', null, array('label' => 'form.title'))
            ->add('url', null, array('label' => 'form.url'))
            ->add('target', null, array('label' => 'form.target'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('title', null, array('label' => 'list.title'))
            ->add('url', null, array('label' => 'list.url', 'editable' => true))
            ->add('target', null, array('label' => 'list.target', 'editable' => true))
            ->add('isHidden', null, array('label' => 'list.is_hidden', 'editable' => true))
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'edit' => array(),
                        'delete' => array(),
                        'history' => array('template' => 'StenikCoreBundle:Admin:list_action_history.html.twig'),
                    ), 'label' => 'actions',
                ))
            ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
                ->add('cinemas', null, array(
                        'label' => 'form.movies',
                        'by_reference' => false,
                        'required' => false,
                        'multiple' => true,
                    )
                )
                ->add('zone', null, array())
                ->add('translations', 'a2lix_translations', array(
                    'fields' => array(
                        'title' => array(
                            'field_type' => 'text',
                            'label' => 'form.title',
                        ),
                        'url' => array(
                            'required' => false,
                            'label' => 'form.url',
                        ),
                        'target' => array(
                            'field_type' => 'choice',
                            'label' => 'form.target',
                            'choices' => $this->choices,
                        ),
                        'banner' => array(
                            'field_type' => 'sonata_media_type',
                            'provider' => 'sonata.media.provider.image',
                            'data_class'   =>  'Application\Sonata\MediaBundle\Entity\Media',
                            'required' => false,
                            'label' => 'form.image',

                            'empty_on_new'  => true,
                            'new_on_update' => false,
                        ),
                    ),
                    'translation_domain' => 'StenikBannersBundle',
                    'label' => 'form.translations',
                ))
                ->add('is_hidden', 'checkbox', array('required' => false, 'label' => 'form.is_hidden'))
            ->end();
    }
}
