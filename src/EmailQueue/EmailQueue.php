<?php

namespace Ludoi\EmailQueue;

use Ludoi\Logger;
use Nette\Utils\DateTime;
use Nette\Environment;
use Nette\Mail\Message;
use Nette\Mail\SmtpException;
use Nette\Mail\SmtpMailer;
use Nette\Utils\Strings;

/**
 * Model starající se o tabulku emailqueue
 */
class EmailQueue {

    use \Nette\SmartObject;

    /** @var string */
    protected $tableName = 'emailqueue';

    /** @var Logger */
    private $logger;
    private $mailer = NULL;
    private $addressFrom = NULL;

    const PRIO_LOW = 4;
    const PRIO_MEDIUM = 3;
    const PRIO_HIGH = 2;
    const PRIO_VERYHIGH = 1;

    public function __construct(Logger $logger, array $mailer, string $addressFrom) {
	$this->createMailer($mailer);
	$this->addressFrom = $addressFrom;
	$this->logger = $logger->channel('email');
    }

    private function createMailer(array $mailer) {
	try {
	    $this->mailer = new SmtpMailer($mailer);
	} catch (SmtpException $e) {
	    $this->mailer = NULL;
	    $this->logger->addError($e->getMessage());
	}
    }

    public function insertEmail($template, $priority = self::PRIO_LOW, $tag, $to = array(), $cc = array(), $bcc = array()) {
	$toDb = Strings::trim(implode(';', $to));
	$ccDb = Strings::trim(implode(';', $cc));
	$bccDb = Strings::trim(implode(';', $bcc));
	if (is_object($template)) {
	    $content = $template->__toString();
	} else {
	    $content = $template;
	}
	$now = new \DateTime();
	if ($toDb <> '' && $content <> '') {
	    $data = array(
		'subject' => NULL,
		'content' => $content,
		'to' => $toDb,
		'cc' => $ccDb,
		'bcc' => $bccDb,
		'sent' => FALSE,
		'created_on' => $now,
		'priority' => $priority,
		'tag' => $tag
	    );
//	    $row = $this->insert($data);
	    $this->logger->addInfo(sprintf('PREPARE, ID = %d, TAG = %s, TO = %s', $row->id, $tag, $toDb));
	}
    }

    public function sendEmails() {
	if (is_null($this->mailer)) {
	    return;
	}
//	$emails = $this->findBy(array('sent' => 0))->order('sent ASC, priority ASC')->limit($this->maxEmailsPerRound);
	try {
	    $now = new \DateTime();
	    foreach ($emails as $row) {
		$mail = new Message;
		$mail->setFrom($this->addressFrom)
			->setSubject($row->subject)
			->setHtmlBody($row->content);
		$to = explode(';', $row->to);
		if (!Environment::isProduction() && sizeof($to) > 0) {
		    $to = array('ludek.bednarz@gmail.com');
		}
		foreach ($to as $address) {
		    if ($address <> '')
			$mail->addTo($address);
		}
		$cc = explode(';', $row->cc);
		if (!Environment::isProduction() && sizeof($cc) > 0) {
		    $cc = array('ludek.bednarz@gmail.com');
		}
		foreach ($cc as $address) {
		    if ($address <> '')
			$mail->addCc($address);
		}
		$bcc = explode(';', $row->bcc);
		if (!Environment::isProduction() && sizeof($bcc) > 0) {
		    $bcc = array('ludek.bednarz@gmail.com');
		}
		foreach ($bcc as $address) {
		    if ($address <> '')
			$mail->addBcc($address);
		}
		$this->mailer->send($mail);
		$this->logMail($row->id, $mail);
		$data = array('sent' => TRUE, 'sent_on' => $now);
		$row->update($data);
	    }
	} catch (SmtpException $e) {
	    $this->logger->addError($e->getMessage());
	}
    }

    private function logMail($id, Message $mail) {
	$now = new DateTime;
	$subject = $mail->getSubject();
	$headers = $mail->getHeaders();
	if (isset($headers['To'])) {
	    $to = 'To: ' . implode('| ', array_keys($headers['To']));
	} else {
	    $to = '';
	}
	if (isset($headers['Cc'])) {
	    $cc = 'Cc: ' . implode('| ', array_keys($headers['Cc']));
	} else {
	    $cc = '';
	}
	if (isset($headers['Bcc'])) {
	    $bcc = 'Bcc:' . implode('| ', array_keys($headers['Bcc']));
	} else {
	    $bcc = '';
	}
	$this->logger->addInfo(sprintf('SENT ID = %d, TO = %s', $id, $to));
    }

}
