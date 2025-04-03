<?php
declare(strict_types=1);


namespace Ludoi\Utils\EmailQueue;

use Contributte\Mailing\IMailBuilderFactory;
use Nette\Application\LinkGenerator;

class EmailDesigner
{
	private LinkGenerator $linkGenerator;
	private IMailBuilderFactory $mailBuilderFactory;
	private EmailCollection $emailCollection;

	public function __construct(LinkGenerator $linkGenerator, IMailBuilderFactory $mailBuilderFactory, EmailCollection $emailCollection)
	{
		$this->linkGenerator = $linkGenerator;
		$this->mailBuilderFactory = $mailBuilderFactory;
		$this->emailCollection = $emailCollection;
	}

	protected function addGeneralLinks(array $source): array
	{
		return [];
	}

	public function createEmail(string $to, string $subject, array $parameters, string $templateFile,
								array $tags): void
	{
		if ($to == '')
		{
			return;
		}
		$toArray = explode(';', $to);
		$mail = $this->mailBuilderFactory->create();
		foreach ($toArray as $toLine)
		{
			$mail->addTo($toLine);
		}
		$mail->setSubject($subject);
		$mail->setTemplateFile($templateFile);
		$mail->setParameters($this->addGeneralLinks($parameters));
		$this->emailCollection->insertMessageFromBuilder($mail, $tags);
	}
}