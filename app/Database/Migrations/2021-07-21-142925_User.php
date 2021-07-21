<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class User extends Migration
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
			'email' => [
				'type' => 'VARCHAR',
				'constraint' => 100
			],
			'password' => [
				'type' => 'VARCHAR',
				'constraint' => 255
			],
		]);

		$this->forge->addPrimaryKey('id', true);
		$this->forge->createTable('user', true);
	}

	public function down()
	{
		$this->forge->dropTable('user');
	}
}
