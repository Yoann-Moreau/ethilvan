<?php

namespace App\Controller;

use App\Entity\Period;
use App\Form\CopyChallengesType;
use App\Form\PeriodType;
use App\Repository\ChallengeDifficultyRepository;
use App\Repository\ChallengeRepository;
use App\Repository\PeriodRepository;
use App\Service\ToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/period')]
class AdminPeriodController extends AbstractController {

	#[Route('/', name: 'app_admin_period_index', methods: ['GET'])]
	public function index(PeriodRepository $period_repository): Response {
		return $this->render('admin_period/index.html.twig', [
				'periods' => $period_repository->findBy([], ['id' => 'DESC']),
		]);
	}


	#[Route('/new', name: 'app_admin_period_new', methods: ['GET', 'POST'])]
	public function new(
			Request $request,
			PeriodRepository $period_repository,
			ToolsService $tools_service,
			ValidatorInterface $validator
	): Response {

		$form_errors = [];
		$errors = [];

		$period = new Period();
		$form = $this->createForm(PeriodType::class, $period);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$start_date = $form->get('start_date')->getData();
			$end_date = $form->get('end_date')->getData();

			if ($end_date < $start_date) {
				$errors[] = 'La date de fin doit être égale ou suéprieure à la date de début';
			}

			if (empty($errors)) {
				$banner = $form->get('banner')->getData();

				if (!empty($banner)) {
					$file_name = $tools_service->slugify($period->getName()) . uniqid('_') . '.' . $banner->guessExtension();
					$directory = $this->getParameter('period_banners_directory');

					$banner->move($directory, $file_name);

					$period->setBanner($file_name);
				}
				$period_repository->save($period, true);

				return $this->redirectToRoute('app_admin_period_index', [], Response::HTTP_SEE_OTHER);
			}
		}
		elseif ($form->isSubmitted() && !$form->isValid()) {
			$form_errors = $validator->validate($form);
		}

		return $this->render('admin_period/new.html.twig', [
				'period'      => $period,
				'form'        => $form->createView(),
				'form_errors' => $form_errors,
				'errors'      => $errors,
		]);
	}


	#[Route('/{id}/edit', name: 'app_admin_period_edit', methods: ['GET', 'POST'])]
	public function edit(
			Request $request,
			Period $period,
			PeriodRepository $period_repository,
			ToolsService $tools_service,
			ValidatorInterface $validator
	): Response {

		$form_errors = [];
		$errors = [];

		$old_banner = $period->getBanner();

		$form = $this->createForm(PeriodType::class, $period);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			$start_date = $form->get('start_date')->getData();
			$end_date = $form->get('end_date')->getData();

			if ($end_date < $start_date) {
				$errors[] = 'La date de fin doit être égale ou suéprieure à la date de début';
			}

			if (empty($errors)) {
				$file = $form->get('banner')->getData();

				if (!empty($file)) {
					$file_name = $tools_service->slugify($period->getName()) . uniqid('_') . '.' . $file->guessExtension();
					$directory = $this->getParameter('period_banners_directory');

					// Delete old banner
					if ($old_banner !== $file_name && $old_banner !== null) {
						if (file_exists($directory . '/' . $old_banner)) {
							unlink($directory . '/' . $old_banner);
						}
					}

					$file->move($directory, $file_name);
					$period->setBanner($file_name);
				}
				$period_repository->save($period, true);

				return $this->redirectToRoute('app_admin_period_index', [], Response::HTTP_SEE_OTHER);
			}
		}
		elseif ($form->isSubmitted() && !$form->isValid()) {
			$form_errors = $validator->validate($form);
		}

		return $this->render('admin_period/edit.html.twig', [
				'period'      => $period,
				'form'        => $form->createView(),
				'form_errors' => $form_errors,
				'errors'      => $errors,
		]);
	}


	#[Route('/{id}', name: 'app_admin_period_delete', methods: ['POST'])]
	public function delete(
			Request $request,
			Period $period,
			PeriodRepository $period_repository
	): Response {

		if ($this->isCsrfTokenValid('delete' . $period->getId(), $request->request->get('_token'))) {
			$period_repository->remove($period, true);
		}

		return $this->redirectToRoute('app_admin_period_index', [], Response::HTTP_SEE_OTHER);
	}


	#[Route('/{id}/challenges', name: 'app_admin_period_challenges', methods: ['GET', 'POST'])]
	public function challenges(
			Request $request,
			Period $period,
			ChallengeRepository $challenge_repository,
			ChallengeDifficultyRepository $difficulty_repository,
			FormFactoryInterface $form_factory,
			EntityManagerInterface $entity_manager,
	): Response {

		$total = $challenge_repository->countByPeriodAndDifficulty($period);
		$difficulties = $difficulty_repository->findAll();
		foreach ($difficulties as $difficulty) {
			$difficulty->setTotalChallenges($challenge_repository->countByPeriodAndDifficulty($period, $difficulty));
		}

		$copy_form = $form_factory->createNamed(
				'copy-challenges-form',
				CopyChallengesType::class,
				null,
				['period' => $period]
		);

		$delete_form = $this->createFormBuilder()->getForm();

		$copy_form->handleRequest($request);
		$delete_form->handleRequest($request);

		if ($copy_form->isSubmitted() && $copy_form->isValid()) {
			$target_period = $copy_form->get('period')->getData();

			foreach ($period->getChallenges() as $challenge) {
				$challenge->addPeriod($target_period);
			}

			$entity_manager->flush();

			return $this->redirectToRoute(
					'app_admin_period_challenges',
					['id' => $target_period->getId()],
					Response::HTTP_SEE_OTHER
			);
		}

		if ($delete_form->isSubmitted() && $delete_form->isValid()) {

			foreach ($period->getChallenges() as $challenge) {
				$challenge->removePeriod($period);
			}
			$entity_manager->flush();

			return $this->redirectToRoute(
					'app_admin_period_challenges',
					['id' => $period->getId()],
					Response::HTTP_SEE_OTHER
			);
		}

		return $this->render('admin_period/challenges.html.twig', [
				'period'       => $period,
				'total'        => $total,
				'difficulties' => $difficulties,
				'copy_form'    => $copy_form->createView(),
				'delete_form'  => $delete_form->createView(),
		]);
	}

}
