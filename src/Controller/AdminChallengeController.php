<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Form\ChallengeAddToYearType;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use App\Repository\PeriodRepository;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/challenge')]
class AdminChallengeController extends AbstractController {

	#[Route('/', name: 'app_admin_challenge_index', methods: ['GET'])]
	public function index(
			Request $request,
			ChallengeRepository $challenge_repository,
			PaginationService $pagination_service
	): Response {

		$elements_per_page = 10;

		$sort_by = $request->query->get('sort_by');
		$page = (int)$request->query->get('page');
		$search = $request->query->get('search');

		if ($sort_by !== 'game' && $sort_by !== 'difficulty' && $sort_by !== 'period') {
			$sort_by = null;
		}
		if ($page < 1) {
			$page = 1;
		}
		if ($search === null) {
			$search = '';
		}
		if ($sort_by === null) {
			$sort_by = '';
		}

		$offset = $elements_per_page * ($page - 1);
		$challenges = $challenge_repository->search($search, $elements_per_page, $offset, $sort_by);

		// Pagination
		$number_of_elements = $challenge_repository->countWithSearch($search);
		$pages = $pagination_service->getPages($number_of_elements, $elements_per_page, $page);

		return $this->render('admin_challenge/index.html.twig', [
				'challenges' => $challenges,
				'page'       => $page,
				'sort_by'    => $sort_by,
				'pages'      => $pages,
				'search'     => $search,
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


	#[Route('/{id}/add_to_year', name: 'app_admin_challenge_add_to_year', methods: ['GET', 'POST'])]
	public function addToYear(
			Challenge $challenge,
			Request $request,
			ChallengeRepository $challenge_repository,
			PeriodRepository $period_repository,
	): Response {

		$new_challenge = new Challenge();

		$form = $this->createForm(ChallengeAddToYearType::class, $new_challenge);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$year = date('Y');
			$current_year_period = $period_repository->findOneBy(['year' => $year, 'type' => 'year']);

			$new_challenge->setName($challenge->getName());
			$new_challenge->setDescription($challenge->getDescription());
			$new_challenge->setGame($challenge->getGame());
			$new_challenge->setNumberOfPlayers($challenge->getNumberOfPlayers());
			$new_challenge->setEventChallenge($challenge);
			$new_challenge->addPeriod($current_year_period);

			$challenge_repository->save($new_challenge, true);

			return $this->redirectToRoute(
					'app_admin_challenge_edit',
					['id' => $new_challenge->getId()],
					Response::HTTP_SEE_OTHER
			);
		}

		return $this->render('admin_challenge/add_to_year.html.twig', [
				'challenge' => $challenge,
				'form'      => $form->createView(),
		]);
	}

}
