<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Gejala extends BaseController
{

	public function __construct()
	{
		$this->gejala = new \App\Models\GejalaModel();
	}

	public function index()
	{
		$data['gejala'] = $this->gejala->get()->getResultArray();
		return view('pages/gejala/list', $data);
	}

	public function create()
	{
		return view('pages/gejala/create');
	}

	public function store()
	{
		try {
			if (!$this->validate([
				'nama' => [
					'rules' => 'required',
					'label' => 'Nama Gejala',
					'errors' => [
						'required' => '{field} Harus diisi'
					]
				],
				'akut' => [
					'rules' => 'required',
					'label' => 'KnowledgeBase AKut',
					'errors' => [
						'required' => '{field} Harus diisi'
					]
				],
				'kronis' => [
					'rules' => 'required',
					'label' => 'KnowledgeBase Kronis',
					'errors' => [
						'required' => '{field} Harus diisi'
					]
				],
				'periodik' => [
					'rules' => 'required',
					'label' => 'KnowledgeBase Periodik',
					'errors' => [
						'required' => '{field} Harus diisi'
					]
				],
			])) {
				return $this->responseFormatter->error($this->validator->getError() ?? 'Terjadi Kesalahan');
			}

			$gejala = $this->gejala->insert([
				'nama' => $this->request->getPost('nama'),
				'akut' => doubleval($this->request->getPost('akut')),
				'kronis' => doubleval($this->request->getPost('kronis')),
				'periodik' => doubleval($this->request->getPost('periodik')),
			]);
			if ($gejala) {
				return $this->responseFormatter->success([], "Berhasil Menambahkan Data Gejala");
			} else {
				return $this->responseFormatter->error('Gagal Menyimpan Data Gejala');
			}
		} catch (\Exception $e) {
			return $this->responseFormatter->error($e->getMessage() ?? 'Terjadi Kesalahan');
		}
	}

	public function edit($id)
	{
		try {
			//code...
			$data['gejala'] = $this->gejala->find($id);
			return view('pages/gejala/create', $data);
		} catch (\Exception $e) {
			//throw $th;
			return redirect()->back();
		}
	}

	public function update($id)
	{
		try {
			if (!$this->validate([
				'nama' => [
					'rules' => 'required',
					'label' => 'Nama Gejala',
					'errors' => [
						'required' => '{field} Harus diisi'
					]
				],
				'akut' => [
					'rules' => 'required',
					'label' => 'KnowledgeBase AKut',
					'errors' => [
						'required' => '{field} Harus diisi'
					]
				],
				'kronis' => [
					'rules' => 'required',
					'label' => 'KnowledgeBase Kronis',
					'errors' => [
						'required' => '{field} Harus diisi'
					]
				],
				'periodik' => [
					'rules' => 'required',
					'label' => 'KnowledgeBase Periodik',
					'errors' => [
						'required' => '{field} Harus diisi'
					]
				],
			])) {
				return $this->responseFormatter->error($this->validator->getError() ?? 'Terjadi Kesalahan');
			}

			$gejala = $this->gejala->update($id, [
				'nama' => $this->request->getPost('nama'),
				'akut' => doubleval($this->request->getPost('akut')),
				'kronis' => doubleval($this->request->getPost('kronis')),
				'periodik' => doubleval($this->request->getPost('periodik')),
			]);
			if ($gejala) {
				return $this->responseFormatter->success([], "Berhasil Memperbarui Data Gejala");
			} else {
				return $this->responseFormatter->error('Gagal Menyimpan Data Gejala');
			}
		} catch (\Exception $e) {
			return $this->responseFormatter->error($e->getMessage() ?? 'Terjadi Kesalahan');
		}
	}

	public function delete($id)
	{
		try {

			if ($this->gejala->delete($id)) {
				return $this->responseFormatter->success([], "Berhasil Menghapus Data Gejala");
			} else {
				return $this->responseFormatter->error('Gagal Menghapus Data Gejala');
			}
		} catch (\Exception $e) {
			return $this->responseFormatter->error($e->getMessage() ?? 'Gagal Menghapus Data Gejala');
		}
	}
}
