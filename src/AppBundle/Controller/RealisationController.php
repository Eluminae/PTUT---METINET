<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Forms\RealisationRegistrationType;
use AppBundle\Dtos\RealisationRegistration;
use AppBundle\Models\Campaign;
use AppBundle\Models\UtcDate;

class RealisationController extends Controller
{
    public function listAction(Request $request)
    {
    }

    public function registerAction(Request $request)
    {
        $form = $this->createForm(RealisationRegistrationType::class, new RealisationRegistration());

        $campaign = new Campaign(
            1,
            new UtcDate(
                1, 
                \DateTimeImmutable::createFromFormat('Y-m-d','2017-01-25')
                ),
            new UtcDate(
                1, 
                \DateTimeImmutable::createFromFormat('Y-m-d','2017-05-25')
                ),
            'Campagne test',
            'Courte description',
            'http://www.ensiacet.fr/_resources/Images/Formations/Ingenieur/D%25C3%25A9partements/Chimie/Verrerie%2520chimie-%2520Glass%2520factory%2520chemistry%2520-%2520jpgphotographie.com.jpg'
        );

        if ($request->isMethod('post')) {

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $realisationRegistration = $form->getData();

                // register the realisation
                
                $this->get('app.realisation.registerer')->create($realisationRegistration, $campaign->getId());

                return new RedirectResponse('/');
            }
        }

        return $this->render(
            'AppBundle:Realisation:realisationRegistration.html.twig', [
                'realisationRegistrationForm' => $form->createView(), 
                'campaign' => $campaign
            ]
        );
    }
}
