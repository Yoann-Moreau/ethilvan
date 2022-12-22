<?php

namespace App\Controller;

use App\Repository\ChallengeRepository;
use App\Service\PaginationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

}
