<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Form\ChallengeType;
use App\Repository\ChallengeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/challenge')]
class AdminChallengeController extends AbstractController {

	#[Route('/', name: 'app_admin_challenge_index', methods: ['GET'])]
	public function index(ChallengeRepository $challenge_repository): Response {
		return $this->render('admin_challenge/index.html.twig', [
				'challenges' => $challenge_repository->findAll(),
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
