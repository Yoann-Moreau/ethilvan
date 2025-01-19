<?php

namespace App\Controller;

use App\Repository\TextRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/member/adventure')]
class MemberAdventureController extends AbstractController {

	#[Route('/', name: 'app_member_adventure')]
	public function adventure(TextRepository $text_repository): Response {

		$texts = $text_repository->findBy(['page' => 'adventure'], ['text_order' => 'ASC']);

		return $this->render('member/adventure/adventure.html.twig', [
				'texts' => $texts,
		]);
	}
}
