<?php

namespace App\Form;

use App\Entity\Challenge;
use App\Entity\ChallengeDifficulty;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChallengeAddToYearType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('difficulty', EntityType::class, [
						'label'        => 'Difficulté',
						'class'        => ChallengeDifficulty::class,
						'choice_label' => 'name',
				]);
	}

	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => Challenge::class,
		]);
	}
}
