<?php


namespace App\Controller;


use App\Entity\Period;
use App\Entity\Ranking;
use App\Entity\RankingPosition;
use App\Repository\PeriodRepository;
use App\Repository\RankingPositionRepository;
use App\Repository\RankingRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/ranking')]
class AdminRankingController extends AbstractController {

	#[Route('/', name: 'app_admin_ranking_index', methods: ['GET'])]
	public function index(PeriodRepository $period_repository): Response {

		$periods = $period_repository->findBy([], ['end_date' => 'ASC']);

		return $this->render('admin_ranking/index.html.twig', [
				'periods' => $periods,
		]);
	}


	#[Route('/ranking/{id}', name: 'app_admin_ranking', methods: ['GET', 'POST'])]
	public function single_ranking(Period $period, Request $request, RankingRepository $ranking_repository,
			RankingPositionRepository $position_repository): Response {

		$ranking = $ranking_repository->findOneBy(['period' => $period]);

		$period->calculateRealTimeRankings();

		if ($request->isMethod('POST') &&
				$this->isCsrfTokenValid('update_ranking' . $period->getId(), $request->request->get('_token'))) {

			// Create ranking if it doesn't exist
			if ($ranking === null) {
				$ranking = new Ranking();
				$ranking->setPeriod($period);
				$ranking_repository->save($ranking, true);
			}

			// Clear current ranking positions
			$ranking_positions = $position_repository->findBy(['ranking' => $ranking], ['position' => 'ASC']);
			foreach ($ranking_positions as $ranking_position) {
				$position_repository->remove($ranking_position, true);
			}

			// Create new ranking positions
			$period_rankings = $period->getRankings();
			uasort($period_rankings, function ($a, $b) {
				return $b['points'] <=> $a['points'];
			});
			$position = 1;
			$player = 1;
			$previous_points = 0;
			foreach ($period_rankings as $period_ranking) {
				if ($period_ranking['points'] !== $previous_points) {
					$position = $player;
				}
				$ranking_position = new RankingPosition();
				$ranking_position->setRanking($ranking);
				$ranking_position->setPosition($position);
				$ranking_position->setUser($period_ranking['player']);
				$ranking_position->setPoints($period_ranking['points']);
				$position_repository->save($ranking_position, true);
				$ranking->addRankingPosition($ranking_position); // update ranking for display

				$player++;
				$previous_points = $period_ranking['points'];
			}
		}

		return $this->render('admin_ranking/single_ranking.html.twig', [
				'ranking' => $ranking,
				'period'  => $period,
		]);
	}

}
