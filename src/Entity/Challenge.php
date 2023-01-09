<?php

namespace App\Entity;

use App\Repository\ChallengeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

	#[ORM\ManyToOne(inversedBy: 'challenges')]
	#[ORM\JoinColumn(nullable: false)]
	private ?Game $game = null;

	#[ORM\ManyToMany(targetEntity: Period::class, inversedBy: 'challenges')]
	private Collection $periods;

	#[ORM\OneToMany(mappedBy: 'challenge', targetEntity: Submission::class)]
	private Collection $submissions;


	public function __construct() {
		$this->periods = new ArrayCollection();
		$this->submissions = new ArrayCollection();
	}


	// ==========================================================================
	// Getters and setters
	// ==========================================================================

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


	public function getGame(): ?Game {
		return $this->game;
	}

	public function setGame(?Game $game): self {
		$this->game = $game;

		return $this;
	}


	/**
	 * @return Collection<int, Period>
	 */
	public function getPeriods(): Collection {
		return $this->periods;
	}

	public function addPeriod(Period $period): self {
		if (!$this->periods->contains($period)) {
			$this->periods->add($period);
		}

		return $this;
	}

	public function removePeriod(Period $period): self {
		$this->periods->removeElement($period);

		return $this;
	}


	/**
	 * @return Collection<int, Submission>
	 */
	public function getSubmissions(): Collection {
		return $this->submissions;
	}

	public function addSubmission(Submission $submission): self {
		if (!$this->submissions->contains($submission)) {
			$this->submissions->add($submission);
			$submission->setChallenge($this);
		}

		return $this;
	}

	public function removeSubmission(Submission $submission): self {
		if ($this->submissions->removeElement($submission)) {
			// set the owning side to null (unless already changed)
			if ($submission->getChallenge() === $this) {
				$submission->setChallenge(null);
			}
		}

		return $this;
	}

	// ==========================================================================
	// Other methods
	// ==========================================================================

	public function isValidForUser(User $user): bool {

		foreach ($user->getSubmissions() as $submission) {
			if ($submission->getChallenge() === $this && $submission->isValid()) {
				return true;
			}
		}

		return false;
	}

}
