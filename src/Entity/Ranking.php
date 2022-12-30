<?php

namespace App\Entity;

use App\Repository\RankingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RankingRepository::class)]
class Ranking {

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\OneToOne(inversedBy: 'ranking', cascade: ['persist', 'remove'])]
	#[ORM\JoinColumn(nullable: false)]
	private ?Period $period = null;


	public function getId(): ?int {
		return $this->id;
	}


	public function getPeriod(): ?Period {
		return $this->period;
	}

	public function setPeriod(Period $period): self {
		$this->period = $period;

		return $this;
	}
}
