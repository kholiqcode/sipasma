<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DetailKuisioner extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'BIGINT',
				'unsigned' => true,
				'auto_increment' => true
			],
			'kuisioner_id' => [
				'type' => 'BIGINT',
				'unsigned' => true,
			],
			'gejala_id' => [
				'type' => 'BIGINT',
				'unsigned' => true,
			],
			'status' => [
				'type' => 'TINYINT',
				'unsigned' => true,
				'constraint' => 1,
				'default' => 0,
			],
		]);

		$this->forge->addPrimaryKey('id', true);
		$this->forge->addForeignKey('gejala_id', 'gejala', 'id', 'CASCADE', 'CASCADE');
		$this->forge->addForeignKey('kuisioner_id', 'kuisioner', 'id', 'CASCADE', 'CASCADE');
		$this->forge->createTable('detail_kuisioner', true);
	}

	public function down()
	{
		$this->forge->dropTable('detail_kuisioner');
	}
}
