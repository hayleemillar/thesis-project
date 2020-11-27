<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class TokenController extends AbstractController
{
    private $jwt;
    private $passwordEncoder;

    public function __construct(JWTTokenManagerInterface $jwt, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->jwt = $jwt;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/jwt")
     */
    public function newTokenAction(Request $request)
    {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException();
        }

        // wipe password
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            ''
        ));

        return new JsonResponse($this->jwt->create($user));
    
        // return $this->render('jwt.html.twig', [
        //     'token' => $this->jwt->create($user)
        // ]);
    }
}
