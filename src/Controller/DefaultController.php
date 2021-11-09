<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function index(): Response
    {
        return $this->forward('App\Controller\CardController::index');
    }

    public function manage(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        return $this->render('_manage/default/index.html.twig');
    }

    /**
     * @Route("/{_locale}/search", name="search", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function search(Request $request) : Response
    {
        //TODO: implement search by params and keywords
        $isbn = $request->get('isbn');

        $cards = [];//$this->repository->findBy(['isbn' => $isbn]);

        return $this->render('default/search.html.twig', [
            'cards' => $cards,
        ]);
    }

    /**
     * @Route("/{_locale}/about", name="about", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function about(): Response
    {
        return $this->render('default/about.html.twig');
    }

    /**
     * @Route("/{_locale}/donation", name="donation", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function donation(): Response
    {
        return $this->render('default/donation.html.twig');
    }

    /**
     * @Route("/{_locale}/contact", name="contact", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function contact(): Response
    {
        return $this->render('default/contact.html.twig');
    }
    /**
     * @Route("/{_locale}/docs", name="docs", requirements={"_locale": "uk|en|ru|bg"})
     */
    public function docs(): Response
    {
        return $this->render('default/docs.html.twig');
    }
}