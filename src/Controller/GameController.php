<?php


namespace App\Controller;


use App\Entity\Game;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Service\ToolsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/game')]
class GameController extends AbstractController {

	#[Route('/', name: 'app_game_index', methods: ['GET'])]
	public function index(GameRepository $game_repository): Response {
		return $this->render('game/index.html.twig', [
				'games' => $game_repository->findAll(),
		]);
	}


	#[Route('/new', name: 'app_game_new', methods: ['GET', 'POST'])]
	public function new(Request $request, GameRepository $game_repository, ToolsService $tools_service): Response {
		$game = new Game();
		$form = $this->createForm(GameType::class, $game);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$file = $form->get('image')->getData();
			$file_name = $tools_service->slugify($game->getName()) . '.' . $file->guessExtension();
			$directory = $this->getParameter('game_images_directory');

			$file->move($directory, $file_name);

			$game->setImage($file_name);
			$game_repository->save($game, true);

			return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('game/new.html.twig', [
				'game' => $game,
				'form' => $form,
		]);
	}


	#[Route('/{id}', name: 'app_game_show', methods: ['GET'])]
	public function show(Game $game): Response {
		return $this->render('game/show.html.twig', [
				'game' => $game,
		]);
	}


	#[Route('/{id}/edit', name: 'app_game_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Game $game, GameRepository $game_repository): Response {
		$form = $this->createForm(GameType::class, $game);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$game_repository->save($game, true);

			return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('game/edit.html.twig', [
				'game' => $game,
				'form' => $form,
		]);
	}


	#[Route('/{id}', name: 'app_game_delete', methods: ['POST'])]
	public function delete(Request $request, Game $game, GameRepository $game_repository): Response {
		if ($this->isCsrfTokenValid('delete' . $game->getId(), $request->request->get('_token'))) {
			$game_repository->remove($game, true);
		}

		return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
	}
}
