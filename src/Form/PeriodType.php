<?php

namespace App\Form;

use App\Entity\Period;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('type', ChoiceType::class, [
						'label'   => "Type de période",
						'choices' => [
								'Année'     => 'year',
								'Evènement' => 'event',
						],
				])
				->add('name', TextType::class, [
						'label' => 'Nom',
				])
				->add('year', IntegerType::class, [
						'label' => 'Année',
						'attr'  => [
								'min' => 2023,
								'max' => 2050,
						],
				])
				->add('start_date', DateType::class, [
						'label'  => 'Date de début',
						'widget' => 'single_text',
						'attr'   => [
								'min' => '2023-01-01',
						],
				])
				->add('end_date', DateType::class, [
						'label'  => 'Date de fin',
						'widget' => 'single_text',
						'attr'   => [
								'min' => '2023-01-01',
						],
				]);
	}


	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => Period::class,
		]);
	}

}
