<?php


namespace App\Entity;


use App\Repository\GameRepository;
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

}
