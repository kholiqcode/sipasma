<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Gejala extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'BIGINT',
				'unsigned' => true,
				'auto_increment' => true
			],
			'nama' => [
				'type' => 'VARCHAR',
				'constraint' => 100
			],
			'akut' => [
				'type' => 'FLOAT',
				'default' => 0,
			],
			'kronis' => [
				'type' => 'FLOAT',
				'default' => 0,
			],
			'periodik' => [
				'type' => 'FLOAT',
				'default' => 0,
			],
		]);

		$this->forge->addPrimaryKey('id', true);
		$this->forge->createTable('gejala', true);
	}

	public function down()
	{
		$this->forge->dropTable('gejala');
	}
}
