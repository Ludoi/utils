<?php
declare(strict_types=1);


namespace Ludoi\Utils\EmailQueue;

use Bunny\Message;
use Contributte\RabbitMQ\Consumer\IConsumer;
use Ludoi\Utils\Logger\Logger;
use Nette\Mail\Mailer;
use Nette\Mail\SendException;

class EmailConsumer implements IConsumer
{
	private Mailer $mailer;
	private string $loggerClass;


	public function __construct(Mailer $mailer, Logger $logger, string $loggerClass)
	{
		$this->mailer = $mailer;
		$this->logger = $logger;
		$this->loggerClass = $loggerClass;
	}

	private function getEmails($emails): array {
		if (is_array($emails)) {
			return $emails;
		} elseif (is_null($emails)) {
			return [];
		} else {
			return explode(';', $emails);
		}
	}

	public function consume(Message $message): int
	{
		$messageData = json_decode($message->content, true);

		$mail = new \Nette\Mail\Message();
		$mail->setFrom($messageData['from'])
			->setHtmlBody($messageData['content']);
		$to = $this->getEmails($messageData['to']);
		foreach ($to as $address) {
			if ($address <> '')
				$mail->addTo($address);
		}
		$cc = $this->getEmails($messageData['cc']);
		foreach ($cc as $address) {
			if ($address <> '')
				$mail->addCc($address);
		}
		$bcc = $this->getEmails($messageData['bcc']);
		foreach ($bcc as $address) {
			if ($address <> '')
				$mail->addBcc($address);
		}

		try {
			$this->mailer->send($mail);
		} catch (SendException $exception) {
			$logEmail = $this->logger->getChannel($this->loggerClass);
			$logEmail->addAlert('Email was not sent ' . $exception->getMessage());
		}

		return IConsumer::MESSAGE_ACK; // Or ::MESSAGE_NACK || ::MESSAGE_REJECT
	}
}