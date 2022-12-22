<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/member')]
class MemberGameController extends AbstractController {

	#[Route('/games', name: 'app_member_games', methods: ['GET'])]
	public function games(GameRepository $game_repository): Response {

		$games = $game_repository->getGamesWithChallenges();
		foreach ($games as $game) {
			$game->countChallenges();
		}

		return $this->render('member/game/games.html.twig', [
				'games' => $games,
		]);
	}

}
