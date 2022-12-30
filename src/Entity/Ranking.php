<?php

namespace App\Entity;

use App\Repository\RankingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

	#[ORM\OneToMany(mappedBy: 'ranking', targetEntity: RankingPosition::class)]
	private Collection $ranking_positions;

	public function __construct() {
		$this->ranking_positions = new ArrayCollection();
	}


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


	/**
	 * @return Collection<int, RankingPosition>
	 */
	public function getRankingPositions(): Collection {
		return $this->ranking_positions;
	}

	public function addRankingPosition(RankingPosition $rankingPosition): self {
		if (!$this->ranking_positions->contains($rankingPosition)) {
			$this->ranking_positions->add($rankingPosition);
			$rankingPosition->setRanking($this);
		}

		return $this;
	}

	public function removeRankingPosition(RankingPosition $rankingPosition): self {
		if ($this->ranking_positions->removeElement($rankingPosition)) {
			// set the owning side to null (unless already changed)
			if ($rankingPosition->getRanking() === $this) {
				$rankingPosition->setRanking(null);
			}
		}

		return $this;
	}

}
