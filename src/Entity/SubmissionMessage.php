<?php

namespace App\Entity;

use App\Repository\SubmissionMessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubmissionMessageRepository::class)]
class SubmissionMessage {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'submission_messages')]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $user = null;

	#[ORM\ManyToOne(inversedBy: 'submission_messages')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Submission $submission = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE)]
	private ?\DateTimeInterface $message_date = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $message = null;


	public function getId(): ?int {
		return $this->id;
	}


	public function getUser(): ?User {
		return $this->user;
	}

	public function setUser(?User $user): self {
		$this->user = $user;

		return $this;
	}


	public function getSubmission(): ?Submission {
		return $this->submission;
	}

	public function setSubmission(?Submission $submission): self {
		$this->submission = $submission;

		return $this;
	}


	public function getMessageDate(): ?\DateTimeInterface {
		return $this->message_date;
	}

	public function setMessageDate(\DateTimeInterface $message_date): self {
		$this->message_date = $message_date;

		return $this;
	}


	public function getMessage(): ?string {
		return $this->message;
	}

	public function setMessage(string $message): self {
		$this->message = $message;

		return $this;
	}

}
