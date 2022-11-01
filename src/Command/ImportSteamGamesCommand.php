<?php


namespace App\Command;


use App\Entity\SteamGame;
use App\Repository\SteamGameRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class ImportSteamGamesCommand extends Command {

	private SteamGameRepository $steam_game_repository;
	private HttpClientInterface $client;

	protected static $defaultName = 'app:steam_games:import';


	public function __construct(SteamGameRepository $steam_game_repository, HttpClientInterface $client) {
		$this->steam_game_repository = $steam_game_repository;
		$this->client = $client;

		parent::__construct();
	}


	protected function configure() {
		$this
				->setDescription('Imports steam app ids and names into database')
				->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run');
	}


	/**
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws ClientExceptionInterface
	 * @throws DecodingExceptionInterface
	 * @throws RedirectionExceptionInterface
	 * @throws ServerExceptionInterface
	 * @throws TransportExceptionInterface
	 */
	protected function execute(InputInterface $input, OutputInterface $output): int {
		$io = new SymfonyStyle($input, $output);

		$url = "https://api.steampowered.com/ISteamApps/GetAppList/v0002/?format=json";

		$response = $this->client->request('GET', $url);
		$status_code = $response->getStatusCode();

		if ($status_code !== 200) {
			$io->error('Error when fetching Steam data.');
			return 0;
		}

		$content = $response->toArray();
		$apps = $content['applist']['apps'];

		$max_id_app = $this->steam_game_repository->findOneBy([], ['id' => 'DESC']);
		$max_id = $max_id_app->getId();

		if ($input->getOption('dry-run')) {
			$io->note('Dry mode enabled');

			$count = 0;

			foreach ($apps as $key => $app) {
				if ($key < $max_id) {
					continue;
				}

				$stored_app = $this->steam_game_repository->findOneBy(['app_id' => $app['appid']]);
				if (empty($stored_app)) {
					$count++;
				}
			}
		}
		else {
			$count = 0;

			foreach ($apps as $key => $app) {
				if ($key < $max_id) {
					continue;
				}

				$stored_app = $this->steam_game_repository->findOneBy(['app_id' => $app['appid']]);

				if (empty($stored_app)) {
					$new_app = new SteamGame();
					$new_app->setAppId($app['appid']);
					$new_app->setName($app['name']);

					$this->steam_game_repository->save($new_app, true);

					$count++;
				}
			}
		}

		$io->success(sprintf('Imported "%d" new games.', $count));

		return 0;
	}
}
