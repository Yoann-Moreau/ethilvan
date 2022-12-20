<?php

namespace App\Form;

use App\Entity\Game;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class GameType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('name', TextType::class, [
						'label' => 'Nom du jeu',
				])
				->add('image', FileType::class, [
						'label'       => 'Image (460x215)',
						'required'    => false,
						'constraints' => [
								new File([
										'maxSize'          => '256k',
										'maxSizeMessage'   => "L'image doit peser moins de 256ko",
										'mimeTypes'        => [
												'image/png',
												'image/jpeg',
										],
										'mimeTypesMessage' => 'Seuls les formats PNG et JPEG sont supportés',
								]),
						],
						'mapped'      => false,
				])
				->add('link', TextType::class, [
						'label'    => 'Lien',
						'required' => false,
				])
				->add('multi', CheckboxType::class, [
						'label'    => 'Multijoueurs',
						'required' => false,
				])
				->add('played', CheckboxType::class, [
						'label'    => 'Apparait dans la liste des jeux joués',
						'required' => false,
				]);
	}


	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => Game::class,
		]);
	}
}
