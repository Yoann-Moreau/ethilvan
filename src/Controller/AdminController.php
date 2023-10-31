<?php


namespace App\Controller;


use App\Repository\AdminNotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin')]
class AdminController extends AbstractController {

	#[Route('/', name: 'app_admin')]
	public function index(): Response {
		return $this->render('admin/index.html.twig', [
				'controller_name' => 'AdminController',
		]);
	}

	#[Route('/notifications', name: 'app_admin_notifications', methods: ['GET', 'POST'])]
	public function notifications(Request $request, AdminNotificationRepository $repository,
			EntityManagerInterface $entity_manager): Response {

		$notifications = $repository->findBy(
				[],
				['seen' => 'ASC', 'id' => 'DESC'],
				50
		);

		if ($request->isMethod('POST')) {
			foreach ($notifications as $notification) {
				if (!$notification->isSeen()) {
					$notification->setSeen(true);
					$repository->save($notification);
				}
			}
			$entity_manager->flush();
		}

		return $this->render('admin/notifications.html.twig', [
				'notifications' => $notifications,
		]);
	}

}
