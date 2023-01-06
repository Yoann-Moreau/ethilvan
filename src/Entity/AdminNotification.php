<?php

namespace App\Entity;

use App\Repository\AdminNotificationRepository;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminNotificationRepository::class)]
class AdminNotification {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $message = null;

	#[ORM\Column]
	private ?bool $seen = null;

	#[ORM\Column(type: Types::DATETIME_MUTABLE)]
	private ?DateTimeInterface $date = null;


	public function getId(): ?int {
		return $this->id;
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


	public function getDate(): ?DateTimeInterface {
		return $this->date;
	}

	public function setDate(DateTimeInterface $date): self {
		$this->date = $date;

		return $this;
	}
}
