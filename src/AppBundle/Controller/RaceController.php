<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contest;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCategory;
use AppBundle\Form\EditRaceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Race controller.
 *
 * @Route("race")
 */
class RaceController extends Controller
{
    /**
     * Lists all race entities.
     *
     * @Route("/", name="race_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $races = $em->getRepository('AppBundle:Race')->findAll();

        return $this->render('race/index.html.twig', array(
            'races' => $races,
        ));
    }

    /**
     * Creates a new race entity.
     *
     * @Route("/new", name="race_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $race = new Race();
        $form = $this->createForm('AppBundle\Form\RaceType', $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($race);
            $em->flush();

            return $this->redirectToRoute('race_show', array('id' => $race->getId()));
        }

        return $this->render('race/new.html.twig', array(
            'race' => $race,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a race entity.
     *
     * @Route("/show/{id}", name="race_show")
     * @Method("GET")
     */
    public function showAction(Race $race)
    {
        return $this->render('race/show.html.twig', array(
            'race' => $race,
        ));
    }

    /**
     * Displays a form to edit an existing race entity.
     *
     * @Route("/edit/{id}", name="race_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Race $race)
    {
        $deleteForm = $this->createDeleteForm($race);
        $editForm = $this->createForm('AppBundle\Form\RaceType', $race);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('race_edit', array('id' => $race->getId()));
        }

        return $this->render('race/edit.html.twig', array(
            'race' => $race,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a race entity.
     *
     * @Route("/delete/{id}", name="race_delete")
     * @Method({"GET", "DELETE"})
     */
    public function deleteAction(Request $request, Race $race)
    {
        $form = $this->createDeleteForm($race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($race);
            $em->flush();
        }

        return $this->redirectToRoute('race_index');
    }

    /**
     * Creates a form to delete a race entity.
     *
     * @param Race $race The race entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Race $race)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('race_delete', array('id' => $race->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Creates a new contest entity.
     *
     * @Route("/add-race/{id}", name="race_add")
     * @Method({"GET", "POST"})
     */
    public function addRaceAction(Request $request, Contest $contest)
    {
        $race = new Race();
        $race->setContest($contest);

        $form = $this->createForm(EditRaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($race);
            $em->flush();

            $this->addFlash('success', 'Wyścig został poprawnie dodany!');
            return $this->redirectToRoute('contest_edit', array('id' => $contest->getId()));
        }

        return $this->render('race/add.html.twig', array(
            'race' => $race,
            'form' => $form->createView(),
        ));
    }


    /**
     * Edit race entity.
     *
     * @Route("/edit-race/{id}", name="race_edit")
     * @Method({"GET", "POST"})
     */
    public function editRaceAction(Request $request, Race $race)
    {
        if($race->getContest()->getCompany() != $this->getUser()->getCompany()){
            return $this->redirectToRoute('company_contests');
        }

        $form = $this->createForm(EditRaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($race);
            $em->flush();

            $this->addFlash('success', 'Wyścig został poprawnie edytowany!');
            return $this->redirectToRoute('contest_edit', array('id' => $race->getContest()->getId()));
        }

        return $this->render('race/edit.html.twig', array(
            'race' => $race,
            'form' => $form->createView(),
        ));
    }
}
