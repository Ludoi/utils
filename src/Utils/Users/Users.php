<?php
declare(strict_types=1);


namespace Ludoi\Utils\Users;

use Ludoi\Utils\NameUtils\NameUtils;
use Ludoi\Utils\Table\Table;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Security\Passwords;
use Nette\Utils\DateTime;
use Nette\Utils\Random;
use Nette\Utils\Strings;

/**
 * Model starající se o tabulku Users
 */
class Users extends Table
{
	/** @var string */
	protected string $tableName = 'users';
	protected string $authTokenField = 'authtoken';

	public const FORGOTTEN_VALIDITY = 3600;
	public const PASSWORD_LENGTH = 20;
	public const ACTIVATION_LENGTH = 80;

	protected int $objectDoesNotExist = 0;

	public function __construct(Explorer $db)
	{
		parent::__construct($db);
	}

	public function getUser(string $user): ?ActiveRow
	{
		$username = $this->normalizeUserName($user);
		return $this->find($username);
	}

	public function getUserByAuthToken(string $authToken): ?ActiveRow
	{
		return $this->findOneBy([$this->authTokenField => $authToken]);
	}

	public function updateAuthToken(ActiveRow $user): void
	{
		$lastLoginTo = DateTime::from('-60 days');
		if (is_null($user->authtoken) || $user->authtoken == '' || $user->last_login < $lastLoginTo) {
			$user->update([$this->authTokenField => Random::generate(30)]);
		}
	}

	public function setPassword(string $user, string $password): void
	{
		$username = $this->normalizeUserName($user);
		$now = new DateTime;
		$pwd = new Passwords();
		$hash = $pwd->hash($password);
		$this->find($username)->update(['password' => $hash, 'forgotten' => false,
			'forgotten_requested' => null, 'initial' => null, 'updated' => $now]);
	}

	public function activate(string $user, bool $activate): void
	{
		$username = $this->normalizeUserName($user);
		$now = new DateTime;
		$this->find($username)->update(['active' => $activate, 'updated' => $now]);
	}

	public function forgottenPassword(string $user): string
	{
		$username = $this->normalizeUserName($user);
		$now = new DateTime;
		$initial = Random::generate(self::ACTIVATION_LENGTH);
		$this->find($username)->update(['forgotten' => true, 'forgotten_requested' => $now,
			'initial' => $initial]);
		return $initial;
	}

	public function createUser(string $user, string $name, string $surname, string $email, string $role,
							   array $additionalData, bool $direct = true): string
	{
		$username = $this->normalizeUserName($user);
		$now = new DateTime;
		$password = Random::generate(self::PASSWORD_LENGTH);
		$pwd = new Passwords();
		$hash = $pwd->hash($password);
		$data = ['uname' => $username, 'firstname' => $name,
			'lastname' => $surname, 'email' => Strings::lower($email),
			'created' => $now, 'password' => $hash, 'active' => true,
			'roles' => Strings::lower($role)];
		foreach ($additionalData as $name => $value) {
			$data[$name] = $value;
		}
		if ($direct) {
			$this->insert($data);
		} else {
			$this->insertMass($data);
		}
		return $password;
	}

	public function createTemporaryUser(array $additionalData): array
	{
		$username = 'user-' . Random::generate(10);
		$username = $this->normalizeUserName($username);
		$now = new DateTime;
		$initial = Random::generate(self::ACTIVATION_LENGTH);
		$activation = Random::generate(self::ACTIVATION_LENGTH);
		$data = ['uname' => $username,
			'created' => $now, 'password' => '', 'active' => false,
			'temporary' => true, 'initial' => $initial,
			'activation' => $activation];
		foreach ($additionalData as $field => $user) {
			$data[$field] = $user;
		}
		$this->insert($data);
		return ['uname' => $username, 'initial' => $initial, 'activation' => $activation];
	}

	public function createNewPassword(string $user): ?string
	{
		$user = $this->normalizeUserName($user);
		$row = $this->find($user);
		if (!is_null($row)) {
			$password = Random::generate(20);
			$pwd = new Passwords();
			$hash = $pwd->hash($password);
			$row->update(['password' => $hash]);
		} else {
			$password = null;
		}
		return $password;
	}

	public function checkRecover(string $initial): ?ActiveRow
	{
		$now = new DateTime();
		$user = $this->findOneBy(['initial' => $initial]);
		if (!is_null($user) && $user->active && $user->forgotten) {
			$diff = abs($now->getTimestamp() - $user->forgotten_requested->getTimestamp());
			if ($diff > self::FORGOTTEN_VALIDITY) {
				$user = null;
			}
		} else {
			$user = null;
		}
		return $user;
	}

	public function checkActivation(string $initial, string $activation): ?ActiveRow
	{
		$now = new DateTime();
		$user = $this->findOneBy(['initial' => $initial]);
		if (is_null($user) || $user->activation !== $activation || !$user->temporary) {
			$user = null;
		}
		return $user;
	}

	public function activateUser(string $temporaryUname, string $uname, string $password): ?ActiveRow
	{
		$temporaryUname = $this->normalizeUserName($temporaryUname);
		$uname = $this->normalizeUserName($uname);
		$user = $this->find($temporaryUname);
		if (!is_null($user) && $user->temporary) {
			$now = new DateTime();
			$data = $user->toArray();
			$data['uname'] = $uname;
			$pwd = new Passwords();
			$data['password'] = $pwd->hash($password);
			$data['initial'] = null;
			$data['activation'] = null;
			$data['created'] = $now;
			$data['updated'] = $now;
			$data['temporary'] = false;
			$data['active'] = true;
			$newUser = $this->insert($data);
			$user->delete();
			return $newUser;
		} else {
			return null;
		}
	}

	public function updateLastLogin(string $uname): void
	{
		$this->find($this->normalizeUserName($uname))?->update(['last_login' => new DateTime()]);
	}

	public function updateLastLoginInRow(ActiveRow $row): void
	{
		$row->update(['last_login' => new DateTime()]);
	}

	public function normalizeUserName(string $uname): string
	{
		return Strings::lower(Strings::trim($uname));
	}

	public function getName(string $uname): string
	{
		$user = $this->getUser($uname);
		return is_null($user)?'':NameUtils::getNameFromRow($user);
	}

	public function getEmail(string $uname): string
	{
		$user = $this->getUser($uname);
		return is_null($user)?'':$user->email;
	}

	public function getNames(array $usernames): array {
		$result = [];
		$users = $this->findBy(['uname' => $usernames]);
		foreach ($users as $user) {
			$result[$user->uname] = NameUtils::getNameFromRow($user);
		}
		return $result;
	}

	public function getAuthTokenField(): string
	{
		return $this->authTokenField;
	}
}