<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user')]
class AdminUserController extends AbstractController {

	#[Route('/', name: 'app_admin_user_index', methods: ['GET'])]
	public function index(UserRepository $user_repository): Response {
		return $this->render('admin_user/index.html.twig', [
				'users' => $user_repository->findBy(['deleted' => false], ['username' => 'ASC']),
		]);
	}


	#[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, User $user, UserRepository $user_repository): Response {
		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$user_repository->save($user, true);

			return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->renderForm('admin_user/edit.html.twig', [
				'user' => $user,
				'form' => $form,
		]);
	}


	#[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
	public function delete(Request $request, User $user, UserRepository $user_repository): Response {

		if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {

			if (count($user->getRoles()) > 1) {
				$this->addFlash('error', "Seuls les utilisateurs n'ayant que le rôle 'utilisateur' peuvent être 
						supprimés");

				return $this->redirectToRoute('app_admin_user_edit', [
						'id' => $user->getId(),
				]);
			}

			$user->setUsername('[Utilisateur supprimé]');
			$user->setEmail('contact@ethilvan.fr');
			$user->setPassword('');
			$user->setDeleted(true);

			$user_repository->save($user, true);
		}

		return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
	}
}
