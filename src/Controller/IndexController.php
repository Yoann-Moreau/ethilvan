<?php


namespace App\Controller;


use App\Repository\TextRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController {

	#[Route('/', name: 'app_index')]
	public function index(TextRepository $text_repository): Response {

		$index_texts = $text_repository->findBy(['page' => 'index'], ['text_order' => 'ASC']);

		return $this->render('index.html.twig',
		                     [
				                     'texts' => $index_texts,
		                     ]
		);
	}

}
