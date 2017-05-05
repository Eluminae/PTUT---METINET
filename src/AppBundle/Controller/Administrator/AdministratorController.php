<?php

namespace AppBundle\Controller\Administrator;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Config\Definition\Exception\Exception;

use AppBundle\Forms\CampaignCreationType;
use AppBundle\Dtos\CampaignCreation;
use AppBundle\Models\Campaign;
use AppBundle\Models\UtcDate;

class AdministratorController extends Controller
{
    public function showAction(Request $request, string $administratorId)
    {
        $administrator = $this->get('app.administrator.repository')->findOneById($administratorId);

        if ($administrator === null) {
            throw new Exception("Pas de campage avec cet id");
        }

        return $this->render(
            'AppBundle:Admin:Administrator/show.html.twig', [
                'administrator' => $administrator
            ]
        );
    }

    public function listAction(Request $request)
    {
        $administrators = $this->get('app.administrator.repository')->findAll();

        return $this->render(
            'AppBundle:Admin:Administrator/list.html.twig', [
                'administrators' => $administrators
            ]
        );
    }

    public function createAction(Request $request)
    {
        $form = $this->createForm(AdministratorCreationType::class, new AdministratorCreation());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $administratorCreation = $form->getData();

            $userId = $this->getUser()->getIdentity()->getId();
            $administrator = $this->get('app.administrator.creator')->create($administratorCreation, $userId);

            $em = $this->getDoctrine()->getManager();
            $em->persist($administrator);
            $em->flush();

            return $this->redirectToRoute('admin.administrator.list');
        }

        return $this->render(
            'AppBundle:Admin:Administrator/administrator.html.twig', [
                'administratorCreationForm' => $form->createView()
            ]
        );
    }

    public function deleteAction(Request $request, string $administratorId)
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

    public function updateAction(Request $request)
    {
        // todo
    }
}
