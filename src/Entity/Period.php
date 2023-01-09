<?php

namespace App\Entity;

use App\Repository\PeriodRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PeriodRepository::class)]
class Period {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 40)]
	private ?string $type = null;

	#[ORM\Column(length: 120)]
	private ?string $name = null;

	#[ORM\Column]
	private ?int $year = null;

	#[ORM\Column(type: Types::DATE_MUTABLE)]
	private ?DateTimeInterface $start_date = null;

	#[ORM\Column(type: Types::DATE_MUTABLE)]
	private ?DateTimeInterface $end_date = null;

	#[ORM\ManyToMany(targetEntity: Challenge::class, mappedBy: 'periods')]
	private Collection $challenges;

	#[ORM\OneToMany(mappedBy: 'period', targetEntity: Submission::class)]
	private Collection $submissions;

	#[ORM\OneToOne(mappedBy: 'period', cascade: ['persist', 'remove'])]
	private ?Ranking $ranking = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $banner = null;

	private array $rankings = [];


	public function __construct() {
		$this->challenges = new ArrayCollection();
		$this->submissions = new ArrayCollection();
	}


	public function __toString() {
		return $this->getName();
	}


	// ==========================================================================
	// Getters and setters
	// ==========================================================================

	public function getId(): ?int {
		return $this->id;
	}


	public function getType(): ?string {
		return $this->type;
	}

	public function setType(string $type): self {
		$this->type = $type;

		return $this;
	}


	public function getName(): ?string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;

		return $this;
	}


	public function getYear(): ?int {
		return $this->year;
	}

	public function setYear(int $year): self {
		$this->year = $year;

		return $this;
	}


	public function getStartDate(): ?DateTimeInterface {
		return $this->start_date;
	}

	public function setStartDate(DateTimeInterface $start_date): self {
		$this->start_date = $start_date;

		return $this;
	}


	public function getEndDate(): ?DateTimeInterface {
		return $this->end_date;
	}

	public function setEndDate(DateTimeInterface $end_date): self {
		$this->end_date = $end_date;

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
			$challenge->addPeriod($this);
		}

		return $this;
	}

	public function removeChallenge(Challenge $challenge): self {
		if ($this->challenges->removeElement($challenge)) {
			$challenge->removePeriod($this);
		}

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
			$submission->setPeriod($this);
		}

		return $this;
	}

	public function removeSubmission(Submission $submission): self {
		if ($this->submissions->removeElement($submission)) {
			// set the owning side to null (unless already changed)
			if ($submission->getPeriod() === $this) {
				$submission->setPeriod(null);
			}
		}

		return $this;
	}


	public function getRanking(): ?Ranking {
		return $this->ranking;
	}

	public function setRanking(Ranking $ranking): self {
		// set the owning side of the relation if necessary
		if ($ranking->getPeriod() !== $this) {
			$ranking->setPeriod($this);
		}

		$this->ranking = $ranking;

		return $this;
	}


	public function getBanner(): ?string {
		return $this->banner;
	}

	public function setBanner(?string $banner): self {
		$this->banner = $banner;

		return $this;
	}


	/**
	 * @return array
	 */
	public function getRankings(): array {
		return $this->rankings;
	}

	/**
	 * @param array $rankings
	 */
	public function setRankings(array $rankings): void {
		$this->rankings = $rankings;
	}


	// ==========================================================================
	// Ohter methods
	// ==========================================================================

	public function calculateRealTimeRankings(): void {
		$rankings = [];

		foreach ($this->getSubmissions() as $submission) {
			if ($submission->isValid()) {
				$player = $submission->getUser();
				$points = $submission->getChallenge()->getDifficulty()->getPoints();

				if (array_key_exists($player->getUsername(), $rankings)) {
					$rankings[$player->getUsername()]['points'] = $rankings[$player->getUsername()]['points'] + $points;
				}
				else {
					$rankings[$player->getUsername()] = [
							'player' => $player,
							'points' => $points,
					];
				}
			}
		}

		$this->setRankings($rankings);
	}

}
