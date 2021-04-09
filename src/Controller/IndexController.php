<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Page;


class IndexController extends AbstractController
{
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
        $page_repo = $this->getDoctrine()->getRepository(Page::class);
        $pages = $page_repo->findBy(
            array('type' => 'text')
        );

        return $this->render('media-list.html.twig', [
            'type' => 'text',
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/audio", name="audio")
     */
    public function audio()
    {
        $page_repo = $this->getDoctrine()->getRepository(Page::class);
        $pages = $page_repo->findBy(
            array('type' => 'audio')
        );

        return $this->render('media-list.html.twig', [
            'type' => 'audio',
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/video", name="video")
     */
    public function video()
    {
        $page_repo = $this->getDoctrine()->getRepository(Page::class);
        $pages = $page_repo->findBy(
            array('type' => 'video')
        );

        return $this->render('media-list.html.twig', [
            'type' => 'video',
            'pages' => $pages
        ]);
    }
}