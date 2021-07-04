<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Kuisioner extends Migration
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
				'null' => true,
			],
			'kronis' => [
				'type' => 'FLOAT',
				'default' => 0,
				'null' => true,
			],
			'periodik' => [
				'type' => 'FLOAT',
				'default' => 0,
				'null' => true,
			],
			'tipe_asma' => [
				'type' => 'TINYINT',
				'unsigned' => true,
				'constraint' => 1,
				'default' => 0,
				'null' => true,
				'comment' => '1=Akut,2=Kronis,3=Periodik',
			],
		]);

		$this->forge->addPrimaryKey('id', true);
		$this->forge->createTable('kuisioner', true);
	}

	public function down()
	{
		$this->forge->dropTable('kuisioner');
	}
}
