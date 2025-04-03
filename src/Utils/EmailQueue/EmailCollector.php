<?php
declare(strict_types=1);


namespace Ludoi\Utils\EmailQueue;

use Contributte\RabbitMQ\Producer\Producer;

final class EmailCollector
{

	/**
	 * @var Producer
	 */
	private Producer $emailProducer;
	private bool $sendEmail;


	public function __construct(Producer $emailProducer, bool $sendEmail)
	{
		$this->emailProducer = $emailProducer;
		$this->sendEmail = $sendEmail;
	}


	public function publish(array $emailContent): void
	{
		$json = json_encode($emailContent);
		$headers = [];

		if ($this->sendEmail) {
			$this->emailProducer->publish($json, $headers);
		}
	}
}