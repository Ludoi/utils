<?php
declare(strict_types=1);

namespace Ludoi\Utils\Table;

use Nette;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\InvalidStateException;

/**
 * Reprezentuje repozitář pro databázovou tabulku
 */
abstract class Table
{
	use Nette\SmartObject;

	/** @var Explorer */
	protected Explorer $connection;

	/** @var string */
	protected string $tableName = '';

	protected array $cache = [];

	protected array $insert = [];

	protected int $objectDoesNotExist = 0;

	/**
	 * @param Explorer $db
	 */
	public function __construct(Explorer $db)
	{
		$this->connection = $db;

		if ($this->tableName == '') {
			$class = get_class($this);
			throw new InvalidStateException("Název tabulky musí být definován v $class::\$tableName.");
		}
	}

	/**
	 * Vrací všechny záznamy z databáze
	 * @return Selection
	 */
	public function findAll(): Selection
	{
		return $this->getTable();
	}

	/**
	 * Vrací celou tabulku z databáze
	 * @return Selection
	 */
	protected function getTable(): Selection
	{
		return $this->connection->table($this->tableName);
	}

	/**
	 * To samé jako findBy akorát vrací vždy jen jeden záznam
	 * @param array $by
	 * @return ActiveRow|null
	 */
	public function findOneBy(array $by): ?ActiveRow
	{
		return $this->findBy($by)->limit(1)->fetch();
	}

	/**
	 * Vrací vyfiltrované záznamy na základě vstupního pole
	 * (pole array('name' => 'David') se převede na část SQL dotazu WHERE name = 'David')
	 * @param array $by
	 * @return Selection
	 */
	public function findBy(array $by): Selection
	{
		return $this->getTable()->where($by);
	}

	/**
	 * Vrací záznam s daným primárním klíčem
	 * @param int $id
	 * @return ActiveRow|null
	 */
	public function find(mixed $id, string $columns = ''): ?ActiveRow
	{
		if ($columns === '') {
			return $this->getTable()->get($id);
		} else {
			return $this->getTable()->select($columns)->get($id);
		}
	}

	public function countBy(array $by): int
	{
		return $this->getTable()->where($by)->count('*');
	}

	public function insert(array $data): bool|int|ActiveRow
	{
		return $this->getTable()->insert($data);
	}

	public function insertMass(array $data): void
	{
		$this->insert[] = $data;
	}

	public function save(): void
	{
		if (count($this->insert) > 0) {
			$this->insert($this->insert);
			$this->insert = [];
		}
	}

	public function update($data): void
	{
		$this->getTable()->update($data);
	}

	protected function getDatabase(): Explorer
	{
		return $this->connection;
	}

	public function getObject(mixed $id): ActiveRow
	{
		if (isset($this->cache[$id])) {
			$object = $this->cache[$id];
		} else {
			$object = $this->find($id, '*');
			$this->cache[$id] = $object;
		}
		if (is_null($object)) {
			$this->raiseException($this->objectDoesNotExist);
		}
		return $object;
	}

	public function checkExist($id): void
	{
		$object = $this->find($id);
		if (is_null($object)) {
			$this->raiseException($this->objectDoesNotExist);
		}
	}

	public function optimizeTable(): void
	{
		$this->connection->query("OPTIMIZE TABLE {$this->tableName}");
	}

	protected function raiseException(int $exception): void
	{

	}
}