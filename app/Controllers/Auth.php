<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
	public function login()
	{
		return view('pages/auth/login');
	}

	public function process(){
		$request = $this->request->getVar();
		try {
			if (!$this->validate([
				'email' => [
					'rules' => 'required|valid_email',
					'errors' => [
						'required' => '{field} Harus diisi',
						'valid_email' => 'Format Email Harus Valid'
					]
				],
				'password' => [
					'rules' => 'required',
					'errors' => [
						'required' => '{field} Harus diisi'
					]
				],
			])) {
				session()->setFlashdata('error', $this->validator->getError() ?? 'Terjadi Kesalahan');
				return redirect()->back()->withInput();;
			}
			$userModel = new UserModel();
			$dataUser = $userModel->where([
				'email' => $request['email'],
			])->first();
			if ($dataUser) {
				if (password_verify($request['password'], $dataUser['password'])) {
					session()->set([
						'nama' => $dataUser['nama'],
						'email' => $dataUser['email'],
						'logged_in' => TRUE
					]);
					return redirect()->to(base_url());
				}
			}
			session()->setFlashdata('error', 'Username & Password Salah');
			return redirect()->back()->withInput();;
		} catch (\Throwable $e) {
			//throw $th;
			session()->setFlashdata('error', $e->getMessage() ??'Username & Password Salah');
			return redirect()->back()->withInput();;
		}
	}

	public function logout(){
		session()->destroy();
		return redirect()->to('/auth/login');
	}
}
