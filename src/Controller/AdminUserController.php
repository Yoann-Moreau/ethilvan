<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\MailjetService;
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
	public function edit(
			Request $request,
			User $user,
			UserRepository $user_repository,
			MailjetService $mailjet_service
	): Response {

		$old_user_roles = $user->getRoles();

		$form = $this->createForm(UserType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$new_user_roles = $user->getRoles();

			// Send email on user validation
			if (!in_array('ROLE_EV', $old_user_roles) && in_array('ROLE_EV', $new_user_roles)) {
				$subject = 'Votre compte vient d\'être activé sur ethilvan.fr';
				$mailjet_service->send($user->getEmail(), $user->getUsername(), $subject,
						$_ENV['ACCOUNT_ACTIVATION_TEMPLATE_ID'], [
								'username' => $user->getUsername(),
						]);
			}

			$user_repository->save($user, true);

			return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('admin_user/edit.html.twig', [
				'user' => $user,
				'form' => $form->createView(),
		]);
	}


	#[Route('/delete/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
	public function delete(
			Request $request,
			User $user,
			UserRepository $user_repository
	): Response {

		if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {

			if (count($user->getRoles()) > 1) {
				$this->addFlash('error', "Seuls les utilisateurs n'ayant que le rôle 'utilisateur' peuvent être 
						supprimés");

				return $this->redirectToRoute('app_admin_user_edit', [
						'id' => $user->getId(),
				]);
			}

			$user_repository->remove($user, true);
		}

		return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
	}


	#[Route('/anonymize/{id}', name: 'app_admin_user_anonymize', methods: ['POST'])]
	public function anonymize(
			Request $request,
			User $user,
			UserRepository $user_repository
	): Response {

		if ($this->isCsrfTokenValid('anonymize' . $user->getId(), $request->request->get('_token'))) {

			if (count($user->getRoles()) > 1) {
				$this->addFlash('error', "Seuls les utilisateurs n'ayant que le rôle 'utilisateur' peuvent être 
						anonymisés");

				return $this->redirectToRoute('app_admin_user_edit', [
						'id' => $user->getId(),
				]);
			}

			$user->setUsername('deleted_user_' . $user->getId());
			$user->setEmail('deleted_user_' . $user->getId() . '@ethilvan.fr');
			$user->setPassword('');
			$user->setAvatar(null);
			$user->setFavoriteGames(null);
			$user->setDeleted(true);

			$user_repository->save($user, true);
		}

		return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
	}
}
