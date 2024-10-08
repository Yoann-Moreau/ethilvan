<?php


namespace App\Entity;


use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 180, unique: true),
			Assert\Length(
					min: 2,
					max: 20,
					minMessage: "Le nom d'utilisateur doit contenir au moins {{ limit }} caractères.",
					maxMessage: "Le nom d'utilisateur doit contenir moins de {{ limit }} caractères."
			),
			Assert\Regex(
					pattern: '/^[\p{L}\p{Mn}\-_\d]+$/u',
					message: "Le nom d'utilisateur ne doit contenir que des caractères alphanumériques, tirets et underscores."
			)
	]
	private ?string $username = null;

	#[ORM\Column]
	private array $roles = [];

	/**
	 * @var string|null The hashed password
	 */
	#[ORM\Column, Assert\Length(
			min: 8,
			max: 60,
			minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.",
			maxMessage: "Le mot de passe doit contenir au plus {{ limit }} caractères"
	)]
	private ?string $password = null;

	#[ORM\Column(length: 255), Assert\Email(message: "L'adresse email doit être valide."), Assert\NotBlank]
	private ?string $email = null;

	#[ORM\Column]
	private ?bool $deleted = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $avatar = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $favorite_games = null;

	#[ORM\Column(type: Types::DATE_MUTABLE)]
	private ?DateTimeInterface $inscription_date = null;

	#[ORM\OneToMany(mappedBy: 'user', targetEntity: Submission::class)]
	private Collection $submissions;

	#[ORM\OneToMany(mappedBy: 'user', targetEntity: SubmissionMessage::class)]
	private Collection $submission_messages;

	#[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class)]
	private Collection $notifications;

	#[ORM\OneToMany(mappedBy: 'user', targetEntity: RankingPosition::class)]
	private Collection $ranking_positions;

	private array $challenges_counts;

	private array $ranking_position_counts;

	private array $year_ranking_position_counts;

	private array $final_points;

	private array $trophies;

	public function __construct() {
		$this->submissions = new ArrayCollection();
		$this->submission_messages = new ArrayCollection();
		$this->notifications = new ArrayCollection();
		$this->ranking_positions = new ArrayCollection();
	}


	// ==========================================================================
	// Getters and setters
	// ==========================================================================

	public function getId(): ?int {
		return $this->id;
	}


	public function getUsername(): ?string {
		return $this->username;
	}

	public function setUsername(string $username): self {
		$this->username = $username;

		return $this;
	}


	/**
	 * A visual identifier that represents this user.
	 *
	 * @see UserInterface
	 */
	public function getUserIdentifier(): string {
		return (string)$this->username;
	}


	/**
	 * @see UserInterface
	 */
	public function getRoles(): array {
		$roles = $this->roles;
		// guarantee every user at least has ROLE_USER
		$roles[] = 'ROLE_USER';

		return array_unique($roles);
	}

	public function setRoles(array $roles): self {
		$this->roles = $roles;

		return $this;
	}


	/**
	 * @see PasswordAuthenticatedUserInterface
	 */
	public function getPassword(): string {
		return $this->password;
	}

	public function setPassword(string $password): self {
		$this->password = $password;

		return $this;
	}


	/**
	 * @see UserInterface
	 */
	public function eraseCredentials() {
		// If you store any temporary, sensitive data on the user, clear it here
		// $this->plainPassword = null;
	}


	public function getEmail(): ?string {
		return $this->email;
	}

	public function setEmail(string $email): self {
		$this->email = $email;

		return $this;
	}


	public function isDeleted(): ?bool {
		return $this->deleted;
	}

	public function setDeleted(bool $deleted): self {
		$this->deleted = $deleted;

		return $this;
	}

	public function getAvatar(): ?string {
		return $this->avatar;
	}

	public function setAvatar(?string $avatar): self {
		$this->avatar = $avatar;

		return $this;
	}

	public function getFavoriteGames(): ?string {
		return $this->favorite_games;
	}

	public function setFavoriteGames(?string $favorite_games): self {
		$this->favorite_games = $favorite_games;

		return $this;
	}

	public function getInscriptionDate(): ?DateTimeInterface {
		return $this->inscription_date;
	}

	public function setInscriptionDate(DateTimeInterface $inscription_date): self {
		$this->inscription_date = $inscription_date;

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
			$submission->setUser($this);
		}

		return $this;
	}

	public function removeSubmission(Submission $submission): self {
		if ($this->submissions->removeElement($submission)) {
			// set the owning side to null (unless already changed)
			if ($submission->getUser() === $this) {
				$submission->setUser(null);
			}
		}

		return $this;
	}

	/**
	 * @return Collection<int, SubmissionMessage>
	 */
	public function getSubmissionMessages(): Collection {
		return $this->submission_messages;
	}

	public function addSubmissionMessage(SubmissionMessage $submissionMessage): self {
		if (!$this->submission_messages->contains($submissionMessage)) {
			$this->submission_messages->add($submissionMessage);
			$submissionMessage->setUser($this);
		}

		return $this;
	}

	public function removeSubmissionMessage(SubmissionMessage $submissionMessage): self {
		if ($this->submission_messages->removeElement($submissionMessage)) {
			// set the owning side to null (unless already changed)
			if ($submissionMessage->getUser() === $this) {
				$submissionMessage->setUser(null);
			}
		}

		return $this;
	}


	/**
	 * @return Collection<int, Notification>
	 */
	public function getNotifications(): Collection {
		return $this->notifications;
	}

	public function addNotification(Notification $notification): self {
		if (!$this->notifications->contains($notification)) {
			$this->notifications->add($notification);
			$notification->setUser($this);
		}

		return $this;
	}

	public function removeNotification(Notification $notification): self {
		if ($this->notifications->removeElement($notification)) {
			// set the owning side to null (unless already changed)
			if ($notification->getUser() === $this) {
				$notification->setUser(null);
			}
		}

		return $this;
	}


	/**
	 * @return Collection<int, RankingPosition>
	 */
	public function getRankingPositions(): Collection {
		return $this->ranking_positions;
	}

	public function addRankingPosition(RankingPosition $rankingPosition): self {
		if (!$this->ranking_positions->contains($rankingPosition)) {
			$this->ranking_positions->add($rankingPosition);
			$rankingPosition->setUser($this);
		}

		return $this;
	}

	public function removeRankingPosition(RankingPosition $rankingPosition): self {
		if ($this->ranking_positions->removeElement($rankingPosition)) {
			// set the owning side to null (unless already changed)
			if ($rankingPosition->getUser() === $this) {
				$rankingPosition->setUser(null);
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
	 * @return array
	 */
	public function getRankingPositionCounts(): array {
		return $this->ranking_position_counts;
	}

	/**
	 * @param array $ranking_position_counts
	 */
	public function setRankingPositionCounts(array $ranking_position_counts): void {
		$this->ranking_position_counts = $ranking_position_counts;
	}


	public function getYearRankingPositionCounts(): array {
		return $this->year_ranking_position_counts;
	}

	public function setYearRankingPositionCounts(array $year_ranking_position_counts): void {
		$this->year_ranking_position_counts = $year_ranking_position_counts;
	}


	public function getFinalPoints(): array {
		return $this->final_points;
	}

	public function setFinalPoints(array $final_points): void {
		$this->final_points = $final_points;
	}


	public function getTrophies(): array {
		return $this->trophies;
	}

	public function setTrophies(array $trophies): void {
		$this->trophies = $trophies;
	}


	// ==========================================================================
	// Other methods
	// ==========================================================================

	public function countChallengesByDifficulty(): void {
		$counts = ['Bronze' => 0, 'Argent' => 0, 'Or' => 0, 'Platine' => 0];

		foreach ($this->getSubmissions() as $submission) {
			if ($submission->isValid()) {
				$difficulty = $submission->getChallenge()->getDifficulty()->getName();
				$counts[$difficulty] = $counts[$difficulty] + 1;
			}
		}

		$this->setChallengesCounts($counts);
	}


	public function countRankingPositions(): void {
		$counts = [1 => 0, 2 => 0, 3 => 0];

		foreach ($this->getRankingPositions() as $ranking_position) {
			foreach ($counts as $key => &$count) {
				if ($ranking_position->getPosition() === $key) {
					$count++;
				}
			}
		}

		$this->setRankingPositionCounts($counts);
	}


	public function countYearRankingPositions(): void {
		$current_year = date('Y');
		$year_ranking_positions = [];

		for ($year = 2023; $year < $current_year; $year++) {
			$year_ranking_positions[$year] = [1 => 0, 2 => 0, 3 => 0];
		}

		foreach ($this->getRankingPositions() as $ranking_position) {
			$year = $ranking_position->getRanking()->getPeriod()->getYear();
			if ($year >= $current_year || $year === 2022) {
				continue;
			}
			foreach ($year_ranking_positions[$year] as $key => &$count) {
				if ($ranking_position->getPosition() === $key) {
					$count++;
				}
			}
		}

		$this->setYearRankingPositionCounts($year_ranking_positions);
	}


	public function countFinalPoints(): void {
		$current_year = date('Y');
		$final_points = [];

		for ($year = 2023; $year < $current_year; $year++) {
			$final_points[$year] = 0;
		}

		foreach ($this->getRankingPositions() as $ranking_position) {
			$year = $ranking_position->getRanking()->getPeriod()->getYear();
			if ($year >= $current_year || $year === 2022) {
				continue;
			}
			$period_type = $ranking_position->getRanking()->getPeriod()->getType();
			$multiplier = 1;
			if ($period_type === 'year') {
				$multiplier = 2;
			}
			$final_points[$year] += $this->getPositionPoints($ranking_position->getPosition()) * $multiplier;
		}

		$this->setFinalPoints($final_points);
	}


	/**
	 * @param User[] $users
	 * @return void
	 */
	public function countTrophies(array $users): void {
		$current_year = date('Y');
		$counts = [1 => 0, 2 => 0, 3 => 0];
		$sorted_users = [];
		$years = [];

		foreach ($users as $user) {
			$user->countFinalPoints();
			$user->countYearRankingPositions();
			$sorted_users[] = $user;
			foreach ($user->getRankingPositions() as $ranking_position) {
				$year = $ranking_position->getRanking()->getPeriod()->getYear();
				if ($year === 2022 || $year >= $current_year) {
					continue;
				}
				if (!in_array($year, $years)) {
					$years[] = $year;
				}
			}
		}

		foreach ($years as $year) {
			usort($sorted_users, function ($a, $b) use ($year) {
				$return_value = $b->getFinalPoints()[$year] <=> $a->getFinalPoints()[$year];
				if ($return_value === 0) {
					$return_value = $b->getYearRankingPositionCounts()[$year][1] <=>
							$a->getYearRankingPositionCounts()[$year][1];
					if ($return_value === 0) {
						$return_value = $b->getYearRankingPositionCounts()[$year][2] <=>
								$a->getYearRankingPositionCounts()[$year][2];
						if ($return_value === 0) {
							$return_value = $b->getYearRankingPositionCounts()[$year][3] <=>
									$a->getYearRankingPositionCounts()[$year][3];
						}
					}
				}
				return $return_value;
			});


			foreach ($counts as $key => &$count) {
				if (array_search($this, $sorted_users) + 1 === $key) {
					$count++;
				}
			}
		}

		$this->setTrophies($counts);
	}


	private function getPositionPoints(int $position): int {
		return match ($position) {
			1 => 10,
			2 => 7,
			3 => 5,
			4 => 3,
			default => 1,
		};
	}

}
