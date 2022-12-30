<?php

namespace App\Entity;

use App\Repository\RankingPositionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingPositionRepository::class)]
class RankingPosition {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'ranking_positions')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Ranking $ranking = null;

	#[ORM\ManyToOne(inversedBy: 'ranking_positions')]
	#[ORM\JoinColumn(nullable: false)]
	private ?User $user = null;

	#[ORM\Column]
	private ?int $position = null;

	#[ORM\Column]
	private ?int $points = null;


	public function getId(): ?int {
		return $this->id;
	}


	public function getRanking(): ?Ranking {
		return $this->ranking;
	}

	public function setRanking(?Ranking $ranking): self {
		$this->ranking = $ranking;

		return $this;
	}


	public function getUser(): ?User {
		return $this->user;
	}

	public function setUser(?User $user): self {
		$this->user = $user;

		return $this;
	}


	public function getPosition(): ?int {
		return $this->position;
	}

	public function setPosition(int $position): self {
		$this->position = $position;

		return $this;
	}


	public function getPoints(): ?int {
		return $this->points;
	}

	public function setPoints(int $points): self {
		$this->points = $points;

		return $this;
	}

}
