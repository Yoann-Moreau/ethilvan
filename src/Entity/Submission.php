<?php

namespace App\Entity;

use App\Repository\SubmissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubmissionRepository::class)]
class Submission {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'submissions')]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $user = null;

	#[ORM\ManyToOne(inversedBy: 'submissions')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Challenge $challenge = null;

	#[ORM\ManyToOne(inversedBy: 'submissions')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Period $period = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE)]
	private ?\DateTimeInterface $submission_date = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE)]
	private ?\DateTimeInterface $validation_date = null;

	#[ORM\Column]
	private ?bool $valid = null;

	#[ORM\OneToMany(mappedBy: 'submission', targetEntity: SubmissionMessage::class)]
	private Collection $submission_messages;


	public function __construct() {
		$this->submission_messages = new ArrayCollection();
	}


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


	public function getChallenge(): ?Challenge {
		return $this->challenge;
	}

	public function setChallenge(?Challenge $challenge): self {
		$this->challenge = $challenge;

		return $this;
	}


	public function getPeriod(): ?Period {
		return $this->period;
	}

	public function setPeriod(?Period $period): self {
		$this->period = $period;

		return $this;
	}


	public function getSubmissionDate(): ?\DateTimeInterface {
		return $this->submission_date;
	}

	public function setSubmissionDate(\DateTimeInterface $submission_date): self {
		$this->submission_date = $submission_date;

		return $this;
	}


	public function getValidationDate(): ?\DateTimeInterface {
		return $this->validation_date;
	}

	public function setValidationDate(\DateTimeInterface $validation_date): self {
		$this->validation_date = $validation_date;

		return $this;
	}


	public function isValid(): ?bool {
		return $this->valid;
	}

	public function setValid(bool $valid): self {
		$this->valid = $valid;

		return $this;
	}


	/**
	 * @return Collection<int, SubmissionMessage>
	 */
	public function getSubmissionMessages(): Collection {
		return $this->submission_messages;
	}

	public function addSubmissionMessage(SubmissionMessage $submissionMessage): self {
		if (!$this->submission_messages->contains($submissionMessage)) {
			$this->submission_messages->add($submissionMessage);
			$submissionMessage->setSubmission($this);
		}

		return $this;
	}

	public function removeSubmissionMessage(SubmissionMessage $submissionMessage): self {
		if ($this->submission_messages->removeElement($submissionMessage)) {
			// set the owning side to null (unless already changed)
			if ($submissionMessage->getSubmission() === $this) {
				$submissionMessage->setSubmission(null);
			}
		}

		return $this;
	}

}
