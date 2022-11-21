<?php


namespace App\Controller;


use App\Repository\GameRepository;
use App\Repository\TextRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController {

	#[Route('/', name: 'app_index')]
	public function index(TextRepository $text_repository, GameRepository $game_repository): Response {

		$index_texts = $text_repository->findBy(['page' => 'index'], ['text_order' => 'ASC']);
		$current_games = $game_repository->findBy(['current' => true], ['name' => 'ASC'], 3);

		return $this->render('index.html.twig', [
				'texts'         => $index_texts,
				'current_games' => $current_games,
		]);
	}

}
