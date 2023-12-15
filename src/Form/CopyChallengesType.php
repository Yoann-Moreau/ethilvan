<?php

namespace App\Form;

use App\Entity\Period;
use App\Repository\PeriodRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CopyChallengesType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {

		$period = $options['period'];

		$builder
				->add('period', EntityType::class, [
						'label'         => 'Période ciblée',
						'class'         => Period::class,
						'query_builder' => function (PeriodRepository $period_repository) use ($period): QueryBuilder {
							return $period_repository->createQueryBuilder('p')
									->where('p != :p')
									->setParameter('p', $period)
									->orderBy('p.end_date', 'DESC');
						},
				]);
	}


	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'period' => null,
		]);
	}
}
