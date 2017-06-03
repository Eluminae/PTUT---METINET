<?php

namespace AppBundle\Controller\Administration;

use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Forms\RealisationRegistrationType;
use AppBundle\Models\Campaign;
use AppBundle\Models\Realisation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RealisationController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $realisations = $this->get('app.realisation.repository')->findAll();

        $user = $this->get('security.token_storage')->getToken()->getUser();
        foreach ($realisations as $key => $realisation) {
            if (
                false === $this->get('app.user.authorization_checker')->isAllowedToShowCampaign($user, $realisation->getCampaign())
            ) {
                unset($realisations[$key]);
            }
        }

        return $this->render(
            'AppBundle:Admin:Realisation/list.html.twig',
            [
                'realisations' => $realisations,
            ]
        );
    }

    /**
     * @param Request     $request
     * @param Realisation $realisation
     *
     * @ParamConverter("realisation", class="AppBundle:Realisation")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, Realisation $realisation)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (
            false === $this->get('app.user.authorization_checker')->isAllowedToShowCampaign($user, $realisation->getCampaign())
        ) {
            throw new AccessDeniedException('Vous n\'êtes pas authorisé à administrer cette réalisation');
        }

        return $this->render(
            'AppBundle:Admin:Realisation/show.html.twig',
            [
                'realisation' => $realisation,
            ]
        );
    }

    /**
     * @param Request     $request
     * @param Realisation $realisation
     *
     * @ParamConverter("realisation", class="AppBundle:Realisation")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
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
     * @throws \LogicException
     */
    public function createAction(Request $request, Campaign $campaign)
    {
        $form = $this->createForm(RealisationRegistrationType::class, new RealisationRegistration());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $realisationRegistration = $form->getData();

            $realisation = $this->get('app.realisation.registerer')->create($realisationRegistration, $campaign->getId());

            $em = $this->getDoctrine()->getManager();
            $em->persist($realisation);
            $em->flush();

            return $this->redirectToRoute('admin.realisation.list');
        }

        return $this->render(
            'AppBundle:Admin:Realisation/create.html.twig',
            [
                'realisationCreationForm' => $form->createView(),
                'campaign' => $campaign,
            ]
        );
    }

    /**
     * @param Request     $request
     * @param Realisation $realisation
     *
     * @ParamConverter("realisation", class="AppBundle:Realisation")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function downloadAction(Request $request, Realisation $realisation)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (
            false === $this->get('app.user.authorization_checker')->isAllowedToShowCampaign($user, $realisation->getCampaign())
        ) {
            throw new AccessDeniedException('Vous n\'êtes pas authorisé à récupérer cette réalisation');
        }

        return $this->file($realisation->getFilePath());
    }
}
