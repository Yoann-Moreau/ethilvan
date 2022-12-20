<?php

namespace App\Entity;

use App\Repository\PeriodRepository;
use DateTimeInterface;
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

}
