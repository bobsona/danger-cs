<?php

namespace Application\Stenik\SliderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;

use Stenik\SliderBundle\Entity\Slider;

class SliderFrontendController extends Controller
{
    /**
     * @Template("ApplicationStenikSliderBundle:Frontend:slider.html.twig")
     */
     public function viewAction(Request $request)
     {
        $em = $this->getDoctrine()->getManager();
        $slider = $em->getRepository('ApplicationStenikSliderBundle:Slider')->getAllByLocale($request->getLocale());
        return array(
            'slider' => $slider
        );
     }

}
