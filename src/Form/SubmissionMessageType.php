<?php

namespace App\Form;

use App\Entity\SubmissionMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

class SubmissionMessageType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('message', TextareaType::class, [
						'label'    => 'Message',
						'required' => true,
				])
				->add('images', FileType::class, [
						'label'       => 'Images',
						'multiple'    => true,
						'required'    => false,
						'mapped'      => false,
						'constraints' => [
								new All([
										new File([
												'maxSize'          => '1024k',
												'maxSizeMessage'   => 'Les images ne doivent pas dépasser 1024ko',
												'mimeTypes'        => [
														'image/png',
														'image/jpeg',
												],
												'mimeTypesMessage' => 'Seuls les formats PNG et JPEG sont supportés',
										]),
								]),
						],
						'attr'        => [
								'accept' => 'image/png, image/jpeg',
						],
				]);
	}


	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => SubmissionMessage::class,
		]);
	}

}
