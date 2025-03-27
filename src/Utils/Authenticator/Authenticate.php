<?php
declare(strict_types=1);

namespace Ludoi\Utils\Authenticator;

use Ludoi\Utils\Users\Users;
use Nette;
use Nette\Security\AuthenticationException;
use Nette\Security\Authenticator;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;
use Nette\Utils\Strings;

class Authenticate implements Authenticator, Nette\Security\IdentityHandler
{
	use Nette\SmartObject;

	protected Users $users;

	function __construct(Users $users)
	{
		$this->users = $users;
	}

	function authenticate(string $username, string $password): SimpleIdentity
	{
		$username = Strings::lower($username);
		$row = $this->users->getUser($username);

		if (!$row) {
			throw new AuthenticationException('User not found.');
		}

		$authenticate = new Passwords();
		if (!$authenticate->verify($password, $row->password)) {
			throw new AuthenticationException('Invalid password.');
		}

		$this->users->updateAuthToken($row);
		$this->users->updateLastLoginInRow($row);

		return new SimpleIdentity($row->uname, explode(',', $row->roles), $row->toArray());
	}

	public function sleepIdentity(IIdentity $identity): SimpleIdentity
	{
		$data = $identity->getData();
		return new SimpleIdentity($data[$this->users->getAuthTokenField()]);
	}

	public function wakeupIdentity(IIdentity $identity): ?SimpleIdentity
	{
		$row = $this->users->findOneBy([$this->users->getAuthTokenField() => $identity->getId()]);
		return $row
			? new SimpleIdentity($row->uname, explode(',', $row->roles), (array) $row->toArray())
			: null;
	}
}