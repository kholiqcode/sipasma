<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	public function run()
	{
		$data = [
			[
				'nama' => 'Batuk',
				'akut' => 0.83,
				'kronis' => 0.88,
				'periodik' => 0.67,
			],
			[
				'nama' => 'Kadang-kadang batuk',
				'akut' => 0.17,
				'kronis' => 0.25,
				'periodik' => 0.83,
			],
			[
				'nama' => 'Bunyi napas (mengi)',
				'akut' => 0.83,
				'kronis' => 0.75,
				'periodik' => 0.83,
			],
			[
				'nama' => 'Sesak tiba-tiba',
				'akut' => 0.83,
				'kronis' => 1,
				'periodik' => 0.83,
			],
			[
				'nama' => 'Intensitas sesak napas yang berat',
				'akut' => 0.33,
				'kronis' => 0.5,
				'periodik' => 0.5,
			],
			[
				'nama' => 'Dada terasa berat',
				'akut' => 0,
				'kronis' => 0.5,
				'periodik' => 0.17,
			],
			[
				'nama' => 'Gelisah',
				'akut' => 0.5,
				'kronis' => 0.38,
				'periodik' => 0.67,
			],
			[
				'nama' => 'Sesak kambuh-kambuh',
				'akut' => 0.67,
				'kronis' => 0.38,
				'periodik' => 0.5,
			],
			[
				'nama' => 'Intensitas sesak napas dari ringan hingga sedang',
				'akut' => 0.33,
				'kronis' => 0.5,
				'periodik' => 0.17,
			],
			[
				'nama' => 'Terkadang mengi terkadang tidak',
				'akut' => 0.17,
				'kronis' => 0.25,
				'periodik' => 0.83,
			],
			[
				'nama' => 'Sesak napas kambuh karena udara kotor dan berdebu',
				'akut' => 0,
				'kronis' => 1,
				'periodik' => 0,
			],
		];

		$this->db->table('gejala')->insertBatch($data);

		$dataKuiosiner = [
			[
				'nama' => 'A',
				'tipe_asma' => 1,
			],
			[
				'nama' => 'A',
				'tipe_asma' => 2,
			],
			[
				'nama' => 'A',
				'tipe_asma' => 2,
			],
			[
				'nama' => 'A',
				'tipe_asma' => 1,
			],
			[
				'nama' => 'A',
				'tipe_asma' => 2,
			],
			[
				'nama' => 'A',
				'tipe_asma' => 1,
			],
			[
				'nama' => 'A',
				'tipe_asma' => 3,
			],
			[
				'nama' => 'A',
				'tipe_asma' => 3,
			],
			[
				'nama' => 'A',
				'tipe_asma' => 3,
			],
			[
				'nama' => 'A',
				'tipe_asma' => 3,
			],
			
		];

		$this->db->table('kuisioner')->insertBatch($dataKuiosiner);
	}
}
