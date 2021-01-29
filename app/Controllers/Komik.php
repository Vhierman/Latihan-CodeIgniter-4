<?php

namespace App\Controllers;

use App\Models\KomikModel;

class Komik extends BaseController
{
    //Harus Digunakan agar komikModel Bisa Dipakai Di class Komik dan class turunannya
    protected $komikModel;
    //Agar Semua Method Bisa Menggunakan komikModel
    public function __construct()
    {
        $this->komikModel = new KomikModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Daftar Komik',
            'komik' => $this->komikModel->getKomik()
        ];

        return view('komik/index', $data);
    }

    public function detail($slug)
    {
        $data = [
            'title' => 'Detail Komik',
            'komik' => $this->komikModel->getKomik($slug)
        ];


        //jika komik tidak ada di tabel
        if (empty($data['komik'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul Komik' . $slug . 'Tidak Ditemukan.');
        }
        //jika ada

        return view('komik/detail', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Form Tambah Data Komik',
            'validation' => \Config\Services::validation()
        ];

        return view('komik/create', $data);
    }


    public function save()
    {

        //Validasi Input
        if (!$this->validate([
            'judul'         => [
                'rules'         => 'required|is_unique[komik.judul]',
                'errors'        => [
                    'required'  => '{field} komik harus diisi',
                    'is_unique' => '{field} komik sudah terdaftar'
                ]
            ],
            'penulis'       => [
                'rules'         => 'required',
                'errors'        => [
                    'required'  => '{field} penulis harus diisi'
                ]
            ],
            'penerbit'      => [
                'rules'         => 'required',
                'errors'        => [
                    'required'  => '{field} penerbit harus diisi'
                ]
            ],
            'sampul'        => [
                'rules'         => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png,]',
                'errors'        => [
                    'max_size' => 'Ukuran Gambar Terlalu Besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            //Mengambil Pesan Kesalahan
            // $validation = \Config\Services::validation();
            //Redirect Ke Halaman create
            // return redirect()->to('/komik/create')->withInput()->with('validation', $validation);
            return redirect()->to('/komik/create')->withInput();
        }


        //Ambil Gambar
        $fileSampul     = $this->request->getFile('sampul');
        // Apakah tidak ada gambar yang di upload
        // ==4 artinya adalah apabila tidak ada gambar yang di upload 
        if ($fileSampul->getError() == 4) {
            $namaSampul = 'no_document.jpg';
        } else {
            //Generate Nama Image Random
            $namaSampul     = $fileSampul->getRandomName();
            //Pindah File Ke Folder
            $fileSampul->move('img', $namaSampul);
        }


        //Untuk membuat slug dari url/judul agar ramah terhadap SEO.
        $slug = url_title($this->request->getVar('judul'), '-', true);
        //Save Ke Dalam Database
        $this->komikModel->save([
            'judul'     => $this->request->getVar('judul'),
            'slug'      => $slug,
            'penulis'   => $this->request->getVar('penulis'),
            'penerbit'  => $this->request->getVar('penerbit'),
            'sampul'    => $namaSampul
        ]);
        //Menampilkan Pesan Berhasil
        session()->setFlashdata('pesan', 'Data Berhasil Ditambahkan.');
        //Redirect ke dalam Halaman Komik
        return redirect()->to('/komik');
    }

    //delete
    public function delete($id)
    {
        //Cari Gambar Berdasarkan ID
        $komik = $this->komikModel->find($id);
        //Cek Jika File Gambar Default Jangan Di Hapus
        if ($komik['sampul'] != 'no_document.jpg') {
            //Hapus Gambar Di Folder
            unlink('img/' . $komik['sampul']);
            $this->komikModel->delete($id);
        } else {
            //Menghapus Data Di Database
            $this->komikModel->delete($id);
        }

        //Menampilkan Pesan
        //Menampilkan Pesan Berhasil
        session()->setFlashdata('pesan', 'Data Berhasil Dihapus.');
        //Redirect
        return redirect()->to('/komik');
    }

    public function edit($slug)
    {
        $data = [
            'title' => 'Form Edit Data Komik',
            'validation' => \Config\Services::validation(),
            'komik' => $this->komikModel->getKomik($slug)
        ];

        return view('komik/edit', $data);
    }

    public function update($id)
    {
        //cek judul
        $komiklama = $this->komikModel->getKomik($this->request->getVar('slug'));
        if ($komiklama['judul'] == $this->request->getVar('judul')) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[komik.judul]';
        }

        //Validasi Input
        if (!$this->validate([
            'judul'         => [
                'rules'         => $rule_judul,
                'errors'        => [
                    'required'  => '{field} komik harus diisi',
                    'is_unique' => '{field} komik sudah terdaftar'
                ]
            ],
            'penulis'         => [
                'rules'         => 'required',
                'errors'        => [
                    'required'  => '{field} penulis harus diisi'
                ]
            ],
            'penerbit'         => [
                'rules'         => 'required',
                'errors'        => [
                    'required'  => '{field} penerbit harus diisi'
                ]
            ],
            'sampul'        => [
                'rules'         => 'max_size[sampul,1024]|is_image[sampul]|mime_in[sampul,image/jpg,image/jpeg,image/png,]',
                'errors'        => [
                    'max_size' => 'Ukuran Gambar Terlalu Besar',
                    'is_image' => 'Yang anda pilih bukan gambar',
                    'mime_in' => 'Yang anda pilih bukan gambar'
                ]
            ]
        ])) {
            //Redirect Ke Halaman edit
            return redirect()->to('/komik/edit/' . $this->request->getVar('slug'))->withInput();
        }

        //Mengambil File Sampul
        $fileSampul  = $this->request->getFile('sampul');

        //Cek Gambar Apakah Tetap Gambar Lama
        if ($fileSampul->getError() == 4) {
            $namaSampul = $this->request->getVar('sampulLama');
        } else {
            //Generate Nama File Random
            $namaSampul = $fileSampul->getRandomName();
            //PIndahkan Gambar
            $fileSampul->move('img', $namaSampul);
            //Hapus File Lama
            unlink('img/' . $this->request->getVar('sampulLama'));
        }


        //Untuk membuat slug dari url/judul agar ramah terhadap SEO.
        $slug = url_title($this->request->getVar('judul'), '-', true);
        //Save Ke Dalam Database
        $this->komikModel->save([
            'id'        => $id,
            'judul'     => $this->request->getVar('judul'),
            'slug'      => $slug,
            'penulis'   => $this->request->getVar('penulis'),
            'penerbit'  => $this->request->getVar('penerbit'),
            'sampul'    => $namaSampul
        ]);
        //Menampilkan Pesan Berhasil
        session()->setFlashdata('pesan', 'Data Berhasil Diupdate.');
        //Redirect ke dalam Halaman Komik
        return redirect()->to('/komik');
    }

    //--------------------------------------------------------------------

}
