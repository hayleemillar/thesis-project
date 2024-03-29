<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Page;
use App\Entity\SubscriptionTier;
use App\Entity\UserSubscription;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;


class SubscriptionController extends AbstractController
{
    private $jwt;

    public function __construct(JWTTokenManagerInterface $jwt)
    {
        $this->jwt = $jwt;
    }

    /**
     * @Route("/page/{id}", name="page")
     */
    public function page(int $id, string $message = NULL)
    {
        $owner = false;

        $page_repo = $this->getDoctrine()->getRepository(Page::class);
        $page = $page_repo->find($id);

        $tier_repo = $this->getDoctrine()->getRepository(SubscriptionTier::class);
        $tiers = $tier_repo->findBy(
            array('page' => $page->getId()),
        );

        $user = $this->getUser();
        $sub = [];
        if ($user) {
            if ($user->getId() == $page->getUser()) {
                $owner = true;
            }

            if (!$owner && ($page->getPrivate() == 1)) {
                // if the user is not the owner and the page is private, send to unauth page
                return $this->render('security/unauthorized.html.twig');
            }

            // retrieve user subscriptions
            $sub_repo = $this->getDoctrine()->getRepository(UserSubscription::class);

            foreach ($tiers as $tier) {
                $sub = $sub_repo->findOneBy(
                    array(
                        'user' => $user->getId(),
                        'sub_tier' => $tier->getId()
                    ),
                );
                if ($sub) {
                    break;
                }
            }
        } 

        return $this->render('page.html.twig', [
            'owner' => $owner,
            'page' => $page,
            'tiers' => $tiers,
            'sub' => $sub,
            'message' => $message
        ]);
    }

