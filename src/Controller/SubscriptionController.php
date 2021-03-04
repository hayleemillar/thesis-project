<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Page;
use App\Entity\SubscriptionTier;
use App\Entity\UserSubscription;


class SubscriptionController extends AbstractController
{

    /**
     * @Route("/page/{id}", name="page")
     */
    public function page(int $id)
    {
        $page_repo = $this->getDoctrine()->getRepository(Page::class);
        $page = $page_repo->find($id);

        $user = $this->getUser();
        if ($user) {
            // retrieve user subscriptions
            $sub_repo = $this->getDoctrine()->getRepository(UserSubscription::class);
            $sub = $sub_repo->findOneBy(
                array(
                    'user' => $user->getId(),
                    'sub_tier' => $id),
            );
        }

        $tier_repo = $this->getDoctrine()->getRepository(SubscriptionTier::class);
        $tiers = $tier_repo->findBy(
            array('page' => $page->getId()),
        );

        return $this->render('page.html.twig', [
            'page' => $page,
            'tiers' => $tiers,
            'sub' => $sub
        ]);
    }

    /**
     * @Route("/subscribe/{type}/{page_id}/{tier_id}", name="subscribe")
     */
    public function subscribe(int $type, int $page_id, int $tier_id) 
    {
        switch($type) {
            case 0:
                // unsubscribe

                break;

            case 1:
                // subscribe

                break;
        }

        return $this->redirectToRoute('page', ['id' => $page_id]);
    }
}