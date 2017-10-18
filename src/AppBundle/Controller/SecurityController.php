<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Runner;
use AppBundle\Entity\User;
use AppBundle\Form\Security\RunnerType;
use AppBundle\Form\Security\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * User controller.
 *
 * @Route("/")
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="security_login")
     */
    public function loginAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @Route("/register-runner")
     */
    public function registerRunnerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $runner = new Runner();
        $form = $this->createForm(RunnerType::class, $runner);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $runner->getUser();

            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($runner);
            $em->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'security/register-driver.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/register-company")
     */
    public function registerCompanyAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();

        return $this->render('user/index.html.twig', array(
            'users' => $users,
        ));
    }

}
