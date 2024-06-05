<?php

namespace App\Controller;

use App\Entity\AdminNotification;
use App\Entity\Challenge;
use App\Entity\Notification;
use App\Entity\Period;
use App\Entity\Submission;
use App\Entity\SubmissionMessage;
use App\Entity\SubmissionMessageImage;
use App\Entity\User;
use App\Form\SubmissionMessageType;
use App\Repository\AdminNotificationRepository;
use App\Repository\ChallengeRepository;
use App\Repository\NotificationRepository;
use App\Repository\PeriodRepository;
use App\Repository\SubmissionMessageImageRepository;
use App\Repository\SubmissionMessageRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use App\Service\ImageService;
use App\Service\PaginationService;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/member')]
class MemberChallengeController extends AbstractController {

	#[Route('/challenges', name: 'app_member_challenges', methods: ['GET'])]
	public function challenges(Request $request, ChallengeRepository $challenge_repository,
			PaginationService $pagination_service): Response {

		$elements_per_page = 12;

		$sort_by = $request->query->get('sort_by');
		$page = (int)$request->query->get('page');
		$search = $request->query->get('search');

		if ($sort_by !== 'game' && $sort_by !== 'difficulty' && $sort_by !== 'period') {
			$sort_by = '';
		}
		if ($page < 1) {
			$page = 1;
		}
		if ($search === null) {
			$search = '';
		}

		$offset = $elements_per_page * ($page - 1);

		$challenges = $challenge_repository->search($search, $elements_per_page, $offset, $sort_by);

		// Pagination
		$number_of_elements = $challenge_repository->countWithSearch($search);
		$pages = $pagination_service->getPages($number_of_elements, $elements_per_page, $page);

		return $this->render('member/challenge/challenges.html.twig', [
				'challenges' => $challenges,
				'sort_by'    => $sort_by,
				'page'       => $page,
				'pages'      => $pages,
				'search'     => $search,
		]);
	}


	#[Route('/challenge/{id}', name: 'app_member_single_challenge', methods: ['GET', 'POST'])]
	public function singleChallenge(Challenge $challenge, Request $request, SubmissionRepository $submission_repository,
			SubmissionMessageRepository $message_repository, UserRepository $user_repository,
			PeriodRepository $period_repository, SubmissionMessageImageRepository $image_repository,
			ValidatorInterface $validator, ImageService $image_service,
			NotificationRepository $notification_repository,
			AdminNotificationRepository $admin_notification_repository): Response {

		$new_message = new SubmissionMessage();
		$form = $this->createForm(SubmissionMessageType::class, $new_message);

		$current_user = $user_repository->find($this->getUser()->getId());
		$form_errors = [];
		$errors = [];

		// Check if one the periods is current (to display form)
		$period = null;
		$is_current = false;
		$current_periods = $period_repository->findCurrentPeriods();
		foreach ($current_periods as $current_period) {
			if ($challenge->getPeriods()->contains($current_period)) {
				$is_current = true;
				$period = $current_period;
				break;
			}
		}

		// Check if user has already submitted this challenge
		$already_submitted = false;
		$current_submission = null;
		$is_valid = false;
		foreach ($current_user->getSubmissions() as $submission) {
			if ($submission->getChallenge()->getId() === $challenge->getId()) {
				$already_submitted = true;
				$current_submission = $submission;
				$is_valid = $submission->isValid();
				break;
			}
		}

		$messages = null;
		if ($already_submitted) {
			$messages = $current_submission->getSubmissionMessages();
		}

		// Add input for players
		if (!$already_submitted && $challenge->getNumberOfPlayers() > 1) {
			$form->add('players', EntityType::class, [
					'label'         => 'Autres joueurs ayant participé',
					'mapped'        => false,
					'class'         => User::class,
					'choice_label'  => 'username',
					'multiple'      => true,
					'required'      => false,
					'query_builder' => function (UserRepository $user_repository) use ($current_user) {
						return $user_repository->otherEvQueryBuilder($current_user->getId());
					},
			]);
		}

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid() && $is_current && !empty($period) && !$is_valid) {

			$player_ids = [];
			if (array_key_exists('players', $form->all())) {
				$player_ids = $form->get('players')->getData(); // ArrayCollection
				$player_ids = $player_ids->toArray();
			}
			$player_ids[] = $current_user->getId();

			if (count($player_ids) > $challenge->getNumberOfPlayers()) {
				$errors[] = 'Ce défi ne peut accepter que ' . $challenge->getNumberOfPlayers() . ' joueurs (vous y compris)';
			}

			foreach ($player_ids as $player_id) {
				$checked_user = $user_repository->find($player_id);

				if ($checked_user === $current_user) {
					continue;
				}

				foreach ($checked_user->getSubmissions() as $submission) {
					if ($submission->getChallenge() === $challenge && $submission->getPeriod() === $period) {
						$errors[] = 'Le joueur ' . $checked_user->getUsername() .
								' a déjà soumis ce défi à validation pour cette période.';
					}
					elseif ($submission->getChallenge() === $challenge) {
						$errors[] = 'Le joueur ' . $checked_user->getUsername() .
								'a déjà soumis ce défi à validation pour une autre prériode.';
					}
				}
			}

			if (empty($errors)) {
				$submissions = $this->postSubmissions($already_submitted, $player_ids, $challenge, $period, $current_user,
						$user_repository, $submission_repository, $notification_repository, $admin_notification_repository);

				$this->postSubmissionMessages($new_message, $submissions, $current_user, $form, $message_repository,
						$image_service, $image_repository);

				if ($already_submitted) {
					// Add notification for admins
					$submission_link = $this->generateUrl('app_admin_submission', ['id' => $submission->getId()]);
					$message = 'Le joueur ' . $current_user->getUsername() . ' a répondu pour le défi ' . $challenge->getName() .
							' du jeu ' . $challenge->getGame()->getName() . ". <a href='$submission_link'>Demande #" .
							$submission->getId() . '</a>';
					$notification = new AdminNotification();
					$notification->setMessage($message);
					$notification->setDate(new DateTime());
					$admin_notification_repository->save($notification, true);
				}

				// Redirect to notifications on success
				return $this->redirectToRoute('app_member_notifications');
			}
		}
		elseif ($form->isSubmitted() && !$form->isValid()) {
			$form_errors = $validator->validate($form);
		}

