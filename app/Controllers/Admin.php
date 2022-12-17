<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelSetting;
use App\Models\ModelAdministrasi;
use App\Models\ModelGeojson;
use App\Models\ModelKv;
use App\Models\ModelFoto;
use Faker\Extension\Helper;

class Admin extends BaseController
{
    protected $ModelSetting;
    protected $ModelAdministrasi;
    protected $ModelGeojson;
    protected $ModelKv;
    public function __construct()
    {
        helper(['url']);
        $this->setting = new ModelSetting();
        $this->Administrasi = new ModelAdministrasi();
        $this->FGeojson = new ModelGeojson();
        $this->kafe = new ModelKv();
        $this->fotoKafe = new ModelFoto();
    }
    public function index()
    {
        $data = [
            'title' => 'JUDUL',
        ];

        return view('admin/tempp', $data);
    }


    public function pending()
    {
        $data = [
            'title' => 'PENDING LIST',
            'tampilKafe' => $this->kafe->callPendingData()->getResult(),
        ];


        return view('admin/pendingList', $data);
    }


    // SETTING MAP VIEW  ===================================================================================


    public function Setting()
    {
        $data = [
            'title' => 'Setting Map View',
            'tampilData' => $this->setting->tampilData()->getResult(),
            'tampilGeojson' => $this->FGeojson->callGeojson()->getResult(),
        ];

        return view('admin/settingMapView', $data);
    }

    public function UpdateSetting()
    {
        // dd($this->request->getVar());
        $data = [
            'id' => 1,
            'nama_web' => $this->request->getPost('nama_web'),
            'coordinat_wilayah' => $this->request->getPost('coordinat_wilayah'),
            'zoom_view' => $this->request->getPost('zoom_view'),
        ];
        $this->setting->updateData($data);
        session()->setFlashdata('alert', 'Data Berhasil disimpan.');
        return $this->response->redirect(site_url('admin/setting'));
    }




    public function table()
    {
        $data = [
            'title' => 'TABLE',
        ];

        return view('admin/table', $data);
    }


    // GEOJSONDATA =======================================================================================


    public function geojson()
    {
        $data = [
            'title' => 'DATA GEOJSON',
            'tampilData' => $this->setting->tampilData()->getResult(),
            'tampilGeojson' => $this->FGeojson->callGeojson()->getResult(),
            'updateGeojson' => $this->FGeojson->callGeojson()->getRow(),
        ];

        return view('admin/geojsonData', $data);
    }

    public function editGeojson($id)
    {
        $data = [
            'title' => 'DATA GEOJSON',
            'updateGeojson' => $this->FGeojson->callGeojson($id)->getRow(),
        ];

        return view('admin/updateGeojson', $data);
    }

    public function tambahGeojson()
    {
        $data = [
            'title' => 'DATA GEOJSON',
        ];

        return view('admin/tambahGeojson', $data);
    }

    // insert data
    public function tambah_Geojson()
    {
        // dd($this->request->getVar());

        // ambil file
        $fileGeojson = $this->request->getFile('Fjson');
        //generate random file name
        $randomName = $fileGeojson->getRandomName();
        $explode = explode('.', $randomName);
        array_pop($explode);
        $randomName = implode('.', $explode);
        $randomName = $randomName . ".geo" . $fileGeojson->getExtension();
        // pindah file to hosting
        $fileGeojson->move(ROOTPATH . 'public/geojson/', $randomName);


        $data = [
            'kode_wilayah' => $this->request->getVar('kodeG'),
            'nama_wilayah'  => $this->request->getVar('Nkec'),
            'geojson'  => $randomName,
            'warna'  => $this->request->getVar('Kwarna'),
        ];

        $addGeojson = $this->FGeojson->addGeojson($data);

        if ($addGeojson) {
            session()->setFlashdata('alert', 'Data Anda Berhasil Ditambahkan.');
            return $this->response->redirect(site_url('/admin/data/geojson'));
        }
    }

