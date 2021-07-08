<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\RESTful\ResourceController;

class Api extends ResourceController
{
	public function __construct()
	{
		helper('custom');
		$this->db     = \Config\Database::connect();
	}
	//Keparahan
	public const AKUT = 1;
	public const KRONIS = 2;
	public const PERIODIK = 3;

	public $akut = 0;
	public $periodik = 0;
	public $kronis = 0;

	public $persentaseAkut = 0;
	public $persentasePeriodik  = 0;
	public $persentaseKronis   = 0;

	public $keparahan = [self::AKUT, self::KRONIS, self::PERIODIK];

	public function gejala()
	{
		try {
			//code...
			$gejala = new \App\Models\GejalaModel();
			$data['gejala'] = $gejala->get()->getResultArray();
			return \App\Libraries\ResponseFormatter::success($data, "Data Gejala Berhasil Diambil");
		} catch (\Exception $e) {
			return \App\Libraries\ResponseFormatter::error($e->getMessage() ?? "Terjadi Kesalahan");
		}
	}

	public function assessment()
	{
		try {
			$this->db->transBegin();
			$reqGejala = $this->request->getPost('gejala');
			$gejala = $this->db->table('gejala')->get()->getResultArray();
			$penyakit = [];
			$kondisi = [];
			$nama = $this->request->getPost('nama');
			$kuisionerModel = new \App\Models\KuisionerModel();
			$kuisionerModel->insert([
				'nama' => $nama
			]);
			$kuisioner_id = $kuisionerModel->getInsertID();
			foreach ($reqGejala as $key => $item) {
				if (in_array($item, ['akut', 'kronis', 'periodik'])) {
					if (strval($item) == "akut") {
						$penyakit[$key] = self::AKUT;
					} else if (strval($item) == "kronis") {
						$penyakit[$key] = self::KRONIS;
					} else if (strval($item) == "periodik") {
						$penyakit[$key] = self::PERIODIK;
					}
					$kondisi[$key] = [
						'id' => $key,
						'nama' => $gejala[$key - 1]['nama'],
						'keparahan' => convertKeparahan($penyakit[$key])
					];
					$this->db->table('detail_kuisioner')->insert([
						'kuisioner_id' => $kuisioner_id,
						'gejala_id' => $key,
						'status' => $penyakit[$key]
					]);
				}
			}

			$kuisioner = $this->db->table('kuisioner')->get()->getResultObject();

			foreach ($kuisioner as $element) {
				switch ($element->tipe_asma) {
					case 1:
						$this->akut++;
						break;
					case 3:
						$this->periodik++;
						break;
					case 2:
						$this->kronis++;
						break;
				}
			}

			$this->persentaseAkut = $this->akut / count($kuisioner);
			$this->persentasePeriodik = $this->periodik / count($kuisioner);
			$this->persentaseKronis = $this->kronis / count($kuisioner);

			//Proses NaiveBayes
			$index1  = 1;
			$ress = [];
			$kemungkinan = [];
			foreach ($this->keparahan as $key1 => $value1) {
				$pElement = [];
				// Start Upper Element
				$index6  = 1;
				foreach ($penyakit as $key3 => $value3) {
					if ($value1 == self::AKUT) {
						$pElement[0][0] = $this->persentaseAkut;
						$pElement[0][$index6] = $value3 === self::AKUT ? doubleval($gejala[$key3 - 1]['akut']) : $this->persentaseAkut;
					} else if ($value1 == self::KRONIS) {
						$pElement[0][0] = $this->persentaseKronis;
						$pElement[0][$index6] = $value3 === self::KRONIS ? doubleval($gejala[$key3 - 1]['kronis']) : $this->persentaseKronis;
					} else {
						$pElement[0][0] = $this->persentasePeriodik;
						$pElement[0][$index6] = $value3 === self::PERIODIK ? doubleval($gejala[$key3 - 1]['periodik']) : $this->persentasePeriodik;
					}
					$index6++;
				}
				// End Upper Element

				// Start Bottom  Element
				$index2  = 1;
				foreach ($this->keparahan as $key4 => $value4) {
					$index3  = 1;
					foreach ($penyakit as $key5 => $value5) {
						if ($value4 == self::AKUT) {
							$pElement[$index2][0] = $this->persentaseAkut;
							$pElement[$index2][$index3] = $value5 === $value4 ? doubleval($gejala[$key5 - 1]['akut']) : $this->persentaseAkut;
						} else if ($value4 == self::KRONIS) {
							$pElement[$index2][0] = $this->persentaseKronis;
							$pElement[$index2][$index3] = $value5 === $value4 ? doubleval($gejala[$key5 - 1]['kronis']) : $this->persentaseKronis;
						} else {
							$pElement[$index2][0] = $this->persentasePeriodik;
							$pElement[$index2][$index3] = $value5 === $value4 ? doubleval($gejala[$key5 - 1]['periodik']) : $this->persentasePeriodik;
						}
						$index3++;
					}
					$index2++;
				}
				// End Bottom  Element

				// Start Upper Element divided by Bottom Element
				$pertama = 0;
				foreach ($pElement[0] as $key6 => $value6) {
					$pertama = $pertama === 0 ?  $value6 :  $value6 * $pertama;
				}

				$pKedua  = 0;
				$index4 = 1;
				foreach ($this->keparahan as $key7 => $value7) {
					$kedua = 0;
					foreach ($pElement[$index4] as $key8 => $value8) {
						$kedua = $kedua === 0 ?  $value8 : $value8 * $kedua;
					}
					$pKedua  = $pKedua + $kedua;
					$index4++;
				}

				if ($value1 == self::AKUT) {
					$kemungkinan['akut'] = intval(($pertama / $pKedua) * 100);
					// echo "Kemungkinan Asma Akut: ($pertama / $pKedua)*100 = " . intval(($pertama / $pKedua) * 100) . "% <br>";
				} else if ($value1 == self::KRONIS) {
					$kemungkinan['kronis'] = intval(($pertama / $pKedua) * 100);
					// echo "Kemungkinan Asma Kronis: ($pertama / $pKedua)*100 = " . intval(($pertama / $pKedua) * 100) . "% <br>";
				} else if ($value1 == self::PERIODIK) {
					$kemungkinan['periodik'] = intval(($pertama / $pKedua) * 100);
					// echo "Kemungkinan Asma Periodik: ($pertama / $pKedua)*100 = " . intval(($pertama / $pKedua) * 100) . "% <br>";
				}
				// End Upper Element divided by Bottom Element
				array_push($ress, $pElement);
				$index1++;
			}
			$terbesar = max([$kemungkinan['akut'], $kemungkinan['kronis'], $kemungkinan['periodik']]);
			$tipeAsma = 0;
			if ($terbesar == $kemungkinan['akut']) {
				$tipeAsma = 1;
			} else if ($terbesar == $kemungkinan['kronis']) {
				$tipeAsma = 2;
			} else if ($terbesar == $kemungkinan['periodik']) {
				$tipeAsma = 3;
			}

			$results = [
				'akut' => $kemungkinan['akut'],
				'kronis' => $kemungkinan['kronis'],
				'periodik' => $kemungkinan['periodik'],
				'tipe_asma' => $tipeAsma,
			];

			$this->db->table('kuisioner')->where('id', $kuisioner_id)->update($results);

			if ($this->db->transStatus() === FALSE) {
				$this->db->transRollback();
			} else {
				$this->db->transCommit();
			}
			$results['kondisi'] = $kondisi;
			return \App\Libraries\ResponseFormatter::success($results, "Assessment Berhasil");
		} catch (\Throwable $e) {
			$this->db->transRollback();
			return \App\Libraries\ResponseFormatter::error($e->getMessage() ?? 'Terjadi Kesalahan');
		}
	}
}
