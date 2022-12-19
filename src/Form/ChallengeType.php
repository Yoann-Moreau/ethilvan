<?php

namespace App\Form;

use App\Entity\Challenge;
use App\Entity\ChallengeDifficulty;
use App\Entity\Game;
use App\Repository\GameRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChallengeType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('game', EntityType::class, [
						'label'         => 'Jeu',
						'class'         => Game::class,
						'choice_label'  => 'name',
						'query_builder' => function (GameRepository $game_repository) {
							return $game_repository->createAlphabeticalQueryBuilder();
						},
				])
				->add('name', TextType::class, [
						'label' => 'Nom du défi',
				])
				->add('number_of_players', IntegerType::class, [
						'label' => 'Nombre de joueurs',
						'attr'  => [
								'min'   => 1,
								'max'   => 10,
								'value' => $options['number_of_players'],
						],
				])
				->add('difficulty', EntityType::class, [
						'label'        => 'Difficulté',
						'class'        => ChallengeDifficulty::class,
						'choice_label' => 'name',
				])
				->add('description', TextareaType::class, [
						'label' => 'Description',
				]);
	}


	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class'        => Challenge::class,
				'number_of_players' => 1,
		]);
	}

}
