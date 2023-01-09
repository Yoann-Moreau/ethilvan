<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Submission;
use App\Entity\SubmissionMessage;
use App\Form\SubmissionMessageType;
use App\Repository\NotificationRepository;
use App\Repository\SubmissionMessageImageRepository;
use App\Repository\SubmissionMessageRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use App\Service\ImageService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/admin/submission')]
class AdminSubmissionController extends AbstractController {

	#[Route('/', name: 'app_admin_submission_index', methods: ['GET'])]
	public function index(Request $request, SubmissionRepository $submission_repository): Response {

		$valid = boolval($request->query->get('valid'));

		if ($valid) {
			$submissions = $submission_repository->findBy(['valid' => true], ['validation_date' => 'DESC']);
		}
		else {
			$submissions = $submission_repository->findBy(['valid' => false], ['submission_date' => 'ASC']);
		}


		return $this->render('admin_submission/index.html.twig', [
				'submissions' => $submissions,
		]);
	}


	#[Route('/{id}', name: 'app_admin_submission', methods: ['GET', 'POST'])]
	public function submission(Request $request, Submission $submission, ValidatorInterface $validator,
			UserRepository $user_repository, SubmissionMessageRepository $message_repository,
			SubmissionMessageImageRepository $image_repository, ImageService $image_service,
			NotificationRepository $notification_repository): Response {

		$new_message = new SubmissionMessage();
		$form = $this->createForm(SubmissionMessageType::class, $new_message);
		$form->handleRequest($request);

		$form_errors = [];

		if ($form->isSubmitted() && $form->isValid()) {
			$current_user = $user_repository->find($this->getUser()->getId());

			$new_message->setSubmission($submission);
			$new_message->setUser($current_user);
			$new_message->setMessageDate(new DateTime());

			$message_repository->save($new_message, true);

			// Notify user
			$current_user_link = $this->generateUrl('app_member_user', ['id' => $current_user->getId()]);
			$challenge_link = $this->generateUrl('app_member_single_challenge',
					['id' => $submission->getChallenge()->getId()]);
			$message = "<a href='$current_user_link'>" . $current_user->getUsername() . '</a> a répondu à votre demande de ' .
					"validation du défi <a href='$challenge_link'>" . $submission->getChallenge()->getName() . '</a>';
			$notification = new Notification();
			$notification->setUser($submission->getUser());
			$notification->setMessage($message);
			$notification_repository->save($notification, true);

			// Manage images attachments
			$images = $form->get('images')->getData();
			$image_service->uploadSubmissionMessageImages($images, $new_message, $image_repository);
		}
		elseif ($form->isSubmitted() && !$form->isValid()) {
			$form_errors = $validator->validate($form);
		}

		return $this->render('admin_submission/submission.html.twig', [
				'submission'  => $submission,
				'form'        => $form->createView(),
				'form_errors' => $form_errors,
		]);
	}


	#[Route('/{id}/validate', name: 'app_admin_submission_validate', methods: ['POST'])]
	public function validate(Submission $submission, Request $request,
			SubmissionRepository $submission_repository, NotificationRepository $notification_repository): Response {

		if ($this->isCsrfTokenValid('validate' . $submission->getId(), $request->request->get('_token'))) {
			$submission->setValid(true);
			$submission->setValidationDate(new DateTime());
			$submission_repository->save($submission, true);

			$challenge_url = $this->generateUrl('app_member_single_challenge',
					['id' => $submission->getChallenge()->getId()]);
			$game_url = $this->generateUrl('app_member_single_game',
					['id' => $submission->getChallenge()->getGame()->getId()]);

			$message = "Le défi <a href='$challenge_url'>" . $submission->getChallenge()->getName() .
					"</a> pour le jeu <a href='$game_url'>" .	$submission->getChallenge()->getGame()->getName() .
					'</a> a été validé pour la période ' . $submission->getPeriod()->getName();

			$notification = new Notification();
			$notification->setUser($submission->getUser());
			$notification->setMessage($message);
			$notification_repository->save($notification, true);
		}

		return $this->redirectToRoute('app_admin_submission_index', [], Response::HTTP_SEE_OTHER);
	}


	#[Route('/{id}/refuse', name: 'app_admin_submission_refuse', methods: ['POST'])]
	public function refuse(Submission $submission, Request $request, SubmissionRepository $submission_repository,
			NotificationRepository $notification_repository, SubmissionMessageRepository $message_repository,
			SubmissionMessageImageRepository $image_repository): Response {

		if ($this->isCsrfTokenValid('refuse' . $submission->getId(), $request->request->get('_token'))) {
			$image_directory = $this->getParameter('submission_images_directory');

			foreach ($submission->getSubmissionMessages() as $message) {
				// Delete images
				foreach ($message->getImages() as $image) {
					if (file_exists($image_directory . '/' . $image->getImage())) {
						unlink($image_directory . '/' . $image->getImage());
					}
					$image_repository->remove($image, true);
				}
				// Delete message
				$message_repository->remove($message, true);
			}
			// Delete submission
			$submission_repository->remove($submission, true);

			$challenge_url = $this->generateUrl('app_member_single_challenge',
					['id' => $submission->getChallenge()->getId()]);
			$game_url = $this->generateUrl('app_member_single_game',
					['id' => $submission->getChallenge()->getGame()->getId()]);

			$message = "La validation du défi <a href='$challenge_url'>" . $submission->getChallenge()->getName() .
					"</a> pour le jeu <a href='$game_url'>" .	$submission->getChallenge()->getGame()->getName() .
					'</a> a été refusée pour la période ' . $submission->getPeriod()->getName();

			$notification = new Notification();
			$notification->setUser($submission->getUser());
			$notification->setMessage($message);
			$notification_repository->save($notification, true);
		}

		return $this->redirectToRoute('app_admin_submission_index', [], Response::HTTP_SEE_OTHER);
	}

}
