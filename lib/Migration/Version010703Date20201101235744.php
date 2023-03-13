<?php

declare(strict_types=1);

namespace OCA\Weather\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version010703Date20201101235744 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
		/** @var ISchemaWrapper $schema */
		$schema = $schemaClosure();

		if (!$schema->hasTable('weather_city')) {
			$table = $schema->createTable('weather_city');
			$table->addColumn('id', 'bigint', [
				'autoincrement' => true,
				'notnull' => true,
				'length' => 10,
			]);
			$table->addColumn('name', 'string', [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn('user_id', 'string', [
				'notnull' => true,
				'length' => 255,
			]);
			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable('weather_config')) {
			$table = $schema->createTable('weather_config');
			$table->addColumn('user', 'string', [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn('key', 'string', [
				'notnull' => true,
				'length' => 255,
			]);
			$table->addColumn('value', 'string', [
				'notnull' => false,
				'length' => 4000,
			]);
		}
		return $schema;
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
	}
}
