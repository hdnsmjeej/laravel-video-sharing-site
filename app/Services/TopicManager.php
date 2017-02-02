<?php

namespace App\Services;

use App\Models\Video;
use GuzzleHttp\Client;
use App\Repositories\TopicRepository;

class TopicManager
{
	/**
	 * @var TopicRepository
	 */
	protected $topicRepository;

	public function __construct(TopicRepository $topicRepository)
	{
		$this->topicRepository = $topicRepository;
	}

	public function setTopics(Video $video, array $google_ids)
	{
		$this->handleTopics($google_ids);
	}

	public function setRelevantTopics(Video $video, array $google_ids)
	{
		$this->handleTopics($google_ids);
	}

	private function handleTopics(array $google_ids)
	{
		$missingTopics = [];
		foreach ($google_ids as $google_id) {
			$topic = $this->topicRepository->getByGoogleId($google_id);

			if ( ! $topic) {
				$missingTopics []= $google_id;
			}
		}

		//$this->fetchAndInsertTopics($missingTopics);
		$this->fetchAndInsertTopics([
			'/m/010lvgg4',
			'/m/021dsh',
			'/m/0glt670'
		]);
	}

	private function fetchAndInsertTopics(array $google_ids)
	{
		if ( ! count($google_ids)) {
			return;
		}

		dump($google_ids);

		// Keep track of which ones have been filled/inserted.
		$filled = [];
		foreach ($google_ids as $google_id) {
			$filled[$google_id] = false;
		}

		$query = 'key=' . urlencode(env('GOOGLE_API_KEY'));
		foreach ($google_ids as $google_id) {
			$query .= '&ids=' . urlencode($google_id);
		}

		dump($query);

		// This must stay in a try/catch so that our API key never reaches
		// the light of day.
		try {
			$client = new Client();
			$response = $client->request('GET', 'https://kgsearch.googleapis.com/v1/entities:search', [
				'query' => $query
			]);
		} catch (\Exception $e) {
			$this->insertBlankTopics($google_ids);
			return;
		};

		dump($response);

		if ($response->getStatusCode() !== 200) {
			$this->insertBlankTopics($google_ids);
			return;
		}

		$json = json_decode($response->getBody());

		dump($json);

		if ( ! is_array($json->itemListElement) || ! count($json->itemListElement)) {
			$this->insertBlankTopics($google_ids);
			return;
		}

		foreach ($json->itemListElement as $itemListElement) {
			$item = $itemListElement->result;

			$item_id = $item->{'@id'};
			$original_id = null;

			foreach ($google_ids as $google_id) {
				if (strpos($item_id, $google_id) !== false) {
					$original_id = $google_id;
					break;
				}
			}

			if ($original_id !== null) {
				$this->topicRepository->create([
					'google_id' => $original_id,
					'name' => $item->name,
					'json' => json_encode($item)
				]);
				$filled[$original_id] = true;
			}
		}

		dd($filled);
	}

	private function insertBlankTopics(array $google_ids)
	{
		foreach ($google_ids as $google_id) {
			$this->topicRepository->create([
				'google_id' => $google_id
			]);
		}
	}
}
