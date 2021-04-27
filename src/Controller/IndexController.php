<?php
namespace App\Controller;

use Elastica\Util;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
     * @Route("/media/{type}", name="media")
     */
    public function media(string $type, Request $request, SessionInterface $session, TransformedFinder $pageFinder): Response
    {
        if (in_array($type, ['text', 'audio', 'video', 'all'])) {
            $results = NULL;
            $pages = NULL;
            
            $q = (string) $request->query->get('q', '');
            if (!empty($q)) {
                // if user is attempting to make a search
                $results = !empty($q) ? $pageFinder->findHybrid(Util::escapeTerm($q)) : [];
                $session->set('q', $q);
            } else {
                $page_repo = $this->getDoctrine()->getRepository(Page::class);
                if ($type == 'all') {
                    $pages = $page_repo->findBy([
                        'private' => 0
                    ]);
                } else {
                    $pages = $page_repo->findBy([
                        'type' => $type,
                        'private' => 0
                    ]);
                }
            }

            return $this->render('media-list.html.twig', [
                'type' => $type,
                'pages' => $pages,
                'q' => $q,
                'results' => $results
            ]);
        } else {
            return $this->render('security/unauthorized.html.twig');
        }
    }
}