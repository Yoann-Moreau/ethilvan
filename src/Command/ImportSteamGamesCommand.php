<?php


namespace App\Command;


use JsonMachine\Exception\InvalidArgumentException;
use JsonMachine\Items;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


#[AsCommand(name: 'app:steam_games:import')]
class ImportSteamGamesCommand extends Command {

	private HttpClientInterface $client;


	public function __construct(HttpClientInterface $client) {
		$this->client = $client;

		parent::__construct();
	}


	protected function configure(): void {
		$this
				->setDescription('Imports steam app ids and names into a JSON file.');
	}


	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws ClientExceptionInterface
	 * @throws RedirectionExceptionInterface
	 * @throws ServerExceptionInterface
	 * @throws TransportExceptionInterface
	 * @throws InvalidArgumentException
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$io = new SymfonyStyle($input, $output);

		$url = "https://api.steampowered.com/ISteamApps/GetAppList/v2/?format=json";

		$response = $this->client->request('GET', $url);
		$status_code = $response->getStatusCode();

		if ($status_code !== 200) {
			$io->error('Error when fetching Steam data.');
			return Command::FAILURE;
		}

		file_put_contents('data/steam_games.json', $response->getContent());

		$input_file = 'data/steam_games.json';
		$output_file = 'data/steam_games_delimited.json';

		// Open the output file for writing
		$output_handle = fopen($output_file, 'w');
		if (!$output_handle) {
			$io->error('Failed to create output file.');
			return Command::FAILURE;
		}

		// Process the JSON file incrementally
		$json_stream = Items::fromFile($input_file, ['pointer' => '/applist/apps']);
		foreach ($json_stream as $game) {
			fwrite($output_handle, json_encode($game, JSON_UNESCAPED_UNICODE) . PHP_EOL);
		}

		fclose($output_handle);

		$io->success('Imported Steam games list as a json file');

		return Command::SUCCESS;
	}
}
