<?php

namespace App\Models;

use CodeIgniter\Model;

class OrangModel extends Model
{
    //Diisi Dengan nama Table
    protected $table            = 'orang';

    //Dibuat true agar ketika kita melakukan input, update, dan delete akan menyimpan waktu sekarang
    protected $useTimestamps    = true;

    //digunakan jika primary key pada table bukan id. Jika primary key nya id, maka tidak digunakan
    //protected $primarykey = 'id';

    // Untuk memberitahu kedalam model, bahwa ini yang boleh diisi kedalam database
    protected $allowedFields    = ['nama', 'alamat'];

    //Searching
    public function search($keyword)
    {
        // $builder = $this->table('orang');
        // $builder->like('nama', $keyword);
        // return  $builder;
        return  $this->table('orang')->like('nama', $keyword)->orLike('alamat', $keyword);
    }
}
