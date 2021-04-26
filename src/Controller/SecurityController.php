<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserSubscription;
use App\Entity\SubscriptionTier;
use App\Entity\Page;
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
        // we'll need this for later
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
    public function index($message = NULL)
    {
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile($message = NULL) {
        
        $user = $this->getUser();

        $sub_repo = $this->getDoctrine()->getRepository(UserSubscription::class);
        $subs = $sub_repo->findBy(
            array('user' => $user->getId()),
        );

        $sub_repo = $this->getDoctrine()->getRepository(SubscriptionTier::class);
        $page_repo = $this->getDoctrine()->getRepository(Page::class);

        $subscriptions = [];
        foreach ($subs as $sub) {
            $tier_data = $sub_repo->find($sub->getSubTier());
            $page_data = $page_repo->find($tier_data->getPage());

            $id = $sub->getId();

            $subscriptions[$id] = [
                'title' => $tier_data->getTitle(),
                'site' => $page_data->getTitle(),
                'amount' => $tier_data->getAmount(),
                'id' => $page_data->getId(),
                'url'=> $page_data->getUrl()
            ];

            $id = $sub->getId();
        }
        
        $pages = $page_repo->findBy(
            array('user' => $user->getId())
        );

        return $this->render('security/profile.html.twig', [
            'name' => $user->getName(),
            'subs' => $subscriptions,
            'pages' => $pages,
            'message' => $message
        ]);
    }

    /**
     * @Route("/settings", name="settings")
     */
    public function settings(Request $request)
    {
        // handle display
        $user = $this->getUser();

        if ($request->isMethod('POST')) {
            // check existence of email in DB that is NOT the current user
            $user_repo = $this->getDoctrine()->getRepository(User::class);
            $existing_user = $user_repo->findOneBy(
                array('email' => $request->request->get('email'))
            );

            // if there is a user with email AND its not the CURRENT use, we have a problem
            if ($existing_user && ($user->getId() != $existing_user->getId())) {
                return $this->render('security/settings.html.twig', [
                    'name' => $request->request->get('name'),
                    'email' => $request->request->get('email'),
                    'message' => 'The email you have requested has already been associated with another account. Please choose a different email for your account.'
                ]);
            }

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

            // save
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->render('security/settings.html.twig', [
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'message' => 'Your account has been successfully updated.'
            ]);
        }

        return $this->render('security/settings.html.twig', [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'message' => NULL
        ]);
    }
}