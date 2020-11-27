<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class IndexController extends AbstractController
{
    //  /**
    //   * @Route("/", name="index")
    //   */
    // public function index()
    // {
    //     return $this->render('index.html.twig');
    // }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('about.html.twig');
    }

    /**
     * @Route("/text", name="text")
     */
    public function text()
    {
        return $this->render('media-list.html.twig', [
            'type' => 'text'
        ]);
    }

    /**
     * @Route("/audio", name="audio")
     */
    public function audio()
    {
        return $this->render('media-list.html.twig', [
            'type' => 'audio'
        ]);
    }

    /**
     * @Route("/video", name="video")
     */
    public function video()
    {
        return $this->render('media-list.html.twig', [
            'type' => 'video'
        ]);
    }
}