<?php

namespace AppBundle\Controller\Administrator;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Config\Definition\Exception\Exception;

use AppBundle\Forms\RealisationRegistrationType;
use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Models\Campaign;
use AppBundle\Models\UtcDate;

class RealisationController extends Controller
{
    public function listAction(Request $request)
    {
        $realisations = $this->get('app.realisation.repository')->findAll();
        return $this->render(
            'AppBundle:Admin:Realisation/list.html.twig', [
                'realisations' => $realisations,
            ]
        );
    }

    public function showAction(Request $request, string $realisationId)
    {
        $realisation = $this->get('app.realisation.repository')->findOneById($realisationId);
        if (null === $realisation) {
            throw new \Exception(sprintf('Realisation %s not found.', $realisationId));
        }

        return $this->render(
            'AppBundle:Admin:Realisation/show.html.twig', [
                'realisation' => $realisation,
            ]
        );
    }

    public function deleteAction(Request $request, string $realisationId)
    {
        $realisation = $this->get('app.realisation.repository')->findOneById($realisationId);
        if (null === $realisation) {
            throw new \Exception(sprintf('Realisation %s not found.', $realisationId));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($realisation);
        $em->flush();

        return $this->redirectToRoute('admin.realisation.list');
    }

    public function createAction(Request $request, string $campaignId)
    {
        $campaign = $this->get('app.campaign.repository')->findOneById($campaignId);
        if (null === $campaign) {
            throw new \Exception(sprintf('Campaign %s not found.', $campaignId));
        }

        $form = $this->createForm(RealisationRegistrationType::class, new RealisationRegistration());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $realisationRegistration = $form->getData();

            $realisation = $this->get('app.realisation.registerer')->create($realisationRegistration, $campaignId);

            $em = $this->getDoctrine()->getManager();
            $em->persist($realisation);
            $em->flush();

            return $this->redirectToRoute('admin.realisation.list');
        }

        return $this->render(
            'AppBundle:Admin:Realisation/create.html.twig', [
                'realisationCreationForm' => $form->createView(),
                'campaign' => $campaign
            ]
        );
    }

    public function updateAction(Request $request)
    {
        // todo
    }
}