    /**
     * @Route("/unsubscribe/{page_id}/{tier_id}", name="unsubscribe")
     */
    public function unsubscribe(int $page_id, int $tier_id)
    {
        $message = 'Sorry, something went wrong. Please try again later.';

        $user = $this->getUser();
        if ($user) {
            $sub_repo = $this->getDoctrine()->getRepository(UserSubscription::class);
            $sub = $sub_repo->findOneBy(
                array(
                    'user' => $user->getId(),
                    'sub_tier' => $tier_id
                ),
            );

            if ($sub) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($sub);
                $entityManager->flush();

                $message = 'Successfully unsubscribed.';
            }

            return $this->redirectToRoute('page', 
                [
                    'id' => $page_id,
                    'message' => $message
                ]);
        } else { return $this->render('security/unauthorized.html.twig'); }
    }

    /**
     * @Route("/subscribe/{page_id}/{tier_id}/{unsub_tier}", name="subscribe")
     */
    public function subscribe(int $page_id, int $tier_id, int $unsub_tier)
    {
        $message = 'Sorry, something went wrong. Please try again later.';

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        if ($user) {
            if ($unsub_tier != -1) {
                // check for current subscriptions -- unsubscribe
                $sub_repo = $this->getDoctrine()->getRepository(UserSubscription::class);
                $sub = $sub_repo->findOneBy(
                    array(
                        'user' => $user->getId(),
                        'sub_tier' => $unsub_tier
                    ),
                );

                if ($sub) {
                    $em->remove($sub);
                    $em->flush();
                }
            }


            // validate new tier choice
            $tier_repo = $this->getDoctrine()->getRepository(SubscriptionTier::class);
            $tier = $tier_repo->findOneBy(
                array('id' => $tier_id)
            );

            if ($tier) {
                // subscribe to new
                $new_sub = new UserSubscription();
                $new_sub->setUser($user->getId());
                $new_sub->setSubTier($tier_id);

                // save
                $em->persist($new_sub);
                $em->flush();

                $message = 'Your subscription has successfully been changed.';
            }

            return $this->redirectToRoute('page', 
            ['id' => $page_id,
             'message' => $message]);
        } else { return $this->render('security/unauthorized.html.twig'); }
    }

    /**
     * @Route("/new/page", name="page_form")
     */
    public function createPage(Request $request) 
    {
        $user = $this->getUser();
        if ($user) {
            // creates a task object and initializes some data for this example
            $page = new Page();
            $form = $this->createFormBuilder($page)
                ->add('url', TextType::class)
                ->add('type', ChoiceType::class, [
                    'choices' => [
                        'audio' => 'audio',
                        'video' => 'video',
                        'text' => 'text',
                    ],
                ])
                ->add('title', TextType::class)
                ->add('description', TextareaType::class)
                ->add('private', CheckboxType::class, ['required' => false])
                ->add('save', SubmitType::class, ['label' => 'Create Page'])
                ->getForm();

                // check for form submission
                if ($request->isMethod('POST')) {
                    $form->submit($request->request->get($form->getName()));
                    var_dump($request->request->get($form->getName()));
                    
                    // ensure valid
                    // if ($form->isSubmitted() && $form->isValid()) {
                    if ($form->isSubmitted()) {
                        // check the URL isn't already existent in DB
                        $page_repo = $this->getDoctrine()->getRepository(Page::class);
                        $existing_page = $page_repo->findOneBy(
                            array('url' => $page->getUrl())
                        );

                        // if a page is found by URL, do NOT pass go
                        if ($existing_page) {
                            return $this->render('page-form.html.twig', [
                                'form' => $form->createView(),
                                'message' => 'Sorry, the URL you chose already has a landing page associated with it. Please choose another.'
                            ]);
                        }

                        // save new page
                        $page->setUser($user->getId());

                        // commit to db
                        $em = $this->getDoctrine()->getManager();
                        $em->persist($page);
                        $em->flush();

                        // redirect to newly created page
                        return $this->redirectToRoute('page', 
                            ['id' => $page->getId()]);
                    }
                }

            return $this->render('page-form.html.twig', [
                'form' => $form->createView(),
                'message' => NULL
            ]);
        } else { return $this->render('security/unauthorized.html.twig'); }
    }

    /**
     * @Route("/page/{id}/edit/{type}", name="page_edit")
     */
    public function editPage(int $id, string $type, Request $request) {
        $user = $this->getUser();
        if ($user) { 
            // retrieve page from database
            $page_repo = $this->getDoctrine()->getRepository(Page::class);
            $page = $page_repo->find($id);

            if ($user->getId() == $page->getUser()) {
                // user owns page, continue
                switch($type) {
                    case 'title':
                        $page->setTitle($request->request->get('form')['title']);
                        break;
                    case 'url':
                        // check the URL isn't already existent in DB
                        $page_repo = $this->getDoctrine()->getRepository(Page::class);
                        $existing_page = $page_repo->findOneBy(
                            array('url' => $request->request->get('form')['url'])
                        );

                        // if a page is found by URL, do NOT pass go
                        if ($existing_page) {
                            return $this->page($id, 'Sorry, the URL you chose already has a landing page associated with it. Please choose another.');
                        }

                        $page->setUrl($request->request->get('form')['url']);
                        break;

                    case 'description':
                        $page->setDescription($request->request->get('form')['description']);
                        break;
                }

                // commit page to db
                $em = $this->getDoctrine()->getManager();
                $em->persist($page);
                $em->flush();

                // redirect with message
                return $this->redirectToRoute('page', 
                    ['id' => $page->getId(),
                    'message' => 'Page ' . $type . ' has been updated.']);
            }
        }
        return $this->render('security/unauthorized.html.twig');
    }

    /**
     * @Route("/page/edit/tier/{tier_id}")
     */
    public function editPageTier(int $tier_id, Request $request) {
        $user = $this->getUser();

        $tier_repo = $this->getDoctrine()->getRepository(SubscriptionTier::class);
        $tier = $tier_repo->findOneBy(
            array('id' => $tier_id),
        );

        $page_repo = $this->getDoctrine()->getRepository(Page::class);
        $page = $page_repo->findOneBy(
            array('id' => $tier->getPage())
        );

        if ($user->getId() == $page->getUser()) {
            // check user owns page
            // if so, handle request
            $tier->setTitle($request->request->get('form')['title']);
            $tier->setAmount($request->request->get('form')['amount']);
            $tier->setDescription($request->request->get('form')['description']);

            // commit page to db
            $em = $this->getDoctrine()->getManager();
            $em->persist($tier);
            $em->flush();

            // redirect with message
            return $this->redirectToRoute('page', 
            ['id' => $page->getId(),
            'message' => 'Page tiers have been updated.']);
        } else {
            // unauthorized
            return $this->render('security/unauthorized.html.twig');
        }
    }

    /**
     * @Route("/add/{page_id}/tier")
     */
    public function addPageTier(int $page_id, Request $request) {
        $user = $this->getUser();

        $tier = new SubscriptionTier();

        $page_repo = $this->getDoctrine()->getRepository(Page::class);
        $page = $page_repo->findOneBy(
            array('id' => $page_id)
        );

        if ($user->getId() == $page->getUser()) {
            // check user owns page
            // if so, handle request
            $tier->setTitle($request->request->get('form')['title']);
            $tier->setAmount($request->request->get('form')['amount']);
            $tier->setDescription($request->request->get('form')['description']);
            $tier->setPage($page_id);

            // commit page to db
            $em = $this->getDoctrine()->getManager();
            $em->persist($tier);
            $em->flush();

            // redirect with message
            return $this->redirectToRoute('page', 
            ['id' => $page->getId(),
            'message' => 'Page tiers have been updated.']);
        } else {
            // unauthorized
            return $this->render('security/unauthorized.html.twig');
        }
    }

    /**
     * @Route("/delete/{id}", name="page_delete")
     */
    public function deletePage(int $id) {
        $user = $this->getUser();
        if ($user) { 
            // retrieve page from database
            $page_repo = $this->getDoctrine()->getRepository(Page::class);
            $page = $page_repo->find($id);

            if ($user->getId() == $page->getUser()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($page);
                $em->flush();

                // redirect with message
                return $this->redirectToRoute('profile', 
                [
                    'id' => $page->getId(),
                    'message' => 'Your page has been successfully deleted.'
                ]);
            }
        }
        return $this->render('security/unauthorized.html.twig');
    }

    /**
     * @Route("/delete/tier/{id}", name="page_delete")
     */
    public function deleteTier(int $id) {
        $user = $this->getUser();
        if ($user) { 
            $tier_repo = $this->getDoctrine()->getRepository(SubscriptionTier::class);
            $tier = $tier_repo->findOneBy(
                array('id' => $id),
            );

            $page_repo = $this->getDoctrine()->getRepository(Page::class);
            $page = $page_repo->findOneBy(
                array('id' => $tier->getPage())
            );

            if ($user->getId() == $page->getUser()) {
                // confirm current user owns page
                $em = $this->getDoctrine()->getManager();
                $em->remove($tier);
                $em->flush();

                // redirect with message
                return $this->redirectToRoute('profile', 
                [
                    'id' => $page->getId(),
                    'message' => 'Your page has been successfully updated.'
                ]);
            }
        }
        return $this->render('security/unauthorized.html.twig');
    }

    /**
     * @Route("/publish/{id}", name="page_publish")
     */
    public function publishPage(int $id) {
        $user = $this->getUser();
        if ($user) { 
            $tier_repo = $this->getDoctrine()->getRepository(SubscriptionTier::class);
            $tier = $tier_repo->findOneBy(
                array('id' => $id),
            );

            // retrieve page from database
            $page_repo = $this->getDoctrine()->getRepository(Page::class);
            $page = $page_repo->find($tier->getid);

            if ($page->getPrivate()) {
                $page->setPrivate(0);
                $type = 'published.';
            } else {
                $page->setPrivate(1);
                $type = 'privated.';
            }

            if ($user->getId() == $page->getUser()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($page);
                $em->flush();

                // redirect with message
                return $this->redirectToRoute('page', 
                    ['id' => $page->getId(),
                    'message' => 'Page has been ' . $type]);
            }
        }
        return $this->render('security/unauthorized.html.twig');
    }

    /**
     * @Route("/outbound/{id}", name="page_outbound")
     */
    public function outbound(int $id) {
        $token = '';

        // retrieve page from database
        $page_repo = $this->getDoctrine()->getRepository(Page::class);
        $page = $page_repo->find($id);

        // retrieve tiers for page
        $tier_repo = $this->getDoctrine()->getRepository(SubscriptionTier::class);
        $tiers = $tier_repo->findBy(
            array('page' => $page->getId()),
        );

        $user = $this->getUser();
        if ($user) {  
            // check to see if user has subscription
            // retrieve user subscriptions
            $sub_repo = $this->getDoctrine()->getRepository(UserSubscription::class);
            foreach ($tiers as $tier) {
                $sub = $sub_repo->findOneBy(
                    array(
                        'user' => $user->getId(),
                        'sub_tier' => $tier->getId()
                    ),
                );
                if ($sub) {
                    break;
                }
            }

            if ($sub) {
                $temp = new User();
                $temp->setEmail($user->getEmail());
                $temp->setName($user->getName());

                $token = $this->jwt->create($temp);
            }
        }
        return $this->redirect($page->getUrl() . '?roots=' . $token);
    }
}
