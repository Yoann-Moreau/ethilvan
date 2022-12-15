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
						'label'       => 'Image',
						'required'    => false,
						'constraints' => [
								new File([
										'maxSize'          => '1024k',
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
				]);
	}


	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => Game::class,
		]);
	}
}
