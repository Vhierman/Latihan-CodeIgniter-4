<?php

namespace App\Models;

use CodeIgniter\Model;

class KomikModel extends Model
{
    //Diisi Dengan nama Table
    protected $table            = 'komik';

    //Dibuat true agar ketika kita melakukan input, update, dan delete akan menyimpan waktu sekarang
    protected $useTimestamps    = true;

    //digunakan jika primary key pada table bukan id. Jika primary key nya id, maka tidak digunakan
    //protected $primarykey = 'id';

    // Untuk memberitahu kedalam model, bahwa ini yang boleh diisi kedalam database
    protected $allowedFields    = ['judul', 'slug', 'penulis', 'penerbit', 'sampul'];

    public function getKomik($slug = false)
    {
        if ($slug == false) {
            return $this->findAll();
        }
        return $this->where(['slug' => $slug])->first();
    }
}
