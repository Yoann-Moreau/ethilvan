<?php

namespace App\Entity;

use App\Repository\CupRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CupRepository::class)]
class Cup {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 20)]
	private ?string $name = null;

	#[ORM\Column]
	private ?int $position = null;

	#[ORM\Column(length: 120)]
	private ?string $icon = null;


	public function getId(): ?int {
		return $this->id;
	}


	public function getName(): ?string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;

		return $this;
	}


	public function getPosition(): ?int {
		return $this->position;
	}

	public function setPosition(int $position): self {
		$this->position = $position;

		return $this;
	}


	public function getIcon(): ?string {
		return $this->icon;
	}

	public function setIcon(string $icon): self {
		$this->icon = $icon;

		return $this;
	}

}
