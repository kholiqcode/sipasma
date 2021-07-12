<?php


namespace App\Controllers\NaiveBayes;

/**
 * Class NaiveBayesPredictor
 *
 * Kelas yang menampung seluruh inti dan melakukan perhitungan
 * @package App\Controllers\NaiveBayes
 *
 */
class NaiveBayesPredictor
{
    /**
     * @var array Array yang berisi hipotesa awal atau hipetosa dasar
     */
    public $hipotesaBucket;
    /**
     * @var array Array yang berisi gejala
     */
    public $gejalaBucket;
    /**
     * @var array Array yang berisi inputan dari user
     */
    public $input;

    public function __construct()
    {
        $this->hipotesaBucket = [];
        $this->gejalaBucket = [];
        $this->input = [];
    }

    /**
     * Mengisi hipotesa awal dengan properti dari kelas HipotesaAsmaDefault
     * @param HipotesaAsmaDefault $item Obyek HipotesaAsmaDefault yang berisikan properti Hipotesa Dasar
     */
    public function isiHipotesa(HipotesaAsmaDefault $item)
    {
        array_push($this->hipotesaBucket, $item);
    }

    /**
     * Mengisi hipotesa pada setiap gejala berdasarkan properti yang diberikan
     * @param HipotesaAsma $item Obyek HipotesaAsma yang berisikan properti Hipotesa dari masing-masing gejala
     */
    public function isiGejala(HipotesaAsma $item)
    {
        array_push($this->gejalaBucket, $item);
    }

    public function lakukanPrediksiDanPengelompokan()
    {
        $pembilang = [];
        $penyebut = 1;

        $pembilang = $this->filterHipotesa($this->input, $pembilang);

        return $pembilang;
    }

    /**
     * Mengisi input berdasarkan id penyakit dan nilai. Nilai berupa <code>true</code> apabila mengalami gejala, dan <code>false</code> apabila tidak mengalami gejala
     * @param $id_penyakit Id penyakit
     * @param $kondisi kondisi berupa <code>true</code> apabila mengalami gejala, <code>false</code> apabila tidak mengalami gejala
     */
    public function isiInput($id_penyakit, $kondisi)
    {
        array_push($this->input, ["id_penyakit" => $id_penyakit, "gejala_dialami" => $kondisi]);
    }

    /**
     * Melakukan Prediksi
     * @return array Array yang berisikan properti Hipotesa beserta kemungkinannya
     */
    public function prediksi()
    {
        // Membuat angka pembilang
        $pembilang = 0;

        // Melakukan Pengelompokan dan Prediksi
        $prediktor = $this->lakukanPrediksiDanPengelompokan();

        // Menjumlahkan nilai dari seluruh prediksi
        foreach($prediktor as $pPrediktor)
            $pembilang += $pPrediktor["prediksi"];

        $hasil = [];

        //Melakukan pembagian
        foreach($prediktor as $pPrediktor)
        {
            $hitung = $pPrediktor["prediksi"] / $pembilang;
            $bulat = round($hitung, 2);
            array_push($hasil, ["nama_hipotesa" => $pPrediktor["nama_hipotesis"], "prediksi" => $bulat * 100]);
        }

        return $hasil;

    }


    /**
     * Mengelompokkan gejala dan hipotesa dasar
     * @param array $filterPenyakit Array yang berisi gejala
     * @param array $pembilang Array yang berisi pembilang untuk melakukan pembagian
     * @return array Array yang berisi hipotesa akhir
     */
    public function filterHipotesa(array $filterPenyakit, array $pembilang): array
    {
        for ($i = 0; $i < count($this->hipotesaBucket); $i++) {
            $tempPenyakit = 1;
            $tempPenyakit = $this->filterPenyakit($filterPenyakit, $i, $tempPenyakit);
            $tempPenyakit *= $this->hipotesaBucket[$i]->nilai;
            array_push($pembilang, ["nama_hipotesis" => $this->hipotesaBucket[$i]->label, "prediksi" => $tempPenyakit]);
        }
        return $pembilang;
    }

    /**
     * Pengelompokan penyakit dan responden pasien
     * @param array $filterPenyakit Array yang berisi gejala
     * @param int $i Index hipotesa dasar dari perulangan
     * @param $tempPenyakit Nilai dari gejala yang dihitung
     * @return mixed
     */
    public function filterPenyakit(array $filterPenyakit, int $i, $tempPenyakit)
    {
        for ($j = 0; $j < count($filterPenyakit); $j++) {
            if(!$filterPenyakit[$j]["gejala_dialami"])continue;
            $tempPenyakit = $this->filterGejala($filterPenyakit[$j]["id_penyakit"], $i, $tempPenyakit);
        }
        return $tempPenyakit;
    }


    /**
     * Pengelompokkan hipotesa gejala dan hipotesa dasar
     * @param $id Id dari hipotesa gejala
     * @param int $i Index dari hipotesa dasar
     * @param $tempPenyakit Nilai sementara dari gejala yang dihitung
     * @return mixed
     */
    public function filterGejala($id, int $i, $tempPenyakit)
    {
        $isFound = false;
        for ($k = 0; $k < count($this->gejalaBucket); $k++) {
            if ($this->gejalaBucket[$k]->id == $id) {
                for ($l = 0; $l < count($this->gejalaBucket[$k]->tipe); $l++) {
                    if ($this->gejalaBucket[$k]->tipe[$l]->id == $this->hipotesaBucket[$i]->id) {
                        $tempPenyakit *= $this->gejalaBucket[$k]->tipe[$l]->nilai;
                        $isFound = true;
                        break;
                    }
                }
            }
        }
        if (!$isFound)
            $tempPenyakit *= $this->hipotesaBucket[$i]->nilai;
        return $tempPenyakit;
    }

}