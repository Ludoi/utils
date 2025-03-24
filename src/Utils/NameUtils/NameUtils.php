<?php
declare(strict_types=1);


namespace Ludoi\Utils\NameUtils;

use Nette\Database\Row;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\Strings;
use Vokativ\Name;

class NameUtils
{
	static public function getName(?string $firstname, ?string $lastname): ?string
	{
		if (!is_null($firstname) && !is_null($lastname)) {
			$name = Strings::trim($firstname) . ' ' . Strings::trim($lastname);
		} elseif (!is_null($firstname)) {
			$name = Strings::trim($firstname);
		} elseif (!is_null($lastname)) {
			$name = Strings::trim($lastname);
		} else {
			$name = '';
		}
		return $name;
	}

	static public function getVocative(?string $firstname): string
	{
		if (!is_null($firstname)) {
			$transform = new Name();
			return Strings::capitalize($transform->vokativ($firstname));
		} else {
			return 'závodníku';
		}
	}

	static private function maskText(string $text): string {
		if (Strings::length($text) > 2) {
			$result = Strings::substring($text, 0, 1) . Strings::padLeft('', Strings::length($text) - 2, '*') .
				Strings::substring($text, Strings::length($text) - 1, 1);
		} else {
			$result = Strings::padLeft('', Strings::length($text), '*');
		}
		return $result;
	}

	static public function maskEmail(?string $email): string
	{
		if (is_null($email) || $email === '') {
			return 'žádný email není k dispozici';
		} else {
			$emailParts = explode('@', $email);
			$emailParts[0] = self::maskText($emailParts[0]);
			if (isset($emailParts[1])) {
				$domainParts = explode('.', $emailParts[1]);
				$domainParts[0] = self::maskText($domainParts[0]);
				$emailParts[1] = implode('.', $domainParts);
			}
			return implode('@', $emailParts);
		}
	}

	static public function getNameFromRow(ActiveRow|Row|null $row): ?string
	{
		if (!is_null($row))
		{
			return self::getName($row->firstname, $row->lastname);
		} else {
			return '';
		}
	}

	static public function getNicknameFromRow(?ActiveRow $row): ?string
	{
		if (!is_null($row))
		{
			if ($row->nickname !== '')
			{
				return $row->nickname;
			} else {
				return self::getName($row->firstname, $row->lastname);
			}
		} else {
			return '';
		}
	}
}