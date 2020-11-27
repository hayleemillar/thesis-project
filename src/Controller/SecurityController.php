<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/settings", name="settings")
     */
    public function display_settings()
    {
        // handle display
        $user = $this->getUser();

        return $this->render('security/settings.html.twig', [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ]);
    }

    /**
     * @Route("/settings/post", name="post_settings")
     */
    public function post_settings(Request $request) : RedirectResponse
    {
        $user = $this->getUser();

        // handle submission
        $user->setName($request->request->get('name'));
        $user->setEmail($request->request->get('email'));

        $old_pass = $request->request->get('oldpassword');
        $pass = $request->request->get('password');
        $pass1 = $request->request->get('password1');

        if ($this->passwordEncoder->isPasswordValid($user, $old_pass) && ($pass == $pass1)){
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $pass
            ));
        }

        // Save
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        
        return $this->redirectToRoute('settings');
    }
}