		return $this->render('member/challenge/challenge.html.twig', [
				'challenge'         => $challenge,
				'form'              => $form->createView(),
				'already_submitted' => $already_submitted,
				'is_current'        => $is_current,
				'is_valid'          => $is_valid,
				'messages'          => $messages,
				'form_errors'       => $form_errors,
				'errors'            => $errors,
		]);
	}


	/**
	 * @param bool $already_submitted
	 * @param array $player_ids
	 * @param Challenge $challenge
	 * @param Period $period
	 * @param User $current_user
	 * @param UserRepository $user_repository
	 * @param SubmissionRepository $submission_repository
	 * @param NotificationRepository $notification_repository
	 * @return Submission[]
	 */
	private function postSubmissions(bool $already_submitted, array $player_ids, Challenge $challenge,
			Period $period, User $current_user, UserRepository $user_repository, SubmissionRepository $submission_repository,
			NotificationRepository $notification_repository,
			AdminNotificationRepository $admin_notification_repository): array {

		$submissions = [];

		if (!$already_submitted) {

			$players = [];
			foreach ($player_ids as $player_id) {
				$user = $user_repository->find($player_id);

				$players[] = $user->getUsername();

				$new_submission = new Submission();
				$new_submission->setUser($user);
				$new_submission->setChallenge($challenge);
				$new_submission->setPeriod($period);
				$new_submission->setSubmissionDate(new DateTime());

				$submission_repository->save($new_submission, true);
				$submissions[] = $new_submission;

				// Add notification for each user
				$challenge_url = $this->generateUrl('app_member_single_challenge', ['id' => $challenge->getId()]);
				$message = "Votre demande de validation du défi <a href='$challenge_url'>" . $challenge->getName() .
						"</a> a été postée avec succès";
				$notification = new Notification();
				$notification->setUser($user);
				$notification->setMessage($message);
				$notification_repository->save($notification, true);
			}

			// Add notification for admins
			$players_string = implode(', ', $players);
			$message = 'Le défi ' . $challenge->getName() . ' pour le jeu ' . $challenge->getGame()->getName() .
					' a été soumis à validation par les joueurs suivants : ' . $players_string;
			$notification = new AdminNotification();
			$notification->setMessage($message);
			$notification->setDate(new DateTime());
			$admin_notification_repository->save($notification, true);
		}
		else { // if already submitted
			$submissions[] = $submission_repository->findOneBy(['user'   => $current_user, 'challenge' => $challenge,
			                                                    'period' => $period]);
		}

		return $submissions;
	}


	/**
	 * @param SubmissionMessage $new_message
	 * @param Submission[] $submissions
	 * @param User $current_user
	 * @param FormInterface $form
	 * @param SubmissionMessageRepository $message_repository
	 * @param ImageService $image_service
	 * @param SubmissionMessageImageRepository $image_repository
	 * @return void
	 */
	private function postSubmissionMessages(SubmissionMessage $new_message, array $submissions, User $current_user,
			FormInterface $form, SubmissionMessageRepository $message_repository, ImageService $image_service,
			SubmissionMessageImageRepository $image_repository): void {

		$image_names = [];
		foreach ($submissions as $key => $submission) {
			if ($key !== 0) {
				$new_message = new SubmissionMessage();
				$new_message->setMessage($form->get('message')->getData());
			}

			$new_message->setUser($current_user);
			$new_message->setSubmission($submission);
			$new_message->setMessageDate(new DateTime());

			$message_repository->save($new_message, true);

			// Manage images attachments
			if ($key === 0) {
				$images = $form->get('images')->getData();
				$image_names = $image_service->uploadSubmissionMessageImages($images, $new_message, $image_repository);
			}
			else {
				foreach ($image_names as $image_name) {
					$new_image = new SubmissionMessageImage();
					$new_image->setImage($image_name);
					$new_image->setSubmissionMessage($new_message);
					$new_message->addImage($new_image);

					$image_repository->save($new_image, true);
				}
			}
		}
	}

}
