<?php

namespace AppBundle\Controller\Administration;

use AppBundle\Forms\SignInType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AdministratorController extends Controller
{
    public function listAction()
    {
        $administrators = $this->get('app.administrator.repository')->findAll();

        return $this->render(
            'AppBundle:Admin:Administrator/list.html.twig', [
                'administrators' => $administrators,
            ]
        );
    }

    public function showAction($administratorId)
    {
        $administrator = $this->get('app.administrator.repository')->findOneById($administratorId);
        if (null === $administrator) {
            throw new \Exception(sprintf('Administrator %s not found.', $administratorId));
        }

        return $this->render(
            'AppBundle:Admin:Administrator/show.html.twig', [
                'administrator' => $administrator,
            ]
        );
    }

    public function deleteAction($administratorId)
    {
        $administrator = $this->get('app.administrator.repository')->findOneById($administratorId);
        if (null === $administrator) {
            throw new \Exception(sprintf('Administrator %s not found.', $administratorId));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($administrator);
        $em->flush();

        return $this->redirectToRoute('admin.administrator.list');
    }

    public function indexAction(Request $request)
    {
        $campaignAdministrators = $this->get('app.campaign_administrator.repository')->findAll();
        $administrators = $this->get('app.administrator.repository')->findAll();
        $jurors = $this->get('app.juror.repository')->findAll();

        return $this->render('AppBundle:Admin:index.html.twig', [
            'campaignAdministrators' => $campaignAdministrators,
            'administrators' => $administrators,
            'jurors' => $jurors
        ]);
    }

    public function updateAction()
    {
    }
}
