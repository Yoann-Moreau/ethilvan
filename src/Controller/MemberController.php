<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/member')]
class MemberController extends AbstractController {

	#[Route('/', name: 'app_member')]
	public function index(): Response {
		return $this->render('member/index.html.twig');
	}


	#[Route('/profile', name: 'app_member_profile')]
	public function profile(): Response {
		return $this->render('member/profile.html.twig');
	}


	#[Route('/profile/edit', name: 'app_member_profile_edit')]
	public function profileEdit(): Response {
		return $this->render('member/profile_edit.html.twig');
	}


	#[Route('/users', name: 'app_member_users')]
	public function users(UserRepository $user_repository): Response {

		$users = $user_repository->findEv();

		return $this->render('member/users.html.twig', [
				'users' => $users,
		]);
	}


	#[Route('/user/{id}', name: 'app_member_user')]
	public function user(int $id, UserRepository $user_repository): Response {
		$user = $user_repository->find($id);

		if ($user === null || $user->isDeleted()) {
			throw $this->createNotFoundException("L'utilisateur n'existe pas");
		}

		return $this->render('member/user.html.twig', [
				'user' => $user,
		]);
	}

}
