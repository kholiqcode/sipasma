<?php


namespace App\Controllers\NaiveBayes;

/**
 * Class Hipotesa
 *
 * Root class untuk Hipotesa maupun Gejala. Satu properti untuk semua
 * @package App\Controllers\NaiveBayes
 *
 */
abstract class Hipotesa
{
    /**
     * @var id dari setiap hipotesa dasar maupun hipotesa gejala
     */
    public $id;

    /**
     * @var tipe dari setiap hipotesa dasar maupun hipotesa gejala.
     * Bernilai <code>HIPOTESA</code> menandakan hipotesa dasar,
     * dan <code>HIPOTESA_VALUE</code> menandakan hipotesa gejala
     */
    public $tipe;

    /**
     * @var label dari setiap hipotesa dasar maupun hipotesa gejala
     */
    public $label;

    /**
     * @var nilai dasar dari setiap hipotesa dasar maupun hipotesa gejala
     */
    public $nilai;
}