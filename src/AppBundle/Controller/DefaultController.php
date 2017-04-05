<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function homeAction(Request $request)
    {
        $repo = $this->getDoctrine()->getRepository('AppBundle:Administrator')->loadUserByUsername('corentin@penis.fr');



dump($repo);
//        dump($entity[0]->getIdentity()->getEmail());

        return $this->render('AppBundle:default:home.html.twig');
    }
}
