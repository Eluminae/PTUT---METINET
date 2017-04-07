<?php

namespace AppBundle\Controller\Administration;

use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Forms\RealisationRegistrationType;
use AppBundle\Models\Campaign;
use AppBundle\Models\Realisation;
use AppBundle\Models\UtcDate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @param Request  $request
     * @param Realisation $realisation
     *
     * @ParamConverter("realisation", class="AppBundle:Realisation")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Realisation $realisation)
    {
        return $this->render(
            'AppBundle:Admin:Realisation/show.html.twig', [
                'realisation' => $realisation,
            ]
        );
    }

    /**
     * @param Request  $request
     * @param Realisation $realisation
     *
     * @ParamConverter("realisation", class="AppBundle:Realisation")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Realisation $realisation)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($realisation);
        $em->flush();

        return $this->redirectToRoute('admin.realisation.list');
    }

    /**
     * @param Request  $request
     * @param Campaign $campaign
     *
     * @ParamConverter("campaign", class="AppBundle:Campaign")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, Campaign $campaign)
    {
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
