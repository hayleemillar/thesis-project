<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class PageController extends AbstractController
{

    /**
     * @Route("/page/{id}", name="page")
     */
    public function page(int $id)
    {
        

        return $this->render('page.html.twig');
    }
}