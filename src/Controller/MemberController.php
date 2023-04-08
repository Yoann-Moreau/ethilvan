<?php


namespace App\Controller;


use App\Entity\Period;
use App\Form\ChangeEmailType;
use App\Form\ChangePasswordType;
use App\Form\ProfileEditType;
use App\Repository\ChallengeDifficultyRepository;
use App\Repository\CupRepository;
use App\Repository\NotificationRepository;
use App\Repository\PeriodRepository;
use App\Repository\RankingPositionRepository;
use App\Repository\SubmissionRepository;
use App\Repository\TextRepository;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use App\Service\ToolsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


#[Route('/member')]
class MemberController extends AbstractController {

	#[Route('/', name: 'app_member', methods: ['GET'])]
	public function index(UserRepository $user_repository, SubmissionRepository $submission_repository,
			PeriodRepository $period_repository): Response {

		$current_user = $user_repository->find($this->getUser()->getId());

		$current_periods = $period_repository->findCurrentPeriods();
		$current_year = null;
		$current_event = null;
		foreach ($current_periods as $period) {
			if ($period->getType() === 'year') {
				$current_year = $period;
				$current_year->calculateRealTimeRankings();
			}
			elseif ($period->getType() === 'event') {
				$current_event = $period;
				$current_event->calculateRealTimeRankings();
			}
		}

		$last_own_submissions = $submission_repository->findBy(
				['user' => $current_user, 'valid' => true, 'period' => $current_year],
				['validation_date' => 'DESC'],
				3
		);
		$last_submissions = $submission_repository->findBy(
				['valid' => true, 'period' => $current_year],
				['validation_date' => 'DESC'],
				5
		);

		return $this->render('member/index.html.twig', [
				'last_own_submissions' => $last_own_submissions,
				'last_submissions'     => $last_submissions,
				'current_year'         => $current_year,
				'current_event'        => $current_event,
		]);
	}


