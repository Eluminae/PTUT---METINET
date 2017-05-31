<?php

namespace AppBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Config\Definition\Exception\Exception;

class CampaignAdministratorController extends Controller
{
    /**
     * @param Request $request
     * @param string  $campaignAdministratorId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Request $request, string $campaignAdministratorId)
    {
        $campaignAdministrator = $this->get('app.campaign_administrator.repository')->findOneById(
            $campaignAdministratorId
        );

        if ($campaignAdministrator === null) {
            throw new Exception("Pas de campage avec cet id");
        }

        return $this->render(
            'AppBundle:Admin:CampaignAdministrator/show.html.twig',
            [
                'campaignAdministrator' => $campaignAdministrator,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request)
    {
        $campaignAdministrators = $this->get('app.campaign_administrator.repository')->findAll();

        return $this->render(
            'AppBundle:Admin:CampaignAdministrator/list.html.twig',
            [
                'campaignAdministrators' => $campaignAdministrators,
            ]
        );
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(CampaignAdministratorCreationType::class, new CampaignAdministratorCreation());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $campaignAdministratorCreation = $form->getData();

            $userId = $this->getUser()->getIdentity()->getId();
            $campaignAdministrator = $this->get('app.campaign.creator')->create(
                $campaignAdministratorCreation,
                $userId
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($campaignAdministrator);
            $em->flush();

            return $this->redirectToRoute('admin.campaign_administrator.list');
        }

        return $this->render(
            'AppBundle:Admin:CampaignAdministrator/campaignAdministrator.html.twig',
            [
                'campaignAdministratorCreationForm' => $form->createView(),
            ]
        );
    }

    /**
     * @param Request $request
     * @param string  $campaignAdministratorId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function deleteAction(Request $request, string $campaignAdministratorId)
    {
        $campaignAdministrator = $this->get('app.campaign_administrator.repository')->findOneById(
            $campaignAdministratorId
        );
        if (null === $campaignAdministrator) {
            throw new \Exception(sprintf('CampaignAdministrator %s not found.', $campaignAdministratorId));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($campaignAdministrator);
        $em->flush();

        return $this->redirectToRoute('admin.campaign_administrator.list');
    }

    /**
     * @param Request $request
     */
    public function updateAction(Request $request)
    {
        // todo
    }
}
