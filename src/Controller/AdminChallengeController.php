<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/challenge')]
class AdminChallengeController extends AbstractController {

	#[Route('/', name: 'app_admin_challenge_index', methods: ['GET'])]
	public function index(Request $request, ChallengeRepository $challenge_repository,
			PaginationService $pagination_service): Response {

		$elements_per_page = 10;

		$sort_by = $request->query->get('sort_by');
		$page = (int)$request->query->get('page');

		if ($sort_by !== 'game' && $sort_by !== 'difficulty' && $sort_by !== 'period') {
			$sort_by = null;
		}
		if ($page < 1) {
			$page = 1;
		}

		$offset = $elements_per_page * ($page - 1);
		if ($sort_by === 'game') {
			$challenges = $challenge_repository->findOrderedByGameName($elements_per_page, $offset);
		}
		elseif ($sort_by === 'difficulty') {
			$challenges = $challenge_repository->findBy([], ['difficulty' => 'ASC'], $elements_per_page, $offset);
		}
		elseif ($sort_by === 'period') {
			$challenges = $challenge_repository->findOrderedByPeriod($elements_per_page, $offset);
		}
		else {
			$challenges = $challenge_repository->findBy([], ['id' => 'ASC'], $elements_per_page, $offset);
		}

		// Pagination
		$number_of_elements = $challenge_repository->count([]);
		$pages = $pagination_service->getPages($number_of_elements, $elements_per_page, $page);

		return $this->render('admin_challenge/index.html.twig', [
				'challenges' => $challenges,
				'page'       => $page,
				'sort_by'    => $sort_by,
				'pages'      => $pages,
		]);
	}


	#[Route('/new', name: 'app_admin_challenge_new', methods: ['GET', 'POST'])]
	public function new(Request $request, ChallengeRepository $challenge_repository): Response {
		$challenge = new Challenge();
		$form = $this->createForm(ChallengeType::class, $challenge);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$challenge_repository->save($challenge, true);

			return $this->redirectToRoute('app_admin_challenge_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('admin_challenge/new.html.twig', [
				'challenge' => $challenge,
				'form'      => $form->createView(),
		]);
	}


	#[Route('/{id}/edit', name: 'app_admin_challenge_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Challenge $challenge, ChallengeRepository $challenge_repository): Response {
		$form = $this->createForm(ChallengeType::class, $challenge, [
				'number_of_players' => $challenge->getNumberOfPlayers(),
		]);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$challenge_repository->save($challenge, true);

			return $this->redirectToRoute('app_admin_challenge_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('admin_challenge/edit.html.twig', [
				'challenge' => $challenge,
				'form'      => $form->createView(),
		]);
	}


	#[Route('/{id}', name: 'app_admin_challenge_delete', methods: ['POST'])]
	public function delete(Request $request, Challenge $challenge, ChallengeRepository $challenge_repository): Response {
		if ($this->isCsrfTokenValid('delete' . $challenge->getId(), $request->request->get('_token'))) {
			$challenge_repository->remove($challenge, true);
		}

		return $this->redirectToRoute('app_admin_challenge_index', [], Response::HTTP_SEE_OTHER);
	}
}
