<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\ResponseFormatter;
use CodeIgniter\RESTful\ResourceController;

class Api extends BaseController
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
	    // Rerouting karena ga work
        return $this->assessmentV2();


		try {
			$this->db->transBegin();
			$reqGejala = $this->request->getGet("gejala");
			// return \App\Libraries\ResponseFormatter::success($reqGejala, "Assessment Berhasil");
			$gejala = $this->db->table('gejala')->get()->getResultArray();
			$penyakit = [];
			$kondisi = [];
			$nama = $this->request->getGet('nama');
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
					array_push($kondisi, [
						'id' => $key,
						'nama' => $gejala[$key - 1]['nama'],
						'keparahan' => $penyakit[$key] == 1 ? 'Ya' : 'Tidak',
					]);
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
				'tipe_asma' => convertKeparahan($tipeAsma),
			];

			$this->db->table('kuisioner')->where('id', $kuisioner_id)->update($results);

			if ($this->db->transStatus() === FALSE) {
				$this->db->transRollback();
			} else {
				$this->db->transCommit();
			}
			$results['nama'] = $nama;
			$results['kondisi'] = $kondisi;
			return \App\Libraries\ResponseFormatter::success($results, "Assessment Berhasil");
		} catch (\Throwable $e) {
			$this->db->transRollback();
			return \App\Libraries\ResponseFormatter::error($e->getMessage() ?? 'Terjadi Kesalahan');
		}
	}


    /**
     * Melakukan proses perhitungan, serta melakukan insert data kedalam database
     *
     * @licence https://github.com/kholiqcode/sipasma/blob/main/LICENSE
     * @return ResponseFormatter API Formatter
     */
	public function assessmentV2()
    {
        try {
            $this->db->transBegin();

            $reqGejala = $this->request->getGet("gejala");
            $gejala = $this->db->table('gejala')->get()->getResultArray();

            $kondisi = [];
            $penyakit = [];

            $nama = $this->request->getGet('nama');
            $kuisionerModel = new \App\Models\KuisionerModel();
            $kuisionerModel->insert([
                'nama' => $nama
            ]);

            $kuisioner_id = $kuisionerModel->getInsertID();

            // Membuat objek prediktor untuk menaruh hasil prediksi
            $nb = new NaiveBayes\NaiveBayesPredictor;

            // Mengisi nilai default dari hipotesa
            $nb->isiHipotesa(new NaiveBayes\HipotesaAsmaDefault("1", "AKUT", 0.3, "HIPOTESA"));
            $nb->isiHipotesa(new NaiveBayes\HipotesaAsmaDefault("2", "KRONIS", 0.3, "HIPOTESA"));
            $nb->isiHipotesa(new NaiveBayes\HipotesaAsmaDefault("3", "PERIODIK", 0.4, "HIPOTESA"));

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

            // Merubah koresponeden pasien menjadi nilai boolean
            foreach ($reqGejala as $key => $response)
            {
                $penyakit[$key] = $response == "ya";

                // Menginputkan ke dalam database
                $this->db->table('detail_kuisioner')->insert([
                    'kuisioner_id' => $kuisioner_id,
                    'gejala_id' => $key,
                    'status' => $response === "ya"
                ]);

                array_push($kondisi, [
                    'id' => $key,
                    'nama' => $gejala[$key - 1]['nama'],
                    'keparahan' => $penyakit[$key] == 1 ? 'Ya' : 'Tidak',
                ]);

                $nb->isiInput($key, $response === "ya");
            }

            $kuisioner = $this->db->table('kuisioner')->get()->getResultObject();

            //Melakukan prediksi berdasarkan inputan
            $prediksi = $nb->prediksi();


            // Mencari data terbesar serta indexnya
            $value = max(array_column($prediksi, "prediksi"));
            
            $key = array_search($value, array_column($prediksi, "prediksi"));


            // Membuat formasi baru untuk diupdate kedalam database
            $results = [
                'akut' => $prediksi[0]["prediksi"],
                'kronis' => $prediksi[1]["prediksi"],
                'periodik' => $prediksi[2]["prediksi"],
                'tipe_asma' => $key + 1,
            ];

            // Mengupdate DB
            $this->db->table('kuisioner')->where('id', $kuisioner_id)->update($results);

            if ($this->db->transStatus() === FALSE) {
                $this->db->transRollback();
            } else {
                $this->db->transCommit();
            }
            $results['nama'] = $nama;
            $results['kondisi'] = $kondisi;
            return \App\Libraries\ResponseFormatter::success($results, "Assessment Berhasil");
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return \App\Libraries\ResponseFormatter::error($e->getMessage() ?? 'Terjadi Kesalahan');
        }
    }

}
