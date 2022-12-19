<?php


namespace App\Controller;


use App\Entity\Text;
use App\Form\PageTextType;
use App\Repository\TextRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/text')]
class AdminTextController extends AbstractController {

	#[Route('/', name: 'app_text_index', methods: ['GET'])]
	public function index(TextRepository $text_repository): Response {
		return $this->render('admin_text/index.html.twig', [
				'texts' => $text_repository->findBy([], ['page' => 'ASC', 'text_order' => 'ASC']),
		]);
	}


	#[Route('/new', name: 'app_text_new', methods: ['GET', 'POST'])]
	public function new(Request $request, TextRepository $text_repository): Response {
		$text = new Text();
		$form = $this->createForm(PageTextType::class, $text);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$text_repository->save($text, true);

			return $this->redirectToRoute('app_text_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('admin_text/new.html.twig', [
				'text' => $text,
				'form' => $form,
		]);
	}


	#[Route('/{id}/edit', name: 'app_text_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Text $text, TextRepository $text_repository): Response {
		$form = $this->createForm(PageTextType::class, $text);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$text_repository->save($text, true);

			return $this->redirectToRoute('app_text_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('admin_text/edit.html.twig', [
				'text' => $text,
				'form' => $form,
		]);
	}


	#[Route('/{id}', name: 'app_text_delete', methods: ['POST'])]
	public function delete(Request $request, Text $text, TextRepository $text_repository): Response {
		if ($this->isCsrfTokenValid('delete' . $text->getId(), $request->request->get('_token'))) {
			$text_repository->remove($text, true);
		}

		return $this->redirectToRoute('app_text_index', [], Response::HTTP_SEE_OTHER);
	}

}
