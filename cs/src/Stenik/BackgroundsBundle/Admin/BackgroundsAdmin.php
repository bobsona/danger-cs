<?php
/**
 * This file is part of the StenikBackgroundsBundle.
 *
 * (c) Georgi Gyurov <georgi@stenik.bg>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Stenik\BackgroundsBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
/**
 *  Admin class for Backgrounds
 *
 * @package StenikBackgroundsBundle
 * @author Georgi Gyurov <georgi@stenik.bg>
 */
class BackgroundsAdmin extends Admin
{
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
    protected function configureListFields(ListMapper $list)
    {
        $list
            ->addIdentifier('image', null, array('label' => 'list.title'))
            ->add('isHidden', null, array('label' => 'list.isHidden'))
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
                ->add('image', 'sonata_type_model_list', array('required' => false, 'label' => 'form.image'), array(
                    'link_parameters' => array(
                        'context' => 'stenik_backgrounds',
                    ),
                ))
                ->add('isHidden', null, array('required' => false, 'label' => 'form.isHidden'))
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    public function prePresist($item)
    {
        $this->processBackground();
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($item)
    {
        $this->processBackground();
    }

    /**
     * Processing background's visibility
     *
     * @param  \Stenik\BackgroundsBundle\Entity\Background $item
     * @return \Stenik\BackgroundsBundle\Entity\Background $item
     */
    private function processBackground($item)
    {
        $settingsManager = $this->getConfigurationPool()->getContainer()->get('settings_manager');
        $em = $this->getConfigurationPool()->getContainer()->get('doctrine')->getManager();
        $background = $settingsManager->get('background');
        if ($background == 0) {
            $repo = $em->getRepository('StenikBackgroundsBundle:Background');
            $backgrounds  = $repo->findAll();
            foreach ($backgrounds as $bg) {
                $bg->setIsHidden(1);
                $em->persist($bg);
            }
            $em->flush();
        }

        return $item;
    }
}
