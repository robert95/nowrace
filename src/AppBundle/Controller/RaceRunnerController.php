<?php

namespace AppBundle\Controller;

use AppBundle\Entity\RaceRunner;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Racerunner controller.
 *
 * @Route("racerunner")
 */
class RaceRunnerController extends Controller
{
    /**
     * Lists all raceRunner entities.
     *
     * @Route("/", name="racerunner_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $raceRunners = $em->getRepository('AppBundle:RaceRunner')->findAll();

        return $this->render('racerunner/index.html.twig', array(
            'raceRunners' => $raceRunners,
        ));
    }

    /**
     * Creates a new raceRunner entity.
     *
     * @Route("/new", name="racerunner_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $raceRunner = new Racerunner();
        $form = $this->createForm('AppBundle\Form\RaceRunnerType', $raceRunner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($raceRunner);
            $em->flush();

            return $this->redirectToRoute('racerunner_show', array('id' => $raceRunner->getId()));
        }

        return $this->render('racerunner/new.html.twig', array(
            'raceRunner' => $raceRunner,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a raceRunner entity.
     *
     * @Route("/{id}", name="racerunner_show")
     * @Method("GET")
     */
    public function showAction(RaceRunner $raceRunner)
    {
        $deleteForm = $this->createDeleteForm($raceRunner);

        return $this->render('racerunner/show.html.twig', array(
            'raceRunner' => $raceRunner,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing raceRunner entity.
     *
     * @Route("/{id}/edit", name="racerunner_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, RaceRunner $raceRunner)
    {
        $deleteForm = $this->createDeleteForm($raceRunner);
        $editForm = $this->createForm('AppBundle\Form\RaceRunnerType', $raceRunner);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('racerunner_edit', array('id' => $raceRunner->getId()));
        }

        return $this->render('racerunner/edit.html.twig', array(
            'raceRunner' => $raceRunner,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a raceRunner entity.
     *
     * @Route("/{id}", name="racerunner_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, RaceRunner $raceRunner)
    {
        $form = $this->createDeleteForm($raceRunner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($raceRunner);
            $em->flush();
        }

        return $this->redirectToRoute('racerunner_index');
    }

    /**
     * Creates a form to delete a raceRunner entity.
     *
     * @param RaceRunner $raceRunner The raceRunner entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RaceRunner $raceRunner)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('racerunner_delete', array('id' => $raceRunner->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
