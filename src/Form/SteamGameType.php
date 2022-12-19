<?php


namespace App\Form;


use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
				->add('app_id', TextType::class, [
						'label'  => 'ID Steam',
						'mapped' => false,
						'attr'   => [
								'readonly' => 'readonly',
						],
				])
				->add('multi', CheckboxType::class, [
						'label' => 'Multijoueurs',
						'required' => false,
				])
				->add('played', CheckboxType::class, [
						'label' => 'Apparait dans la liste des jeux joués',
						'required' => false,
				]);
	}


	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => Game::class,
		]);
	}
}
