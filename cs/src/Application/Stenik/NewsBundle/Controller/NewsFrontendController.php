<?php

namespace Application\Stenik\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Knp\Menu\MenuFactory;
use Knp\Menu\Renderer\ListRenderer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class NewsFrontendController extends Controller
{   
    
    /**
     * @Route("/news/{page}", name="news" , defaults={"page" = 1})
     * @Template("ApplicationStenikNewsBundle:Frontend:index.html.twig")
     */
    public function indexAction(Request $request, $page)
    {
        $em = $this->getDoctrine()->getManager();
        $trans = $this->get('translator');
        $settingsManager = $this->get('settings_manager');
        
        $content = $em->getRepository('ApplicationStenikContentBundle:Content')->find(12);
        $newsRepo = $em->getRepository('ApplicationStenikNewsBundle:News');
        $pageSize = $settingsManager->get('news_per_page');
        $cat = $request->query->get('category');
        if (isset($cat)) {
            $query = $newsRepo->findAllByCategory($request->getLocale(), $cat, $pageSize, $page);
        }else{   
            $query = $newsRepo->findAllByLocale($request->getLocale(), $pageSize, $page);
        }

        $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query, true);

        $choices = array(
            1 => $trans->trans('food_recipes', array(), 'StenikNewsBundle'),
            2 => $trans->trans('music', array(), 'StenikNewsBundle'),
            3 => $trans->trans('interesting_facts', array(), 'StenikNewsBundle'),
            4 => $trans->trans('drinks', array(), 'StenikNewsBundle'),
            5 => $trans->trans('american_culture', array(), 'StenikNewsBundle'),
        );



        $breadCrumbs = array($trans->trans('news', array(), 'StenikNewsBundle') => null);
        
        return array(
            'paginator' => $paginator,
            'breadCrumbs' => $breadCrumbs,
            'content' => $content,
            'categoriesMapping' => $choices,
            'filter' => isset($cat) ? $cat : null
        );
    }

    /**
     * @Route("/news-detailed/{slug}", name="news_view")
     * @Template("ApplicationStenikNewsBundle:Frontend:view.html.twig")
     */
     public function viewAction(Request $request, $slug)
     {
        $trans = $this->get('translator');
        $choices = array(
            1 => $trans->trans('food_recipes', array(), 'StenikNewsBundle'),
            2 => $trans->trans('music', array(), 'StenikNewsBundle'),
            3 => $trans->trans('interesting_facts', array(), 'StenikNewsBundle'),
            4 => $trans->trans('drinks', array(), 'StenikNewsBundle'),
            5 => $trans->trans('american_culture', array(), 'StenikNewsBundle'),
        );
        $em = $this->getDoctrine()->getManager();
        $newsRepo = $em->getRepository('ApplicationStenikNewsBundle:News');
        
        $news = $newsRepo->findAllByLocale($request->getLocale())->getResult();
        $object = $newsRepo->findOneBySlugAndLocale($request->getLocale(), $slug);
        
        if (!$object) {
            throw new NotFoundHttpException("Page not found!");
        }
        $leftSidebar = $this->executeleftSideBar($news);
        
        $breadCrumbs = array($trans->trans('news', array(), 'StenikNewsBundle') => $this->generateUrl('news'), $object->getTitle() => null);
        
        return array(
            'item' => $object,
            'breadCrumbs' => $breadCrumbs,
            'leftSidebar' => $leftSidebar,
            'categoriesMapping' => $choices
        );
     }

    private function executeLeftSideBar($root)
    {
        $menu = $this->createMenu($root);
        $renderer = new ListRenderer(new \Knp\Menu\Matcher\Matcher());
        return $renderer->render($menu);
    }

    private function createMenu($root) {
        $factory = new MenuFactory();
        $menu = $factory->createItem('My menu');
        foreach($root as $item) {
            if (strcmp($this->container->get('request')->getRequestUri(), $this->generateUrl($item->getRoute(), $item->getRouteParams(array('slug' => $item->getSlug())))) === 0) {
                $menu->addChild($item->getTitle(), array('attributes' => array('class'=> 'selected'), 'uri' => $this->generateUrl($item->getRoute(), $item->getRouteParams(array('slug' => $item->getSlug())))));
            }else{
                $menu->addChild($item->getTitle(), array('uri' => $this->generateUrl($item->getRoute(), $item->getRouteParams(array('slug' => $item->getSlug())))));
            }  
        }
        return $menu;
    }
}
