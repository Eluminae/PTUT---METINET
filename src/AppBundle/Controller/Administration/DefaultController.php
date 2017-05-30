<?php

namespace AppBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Config\Definition\Exception\Exception;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('AppBundle:Admin:index.html.twig');
    }

}
