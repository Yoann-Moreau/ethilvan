<?php

namespace App\Entity;

use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'notifications')]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $user = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $message = null;

	#[ORM\Column]
	private ?bool $seen = null;

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


	public function getMessage(): ?string {
		return $this->message;
	}

	public function setMessage(string $message): self {
		$this->message = $message;

		return $this;
	}


	public function isSeen(): ?bool {
		return $this->seen;
	}

	public function setSeen(bool $seen): self {
		$this->seen = $seen;

		return $this;
	}

}
