<?php

namespace Application\Stenik\ContentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;

use Stenik\ContentBundle\Entity\Content;

class ContentFrontendController extends Controller
{
    /**
     * @Route("/{slug}", name="content")
     * @Template("ApplicationStenikContentBundle:Frontend:content.html.twig")
     */
    public function indexAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $contentRepo = $em->getRepository('ApplicationStenikContentBundle:Content');
        $bannerRepo = $em->getRepository('StenikBannersBundle:Banner');

        $content = $contentRepo->findOneBySlugAndLocale($request->getLocale(), $slug);
        if (!$content || $content->getIsHidden()) {
            throw new NotFoundHttpException("Page not found");
            
        }
        $children = $content->getChildren();
        if ($children) {
            if (count($children) && mb_strlen($content->getDescription()) < 1) {
                foreach ($children as $child) {
                    if (!$child->getIsHidden() && mb_strlen($child->getDescription()) > 0) {
                        return $this->redirect($this->generateUrl($child->getRoute(), $child->getRouteParams()));
                    }
                }
            }
        }
        $gallery = $content->getGallery();
        $leftSidebar = $this->executeLeftSideBar($content->getParent()->getLvl() != 0 ? $content->getParent() : $content);
        $banner = $bannerRepo->find(1);
        if (!$content) {
            throw new NotFoundHttpException("Page not found!");
        }
        return array(
            'content' => $content,
            'leftSidebar' => $leftSidebar,
            'root' => $content->getParent()->getLvl() != 0 ? $content->getParent() : $content,
            'banner' => $banner,
            'gallery' => $gallery
        );
    }

    private function executeLeftSideBar($root)
    {
        $menu = $this->createMenu($root);
        $renderer = new ListRenderer(new \Knp\Menu\Matcher\Matcher());
        
        if ($menu !== null) {
            return $renderer->render($menu);
        }
        return $menu;
    }

    private function createMenu($root) {
        $factory = new MenuFactory();
        $menu = $factory->createItem('My menu');
        if (count($root->getChildren())) {
            foreach($root->getChildren() as $item) {
                if (strpos($this->container->get('request')->getRequestUri(), $this->generateUrl($item->getRoute(), $item->getRouteParams(array('slug' => $item->getSlug())))) !== false) {
                    $menu->addChild($item->getTitle(), array('attributes' => array('class'=> 'selected'), 'uri' => $this->generateUrl($item->getRoute(), $item->getRouteParams(array('slug' => $item->getSlug())))));
                }else{
                    $menu->addChild($item->getTitle(), array('uri' => $this->generateUrl($item->getRoute(), $item->getRouteParams(array('slug' => $item->getSlug())))));
                }  
                if(count($children = $item->getChildren())) {
                    $this->createMenu($children, $menu);
                }
            }
            return $menu;
        }
    }

    /**
     * @Route("/contacts", name="contacts")
     * @Template("ApplicationStenikContentBundle:Frontend:contacts.html.twig")
     */
    public function contactsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $settingsManager = $this->get('settings_manager');
        $latitude = $settingsManager->get('latitude');
        $longitude = $settingsManager->get('longitude');
        $content = $em->getRepository('ApplicationStenikContentBundle:Content')->find(20);
        $breadCrumbs = array($content->getTitle() => null);
        $session = $this->get('session');
        $form = $this->createForm('contacts', null, array('action' => $this->generateUrl('contacts', array(), true)));
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $data = $form->getData();
                
                $settings = $this->get('settings_manager');
                $message = \Swift_Message::newInstance()
                    ->setSubject('Съобщение през формата за контакти')
                    ->setFrom(explode(',',$settings->get('contact_mail')))
                    ->setTo($data['email'])
                    ->setBody(
                        $this->renderView(
                            'ApplicationStenikContentBundle:Email:contact_mail.html.twig', array('data'=>$data)
                        )
                    , 'text/html')
                ; 
                $this->get('mailer')->send($message);
                
                $form = $this->createForm('contacts', null, array('action' => $this->generateUrl('contacts', array(), true)));
                
                $session->getFlashBag()->clear();
                $session->getFlashBag()->add('success', 'Your message has been sent.');
            } else {
                $session->getFlashBag()->clear();
                $session->getFlashBag()->add('error', 'Your message has not been sent.');
            }
        }

        return array(
            'form' => $form->createView(),
            'content' => $content,
            'breadCrumbs' => $breadCrumbs,
            'latitude' => $latitude,
            'longitude' => $longitude
        );
    }

    /**
     * @Route("/sitemap", name="sitemap")
     * @Template("ApplicationStenikContentBundle:Frontend:sitemap.html.twig")
     */
    public function siteMapAction()
    {
        $em = $this->getDoctrine()->getManager();
        $locale  = $this->getRequest()->getLocale();
        $sitemapContent = $em->getRepository('ApplicationStenikContentBundle:Content')->find(21);
        $breadCrumbs = array($sitemapContent->getTitle() => null);

        $content = $em->getRepository('ApplicationStenikContentBundle:Content')->findAllByLocale($locale);
        $products = $em->getRepository('StenikProductsBundle:Product')->findAllByLocale($locale);
        $productCategories = $em->getRepository('StenikProductsBundle:ProductCategory')->findAllByLocale($locale);
        $news = $em->getRepository('ApplicationStenikNewsBundle:News')->findAllByLocale($locale);
        $events = $em->getRepository('StenikEventsBundle:Event')->findAllByLocale($locale);
        $careers = $em->getRepository('StenikCareersBundle:Career')->findAllByLocale($locale);
        $partners = $em->getRepository('StenikPartnersBundle:Partner')->findAllByLocale($locale);

        return array(
            'content' => $content,
            'products' => $products,
            'productCategories' => $productCategories,
            'news' => $news,
            'events' => $events,
            'partners' => $partners,
            'sitemapContent' => $sitemapContent,
            'breadCrumbs' => $breadCrumbs
        );
    }
}
