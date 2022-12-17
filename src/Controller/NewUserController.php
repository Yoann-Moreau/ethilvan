<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class NewUserController extends AbstractController {

	#[Route('/new_user', name: 'app_new_user')]
	public function index(): Response {

		if ($this->isGranted('ROLE_EV')) {
			return $this->redirectToRoute('app_member');
		}

		return $this->render('new_user/index.html.twig');
	}
}
