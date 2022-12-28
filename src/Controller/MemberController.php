<?php


namespace App\Controller;


use App\Form\ChangeEmailType;
use App\Form\ChangePasswordType;
use App\Form\ProfileEditType;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/member')]
class MemberController extends AbstractController {

	#[Route('/', name: 'app_member', methods: ['GET'])]
	public function index(): Response {
		return $this->render('member/index.html.twig');
	}


	#[Route('/profile', name: 'app_member_profile', methods: ['GET'])]
	public function profile(UserRepository $user_repository, SubmissionRepository $submission_repository): Response {

		$user = $user_repository->find($this->getUser()->getId());
		$user->countChallengesByDifficulty();

		$last_submissions = $submission_repository->findBy(['valid' => true, 'user' => $user],
				['validation_date' => 'DESC'], 3);

		return $this->render('member/profile.html.twig', [
				'user'             => $user,
				'last_submissions' => $last_submissions,
		]);
	}


	#[Route('/profile/edit', name: 'app_member_profile_edit', methods: ['GET', 'POST'])]
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
			return $this->redirectToRoute('app_member_profile', [], Response::HTTP_SEE_OTHER);
		}
		elseif ($form->isSubmitted() && !$form->isValid()) {
			$form_errors = $validator->validate($form);
		}

		return $this->render('member/profile_edit.html.twig', [
				'form'   => $form->createView(),
				'errors' => $form_errors,
		]);
	}


	#[Route('/change_email', name: 'app_member_change_email', methods: ['GET', 'POST'])]
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


	#[Route('/change_password', name: 'app_member_change_password', methods: ['GET', 'POST'])]
	public function changePassword(Request $request, UserRepository $user_repository,
			ValidatorInterface $validator, UserPasswordHasherInterface $password_hasher): Response {

		$form_errors = [];
		$errors = [];

		$user = $user_repository->find($this->getUser()->getId());
		$form = $this->createForm(ChangePasswordType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$password = $form->get('password')->getData();
			$new_password = $form->get('newPassword')->getData();
			$c_new_password = $form->get('confirmNewPassword')->getData();

			if (!password_verify($password, $user->getPassword())) {
				$errors[] = 'Mauvais mot de passe';
			}

			if ($new_password !== $c_new_password) {
				$errors[] = 'Vous devez renseigner deux fois le même mot de passe';
			}

			if (empty($errors)) {
				$hashed_password = $password_hasher->hashPassword($user, $new_password);
				$user->setPassword($hashed_password);
				$user_repository->save($user, true);
				$this->addFlash('success', 'Mot de passe changé avec succès');
				return $this->redirectToRoute('app_member_profile_edit', [], Response::HTTP_SEE_OTHER);
			}
		}
		elseif ($form->isSubmitted() && !$form->isValid()) {
			$form_errors = $validator->validate($form);
		}

		return $this->render('member/change_password.html.twig', [
				'form'        => $form->createView(),
				'form_errors' => $form_errors,
				'errors'      => $errors,
		]);
	}


	#[Route('/users', name: 'app_member_users', methods: ['GET'])]
	public function users(UserRepository $user_repository): Response {

		$users = $user_repository->findEv();

		return $this->render('member/users.html.twig', [
				'users' => $users,
		]);
	}


	#[Route('/user/{id}', name: 'app_member_user', methods: ['GET'])]
	public function user(int $id, UserRepository $user_repository,
			SubmissionRepository $submission_repository): Response {

		$user = $user_repository->find($id);

		if ($user === null || $user->isDeleted()) {
			throw $this->createNotFoundException("L'utilisateur n'existe pas");
		}

		$user->countChallengesByDifficulty();

		$last_submissions = $submission_repository->findBy(['valid' => true, 'user' => $user],
				['validation_date' => 'DESC'], 3);

		return $this->render('member/user.html.twig', [
				'user'             => $user,
				'last_submissions' => $last_submissions,
		]);
	}


	#[Route('/notifications', name: 'app_member_notifications', methods: ['GET'])]
	public function notifications(): Response {
		return $this->render('member/notifications.html.twig');
	}

}
