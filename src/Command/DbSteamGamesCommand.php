<?php

namespace App\Command;

use App\Entity\SteamGame;
use App\Repository\SteamGameRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:steam_games:db')]
class DbSteamGamesCommand extends Command {

	private SteamGameRepository $steam_game_repository;
	private EntityManagerInterface $entity_manager;

	public function __construct(
			SteamGameRepository $steam_game_repository,
			EntityManagerInterface $entity_manager
	) {

		$this->steam_game_repository = $steam_game_repository;
		$this->entity_manager = $entity_manager;

		parent::__construct();
	}


	protected function configure(): void {
		$this
				->setDescription('Imports steam app ids and names into database from JSON.');
	}


	protected function execute(InputInterface $input, OutputInterface $output): int {
		$io = new SymfonyStyle($input, $output);

		$input_file = 'data/steam_games_delimited.json';
		$lines_processed_file = 'data/lines_processed.txt';

		// Open the input file for reading
		$input_handle = fopen($input_file, 'r');
		if (!$input_handle) {
			$io->error('Failed to open input file.');
			return Command::FAILURE;
		}

		$lines_processed = file_exists($lines_processed_file) ? (int)file_get_contents($lines_processed_file) : 0;

		$batch_size = 500;
		$batch = [];
		$line_number = $lines_processed;

		// Skip lines until the last processed line
		$current_line = 0;
		while ($current_line < $lines_processed && !feof($input_handle)) {
			fgets($input_handle);
			$current_line++;
		}

		// Process the file line by line
		while (!feof($input_handle)) {
			$line = fgets($input_handle);
			if ($line === false) {
				break;
			}

			// Decode the JSON line
			$game = json_decode($line, true);
			if (json_last_error() !== JSON_ERROR_NONE) {
				$io->error('Invalid JSON in line: ' . $line);
				return Command::FAILURE;
			}

			// Add the game to the batch
			$batch[] = $game;
			$line_number++;

			// Process the batch when it reaches the batch size
			if (count($batch) >= $batch_size) {
				$this->processBatch($batch);
				$batch = []; // Reset the batch
				unset($game);
				gc_collect_cycles();
				$usage = memory_get_usage();

				$io->comment("Memory usage: " . $usage / 1024 / 1024 . " MB");

				// Stop if usage close to memory limit (128)
				if ($usage > 120 * 1024 * 1024) {
					break;
				}
			}
		}

		// Process the remaining games in the last batch
		if (!empty($batch)) {
			$this->processBatch($batch);
			$batch = []; // Reset the batch
			gc_collect_cycles(); // Force garbage collection
		}

		fclose($input_handle);

		file_put_contents($lines_processed_file, $line_number);

		$io->success(sprintf('Processing complete. Total lines processed: %s', $line_number));

		return Command::SUCCESS;
	}


	private function processBatch(array $batch): void {

		foreach ($batch as $game) {
			$app = $this->steam_game_repository->findOneBy(['app_id' => $game['appid']]);

			if ($app !== null) {
				continue;
			}

			$steam_game = new SteamGame();
			$steam_game->setAppId($game['appid']);
			$steam_game->setName($game['name']);

			$this->steam_game_repository->save($steam_game);
		}

		$this->entity_manager->flush();
		$this->entity_manager->clear();
	}
}
