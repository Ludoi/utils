<?php
declare(strict_types=1);
/*
   Copyright (C) 2022 Luděk Bednarz

   Project: utils
   Author:  Luděk Bednarz
*/


namespace Ludoi\Utils\Uuid;


class Uuid
{
	public function uuidToBin(string $uuid): string {
		return pack("H*", str_replace('-', '', $uuid));
	}

	public function binToUuid(string $binary): string {
		$string = unpack("H*", $binary);
		return preg_replace("/([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})/", "$1-$2-$3-$4-$5", $string);
	}
}