<?php

namespace App\Controller;

use App\Entity\Submission;
use App\Entity\SubmissionMessage;
use App\Form\SubmissionMessageType;
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
	public function index(SubmissionRepository $submission_repository): Response {

		$submissions = $submission_repository->findBy(['valid' => false], ['submission_date' => 'ASC']);

		return $this->render('admin_submission/index.html.twig', [
				'submissions' => $submissions,
		]);
	}


	#[Route('/{id}', name: 'app_admin_submission', methods: ['GET', 'POST'])]
	public function submission(Request $request, Submission $submission, ValidatorInterface $validator,
			UserRepository $user_repository, SubmissionMessageRepository $message_repository,
			SubmissionMessageImageRepository $image_repository, ImageService $image_service): Response {

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
			SubmissionRepository $submission_repository): Response {

		if ($this->isCsrfTokenValid('validate' . $submission->getId(), $request->request->get('_token'))) {
			$submission->setValid(true);
			$submission_repository->save($submission, true);
		}

		return $this->redirectToRoute('app_admin_submission_index', [], Response::HTTP_SEE_OTHER);
	}

}
