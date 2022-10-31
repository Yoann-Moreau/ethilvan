<?php

namespace App\Entity;

use App\Repository\SteamGameRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SteamGameRepository::class)]
class SteamGame {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column]
	private ?int $app_id = null;

	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $header_image = null;


	public function getId(): ?int {
		return $this->id;
	}


	public function getAppId(): ?int {
		return $this->app_id;
	}

	public function setAppId(int $app_id): self {
		$this->app_id = $app_id;

		return $this;
	}


	public function getName(): ?string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;

		return $this;
	}


	public function getHeaderImage(): ?string {
		return $this->header_image;
	}

	public function setHeaderImage(?string $header_image): self {
		$this->header_image = $header_image;

		return $this;
	}

}
