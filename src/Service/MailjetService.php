<?php


namespace App\Service;


use Mailjet\Client;
use Mailjet\Resources;

class MailjetService {

	/**
	 * @param $mail_to
	 * @param $username
	 * @param $subject
	 * @param $template_id
	 * @param array $variables tableau associatif des variables à afficher dans le template défini par template_id
	 * @return \Mailjet\Response
	 */
	public function send($mail_to, $username, $subject, $template_id, array $variables = []) {

		$mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'], true, ['version' => 'v3.1']);
		$body = [
				'Messages' => [
						[
								'From'             => [
										'Email' => $_ENV['SENDER_EMAIL'],
										'Name'  => "Ethil Van",
								],
								'To'               => [
										[
												'Email' => $mail_to,
												'Name'  => $username,
										],
								],
								'TemplateID'       => (int)$template_id,
								'TemplateLanguage' => true,
								'Subject'          => $subject,
						],
				],
		];
		if (!empty($variables)) {
			$body['Messages'][0]['Variables'] = $variables;
		}
		return $mj->post(Resources::$Email, ['body' => $body]);
	}
}