    // update data
    public function update_Geojson()
    {

        // dd($this->request->getVar());

        // ambil file name
        $fileGeojson = $this->request->getFile('Fjson');
        // cek file input
        if ($fileGeojson->getError() !== 4) {
            // Jika ada file baru

            // hapus file lama
            $file = $this->request->getVar('jsonLama');
            unlink("geojson/" . $file);
            // ambil file name
            $fileGeojson = $this->request->getFile('Fjson');
            //generate random file name
            $fileGeojsonBaru = $fileGeojson->getRandomName();
            $explode = explode('.', $fileGeojsonBaru);
            array_pop($explode);
            $fileGeojsonBaru = implode('.', $explode);
            $fileGeojsonBaru = $fileGeojsonBaru . ".geojson";
            // pindah file to hosting
            $fileGeojson->move('geojson', $fileGeojsonBaru);
        } else {
            //    Jika tidak ada file baru
            $fileGeojsonBaru = $this->request->getPost('jsonLama');
        }

        $id = $this->request->getVar('id');
        $data = [
            'kode_wilayah' => $this->request->getVar('kodeG'),
            'nama_wilayah'  => $this->request->getVar('Nkec'),
            'warna'  => $this->request->getVar('Kwarna'),
            'geojson'  => $fileGeojsonBaru,
        ];

        $this->FGeojson->updateGeojson($data, $id);
        session()->setFlashdata('alert', 'Data Berhasil Diubah.');
        return $this->response->redirect(site_url('/admin/data/geojson'));
    }

    // delete data
    public function delete_Geojson($id)
    {

        $data = $this->FGeojson->callGeojson($id)->getRow();
        $file = $data->geojson;
        unlink("geojson/" . $file);

        $this->FGeojson->delete(['id' => $id]);
        session()->setFlashdata('alert', "Data Berhasil dihapus.");
        return $this->response->redirect(site_url('/admin/data/geojson'));
    }



    //  FASILITAS KV  ====================================================================================

    public function fasilitas()
    {
        $data = [
            'title' => 'DATA KV',
            'tampilData' => $this->setting->tampilData()->getResult(),
        ];

        return view('admin/fasilitasList', $data);
    }




    //  KV  ====================================================================================

    public function Kafe()
    {
        $data = [
            'title' => 'DATA KV',
            'tampilData' => $this->setting->tampilData()->getResult(),
            'tampilGeojson' => $this->FGeojson->callGeojson()->getResult(),
            'updateGeojson' => $this->FGeojson->callGeojson()->getRow(),
            'tampilKafe' => $this->kafe->callKafe()->getResult(),
            'getFoto' => $this->fotoKafe->getFoto()->getResult(),
        ];

        return view('admin/kafeData', $data);
    }

    public function tambahKafe()
    {

        $data = [
            'title' => 'DATA KV',
            'tampilData' => $this->setting->tampilData()->getResult(),
            'tampilGeojson' => $this->FGeojson->callGeojson()->getResult(),
            'tampilKafe' => $this->kafe->callKafe()->getResult(),
            'provinsi' => $this->kafe->allProvinsi(),
        ];

        return view('admin/tambahKafe', $data);
    }

    public function editKafe($id_kafe)
    {
        $data = [
            'title' => 'DATA KV',
            'tampilData' => $this->setting->tampilData()->getResult(), //ambil settingan mapView
            'tampilGeojson' => $this->FGeojson->callGeojson()->getResult(), //ambil data geojson
            'tampilKafe' => $this->kafe->callKafe($id_kafe)->getRow(),
            'getFoto' => $this->fotoKafe->getFoto($id_kafe)->getResult(),
            'provinsi' => $this->kafe->allProvinsi(),
        ];

        return view('admin/updateKafe', $data);
    }

