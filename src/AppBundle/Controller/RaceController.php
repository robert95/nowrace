<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contest;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceCategory;
use AppBundle\Entity\RaceRun;
use AppBundle\Entity\RaceRunner;
use AppBundle\Entity\Track;
use AppBundle\Entity\TrackElem;
use AppBundle\Entity\TrackPoint;
use AppBundle\Form\EditRaceType;
use AppBundle\Form\SignForRaceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
            'form' => $this->createSignForm($race)->createView(),
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
        if($race->getContest()->getCompany() != $this->getUser()->getCompany()){
            return $this->redirectToRoute('company_contests');
        }

        if($race->getStartTime() < new \DateTime()){
            $this->addFlash('warning', 'Nie możesz usunąć wyścigów, której już się zaczęły!!');
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

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
            $this->addNewTrackFromJSON(json_decode($form->get('route')->getData(), true), $race);
            $em->persist($race);
            $em->flush();

            $this->addFlash('success', 'Wyścig został poprawnie dodany do zawodów!');
            return $this->redirectToRoute('contest_show', array('id' => $contest->getId()));
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

        if($race->getStartTime() < new \DateTime()){
            $this->addFlash('warning', 'Nie możesz edytować wyścigów, której już się zaczęły!!');
            return $this->redirect($request->server->get('HTTP_REFERER'));
        }

        $form = $this->createForm(EditRaceType::class, $race);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->editTrackFromJSON(json_decode($form->get('route')->getData(), true), $race);
            $em->flush();

            $this->addFlash('success', 'Wyścig został poprawnie edytowany!');
            return $this->redirectToRoute('contest_edit', array('id' => $race->getContest()->getId()));
        }

        return $this->render('race/edit.html.twig', array(
            'race' => $race,
            'form' => $form->createView(),
        ));
    }

    public function addNewTrackFromJSON($routePoints, Race $race){
        $em = $this->getDoctrine()->getManager();
        $track = new Track();
        $track->setName('TRASA - '.$race->getName());
        $track->addRace($race);

        $trackElem = new TrackElem();
        $track->addTrackElems($trackElem);

        foreach($routePoints as $key => $point){
            $trackElem->addPoint(new TrackPoint($key, $point['lat'], $point['lng']));
        }

        $em->persist($track);
    }

    public function editTrackFromJSON($routePoints, Race $race){
        $em = $this->getDoctrine()->getManager();

        $trackElem = $race->getTrack()->getTrackElems()[0];
        foreach ($trackElem->getPoints() as $point){
            $em->remove($point);
        }

        foreach($routePoints as $key => $point){
            $trackElem->addPoint(new TrackPoint($key, $point['lat'], $point['lng']));
        }

        $em->flush();
    }

    /**
     * @param Race $race
     * @return \Symfony\Component\Form\Form
     */
    public function createSignForm(Race $race){
        return  $this->createForm(SignForRaceType::class, new RaceRunner(), array(
            'race' => $race,
        ));
    }

    /**
     * Display live race.
     *
     * @Route("/live/{id}", name="race_live")
     * @Method("GET")
     */
    public function liveAction(Race $race)
    {
        return $this->render('race/live.html.twig', array(
            'race' => $race
        ));
    }

    /**
     * Set cords for runner race.
     *
     * @Route("/set-cords/{id}", name="race_set_cords")
     * @Method("GET")
     */
    public function setCordsAction(Request $request, RaceRunner $raceRunner)
    {
        $em = $this->getDoctrine()->getManager();

        $raceRun = new RaceRun();
        $raceRun->setDatetime(new \DateTime());
        $raceRun->setLat($request->get('lat'));
        $raceRun->setLng($request->get('lng'));
        $raceRunner->addRaceRun($raceRun);

        $em->flush();

        $connection = $em->getConnection();
        $statement = $connection->prepare("SELECT RR.*, RaRu.runner_id
            FROM race_run RR, race_runner RaRu
            WHERE RR.time = (
                SELECT MAX(RR2.time)
                FROM race_run RR2
                WHERE RR2.race_runner_id = RR.race_runner_id
            )
            AND 
            (
                RR.race_runner_id = RaRu.id
                AND
                RaRu.race_id = :race_id
            )");
        $statement->bindValue('race_id', $raceRunner->getRace()->getId());
        $statement->execute();
        $results = $statement->fetchAll();

        $resultsToSave = [];
        foreach ($results as $result){
            $resultsToSave[] = array(
                'run_point_id' => $result['id'],
                'id' => $result['runner_id'],
                'lat' => $result['lat'],
                'lng' => $result['lng'],
                'timestamp' => (new \DateTime($result['time']))->getTimestamp()
            );
        }

//        $fp = fopen('results/results_'.$raceRunner->getRace()->getId().'.json', 'w');
//        fwrite($fp, json_encode($resultsToSave));
//        fclose($fp);

        $statement = $connection->prepare("
            DELETE FROM live WHERE race_id = :race_id;
            INSERT INTO `live`(`race_id`, `positions`) VALUES (:race_id, :positions);
        ");
        $statement->bindValue('race_id', $raceRunner->getRace()->getId());
        $statement->bindValue('positions', json_encode($resultsToSave));
        $statement->execute();

        return new Response('ok');
    }
}
