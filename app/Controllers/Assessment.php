<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Controllers\NaiveBayes;

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
			$gejala = $this->request->getVar('gejala');

			//$kuisioner_id = $this->processAssessment($gejala);

            $kuisioner_id = $this->postProcessDB($gejala);

			if ($this->db->transStatus() === FALSE) {
				$this->db->transRollback();
			} else {
				$this->db->transCommit();
			}
			return redirect()->to('assessment/result/' . $kuisioner_id);

            // return $this->result($kuisioner_id);

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
			if (in_array($item, ['ya', 'tidak'])) {
				if ($item == "ya") {
					$penyakit[$key] = 1;
				} else if ($item == "tidak") {
					$penyakit[$key] = 0;
				}
				$kondisi[$key] = [
					'id' => $key,
					'nama' => $gejala[$key - 1]['nama'],
					'keparahan' => $penyakit[$key] == 1 ? 'Ya' : 'Tidak',
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
					$pElement[0][$index6] = doubleval($gejala[$key3 - 1]['akut']);
				} else if ($value1 == self::KRONIS) {
					$pElement[0][0] = $this->persentaseKronis;
					$pElement[0][$index6] = doubleval($gejala[$key3 - 1]['kronis']);
				} else {
					$pElement[0][0] = $this->persentasePeriodik;
					$pElement[0][$index6] = doubleval($gejala[$key3 - 1]['periodik']);
				}
				$index6++;
			}
			// End Upper Element

			// Start Bottom  Element
			$index2  = 1;
			foreach ($this->keparahan as $key4 => $value4) {
				$index3  = 1;
				foreach ($penyakit as $key5 => $value5) {
					if ($value4 == self::AKUT && $value5 == 1) {
						$pElement[$index2][0] = $this->persentaseAkut;
						$pElement[$index2][$index3] = doubleval($gejala[$key5 - 1]['akut']);
					} else if ($value4 == self::KRONIS && $value5 == 1) {
						$pElement[$index2][0] = $this->persentaseKronis;
						$pElement[$index2][$index3] = doubleval($gejala[$key5 - 1]['kronis']);
					} else if ($value4 == self::PERIODIK && $value5 == 1) {
						$pElement[$index2][0] = $this->persentasePeriodik;
						$pElement[$index2][$index3] = doubleval($gejala[$key5 - 1]['periodik']);
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
				echo "Kemungkinan Asma Akut: ($pertama / $pKedua)*100 = " . intval(($pertama / $pKedua) * 100) . "% <br>";
			} else if ($value1 == self::KRONIS) {
				$kemungkinan['kronis'] = intval(($pertama / $pKedua) * 100);
				echo "Kemungkinan Asma Kronis: ($pertama / $pKedua)*100 = " . intval(($pertama / $pKedua) * 100) . "% <br>";
			} else if ($value1 == self::PERIODIK) {
				$kemungkinan['periodik'] = intval(($pertama / $pKedua) * 100);
				echo "Kemungkinan Asma Periodik: ($pertama / $pKedua)*100 = " . intval(($pertama / $pKedua) * 100) . "% <br>";
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

	public function list()
	{
		$kuisionerModel = new \App\Models\KuisionerModel();
		$data['kuisioner'] = $kuisionerModel->findAll();

		return view('pages/assessment/list', $data);
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
				'keparahan' => isMengalamiGejala($item['status'])
			];
		}

		$data = [
			'kondisi' => $kondisi,
			'kemungkinan' => $kuisioner->find($id)
		];

		return view('pages/assessment/result', $data);
	}


    /**
     * Melakukan perhitungan
     *
     * @licence https://github.com/kholiqcode/sipasma/blob/main/LICENSE
     * @param array $gejalaResponse Array yang berisikan hasil dari responden
     * @param int $kuisionerID ID kuisioner yang akan diupdate
     * @return array Array yang berisikan hasil kalkulasi
     */
    protected function processAssessmentV2($gejalaResponse, $kuisionerID)
    {
        // Membuat objek prediktor untuk menaruh hasil prediksi
        $nb = new NaiveBayes\NaiveBayesPredictor;

        // Mengisi nilai default dari hipotesa
        $nb->isiHipotesa(new NaiveBayes\HipotesaAsmaDefault("1", "AKUT", 0.3, "HIPOTESA"));
        $nb->isiHipotesa(new NaiveBayes\HipotesaAsmaDefault("2", "KRONIS", 0.3, "HIPOTESA"));
        $nb->isiHipotesa(new NaiveBayes\HipotesaAsmaDefault("3", "PERIODIK", 0.4, "HIPOTESA"));

        // Mendapatkan Gejala
        $gejalaModel = new \App\Models\GejalaModel();
        $gejala = $gejalaModel->findAll();

        foreach ($gejala as $gGejala)
        {
            // Membuat objek untuk menampung properti dari setuiap gejala
            $hipotesaAsma = new NaiveBayes\HipotesaAsma;

            // Labelling pada setiap gejala dan memberikan id yang sesuai
            $hipotesaAsma->label = $gGejala["nama"];
            $hipotesaAsma->id = $gGejala["id"];

            // Memasukkan input nilai dan mengelompokkannya pada setiap hipotesa
            $hipotesaDB = [
                new NaiveBayes\HipotesaAsmaDefault("1", "AKUT", $gGejala["akut"], "HIPOTESA_VALUE"),
                new NaiveBayes\HipotesaAsmaDefault("2", "KRONIS", $gGejala["kronis"], "HIPOTESA_VALUE"),
                new NaiveBayes\HipotesaAsmaDefault("3", "PERIODIK", $gGejala["periodik"], "HIPOTESA_VALUE")
            ];

            // Assign tipe berdasarkan pengelompokkan
            $hipotesaAsma->tipe = $hipotesaDB;

            // Isi gejala ini ke dalam wadah primer
            $nb->isiGejala($hipotesaAsma);
        }


        // Mengulang setiap iterasi input, mengubahnya kedalam format boolean
        foreach ($gejalaResponse as $key => $response)
        {
			if (in_array($response, ['ya', 'tidak'])) {
            $this->db->table('detail_kuisioner')->insert([
                'kuisioner_id' => $kuisionerID,
                'gejala_id' => $key,
                'status' => $response === "ya"
            ]);

            $nb->isiInput($gejala[$key-1], $response === "ya");
		}
        }

        //Melakukan prediksi berdasarkan inputan
        return $nb->prediksi();
    }

    /**
     * Melakukan proses perhitungan, serta melakukan insert data kedalam database
     *
     * @licence https://github.com/kholiqcode/sipasma/blob/main/LICENSE
     * @param array $gGejala Array yang berisikan hasil dari responden
     * @return int id kuisioner yang telah dibuat sebelumnya, digunakan dalam mengupdate data yang akan mendatang
     */
    protected function postProcessDB($gGejala)
    {
        $nama = $this->request->getPost('nama');
        $kuisionerModel = new \App\Models\KuisionerModel();
        $kuisionerModel->insert([
            'nama' => $nama
        ]);
        $kuisioner_id = $kuisionerModel->getInsertID();

        // Mengambil data kalkulasi
        $hasilKalkulasi = $this->processAssessmentV2($gGejala, $kuisioner_id);


        // Mencari data terbesar dan indexnya
        $value = max(array_column($hasilKalkulasi, "prediksi"));

        $key = array_search($value, array_column($hasilKalkulasi, "prediksi"));


        // Mengupdate data
        $this->db->table('kuisioner')->where('id', $kuisioner_id)->update([
            'akut' => $hasilKalkulasi[0]["prediksi"],
            'kronis' => $hasilKalkulasi[1]["prediksi"],
            'periodik' => $hasilKalkulasi[2]["prediksi"],
            'tipe_asma' => $key + 1,
        ]);

        return $kuisioner_id;
    }
}