    // insert data
    public function tambah_Kafe()
    {
        // dd($this->request->getVar());
        $wilayah  = $this->request->getVar('wilayah');
        $wilayah = explode(',', $wilayah);
        $id_kelurahan = $wilayah[0];
        $id_kecamatan = $wilayah[1];
        $id_kabupaten = $wilayah[2];
        $id_provinsi = $wilayah[3];

        $user = user()->username;
        $data = [
            'user' => $user,
            'stat_appv' => $this->request->getVar('stat_appv'),
            'nama_kafe' => $this->request->getVar('nama_kafe'),
            'alamat_kafe'  => $this->request->getVar('alamat_kafe'),
            'coordinate'  => $this->request->getVar('coordinate'),
            'instagram_kafe'  => $this->request->getVar('instagram_kafe'),
            'fasilitas_kafe' => implode(", ", $this->request->getVar('fasil[]')),
            'id_provinsi'  => $id_provinsi,
            'id_kabupaten'  => $id_kabupaten,
            'id_kecamatan'  => $id_kecamatan,
            'id_kelurahan'  => $id_kelurahan,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $addKafe = $this->kafe->addKafe($data);

        $id_foto = $this->kafe->getLastID();
        $files = $this->request->getFiles();
        foreach ($files['foto_kafe'] as $key => $img) {
            if ($img->isValid() && !$img->hasMoved()) {
                $newName = $img->getRandomName();
                $dataF = [
                    'id_kafe' => $id_foto['id_kafe'],
                    'nama_file_foto' => $newName,
                ];
                $this->fotoKafe->addFoto($dataF);
                $img->move(ROOTPATH . 'public/img/kafe', $newName);
            }
        }

        if ($addKafe) {
            session()->setFlashdata('alert', 'Data Anda Berhasil Ditambahkan.');
            return $this->response->redirect(site_url('/admin/data/kafe'));
        }
    }

    // update data
    public function update_Kafe()
    {
        // dd($this->request->getVar());

        $wilayahLama  = $this->request->getVar('wilayahLama');
        $wilayah  = $this->request->getVar('wilayah');
        $id = $this->request->getPost('id');

        if ($wilayah != $wilayahLama) {
            // jika ada berubahan wilayah
            $wilayah = explode(',', $this->request->getVar('wilayah'));
            $id_kelurahan = $wilayah[0];
            $id_kecamatan = $wilayah[1];
            $id_kabupaten = $wilayah[2];
            $id_provinsi = $wilayah[3];
            $data = [
                'id_provinsi'  => $id_provinsi,
                'id_kabupaten'  => $id_kabupaten,
                'id_kecamatan'  => $id_kecamatan,
                'id_kelurahan'  => $id_kelurahan,
            ];
        } else {
            // jika wilayah tetap
        }
        $data = [
            'id_kafe' => $id,
            'nama_kafe' => $this->request->getVar('nama_kafe'),
            'alamat_kafe'  => $this->request->getVar('alamat_kafe'),
            'coordinate'  => $this->request->getVar('coordinate'),
            'instagram_kafe'  => $this->request->getVar('instagram_kafe'),
            // 'fasilitas_kafe' => implode(", ", $this->request->getVar('fasil[]')),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $files = $this->request->getFiles();
        foreach ($files['foto_kafe'] as $key => $img) {
            if ($img->isValid() && !$img->hasMoved()) {
                $newName = $img->getRandomName();
                $dataF = [
                    'id_kafe' => $id,
                    'nama_file_foto' => $newName,
                ];
                $this->fotoKafe->addFoto($dataF);
                $img->move(ROOTPATH . 'public/img/kafe', $newName);
            }
        }


        // var_dump($data);

        $updateKafe = $this->kafe->updateKafe($data, $id);

        if ($updateKafe) {
            session()->setFlashdata('alert', 'Data Anda Berhasil Diupdate.');
            return $this->response->redirect(site_url('/admin/data/kafe'));
        }
    }

    // Delete Data
    public function delete_Kafe($id_kafe)
    {
        $files = $this->fotoKafe->getFoto($id_kafe)->getResult();
        foreach ($files as $img) {
            $file = $img->nama_file_foto;
            unlink("img/kafe/" . $file);
        }

        $this->fotoKafe->where('id_kafe', $id_kafe)->delete('tbl_foto_kafe');
        $this->kafe->delete(['id_kafe' => $id_kafe]);
        session()->setFlashdata('alert', "Data Berhasil dihapus.");
        return $this->response->redirect(site_url('/admin/data/kafe'));
    }

    public function deleteImage()
    {
        // Menerima ID data yang akan dihapus dari permintaan POST
        $id = $this->request->getPost('id');
        $id_kafe = $this->request->getPost('id_kafe');
        $imgData = $this->fotoKafe->getImgRow($id)->getRow();
        // Remove files from the server  
        $file = $imgData->nama_file_foto;
        unlink("img/kafe/" . $file);
        // Delete image data 
        $this->fotoKafe->delete(['id' => $id]);

        // Ambil data gambar dari database
        $imageUrl = $this->fotoKafe->getFoto()->getResult();

        // Kembalikan data ke client dalam format JSON
        return json_encode(['status' => true, 'imageUrl' => $imageUrl]);
    }

    // approve data
    public function approveKafe($id_kafe)
    {
        // dd($this->request->getVar());

        $data = [
            'stat_appv' => '1',
        ];

        $this->kafe->chck_appv($data, $id_kafe);
        session()->setFlashdata('alert', 'Data Approved.');
        return $this->response->redirect(site_url('/admin/pending'));
    }

    public function rejectKafe($id_kafe)
    {

        $data = $this->kafe->callKafe($id_kafe)->getRow();
        $file = $data->foto_kafe;
        unlink("img/sekolah/" . $file);

        $this->kafe->delete(['id_kafe' => $id_kafe]);
        session()->setFlashdata('alert', "Data Berhasil dihapus.");
        return $this->response->redirect(site_url('/admin/data/kafe'));
    }







    public function previewImg($id_kafe)
    {
        $data = [
            'getFoto' => $this->fotoKafe->getFoto($id_kafe)->getResult(),
        ];
        return view('serverSide/previewImg', $data);
    }

    //  SCRAP KAB/KOT, KECAMATAN, KELURAHAN
    // Ajax Remote Wilayah Administrasi
    public function getDataAjaxRemote()
    {
        if ($this->request->isAJAX()) {
            $search = $this->request->getPost('search');
            $results = $this->Administrasi->getDataAjaxRemote($search);
            // var_dump($results);
            if (count($results) > 0) {
                foreach ($results as $row) {
                    $selectajax[] = [
                        'id' => $row['id_kelurahan'] . ", " . $row['id_kecamatan'] . ", " . $row['id_kabupaten'] . ", " . $row['id_provinsi'],
                        'text' => $row['nama_kabupaten'] . ", Kecamatan " . $row['nama_kecamatan'] . ", " . $row['nama_kelurahan'],
                    ];
                };
            }
            // var_dump($selectajax);
            return $this->response->setJSON($selectajax);
        }
    }
    // vardump AjaxRemote
    public function wil()
    {
        $results = $this->Administrasi->Remote();
        if (count($results) > 0) {
            foreach ($results as $row) {
                $selectajax[] = [
                    'id' => $row['id_kelurahan'] . ", " . $row['id_kecamatan'] . ", " . $row['id_kabupaten'],
                    'text' => $row['nama_kabupaten'] . ", Kecamatan " . $row['nama_kecamatan'] . ", " . $row['nama_kelurahan'],
                ];
            };
        }
        print_r($results);
        print_r($selectajax);
    }


    //  SCRAP KAB/KOT, KECAMATAN, KELURAHAN
    public function kabupaten()
    {
        $id_provinsi = $this->request->getPost('id_provinsi');
        $kab = $this->kafe->allKabupaten($id_provinsi);
        echo '<option value="">--Pilih Kab/Kota--</option>';
        foreach ($kab as $key => $value) {
            echo '<option value=' . $value['id_kabupaten'] . '>' . $value['nama_kabupaten'] . '</option>';
        }
    }
    public function kecamatan()
    {
        $id_kabupaten = $this->request->getPost('id_kabupaten');
        $kec = $this->kafe->allKecamatan($id_kabupaten);
        echo '<option value="">--Pilih Kecamatan--</option>';
        foreach ($kec as $key => $value) {
            echo '<option value=' . $value['id_kecamatan'] . '>' . $value['nama_kecamatan'] . '</option>';
        }
    }
    public function kelurahan()
    {
        $id_kecamatan = $this->request->getPost('id_kecamatan');
        $kel = $this->kafe->allKelurahan($id_kecamatan);
        echo '<option value="">--Pilih Desa/Kelurahan--</option>';
        foreach ($kel as $key => $value) {
            echo '<option value=' . $value['id_kelurahan'] . '>' . $value['nama_kelurahan'] . '</option>';
        }
    }
}
