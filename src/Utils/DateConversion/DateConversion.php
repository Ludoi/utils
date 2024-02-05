<?php

namespace Ludoi\Utils\DateConversion;

class DateConversion
{
	public static function datetimeToUnixTime(\DateTime $dateTime): int
	{
		return $dateTime->format('U');
	}
}