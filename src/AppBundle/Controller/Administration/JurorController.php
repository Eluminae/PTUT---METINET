<?php

namespace AppBundle\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class JurorController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $jurors = $this->get('app.juror.repository')->findAll();

        return $this->render(
            'AppBundle:Admin:Juror/list.html.twig',
            [
                'jurors' => $jurors,
            ]
        );
    }

    /**
     * @param $jurorId
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Exception
     */
    public function showAction($jurorId)
    {
        $juror = $this->get('app.juror.repository')->findOneById($jurorId);
        if (null === $juror) {
            throw new \Exception(sprintf('Juror %s not found.', $jurorId));
        }

        return $this->render(
            'AppBundle:Admin:Juror/show.html.twig',
            [
                'juror' => $juror,
            ]
        );
    }

    /**
     * @param $jurorId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Exception
     */
    public function deleteAction($jurorId)
    {
        $juror = $this->get('app.juror.repository')->findOneById($jurorId);
        if (null === $juror) {
            throw new \Exception(sprintf('Juror %s not found.', $jurorId));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($juror);
        $em->flush();

        return $this->redirectToRoute('admin.juror.list');
    }

    public function updateAction()
    {
    }
}
