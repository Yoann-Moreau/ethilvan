<?php

namespace App\Service;

use App\Entity\SubmissionMessage;
use App\Entity\SubmissionMessageImage;
use App\Repository\SubmissionMessageImageRepository;
use GdImage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService {

	private ParameterBagInterface $params;


	public function __construct(ParameterBagInterface $params) {
		$this->params = $params;
	}


	public function uploadSubmissionMessageImages(array $images, SubmissionMessage $new_message,
			SubmissionMessageImageRepository $image_repository): array {

		$image_names = [];

		if (!empty($images)) {
			$directory = $this->params->get('submission_images_directory');

			foreach ($images as $image) {
				$new_image = new SubmissionMessageImage();
				$image_name = uniqid() . '.';
				$ext = $this->compressImageAsJpeg($image, 512000, $directory . '/' . $image_name);
				$new_image->setImage($image_name . $ext);
				$new_image->setSubmissionMessage($new_message);

				$image_names[] = $image_name . $ext;

				$new_message->addImage($new_image);

				$image_repository->save($new_image, true);
			}
		}

		return $image_names;
	}


	public function compressImageAsJpeg(UploadedFile $file, int $size, string $dest, int $quality = 75): string {
		$mime_type = $file->getMimeType();
		$ext = $file->guessExtension();

		if (filesize($file) > $size) {

			if ($mime_type === 'image/jpeg') {
				$image = imagecreatefromjpeg($file);
			}
			else {
				$image = imagecreatefrompng($file);
			}

			$complete_dest = $dest . 'jpeg';
			imagejpeg($image, $complete_dest, $quality);
			unlink($file);
			while (filesize($complete_dest) > $size) {
				$quality--;
				$image = imagecreatefromjpeg($complete_dest);
				imagejpeg($image, $complete_dest, $quality);

				if ($quality <= 1) { // Exit loop if quality is minimum
					break;
				}
			}
			return 'jpeg';
		}
		else {
			$file->move($dest . $ext);
			return $ext;
		}
	}

}
