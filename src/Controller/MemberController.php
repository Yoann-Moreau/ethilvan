<?php


namespace App\Controller;


use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/member')]
class MemberController extends AbstractController {

	#[Route('/', name: 'app_member')]
	public function index(): Response {
		return $this->render('member/index.html.twig', [
				'controller_name' => 'MemberController',
		]);
	}


	#[Route('/profile', name: 'app_member_profile')]
	public function profile(): Response {
		return $this->render('member/profile.html.twig');
	}


	#[Route('/users', name: 'app_member_users')]
	public function users(UserRepository $user_repository): Response {

		$users = $user_repository->findEv();

		return $this->render('member/users.html.twig', [
				'users' => $users,
		]);
	}
}
