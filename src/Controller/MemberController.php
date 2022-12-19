<?php


namespace App\Controller;


use App\Form\ChangeEmailType;
use App\Form\ProfileEditType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


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
	public function profileEdit(Request $request, UserRepository $user_repository,
			ValidatorInterface $validator): Response {

		$form_errors = [];

		$user = $user_repository->find($this->getUser()->getId());
		$form = $this->createForm(ProfileEditType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$avatar = $form->get('avatar')->getData();

			if (!empty($avatar)) {
				$avatar_name = $user->getId() . '_' . uniqid() . $avatar->guessExtension();
				$directory = $this->getParameter('avatars_directory');
				$avatar->move($directory, $avatar_name);
				$user->setAvatar($avatar_name);
			}

			$user_repository->save($user, true);
			$this->redirectToRoute('app_member_profile', [], Response::HTTP_SEE_OTHER);
		}
		elseif ($form->isSubmitted() && !$form->isValid()) {
			$form_errors = $validator->validate($form);
		}

		return $this->render('member/profile_edit.html.twig', [
				'form'   => $form->createView(),
				'errors' => $form_errors,
		]);
	}


	#[Route('/change_email', name: 'app_member_change_email')]
	public function changeEmail(Request $request, UserRepository $user_repository,
			ValidatorInterface $validator): Response {

		$form_errors = [];
		$errors = [];

		$user = $user_repository->find($this->getUser()->getId());
		$form = $this->createForm(ChangeEmailType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$email = $form->get('email')->getData();
			$password = $form->get('password')->getData();

			if (!password_verify($password, $user->getPassword())) {
				$errors[] = 'Mauvais mot de passe';
			}

			if ($user_repository->findOneBy(['email' => $email]) !== null) {
				$errors[] = 'Adresse email déjà utilisée';
			}

			if (empty($errors)) {
				$user_repository->save($user, true);
				$this->addFlash('success', 'Adresse email changée avec succès');
				return $this->redirectToRoute('app_member_profile_edit', [], Response::HTTP_SEE_OTHER);
			}
		}
		elseif ($form->isSubmitted() && !$form->isValid()) {
			$form_errors = $validator->validate($form);
		}

		return $this->render('member/change_email.html.twig', [
				'form'        => $form->createView(),
				'form_errors' => $form_errors,
				'errors'      => $errors,
		]);
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
