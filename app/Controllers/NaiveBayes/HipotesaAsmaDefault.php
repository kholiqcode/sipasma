<?php


namespace App\Controllers\NaiveBayes;

/**
 * Class HipotesaAsmaDefault
 *
 * Kelas yang berfungsi untuk menampung properti Hipotesa dasar, bukan hipotesa yang ada dalam gejala
 * @package App\Controllers\NaiveBayes
 *
 */
class HipotesaAsmaDefault extends Hipotesa
{
    public function __construct($id, $label, $nilai, $tipe)
    {
        $this->label = $label;
        $this->nilai = $nilai;
        $this->id = $id;
        $this->tipe = $tipe;
    }
}