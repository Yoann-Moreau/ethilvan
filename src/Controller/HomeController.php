<?php


namespace App\Controller;


use App\Repository\GameRepository;
use App\Repository\TextRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController {

	#[Route('/', name: 'app_index')]
	public function index(TextRepository $text_repository, GameRepository $game_repository): Response {

		$index_texts = $text_repository->findBy(['page' => 'index'], ['text_order' => 'ASC']);
		$current_games = $game_repository->findBy(['current' => true], ['name' => 'ASC'], 3);

		return $this->render('front/index.html.twig', [
				'texts'         => $index_texts,
				'current_games' => $current_games,
		]);
	}


	#[Route('/games', name: 'app_games')]
	public function games(TextRepository $text_repository, GameRepository $game_repository): Response {

		$games_texts = $text_repository->findBy(['page' => 'games'], ['text_order' => 'ASC']);
		$current_games = $game_repository->findBy(['current' => true], ['name' => 'ASC'], 3);
		$games = $game_repository->findBy([], ['name' => 'ASC']);

		return $this->render('front/games.html.twig', [
				'texts'         => $games_texts,
				'current_games' => $current_games,
				'games'         => $games,
		]);
	}


	#[Route('/legals', name: 'app_legals')]
	public function legals(TextRepository $text_repository): Response {

		$legals_texts = $text_repository->findBy(['page' => 'legals'], ['text_order' => 'ASC']);

		return $this->render('front/legals.html.twig', [
				'texts' => $legals_texts,
		]);
	}

}