	#[Route('/profile', name: 'app_member_profile', methods: ['GET'])]
	public function profile(UserRepository $user_repository, SubmissionRepository $submission_repository,
			ChallengeDifficultyRepository $difficulty_repository, CupRepository $cup_repository,
			RankingPositionRepository $position_repository): Response {

		$user = $user_repository->find($this->getUser()->getId());
		$user->countChallengesByDifficulty();
		$user->countRankingPositions();

		$difficulties = $difficulty_repository->findAll();
		$cups = $cup_repository->findBy([], ['position' => 'DESC']);

		$last_submissions = $submission_repository->findBy(['valid' => true, 'user' => $user],
				['validation_date' => 'DESC'], 3);
		$last_ranking_positions = $position_repository->findBy(['user' => $user], ['id' => 'DESC'], 3);

		return $this->render('member/profile.html.twig', [
				'user'                   => $user,
				'last_submissions'       => $last_submissions,
				'last_ranking_positions' => $last_ranking_positions,
				'difficulties'           => $difficulties,
				'cups'                   => $cups,
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
			SubmissionRepository $submission_repository, ChallengeDifficultyRepository $difficulty_repository,
			CupRepository $cup_repository, RankingPositionRepository $position_repository): Response {

		$user = $user_repository->find($id);

		if ($user === null || $user->isDeleted()) {
			throw $this->createNotFoundException("L'utilisateur n'existe pas");
		}

		$user->countChallengesByDifficulty();
		$user->countRankingPositions();

		$difficulties = $difficulty_repository->findAll();
		$cups = $cup_repository->findBy([], ['position' => 'DESC']);

		$last_submissions = $submission_repository->findBy(['valid' => true, 'user' => $user],
				['validation_date' => 'DESC'], 3);
		$last_ranking_positions = $position_repository->findBy(['user' => $user], ['id' => 'DESC'], 3);

		return $this->render('member/user.html.twig', [
				'user'                   => $user,
				'last_submissions'       => $last_submissions,
				'last_ranking_positions' => $last_ranking_positions,
				'difficulties'           => $difficulties,
				'cups'                   => $cups,
		]);
	}


	#[Route('/user/{id}/challenges', name: 'app_member_user_challenges', methods: ['GET'])]
	public function user_challenges(int $id, Request $request, UserRepository $user_repository,
			SubmissionRepository $submission_repository, PaginationService $pagination_service) {

		$user = $user_repository->find($id);

		if ($user === null || $user->isDeleted()) {
			throw $this->createNotFoundException("L'utilisateur n'existe pas");
		}

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

		$submissions = $submission_repository->search($user, $search, $elements_per_page, $offset, $sort_by);

		// Pagination
		$number_of_elements = $submission_repository->countWithSearch($user, $search);
		$pages = $pagination_service->getPages($number_of_elements, $elements_per_page, $page);

		return $this->render('member/user_challenges.html.twig', [
				'user'        => $user,
				'sort_by'    => $sort_by,
				'page'       => $page,
				'pages'      => $pages,
				'search'     => $search,
				'submissions' => $submissions,
		]);
	}


	#[Route('/notifications', name: 'app_member_notifications', methods: ['GET', 'POST'])]
	public function notifications(Request $request, UserRepository $user_repository,
			EntityManagerInterface $entity_manager, NotificationRepository $notification_repository): Response {

		$current_user = $user_repository->find($this->getUser()->getId());
		$notifications = $notification_repository->findBy(['user' => $current_user], ['seen' => 'ASC', 'id' => 'DESC']);

		if ($request->isMethod('POST')) {
			foreach ($notifications as $notification) {
				if (!$notification->isSeen()) {
					$notification->setSeen(true);
					$notification_repository->save($notification);
				}
			}
			$entity_manager->flush();
		}

		return $this->render('member/notifications.html.twig', [
				'notifications' => $notifications,
		]);
	}


	#[Route('/valid_submissions/{id}', name: 'app_member_valid_submissions', methods: ['GET'])]
	public function validSubmissions(Period $period, SubmissionRepository $submission_repository): Response {

		$submissions = $submission_repository->findBy(['valid' => true, 'period' => $period],
				['validation_date' => 'DESC']);

		return $this->render('member/valid_submissions.html.twig', [
				'period'      => $period,
				'submissions' => $submissions,
		]);
	}


	#[Route('/real_time_final_ranking', name: 'app_member_real_time_final_ranking', methods: ['GET'])]
	public function realTimeFinalRanking(PeriodRepository $period_repository, TextRepository $text_repository,
			ToolsService $tools_service): Response {

		$current_year = (int)date("Y");
		$periods = $period_repository->findBy(['year' => $current_year], ['start_date' => 'DESC']);
		$texts = $text_repository->findBy(['page' => 'real_time_final_ranking'], ['text_order' => 'ASC']);
		$final_rankings = [];

		foreach ($periods as $period) {
			// Finished period with linked ranking
			if ($period->getRanking() !== null) {
				foreach ($period->getRanking()->getRankingPositions() as $ranking_position) {
					$points = $tools_service->getPositionPointsForPeriod($period, $ranking_position->getPosition());
					if (array_key_exists($ranking_position->getUser()->getUsername(), $final_rankings)) {
						$final_rankings[$ranking_position->getUser()->getUsername()]['points'] += $points;
					}
					else {
						$final_rankings[$ranking_position->getUser()->getUsername()]['points'] = $points;
						$final_rankings[$ranking_position->getUser()->getUsername()]['player'] = $ranking_position->getUser();
					}
				}
			}
			// Ongoing period
			else {
				$period->calculateRealTimeRankings();
				foreach ($period->getRankings() as $ranking) {
					$points = $tools_service->getPositionPointsForPeriod($period, $ranking['position']);
					if (array_key_exists($ranking['player']->getUsername(), $final_rankings)) {
						$final_rankings[$ranking['player']->getUsername()]['points'] += $points;
					}
					else {
						$final_rankings[$ranking['player']->getUsername()]['points'] = $points;
						$final_rankings[$ranking['player']->getUsername()]['player'] = $ranking['player'];
					}
				}
			}
		}

		uasort($final_rankings, function ($a, $b) {
			return $b['points'] <=> $a['points'];
		});

		return $this->render('member/real_time_final_ranking.html.twig', [
				'periods'        => $periods,
				'final_rankings' => $final_rankings,
				'texts'          => $texts,
		]);
	}

}
