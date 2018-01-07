<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\Type\LoginType;
use AppBundle\Form\Type\UserType;
use AppBundle\Form\Type\UserVerificationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends Controller
{
    /**
     * @Route("/registration", name="registration")
     */
    public function registrationAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if($form->isValid()) {
            /* @var $existingUser User */
            $em = $this->getDoctrine()->getManager();
            // if user exists with this e-mail and it is not verified, use this user instead of creating new one (check UniqueEmailAddressValidator)
            $existingUser = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $user->getEmail()));
            if($existingUser !== null) {
                $user = $existingUser;
            }

            $user->setVerificationHash($this->getRandomVerificationLink());
            $user->setIsVerified(false);

            $emailHtml = $this->renderView('/user/verification_email.html.twig', array('user' => $user));
            $email = \Swift_Message::newInstance()
                            ->setSubject("Verify your account")
                            ->setFrom($this->container->getParameter('sender_email'))
                            ->setTo($user->getEmail())
                            ->setBody($emailHtml, 'text/html');

            $this->get('mailer')->send($email);

            $em->persist($user);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', "The user has been succesfully created. Now please check your e-mail account.");

            return $this->redirectToRoute('homepage');
        }

        $this->saveInFlashbagFormErrors($form);

        return $this->render('user/registration.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/user/verification/{hash}", name="user_verification")
     * @ParamConverter("user", class="AppBundle:User", options={
     *     "repository_method": "findByVerificationHash",
     *     "mapping": {"hash": "hash"},
     *     "map_method_signature": true
     * })
     */
    public function userVerificationAction(Request $request, User $user)
    {
        if($user->getIsVerified()) {
            return new Response('', Response::HTTP_FORBIDDEN);
        }

        $form = $this->createForm(UserVerificationType::class, $user);
        $form->handleRequest($request);

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $user->setIsVerified(true);

            $this->get('session')->getFlashbag()->add('success', 'The account has been successfully verified. Now you can login.');

            $em->flush();

            return $this->redirectToRoute('login');
        }

        $this->saveInFlashbagFormErrors($form);

        return $this->render('/user/verification.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        /* @var $authenticationUtils AuthenticationUtils */
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();
        if($error) {
            $this->get('session')->getFlashbag()->add('failure', $error->getMessage());
        }

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', array(
            'lastUsername' => $lastUsername
        ));
    }

    private function saveInFlashbagFormErrors(FormInterface $form)
    {
        if($form->isSubmitted()) {
            /* @var $error \Symfony\Component\Form\FormError */
            foreach($form->getErrors(true, true) as $error) {
                $this->get('session')->getFlashbag()->add('failure', $error->getMessage());
            }
        }
    }

    private function getRandomString()
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(32/strlen($x)) )),1,32);
    }

    private function getRandomVerificationLink()
    {
        $userRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:User');

        do {
            $result = $this->getRandomString();
        } while( $userRepo->findOneBy(array('verificationHash' => $result)) !== null );

        return $result;
    }
}
