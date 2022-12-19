<?php

namespace App\Entity;

use App\Repository\ChallengeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChallengeRepository::class)]
class Challenge {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 120)]
	private ?string $name = null;

	#[ORM\Column(type: Types::TEXT)]
	private ?string $description = null;

	#[ORM\Column]
	private ?int $number_of_players = null;

	#[ORM\ManyToOne(inversedBy: 'challenges')]
	#[ORM\JoinColumn(nullable: false)]
	private ?ChallengeDifficulty $difficulty = null;


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


	public function getDescription(): ?string {
		return $this->description;
	}

	public function setDescription(string $description): self {
		$this->description = $description;

		return $this;
	}


	public function getNumberOfPlayers(): ?int {
		return $this->number_of_players;
	}

	public function setNumberOfPlayers(int $number_of_players): self {
		$this->number_of_players = $number_of_players;

		return $this;
	}


	public function getDifficulty(): ?ChallengeDifficulty {
		return $this->difficulty;
	}

	public function setDifficulty(?ChallengeDifficulty $difficulty): self {
		$this->difficulty = $difficulty;

		return $this;
	}

}
