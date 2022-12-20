<?php

namespace App\Form;

use App\Entity\Period;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PeriodType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('type')
				->add('name')
				->add('year')
				->add('start_date')
				->add('end_date');
	}


	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => Period::class,
		]);
	}

}
