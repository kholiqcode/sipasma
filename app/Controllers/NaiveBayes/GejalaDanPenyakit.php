<?php


namespace App\Controllers\NaiveBayes;


/**
 * Class GejalaDanPenyakit
 *
 * Kelas untuk menginisialisasi gejala dan penyakit sebagai persyaratan kedalam wadah Assessment
 * @package App\Controllers\NaiveBayes
 *
 */

class GejalaDanPenyakit
{
    /**
     * @var Label untuk setiap gejala
     */
    public $labelGejala;

    /**
     * @var array|mixed Tipe dari gejala
     */
    public $tipe;

    public function __construct($labelGejala, $tipe = [])
    {
        $this->labelGejala = $labelGejala;
        $this->tipe = $tipe;
    }
}