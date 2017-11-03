<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contest;
use AppBundle\Form\EditContestType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Contest controller.
 *
 * @Route("contest")
 */
class ContestController extends Controller
{
    /**
     * Lists all contest entities.
     *
     * @Route("/", name="contest_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $contests = $em->getRepository('AppBundle:Contest')->findAll();

        return $this->render('contest/index.html.twig', array(
            'contests' => $contests,
        ));
    }

    /**
     * Creates a new contest entity.
     *
     * @Route("/new", name="contest_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $contest = new Contest();
        $form = $this->createForm('AppBundle\Form\ContestType', $contest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contest);
            $em->flush();

            return $this->redirectToRoute('contest_show', array('id' => $contest->getId()));
        }

        return $this->render('contest/new.html.twig', array(
            'contest' => $contest,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a contest entity.
     *
     * @Route("/show/{id}", name="contest_show")
     * @Method("GET")
     */
    public function showAction(Contest $contest)
    {
        $deleteForm = $this->createDeleteForm($contest);

        return $this->render('contest/show.html.twig', array(
            'contest' => $contest,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing contest entity.
     *
     * @Route("/edit/{id}", name="contest_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Contest $contest)
    {
        $deleteForm = $this->createDeleteForm($contest);
        $editForm = $this->createForm('AppBundle\Form\ContestType', $contest);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('contest_edit', array('id' => $contest->getId()));
        }

        return $this->render('contest/edit.html.twig', array(
            'contest' => $contest,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a contest entity.
     *
     * @Route("/delete/{id}", name="contest_delete")
     * @Method({"GET", "DELETE"})
     *
     */
    public function deleteAction(Request $request, Contest $contest)
    {
        if($contest->getCompany() != $this->getUser()->getCompany()){
            return $this->redirectToRoute('company_contests');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($contest);
        $em->flush();

        $this->addFlash('success', 'Zawody zostały poprawnie usunięte!');
        return $this->redirectToRoute('company_contests');
    }

    /**
     * Creates a form to delete a contest entity.
     *
     * @param Contest $contest The contest entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Contest $contest)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contest_delete', array('id' => $contest->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Switch to profile action
     *
     * @Route("/contests-switcher", name="contests_switcher")
     * @Method({"GET", "POST"})
     */
    public function contestsSwitcherAction(Request $request)
    {
        if($this->getUser()->hasRole("ROLE_RUNNER")) return $this->redirectToRoute('company_contests');
        if($this->getUser()->hasRole("ROLE_COMPANY")) return $this->redirectToRoute('company_contests');
    }

    /**
     * Lists all contest for company
     *
     * @Route("/company-contests", name="company_contests")
     * @Method("GET")
     */
    public function companyContestsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $contests = $em->getRepository('AppBundle:Contest')->findByCompany($this->getUser()->getCompany());

        return $this->render('contest/company-contests.html.twig', array(
            'contests' => $contests,
        ));
    }

    /**
     * Creates a new contest entity.
     *
     * @Route("/add-contest", name="contest_add")
     * @Method({"GET", "POST"})
     */
    public function addContestAction(Request $request)
    {
        $contest = new Contest();
        $contest->setCompany($this->getUser()->getCompany());
        $form = $this->createForm(EditContestType::class, $contest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contest);
            $em->flush();

            $this->addFlash('success', 'Zawody zostały poprawnie dodane!');
            return $this->redirectToRoute('company_contests');
        }

        return $this->render('contest/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Edit contest entity.
     *
     * @Route("/edit-contest/{id}", name="contest_edit")
     * @Method({"GET", "POST"})
     */
    public function editContestAction(Request $request, Contest $contest)
    {
        if($contest->getCompany() != $this->getUser()->getCompany()){
            return $this->redirectToRoute('company_contests');
        }

        $contest->setCompany($this->getUser()->getCompany());
        $form = $this->createForm(EditContestType::class, $contest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($contest);
            $em->flush();

            $this->addFlash('success', 'Zawody zostały poprawnie edytowane!');
            return $this->redirectToRoute('company_contests');
        }

        return $this->render('contest/edit.html.twig', array(
            'form' => $form->createView(),
            'contest' => $contest,
        ));
    }
}
