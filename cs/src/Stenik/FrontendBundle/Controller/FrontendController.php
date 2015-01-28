<?php

namespace Stenik\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class FrontendController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @Template("StenikFrontendBundle:Frontend:index.html.twig")
     */
    public function indexAction()
    {
        // $user = $this->getUser();
        // $phpbbuserman = $this->get('widop_php_bb.user_manager');
        // $login = $this->get('widop_php_bb.authentication_manager');
        // // Here 5 and 3 are "owner" (phpbb admin) type.
        // $userGroup = 5; 
        // $userType = 3;
        // $login->login($user->getUsername(), $user->getPassword(), true);
        // $phpbbuserman->addUser($user->getUsername(), $user->getPassword(), 
        // $user->getEmail(), $userGroup, $userType);

        // $msg = $phpbbuserman->getUnreadPrivateMessageCount($this->getUser()->getUsername());
        // var_dump($msg);exit;
        return array();
    }


    /**
     * @Template("ApplicationStenikSliderBundle:Frontend:slider.html.twig")
     */
     public function footerAction(Request $request)
     {
        // $em = $this->getDoctrine()->getManager();
        // $slider = $em->getRepository('ApplicationStenikSliderBundle:Slider')->getAllByLocale($request->getLocale());
        
        return array(
            
        );
     }
}
