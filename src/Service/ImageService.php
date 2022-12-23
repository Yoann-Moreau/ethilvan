<?php

namespace App\Service;

use App\Entity\SubmissionMessage;
use App\Entity\SubmissionMessageImage;
use App\Repository\SubmissionMessageImageRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImageService {

	private $params;

	public function __construct(ParameterBagInterface $params) {
		$this->params = $params;
	}


	public function uploadSubmissionMessageImages(array $images, SubmissionMessage $new_message,
			SubmissionMessageImageRepository $image_repository): void {

		if (!empty($images)) {
			$directory = $this->params->get('submission_images_directory');

			foreach ($images as $image) {
				$new_image = new SubmissionMessageImage();
				$image_name = uniqid() . '.' . $image->guessExtension();
				$image->move($directory, $image_name);
				$new_image->setImage($image_name);
				$new_image->setSubmissionMessage($new_message);

				$image_repository->save($new_image, true);
			}
		}
	}

}
