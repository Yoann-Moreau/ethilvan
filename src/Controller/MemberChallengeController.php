<?php

namespace App\Controller;

use App\Entity\Challenge;
use App\Entity\Submission;
use App\Entity\SubmissionMessage;
use App\Entity\SubmissionMessageImage;
use App\Form\SubmissionMessageType;
use App\Repository\ChallengeRepository;
use App\Repository\PeriodRepository;
use App\Repository\SubmissionMessageImageRepository;
use App\Repository\SubmissionMessageRepository;
use App\Repository\SubmissionRepository;
use App\Repository\UserRepository;
use App\Service\ImageService;
use App\Service\PaginationService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
			ValidatorInterface $validator, ImageService $image_service): Response {

		$new_message = new SubmissionMessage();
		$form = $this->createForm(SubmissionMessageType::class, $new_message);
		$form->handleRequest($request);

		$current_user = $user_repository->find($this->getUser()->getId());
		$form_errors = [];

		// Check if one the periods is current (to display form)
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

		if ($form->isSubmitted() && $form->isValid() && $is_current && isset($period) && !$is_valid) {

			if (!$already_submitted) {
				$submission = new Submission();
				$submission->setUser($current_user);
				$submission->setChallenge($challenge);
				$submission->setPeriod($period);
				$submission->setSubmissionDate(new DateTime());

				$submission_repository->save($submission, true);
			}
			else {
				$submission = $submission_repository->findOneBy(['user'   => $current_user, 'challenge' => $challenge,
				                                                 'period' => $period]);
			}

			$new_message->setUser($current_user);
			$new_message->setSubmission($submission);
			$new_message->setMessageDate(new DateTime());

			$message_repository->save($new_message, true);

			// Manage images attachments
			$images = $form->get('images')->getData();
			$image_service->uploadSubmissionMessageImages($images, $new_message, $image_repository);
		}
		elseif ($form->isSubmitted() && !$form->isValid()) {
			$form_errors = $validator->validate($form);
		}

		return $this->render('member/challenge/challenge.html.twig', [
				'challenge'   => $challenge,
				'form'        => $form->createView(),
				'is_current'  => $is_current,
				'is_valid'    => $is_valid,
				'messages'    => $messages,
				'form_errors' => $form_errors,
		]);
	}

}
