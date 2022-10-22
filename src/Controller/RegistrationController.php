<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class RegistrationController extends AbstractController {

	#[Route('/registration', name: 'app_registration')]
	public function registration(Request $request, UserPasswordHasherInterface $password_hasher,
			UserRepository $user_repository): Response {

		$user = new User();
		$form = $this->createForm(RegistrationType::class, $user);
		$form->handleRequest($request);

		$errors = [];

		if ($form->isSubmitted() && $form->isValid()) {
			$username = $form->get('username')->getData();
			$username_exists = (bool)$user_repository->findOneBy(['username' => $username]);
			if ($username_exists) {
				$errors[] = 'Ce nom d\'utilisateur est déjà utilisé';
			}

			$email = $form->get('email')->getData();
			$email_exists = (bool)$user_repository->findOneBy(['email' => $email]);
			if ($email_exists) {
				$errors[] = 'Cette adresse email est déjà utilisée';
			}

			$password = $form->get('password')->getData();
			$c_password = $form->get('confirm-password')->getData();
			if ($password !== $c_password) {
				$errors[] = 'Les deux mots de passe ne sont pas identiques';
			}

			if (empty($errors)) {
				$hashed_password = $password_hasher->hashPassword($user, $user->getPassword());
				$user->setPassword($hashed_password);

				$user_repository->save($user, true);
			}
		}

		return $this->render('registration/registration.html.twig', [
				'errors' => $errors,
				'form'   => $form->createView(),
		]);
	}

}
