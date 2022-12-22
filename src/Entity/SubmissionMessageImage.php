<?php

namespace App\Entity;

use App\Repository\SubmissionMessageImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubmissionMessageImageRepository::class)]
class SubmissionMessageImage {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'images')]
	#[ORM\JoinColumn(nullable: false)]
	private ?SubmissionMessage $submission_message = null;

	#[ORM\Column(length: 120)]
	private ?string $image = null;


	public function getId(): ?int {
		return $this->id;
	}


	public function getSubmissionMessage(): ?SubmissionMessage {
		return $this->submission_message;
	}

	public function setSubmissionMessage(?SubmissionMessage $submission_message): self {
		$this->submission_message = $submission_message;

		return $this;
	}


	public function getImage(): ?string {
		return $this->image;
	}

	public function setImage(string $image): self {
		$this->image = $image;

		return $this;
	}

}
