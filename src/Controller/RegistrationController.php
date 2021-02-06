<?php

namespace App\Controller;

use App\Entity\Token;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\TokenRepository;
use App\Security\LoginFormAuthenticator;
use App\Services\TokenSendler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param TokenSendler $tokenSendler
     *
     * @return Response
     */
    public function index(
        Request $request,
        EntityManagerInterface $manager,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        LoginFormAuthenticator $loginFormAuthenticator,
        UserPasswordEncoderInterface $userPasswordEncoder,
        TokenSendler $tokenSendler): Response
    {
        $user = new User();

        $registrationUserForm = $this->createForm(RegistrationType::class, $user);

        if ($registrationUserForm->handleRequest($request)->isSubmitted() && $registrationUserForm->isValid()) {

            $passwordEncoded = $userPasswordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($passwordEncoded);
            $user->setRoles(['ROLE_USER']);

            $token = new Token($user);
            $manager->persist($token);
            $manager->flush();

            $tokenSendler->sendToken($user, $token);

            $this->addFlash(
                'success',
                'Vous êtes enregistrez'
            );

            return $this->redirectToRoute('success_registration');
        }

        return $this->render('registration/index.html.twig', [
            'registrationUserForm' => $registrationUserForm->createView(),
        ]);
    }

    /**
     * @Route("/confirmation/{token}", name="token_validation")
     *
     * @param TokenRepository $tokenRepository
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param GuardAuthenticatorHandler $guardAuthenticatorHandler
     * @param LoginFormAuthenticator $loginFormAuthenticator
     *
     * @return Response
     */
    public function validateToken(
        $token,
        TokenRepository $tokenRepository,
        Request $request,
        EntityManagerInterface $manager,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        LoginFormAuthenticator $loginFormAuthenticator ): Response
    {

        $token = $tokenRepository->findOneBy(['value' => $token]);

        $token;

        if (null === $token) {
            throw new NotFoundHttpException();
        }

        $user = $token->getUser();

        if ($token->isValid()) {

            $user->setEnable(true);
            $manager->flush();

            $this->addFlash(
                'success',
            'Email vérifié !');

            return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $loginFormAuthenticator,
                'main'
            );
        }

        $manager->remove($user);
        $manager->remove($token);

        $this->addFlash(
            'notice',
        "Le lien n'est plus valide");

        return $this->render('registration/index.html.twig');
    }

    /**
     * @Route("registration/success", name="success_registration")
     *
     * @return Response
     */
    public function success()
    {
        return $this->render('registration/success.html.twig');
    }
}
