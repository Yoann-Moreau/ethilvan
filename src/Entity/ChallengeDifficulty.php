<?php

namespace App\Entity;

use App\Repository\ChallengeDifficultyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChallengeDifficultyRepository::class)]
class ChallengeDifficulty {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 40)]
	private ?string $name = null;

	#[ORM\Column]
	private ?int $points = null;

	#[ORM\Column(length: 255)]
	private ?string $icon = null;

	#[ORM\OneToMany(mappedBy: 'difficulty', targetEntity: Challenge::class)]
	private Collection $challenges;


	public function __construct() {
		$this->challenges = new ArrayCollection();
	}


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


	public function getPoints(): ?int {
		return $this->points;
	}

	public function setPoints(int $points): self {
		$this->points = $points;

		return $this;
	}


	public function getIcon(): ?string {
		return $this->icon;
	}

	public function setIcon(string $icon): self {
		$this->icon = $icon;

		return $this;
	}


	/**
	 * @return Collection<int, Challenge>
	 */
	public function getChallenges(): Collection {
		return $this->challenges;
	}

	public function addChallenge(Challenge $challenge): self {
		if (!$this->challenges->contains($challenge)) {
			$this->challenges->add($challenge);
			$challenge->setDifficulty($this);
		}

		return $this;
	}

	public function removeChallenge(Challenge $challenge): self {
		if ($this->challenges->removeElement($challenge)) {
			// set the owning side to null (unless already changed)
			if ($challenge->getDifficulty() === $this) {
				$challenge->setDifficulty(null);
			}
		}

		return $this;
	}

}
