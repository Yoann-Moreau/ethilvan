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


	public function __construct() {
               		$this->submissions = new ArrayCollection();
                 $this->submission_messages = new ArrayCollection();
               	}


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
    public function getSubmissionMessages(): Collection
    {
        return $this->submission_messages;
    }

    public function addSubmissionMessage(SubmissionMessage $submissionMessage): self
    {
        if (!$this->submission_messages->contains($submissionMessage)) {
            $this->submission_messages->add($submissionMessage);
            $submissionMessage->setUser($this);
        }

        return $this;
    }

    public function removeSubmissionMessage(SubmissionMessage $submissionMessage): self
    {
        if ($this->submission_messages->removeElement($submissionMessage)) {
            // set the owning side to null (unless already changed)
            if ($submissionMessage->getUser() === $this) {
                $submissionMessage->setUser(null);
            }
        }

        return $this;
    }

}
