<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfileEditType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('avatar', FileType::class, [
						'label'       => 'Avatar',
						'mapped'      => false,
						'required'    => false,
						'constraints' => [
								new File([
										'maxSize'          => '255k',
										'maxSizeMessage'   => "L'avatar doit peser moins de 256ko",
										'mimeTypes'        => [
												'image/png',
												'image/jpeg',
										],
										'mimeTypesMessage' => 'Seuls les formats PNG et JPEG sont supportés',
								]),
						],
						'attr'        => [
								'accept' => 'image/png, image/jpeg',
						],
				])
				->add('favoriteGames', TextareaType::class, [
						'label'    => 'Jeux préférés',
						'required' => false,
				]);
	}

	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => User::class,
		]);
	}

}
