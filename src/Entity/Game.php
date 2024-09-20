<?php


namespace App\Entity;


use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[ORM\Column(length: 255)]
	private ?string $image = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $link = null;

	#[ORM\Column(nullable: true)]
	private ?int $steam_id = null;

	#[ORM\Column]
	private ?bool $current = null;

	#[ORM\Column]
	private ?bool $multi = null;

	#[ORM\Column]
	private ?bool $played = null;

	#[ORM\OneToMany(mappedBy: 'game', targetEntity: Challenge::class)]
	private Collection $challenges;

	private array $challenges_counts = [];

	private int $valid_submissions_count = 0;

	private array $periodsWithValidSubmissions = [];


	public function __construct() {
		$this->challenges = new ArrayCollection();
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


	public function getImage(): ?string {
		return $this->image;
	}

	public function setImage(string $image): self {
		$this->image = $image;

		return $this;
	}


	public function getLink(): ?string {
		return $this->link;
	}

	public function setLink(?string $link): self {
		$this->link = $link;

		return $this;
	}


	public function getSteamId(): ?int {
		return $this->steam_id;
	}

	public function setSteamId(?int $steam_id): self {
		$this->steam_id = $steam_id;

		return $this;
	}


	public function isCurrent(): ?bool {
		return $this->current;
	}

	public function setCurrent(bool $current): self {
		$this->current = $current;

		return $this;
	}


	public function isMulti(): ?bool {
		return $this->multi;
	}

	public function setMulti(bool $multi): self {
		$this->multi = $multi;

		return $this;
	}


	public function isPlayed(): ?bool {
		return $this->played;
	}

	public function setPlayed(bool $played): self {
		$this->played = $played;

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
			$challenge->setGame($this);
		}

		return $this;
	}

	public function removeChallenge(Challenge $challenge): self {
		if ($this->challenges->removeElement($challenge)) {
			// set the owning side to null (unless already changed)
			if ($challenge->getGame() === $this) {
				$challenge->setGame(null);
			}
		}

		return $this;
	}


	/**
	 * @return array
	 */
	public function getChallengesCounts(): array {
		return $this->challenges_counts;
	}

	/**
	 * @param array $challenges_counts
	 */
	public function setChallengesCounts(array $challenges_counts): void {
		$this->challenges_counts = $challenges_counts;
	}


	/**
	 * @return int
	 */
	public function getValidSubmissionsCount(): int {
		return $this->valid_submissions_count;
	}

	/**
	 * @param int $valid_submissions_count
	 */
	public function setValidSubmissionsCount(int $valid_submissions_count): void {
		$this->valid_submissions_count = $valid_submissions_count;
	}


	/**
	 * @return array
	 */
	public function getPeriodsWithValidSubmissions(): array {
		return $this->periodsWithValidSubmissions;
	}

	/**
	 * @param array $periodsWithValidSubmissions
	 */
	public function setPeriodsWithValidSubmissions(array $periodsWithValidSubmissions): void {
		$this->periodsWithValidSubmissions = $periodsWithValidSubmissions;
	}


	// ==========================================================================
	// Other methods
	// ==========================================================================

	/**
	 * @param Period[] $periods
	 * @return void
	 */
	public function countChallenges(array $periods): void {
		$counts = ['Bronze' => 0, 'Argent' => 0, 'Or' => 0, 'Platine' => 0];

		foreach ($this->getChallenges() as $challenge) {
			foreach ($periods as $period) {
				if ($challenge->getPeriods()->contains($period)) {
					$counts[$challenge->getDifficulty()->getName()] = $counts[$challenge->getDifficulty()->getName()] + 1;
				}
			}
		}

		$this->setChallengesCounts($counts);
	}


	/**
	 * @param User $user
	 * @return void
	 */
	public function countValidChallengesForUser(User $user): void {
		$counts = ['Bronze' => 0, 'Argent' => 0, 'Or' => 0, 'Platine' => 0];

		foreach ($this->getChallenges() as $challenge) {
			foreach ($challenge->getSubmissions() as $submission) {
				if ($submission->getUser() === $user && $submission->isValid()) {
					$counts[$challenge->getDifficulty()->getName()] = $counts[$challenge->getDifficulty()->getName()] + 1;
					break;
				}
			}
		}

		$this->setChallengesCounts($counts);
	}


	/**
	 * @param User $user
	 * @param Period[] $periods
	 * @return void
	 */
	public function countValidSubmissions(User $user, array $periods): void {
		$count = 0;

		foreach ($this->getChallenges() as $challenge) {
			foreach ($periods as $period) {
				if ($challenge->getPeriods()->contains($period)) {
					foreach ($challenge->getSubmissions() as $submission) {
						if ($submission->getUser() === $user && $submission->isValid()) {
							$count++;
						}
					}
					if ($challenge->getEventChallenge() !== null) {
						$event_submissions = $challenge->getEventChallenge()->getSubmissions();
						foreach ($event_submissions as $event_submission) {
							if ($event_submission->getUser() === $user && $event_submission->isValid()) {
								$count++;
							}
						}
					}
				}
			}
		}

		$this->setValidSubmissionsCount($count);
	}


	/**
	 * @param User $user
	 * @return void
	 */
	public function findPeriodsWithValidSubmissionsForUser(User $user): void {
		$periods = [];

		foreach ($this->getChallenges() as $challenge) {
			foreach ($challenge->getSubmissions() as $submission) {
				if ($submission->getUser() === $user && $submission->isValid()) {
					if (!in_array($submission->getPeriod(), $periods)) {
						$periods[] = $submission->getPeriod();
					}
				}
			}
		}

		$this->setPeriodsWithValidSubmissions($periods);
	}

}
