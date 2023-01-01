<?php

namespace App\Controller;

use App\Entity\Period;
use App\Form\PeriodType;
use App\Repository\PeriodRepository;
use App\Service\ToolsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/period')]
class AdminPeriodController extends AbstractController {

	#[Route('/', name: 'app_admin_period_index', methods: ['GET'])]
	public function index(PeriodRepository $period_repository): Response {
		return $this->render('admin_period/index.html.twig', [
				'periods' => $period_repository->findAll(),
		]);
	}


	#[Route('/new', name: 'app_admin_period_new', methods: ['GET', 'POST'])]
	public function new(Request $request, PeriodRepository $period_repository, ToolsService $tools_service): Response {
		$period = new Period();
		$form = $this->createForm(PeriodType::class, $period);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

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

		return $this->render('admin_period/new.html.twig', [
				'period' => $period,
				'form'   => $form->createView(),
		]);
	}


	#[Route('/{id}/edit', name: 'app_admin_period_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Period $period, PeriodRepository $period_repository,
			ToolsService $tools_service): Response {

		$old_banner = $period->getBanner();

		$form = $this->createForm(PeriodType::class, $period);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
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

		return $this->render('admin_period/edit.html.twig', [
				'period' => $period,
				'form'   => $form->createView(),
		]);
	}


	#[Route('/{id}', name: 'app_admin_period_delete', methods: ['POST'])]
	public function delete(Request $request, Period $period, PeriodRepository $period_repository): Response {
		if ($this->isCsrfTokenValid('delete' . $period->getId(), $request->request->get('_token'))) {
			$period_repository->remove($period, true);
		}

		return $this->redirectToRoute('app_admin_period_index', [], Response::HTTP_SEE_OTHER);
	}

}
