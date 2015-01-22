<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Admin\Entity;

use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sonata\UserBundle\Model\UserInterface;
/**
 *  Modified users's admin
 *
 * @package ApplicationSonataUserBundle
 * @author  Nikolay Tumbalev <n.tumbalev@stenik.bg>
 */
class UserAdmin extends BaseUserAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('email')
            ->add('firstname')
            ->add('lastname')
            ->add('enabled', null, array('editable' => true))
            ->add('locked', null, array('editable' => true))
            ->add('createdAt')
            ->remove('batch')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'delete' => array(),

                ), 'label' => 'table.label_actions',
            ))
        ;

        /*if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $listMapper
                ->add('impersonating', 'string', array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig'))
            ;
        }*/
    }
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $trans = $this->getTranslator();
        $formMapper
        ->with('tab.general', array('tab' => true))
            ->with('tab.general')
                ->add('gender', 'choice', array(
                    'expanded' => true,
                    'choices' => array(
                        UserInterface::GENDER_FEMALE => 'gender_female',
                        UserInterface::GENDER_MALE   => 'gender_male',
                    ),
                    'translation_domain' => 'SonataUserBundle',
                ))
                ->add('firstname')
                ->add('lastname')
                ->add('email')
                ->add('city', null, array('required' => false))
                ->add('address', null, array('required' => false))
                ->add('phone', null, array('required' => false))
            ->end()
        ->end();

        if (!$this->getSubject()->hasRole('ROLE_ADMIN')) {
            $formMapper
            ->with('tab.password_change', array('tab' => true))
                ->with('tab.password_change')
                    ->add('plainPassword', 'text', array(
                        'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                    ))
                ->end()
            ->end()
            ;
        }

        if (!$this->getSubject()->hasRole('ROLE_ADMIN')) {
            $formMapper
            ->with('tab.interest', array('tab' => true))
                ->with('tab.interest')
                    ->add('cinema', null)
                    ->add('favouriteMovies', null)
                    ->add('favouriteActors', null)
                    ->add('categories', null)
                    ->add('interests', null)
                ->end()
            ->end()
            ;
        }

        if (!$this->getSubject()->hasRole('ROLE_ADMIN')) {
            $formMapper
            ->with('tab.my_movies', array('tab' => true))
                ->with('tab.my_movies')
                    ->add('movies', null)
                ->end()
            ->end()
            ;
        }

        if ($this->getSubject() && $this->getSubject()->hasRole('ROLE_SUPER_ADMIN')) {
            $formMapper
            ->with('tab.management', array('tab' => true))
                ->with('tab.management',  array('translation_domain' => 'SonataUserBundle'))
                    ->add('groups', 'sonata_type_model', array(
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                    ))
                    ->add('realRoles', 'sonata_security_roles', array(
                        'label'    => 'form.label_roles',
                        'expanded' => true,
                        'multiple' => true,
                        'required' => false,
                        'translation_domain' => 'SonataUserBundle',

                    ))
                    ->add('locked', null, array('required' => false))
                    ->add('expired', null, array('required' => false))
                    ->add('enabled', null, array('required' => false))
                    ->add('credentialsExpired', null, array('required' => false))
                ->end()
            ->end()
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->remove('export');
    }

    /**
     * {@inheritdoc}
     */
    public function preRemove($item)
    {
        $realRoles = $item->getRealRoles();
        foreach ($realRoles as $role) {
            if ($role == 'ROLE_SUPER_ADMIN') {
                throw new AccessDeniedException("Error processing request! You are not authorized to delete SUPER ADMINS");
            }
        }
    }
}
