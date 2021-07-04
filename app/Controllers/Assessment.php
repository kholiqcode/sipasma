<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Assessment extends BaseController
{
	public function __construct()
	{
		helper('custom');
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

	public function index()
	{
		$gejala = new \App\Models\GejalaModel();
		$data['gejala'] = $gejala->get()->getResultArray();
		return view('pages/assessment/create', $data);
	}

	public function store()
	{
		// dd($this->request->getPost());
		try {
			if (!$this->validate([
				'nama' => [
					'rules' => 'required',
					'label' => 'Nama',
					'errors' => [
						'required' => '{field} Harus diisi'
					]
				],
			])) {
				return $this->responseFormatter->error($this->validator->getError() ?? 'Terjadi Kesalahan');
			}
			$this->db->transBegin();
			$gejala = $this->request->getPost('gejala');

			$kuisioner_id = $this->processAssessment($gejala);
			if ($this->db->transStatus() === FALSE) {
				$this->db->transRollback();
			} else {
				$this->db->transCommit();
			}
			return redirect()->to('assessment/result/' . $kuisioner_id);
		} catch (\Exception $e) {
			$this->db->transRollback();
			return $this->responseFormatter->error($e->getMessage() ?? 'Terjadi Kesalahan');
		}
	}

	public function processAssessment($reqGejala)
	{
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
				if ($item == "akut") {
					$penyakit[$key] = self::AKUT;
				} else if ($item == "kronis") {
					$penyakit[$key] = self::KRONIS;
				} else if ($item == "periodik") {
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
		$this->db->table('kuisioner')->where('id', $kuisioner_id)->update([
			'akut' => $kemungkinan['akut'],
			'kronis' => $kemungkinan['kronis'],
			'periodik' => $kemungkinan['periodik'],
			'tipe_asma' => $tipeAsma,
		]);

		return $kuisioner_id;
	}

	public function list(){
		$kuisionerModel = new \App\Models\KuisionerModel();
		$data['kuisioner'] = $kuisionerModel->findAll();

		return view('pages/assessment/list',$data);
	}

	public function result($id)
	{
		$kuisioner = new \App\Models\KuisionerModel();
		$gejalaModel = new \App\Models\GejalaModel();
		$gejala = $gejalaModel->findAll();
		$detailKuisionerModel = new \App\Models\DetailKuisionerModel();
		$detailKuisioner = $detailKuisionerModel->where('kuisioner_id', $id)->findAll();
		if (count($detailKuisioner) == 0) return redirect()->back();
		foreach ($detailKuisioner as $key => $item) {
			$kondisi[$item['id']] = [
				'id' => $item['id'],
				'nama' => $gejala[$item['gejala_id'] - 1]['nama'],
				'keparahan' => convertKeparahan($item['status'])
			];
		}

		$data = [
			'kondisi' => $kondisi,
			'kemungkinan' => $kuisioner->find($id)
		];

		return view('pages/assessment/result', $data);
	}
}
