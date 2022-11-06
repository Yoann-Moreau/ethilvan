<?php


namespace App\Controller;


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

		$steam_games = $steam_game_repository->findGamesByName($name, 7, 0);

		return new JsonResponse($steam_games);
	}
}
