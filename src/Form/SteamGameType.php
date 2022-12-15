<?php


namespace App\Form;


use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SteamGameType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('name', TextType::class, [
						'label' => 'Nom du jeu Steam',
						'attr'  => [
								'class'        => 'autocomplete col-100',
								'autocomplete' => 'off',
						],
				])
				->add('app_id', HiddenType::class, [
						'mapped' => false,
				]);
	}


	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => Game::class,
		]);
	}
}
