<?php
declare(strict_types=1);


namespace Ludoi\Utils\EmailQueue;

use Contributte\Mailing\MailBuilder;
use Ludoi\Utils\Logger\Logger;
use Ludoi\Utils\Logger\LoggerChannel;
use Ludoi\Utils\Table\Table;
use Nette\Database\Explorer;
use Nette\Mail\Message;
use Nette\Utils\DateTime;
use Nette\Utils\Strings;

class EmailCollection extends Table
{
	const PRIO_LOW = 4;
	const PRIO_MEDIUM = 3;
	const PRIO_HIGH = 2;
	const PRIO_VERYHIGH = 1;
	protected string $tableName = 'emailqueue';
	private ?LoggerChannel $loggerChannel;
	private EmailTag $emailTag;
	private EmailCollector $emailCollector;
	private string $addressFrom;

	public function __construct(EmailTag $emailTag, Explorer $db, Logger $logger, EmailCollector $emailCollector,
								string $loggerClass, string $addressFrom)
	{
		parent::__construct($db);
		$this->emailTag = $emailTag;
		$this->loggerChannel = $logger->getChannel($loggerClass);
		$this->emailCollector = $emailCollector;
		$this->addressFrom = $addressFrom;
	}

	private function getAddress(object $message, string $key): array {
		$address = is_array($message->getHeader($key)) ? array_keys($message->getHeader($key)) : [];
		return $address;
	}

	private function getAddressToString(array $list): string {
		return Strings::trim(implode(';', $list));
	}

	private function insertTags(int $emailid, array $tags)
	{
		if (count($tags) > 0) {
			$data = [];
			foreach ($tags as $tag => $value) {
				$dataTag['emailid'] = $emailid;
				$dataTag['tag'] = $tag;
				$dataTag['value'] = $value;
				$data[] = $dataTag;
			}
			$this->emailTag->insert($data);
		}
	}

	private function insertMessageToDb(?string $subject, string $content, array $to, array $cc, array $bcc,
									   array $tags): void {
		$now = new DateTime();
		$data = [
			'subject' => $subject,
			'content' => $content,
			'to' => $this->getAddressToString($to),
			'cc' => $this->getAddressToString($cc),
			'bcc' => $this->getAddressToString($bcc),
			'sent' => false,
			'created_on' => $now,
			'priority' => self::PRIO_HIGH
		];
		$row = $this->insert($data);
		$this->insertTags($row->id, $tags);
		$this->loggerChannel?->addInfo(sprintf('PREPARE, ID = %d, TO = %s', $row->id, $data['to']));
	}

	public function insertMessage(Message $message, array $tags = []): void
	{
		$data = [
			'subject' => $message->getSubject(),
			'content' => $message->getHtmlBody(),
			'to' => $this->getAddress($message, 'To'),
			'cc' => $this->getAddress($message, 'Cc'),
			'bcc' => $this->getAddress($message, 'Bcc')
		];
		$this->insertMessageToDb($data['subject'], $data['content'], $data['to'], $data['cc'], $data['bcc'], $tags);
		$this->emailCollector->publish($data);
	}

	public function insertMessageFromBuilder(MailBuilder $mailBuilder, array $tags = []): void
	{
		// Create message
		$message = $mailBuilder->getMessage();

		// Create template
		$template = $mailBuilder->getTemplate();

		// Set template to message
		$message->setHtmlBody(
			$template->__toString(),
			is_string($template->getFile()) ? dirname($template->getFile()) : null
		);

		$this->insertMessage($message, $tags);
	}

	public function insertEmail($template, int $priority, array $tags, array $to = [],
								array $cc = [], array $bcc = []): void
	{
		if (is_object($template)) {
			$content = $template->__toString();
		} else {
			$content = $template;
		}
		$now = new DateTime();
		if (count($to) > 0 && $content <> '') {
			$data = [
				'from' => $this->addressFrom,
				'subject' => null,
				'content' => $content,
				'to' => $to,
				'cc' => $cc,
				'bcc' => $bcc
			];
			$this->insertMessageToDb($data['subject'], $data['content'], $data['to'], $data['cc'], $data['bcc'], $tags);
			$this->emailCollector->publish($data);
		}
	}

	public function deleteOldEmails(): void
	{
		$keyDate = new DateTime('-3 months');
		$this->findAll()->where('created_on < ?', $keyDate)->delete();
		$this->optimizeTable();
		$this->emailTag->optimizeTable();
		$this->loggerChannel?->addInfo('Deleted old emails');
	}
}