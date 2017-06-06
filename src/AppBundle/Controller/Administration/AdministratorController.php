<?php

namespace AppBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AdministratorController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $administrators = $this->get('app.administrator.repository')->findAll();

        return $this->render(
            'AppBundle:Admin:Administrator/list.html.twig',
            [
                'administrators' => $administrators,
            ]
        );
    }

    /**
     * @param $administratorId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function showAction($administratorId)
    {
        $administrator = $this->get('app.administrator.repository')->findOneById($administratorId);
        if (null === $administrator) {
            throw new \Exception(sprintf('Administrator %s not found.', $administratorId));
        }

        return $this->render(
            'AppBundle:Admin:Administrator/show.html.twig',
            [
                'administrator' => $administrator,
            ]
        );
    }

    /**
     * @param $administratorId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     */
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

    public function updateAction()
    {
    }
}
