<?php

namespace App\Entity;

use App\Repository\SubmissionMessageRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
	private ?DateTimeInterface $message_date = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $message = null;

	#[ORM\OneToMany(mappedBy: 'submission_message', targetEntity: SubmissionMessageImage::class)]
	private Collection $images;


	public function __construct() {
		$this->images = new ArrayCollection();
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


	public function getSubmission(): ?Submission {
		return $this->submission;
	}

	public function setSubmission(?Submission $submission): self {
		$this->submission = $submission;

		return $this;
	}


	public function getMessageDate(): ?DateTimeInterface {
		return $this->message_date;
	}

	public function setMessageDate(DateTimeInterface $message_date): self {
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


	/**
	 * @return Collection<int, SubmissionMessageImage>
	 */
	public function getImages(): Collection {
		return $this->images;
	}

	public function addImage(SubmissionMessageImage $image): self {
		if (!$this->images->contains($image)) {
			$this->images->add($image);
			$image->setSubmissionMessage($this);
		}

		return $this;
	}

	public function removeImage(SubmissionMessageImage $image): self {
		if ($this->images->removeElement($image)) {
			// set the owning side to null (unless already changed)
			if ($image->getSubmissionMessage() === $this) {
				$image->setSubmissionMessage(null);
			}
		}

		return $this;
	}

}
