<?php

namespace App\Controller;

use App\Entity\Game;
use App\Repository\ChallengeDifficultyRepository;
use App\Repository\GameRepository;
use App\Repository\PeriodRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/member')]
class MemberGameController extends AbstractController {

	#[Route('/games', name: 'app_member_games', methods: ['GET'])]
	public function games(
			Request $request,
			GameRepository $game_repository,
			UserRepository $user_repository,
			PeriodRepository $period_repository,
			ChallengeDifficultyRepository $difficulty_repository,
	): Response {

		$current_user = $user_repository->find($this->getUser()->getId());
		$current_periods = $period_repository->findCurrentPeriods();

		$current_periods_only = (bool)$request->query->get('current');

		$session = $request->getSession();
		$session->set('current_periods_only', $current_periods_only);

		if (!$current_periods_only) {
			$games = $game_repository->getGamesWithChallenges();
		}
		else {
			$games = $game_repository->getGamesWithChallenges($current_periods);
		}

		foreach ($games as $game) {
			$game->countChallenges($current_periods);
			$game->countValidSubmissions($current_user, $current_periods);
		}

		$difficulties = $difficulty_repository->findAll();

		return $this->render('member/game/games.html.twig', [
				'games'                => $games,
				'difficulties'         => $difficulties,
				'current_periods_only' => $current_periods_only,
		]);
	}


	#[Route('/game/{id}', name: 'app_member_single_game', methods: ['GET'])]
	public function single_game(
			Request $request,
			Game $game,
			UserRepository $user_repository,
			PeriodRepository $period_repository,
	): Response {

		$current_user = $user_repository->find($this->getUser()->getId());

		$session = $request->getSession();
		$current_periods_only = $session->get('current_periods_only');

		$current_periods = $period_repository->findCurrentPeriods();
		$non_current_periods = $period_repository->findNonCurrentPeriods();

		usort($current_periods, function ($a, $b) {
			return $a->getEndDate() <=> $b->getEndDate();
		});

		usort($non_current_periods, function ($a, $b) {
			return $b->getEndDate() <=> $a->getEndDate();
		});

		$periods = array_merge($current_periods, $non_current_periods);

		foreach ($periods as $period) {
			$incomplete_challenges = [];
			$completed_challenges = [];

			foreach ($period->getChallenges() as $challenge) {
				if ($challenge->getGame() !== $game) {
					continue;
				}
				if ($challenge->isValidForUser($current_user)) {
					$completed_challenges[] = $challenge;
				}
				else {
					$incomplete_challenges[] = $challenge;
				}
			}

			usort($incomplete_challenges, function ($a, $b) {
				return $a->getDifficulty() <=> $b->getDifficulty();
			});
			usort($completed_challenges, function ($a, $b) {
				return $a->getDifficulty() <=> $b->getDifficulty();
			});

			$period->setIncompleteChallenges($incomplete_challenges);
			$period->setCompletedChallenges($completed_challenges);
		}

		return $this->render('member/game/game.html.twig', [
				'game'                 => $game,
				'periods'              => $periods,
				'current_periods_only' => $current_periods_only,
		]);
	}

}
