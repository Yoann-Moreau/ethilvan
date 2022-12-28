<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\ChallengeDifficultyRepository;
use App\Repository\GameRepository;
use App\Repository\PeriodRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/member')]
class MemberGameController extends AbstractController {

	#[Route('/games', name: 'app_member_games', methods: ['GET'])]
	public function games(GameRepository $game_repository, UserRepository $user_repository,
			PeriodRepository $period_repository, ChallengeDifficultyRepository $difficulty_repository): Response {

		$current_user = $user_repository->find($this->getUser()->getId());
		$current_periods = $period_repository->findCurrentPeriods();

		$games = $game_repository->getGamesWithChallenges();
		foreach ($games as $game) {
			$game->countChallenges($current_periods);
			$game->countValidSubmissions($current_user, $current_periods);
		}

		$difficulties = $difficulty_repository->findAll();

		return $this->render('member/game/games.html.twig', [
				'games'        => $games,
				'difficulties' => $difficulties,
		]);
	}


	#[Route('/game/{id}', name: 'app_member_single_game', methods: ['GET'])]
	public function single_game(Game $game): Response {
		return $this->render('member/game/game.html.twig', [
				'game' => $game,
		]);
	}

}
