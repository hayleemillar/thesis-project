<?php declare(strict_types=1);

// src/Controller/SearchPart1Controller.php

namespace App\Controller;

use Elastica\Util;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


final class SearchController extends AbstractController
{
    /**
     * @Route("/search",  name="search")
     */
    public function search(Request $request, SessionInterface $session, TransformedFinder $pageFinder): Response
    {
        $q = (string) $request->query->get('q', '');
        $results = !empty($q) ? $pageFinder->findHybrid(Util::escapeTerm($q)) : [];
        $session->set('q', $q);

        return $this->render('search/index.html.twig', compact('results', 'q'));
    }
}