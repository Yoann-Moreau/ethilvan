<?php


namespace App\Controller;


use App\Repository\GameRepository;
use App\Repository\SteamGameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/ajax')]
class AjaxController extends AbstractController {

	#[Route('/fetch_steam_game_names', name: 'ajax_fetch_steam_game_names', methods: ['POST'])]
	public function ajaxFetchSteamGameNames(Request $request, SteamGameRepository $steam_game_repository): Response {

		$name = $request->request->get('name');

		$steam_games = $steam_game_repository->findGamesByName($name, 7);

		return new JsonResponse($steam_games);
	}


	#[Route('/toggle_current_game', name: 'ajax_toggle_current_game', methods: ['POST'])]
	public function ajaxToggleCurrentGame(Request $request, GameRepository $game_repository): Response {

		if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
			return new JsonResponse('Unauthorized');
		}

		$game_id = $request->request->get('id');
		$mode = $request->request->get('mode');

		$game = $game_repository->find($game_id);

		if (empty($game)) {
			return new JsonResponse('Error - This game does not exist');
		}

		if ($mode === 'toggle-off') {
			$game->setCurrent(false);
		}
		elseif ($mode === 'toggle-on') {
			if ($game_repository->count(['current' => true]) >= 3) {
				return new JsonResponse('Error - Already 3 current games');
			}
			$game->setCurrent(true);
		}

		$game_repository->save($game, true);

		return new JsonResponse('ok');
	}
}
