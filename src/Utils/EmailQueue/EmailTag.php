<?php
declare(strict_types=1);


namespace Ludoi\Utils\EmailQueue;

use Ludoi\Utils\Table\Table;

final class EmailTag extends Table
{
	protected string $tableName = 'emailtag';
}