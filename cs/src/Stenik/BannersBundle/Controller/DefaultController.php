<?php

namespace Stenik\BannersBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/insert")
     * @Template("StenikBannersBundle::test.html.twig")
     */
    public function indexAction()
    {
        // $userManager = $this->container->get('fos_user.user_manager');


        // $user = $userManager->createUser();

        // $form = $this->container->get('form.factory')->create('stenik_user_registration', $user, array());
        /*$em = $this->getDoctrine()->getManager();
        $i = 1;
        foreach (\Stenik\BannersBundle\Entity\Banner::$kor as $key => $value) {
            $obj = new \Stenik\BannersBundle\Entity\BannerZone();
            $obj->setKey($key);
            $obj->setZones($i);
            $obj->setValue($value);
            $obj->setWidth(350);
            $obj->setHeight(350);
            $em->persist($obj);
            $i++;
        }
        $em->flush();
        echo "done";
        exit;*/
        //return array('form' => $form->createView());
    }
}
