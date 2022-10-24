<?php


namespace App\Entity;


use App\Repository\UserRepository;
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
	#[ORM\Column, Assert\Length(min: 8, minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.")]
	private ?string $password = null;

	#[ORM\Column(length: 255), Assert\Email(message: "L'adresse email doit être valide."), Assert\NotBlank]
	private ?string $email = null;


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

}
