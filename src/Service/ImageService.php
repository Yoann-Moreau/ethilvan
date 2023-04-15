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

		list($file_width, $file_height) = getimagesize($file);
		if ($file_width > 1920 || $file_height > 1080) {
			$this->resizeImage($file, 1920, 1080);
		}

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
			move_uploaded_file($file, $dest . $ext);
			return $ext;
		}
	}


	public function resizeImage(UploadedFile $file, int $target_width, int $target_height, bool $crop = false): void {
		$mime_type = $file->getMimeType();

		list($file_width, $file_height) = getimagesize($file);
		$ratio = $file_width / $file_height;

		if ($crop) {
			if ($file_width > $file_height) {
				$file_width = ceil($file_width - ($file_width * abs($ratio - $target_width / $target_height)));
			}
			else {
				$file_height = ceil($file_height - ($file_height * abs($ratio - $target_width / $target_height)));
			}
			$new_width = $target_width;
			$new_height = $target_height;
		}
		else {
			if ($target_width / $target_height > $ratio) {
				$new_width = $target_height * $ratio;
				$new_height = $target_height;
			}
			else {
				$new_width = $target_width;
				$new_height = $target_width / $ratio;
			}
		}

		if ($mime_type === 'image/jpeg') {
			$src = imagecreatefromjpeg($file);
			$destination = imagecreatetruecolor($new_width, $new_height);
			imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_width, $new_height, $file_width, $file_height);
			imagejpeg($destination, $file->getPathname());
		}
		else {
			$src = imagecreatefrompng($file);
			$destination = imagecreatetruecolor($new_width, $new_height);
			imagesavealpha($destination, true);
			$trans_color = imagecolorallocatealpha($destination, 0, 0, 0, 127);
			imagefill($destination, 0, 0, $trans_color);
			imagecopyresampled($destination, $src, 0, 0, 0, 0, $new_width, $new_height, $file_width, $file_height);
			imagepng($destination, $file->getPathname());
		}
	}

}
