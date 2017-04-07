<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\RealisationMarkRegisterType;
use AppBundle\Dtos\RealisationMarkDto;

class MarkController extends Controller
{
    public function markAction(Request $request, string $realisationId, string $campaignId)
    {
        if (!$authorizationChecker->isGranted('ROLE_JUROR')) {
            throw new AccessDeniedException();
        }
        $juror = $this->getUser();

        $realisation = $this->get('app.realisation.repository')->findOneById($realisationId);
        if (null === $realisation) {
            throw new \Exception(sprintf('Realisation %s not found.', $realisationId));
        }

        $markDto = new MarkDto();
        $markDo->juror = $juror;
        $markDo->realisation = $realisation;

        $form = $this->createForm(RealisationMarkRegisterType::class, $markDto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        }

        return $this->render(
            'AppBundle:Default:Realisation/markRegistration.html.twig', [
                'realisationRegistrationForm' => $form->createView(),
                'campaign' => $campaign
            ]
        );
    }
}