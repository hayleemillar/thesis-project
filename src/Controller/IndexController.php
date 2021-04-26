<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Page;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class IndexController extends AbstractController
{

    private $params;
     
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * @Route("/pdf", name="thesis")
     */
    public function thesis()
    {
        return new BinaryFileResponse($this->params->get('kernel.project_dir') . '/' .'thesis.pdf');
    }

    /**
     * @Route("/pdf-presentation", name="thesis-presentation")
     */
    public function thesis_presentation()
    {
        return new BinaryFileResponse($this->params->get('kernel.project_dir') . '/' .'thesis-presentation.pdf');
    }

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
        $pages = $page_repo->findBy([
            'type' => 'text',
            'private' => 0
        ]);

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
        $pages = $page_repo->findBy([
                'type' => 'audio',
                'private' => 0
        ]);

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
        $pages = $page_repo->findBy([
            'type' => 'video',
            'private' => 0
        ]);

        return $this->render('media-list.html.twig', [
            'type' => 'video',
            'pages' => $pages
        ]);
    }

    /**
     * @Route("/all", name="all")
     */
    public function all()
    {
        $page_repo = $this->getDoctrine()->getRepository(Page::class);
        $pages = $page_repo->findBy([
            'private' => 0
        ]);

        return $this->render('media-list.html.twig', [
            'type' => 'all',
            'pages' => $pages
        ]);
    }
}