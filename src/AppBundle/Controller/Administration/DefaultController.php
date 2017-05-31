<?php

namespace AppBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $this->addFlash('success', 'YOUUUUUHHOUUUUU');
        $this->addFlash('success', 'YOUUUUUHHOUUUUU');
        $this->addFlash('success', 'YOUUUUUHHOUUUUU');
        $this->addFlash('success', 'YOUUUUUHHOUUUUU');
        $this->addFlash('success', 'YOUUUUUHHOUUUUU');
        return $this->render('AppBundle:Admin:index.html.twig');
    }

}
