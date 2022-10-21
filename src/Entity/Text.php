<?php

namespace App\Entity;

use App\Repository\TextRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TextRepository::class)]
class Text {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 40)]
	private ?string $page = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $title = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $text = null;

	#[ORM\Column(nullable: true)]
	private ?int $text_order = null;


	public function getId(): ?int {
		return $this->id;
	}


	public function getPage(): ?string {
		return $this->page;
	}

	public function setPage(string $page): self {
		$this->page = $page;

		return $this;
	}


	public function getTitle(): ?string {
		return $this->title;
	}

	public function setTitle(?string $title): self {
		$this->title = $title;

		return $this;
	}


	public function getText(): ?string {
		return $this->text;
	}

	public function setText(?string $text): self {
		$this->text = $text;

		return $this;
	}


	public function getTextOrder(): ?int {
		return $this->text_order;
	}

	public function setTextOrder(?int $text_order): self {
		$this->text_order = $text_order;

		return $this;
	}

}
