<?php


namespace App\Controller;


use App\Entity\AdminNotification;
use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\AdminNotificationRepository;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class IdentificationController extends AbstractController {

	#[Route('/registration', name: 'app_registration')]
	public function registration(Request $request, UserPasswordHasherInterface $password_hasher,
			UserRepository $user_repository, ValidatorInterface $validator,
			AdminNotificationRepository $notification_repository): Response {

		$user = new User();
		$form = $this->createForm(RegistrationType::class, $user);
		$form->handleRequest($request);

		$form_errors = [];
		$errors = [];

		if ($form->isSubmitted() && $form->isValid()) {
			$username = $form->get('username')->getData();
			$username_exists = (bool)$user_repository->findOneBy(['username' => $username]);
			if ($username_exists) {
				$errors[] = 'Ce nom d\'utilisateur est déjà utilisé';
			}

			$lastname = $form->get('lastname')->getData();
			if ($lastname !== null) {
				$errors[] = 'Erreur de Captcha';
			}

			$email = $form->get('email')->getData();
			$email_exists = (bool)$user_repository->findOneBy(['email' => $email]);
			if ($email_exists) {
				$errors[] = 'Cette adresse email est déjà utilisée';
			}

			$password = $form->get('password')->getData();
			$c_password = $form->get('confirmPassword')->getData();
			if ($password !== $c_password) {
				$errors[] = 'Les deux mots de passe ne sont pas identiques';
			}

			if (empty($errors)) {
				$hashed_password = $password_hasher->hashPassword($user, $user->getPassword());
				$user->setPassword($hashed_password);
				$user->setRoles(['ROLE_USER']);
				$user->setInscriptionDate(new DateTime());
				$user->setDeleted(false);

				$user_repository->save($user, true);

				// Notify admins
				$user_link = $this->generateUrl('app_admin_user_edit', ['id' => $user->getId()]);
				$message = "Un nouvel utilisateur vient de s'inscrire : <a href='$user_link'>". $username . '</a>';
				$notification = new AdminNotification();
				$notification->setMessage($message);
				$notification->setDate(new DateTime());
				$notification_repository->save($notification, true);

				$this->addFlash('success', 'Compte créé avec succès');

				return $this->redirectToRoute('app_login');
			}
		}
		elseif ($form->isSubmitted() && !$form->isValid()) {
			$form_errors = $validator->validate($user);
		}

		return $this->render('identification/registration.html.twig', [
				'form_errors' => $form_errors,
				'errors'      => $errors,
				'form'        => $form->createView(),
		]);
	}


	#[Route('/login', name: 'app_login')]
	public function login(AuthenticationUtils $authentication_utils): Response {
		// get the login error if there is one
		$error = $authentication_utils->getLastAuthenticationError();

		// last username entered by the user
		$last_username = $authentication_utils->getLastUsername();

		return $this->render('identification/login.html.twig', [
				'last_username' => $last_username,
				'error'         => $error,
		]);
	}


	#[Route('/logout', name: 'app_logout', methods: ['GET'])]
	public function logout() {
		// controller can be blank: it will never be called!
		throw new \Exception('Don\'t forget to activate logout in security.yaml');
	}

}
