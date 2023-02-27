<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ModelSetting;
use App\Models\ModelAdministrasi;
use App\Models\ModelGeojson;
use App\Models\ModelKv;
use App\Models\ModelFoto;
use App\Models\ModelUser;
use Faker\Extension\Helper;

class Admin extends BaseController
{
    protected $ModelSetting;
    protected $ModelUser;
    protected $ModelAdministrasi;
    protected $ModelGeojson;
    protected $ModelKv;
    protected $ModelFoto;
    public function __construct()
    {
        helper(['url']);
        $this->setting = new ModelSetting();
        $this->user = new ModelUser();
        $this->Administrasi = new ModelAdministrasi();
        $this->FGeojson = new ModelGeojson();
        $this->kafe = new ModelKv();
        $this->fotoKafe = new ModelFoto();
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard',
            'countAllKafe' => $this->kafe->countAllKafe(),
            'countAllPending' => $this->kafe->countAllPending(),
            'countAllUser' => $this->user->countAllUser(),
            'userMonth' => $this->user->userMonth()->getResult(),
            'tampilKafe' => $this->kafe->getFiveKafe()->getResult(),
        ];
        // echo '<pre>';
        // print_r($data['tampilKafe']);
        // die;
        return view('admin/dashboard', $data);
    }

    public function iii()
    {
        $data = [
            'title' => 'JUDUL',
        ];
        $opening_hours = [
            "Monday" => ["09:00", "17:00"],
            "Tuesday" => ["09:00", "17:00"],
            "Wednesday" => ["09:00", "17:00"],
            "Thursday" => ["09:00", "17:00"],
            "Friday" => ["09:00", "17:00"],
            "Saturday" => ["10:00", "16:00"],
            "Sunday" => ["Closed"],
        ];
        $amountOfDays = count($opening_hours);
        $arrayKeys = array_keys($opening_hours);

        for ($dayCount = 0; $dayCount < $amountOfDays; $dayCount++) {
            $DayAmountOfConsecutiveSameHours = 1;
            while (isset($arrayKeys[($dayCount + $DayAmountOfConsecutiveSameHours)]) && ($opening_hours[$arrayKeys[$dayCount]] === $opening_hours[$arrayKeys[($dayCount + $DayAmountOfConsecutiveSameHours)]]))
                $DayAmountOfConsecutiveSameHours++;

            if ($DayAmountOfConsecutiveSameHours > 1)
                $result[$arrayKeys[$dayCount] . " - " . $arrayKeys[($dayCount + $DayAmountOfConsecutiveSameHours - 1)]] = $opening_hours[$arrayKeys[$dayCount]];
            else
                $result[$arrayKeys[$dayCount]] = $opening_hours[$arrayKeys[$dayCount]];

            $dayCount += ($DayAmountOfConsecutiveSameHours - 1);
        }
        // print_r($result);
        // die;
        return view('admin/tempp', $data);
    }

    public function tes()
    {
        $data = [
            'title' => 'Lihat Peta',
            'tampilData' => $this->setting->tampilData()->getResult(),
            'tampilKafe' => $this->kafe->tes()->getResultArray(),
        ];
        echo '<pre>';
        print_r($data['tampilKafe']);
        die;
        return view('page/viewMap', $data);
    }

    public function dump()
    {
        // dd($this->request->getVar());
        $data = [
            'title' => 'DUMP',
            'tampilKafe' => $this->kafe->callKafe()->getResult(),
            'getFoto' => $this->fotoKafe->getFoto()->getResult(),
        ];
        $dump = $this->kafe->callKafe();
        echo '<pre>';
        print_r($dump);
        die;

        return view('admin/tempp', $data);
    }


    public function table()
    {
        $data = [
            'title' => 'TABLE',
        ];

        return view('admin/table', $data);
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
        $fileGeojson->move('geojson/', $randomName);


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
            $fileGeojson->move('geojson/', $fileGeojsonBaru);
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
        $todayDate = date("m/d");

        $data = [
            'title' => 'DATA KV',
            'tampilData' => $this->setting->tampilData()->getResult(),
            'tampilGeojson' => $this->FGeojson->callGeojson()->getResult(),
            'updateGeojson' => $this->FGeojson->callGeojson()->getRow(),
            'tampilKafe' => $this->kafe->callKafe(),
        ];
        // echo '<pre>';
        // print_r($data['tampilKafe']);
        // die;
        return view('admin/kafeData', $data);
    }

    public function tambahKafe()
    {
        $data = [
            'title' => 'DATA KV',
            'tampilData' => $this->setting->tampilData()->getResult(),
            'tampilGeojson' => $this->FGeojson->callGeojson()->getResult(),
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
            'tampilKafe' => $this->kafe->callKafe($id_kafe),
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

        $user = user_id();
        $data = [
            'nama_kafe' => $this->request->getVar('nama_kafe'),
            'alamat_kafe'  => $this->request->getVar('alamat_kafe'),
            'longitude'  => $this->request->getVar('longitude'),
            'latitude'  => $this->request->getVar('latitude'),
            'instagram_kafe'  => $this->request->getVar('instagram_kafe'),
            'fasilitas_kafe' => implode(", ", $this->request->getVar('fasil[]')),
            'id_provinsi'  => $id_provinsi,
            'id_kabupaten'  => $id_kabupaten,
            'id_kecamatan'  => $id_kecamatan,
            'id_kelurahan'  => $id_kelurahan,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        // var_dump($data);
        // die;
        $addKafe = $this->kafe->addKafe($data);
        $insert_id = $this->db->insertID();
        $status = [
            'id_kafe' => $insert_id,
            'stat_appv' => $this->request->getVar('stat_appv'),
            'user' => $user,
        ];
        $addStatus = $this->kafe->addStatus($status);
        $files = $this->request->getFiles();
        foreach ($files['foto_kafe'] as $key => $img) {
            if ($img->isValid() && !$img->hasMoved()) {
                $newName = $img->getRandomName();
                $dataF = [
                    'id_kafe' => $insert_id,
                    'nama_file_foto' => $newName,
                ];
                $this->fotoKafe->addFoto($dataF);
                $img->move('img/kafe/', $newName);
            }
        }

        $opens = $this->request->getVar('open-time[]');
        $open = [];
        foreach ($opens as $item) {
            if ($item == '') {
                $open[] = null;
            } else {
                $open[] = $item;
            }
        }
        // print_r($open);
        $closes = $this->request->getVar('close-time[]');
        $close = [];
        foreach ($closes as $item) {
            if ($item == '') {
                $close[] = null;
            } else {
                $close[] = $item;
            }
        }
        // print_r($close);
        $day = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Mengambil id kafe
        $id = [];
        foreach ($open as $key) {
            $id[] = $insert_id;
        }
        // print_r($id);
        $datas = [
            'kafe_id' => $id,
            'hari' => $day,
            'open_time' => $open,
            'close_time' => $close
        ];
        $data = [];
        $i = 0;
        foreach ($datas as $key => $val) {
            $i = 0;
            foreach ($val as $k => $v) {
                $data[$i][$key] = $v;
                $i++;
            }
        }
        $addTime = $this->kafe->addTime($data);
        if ($addKafe && $addTime) {
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
        $id_kafe = $this->request->getPost('id');

        if ($wilayah != $wilayahLama) {
            // jika ada berubahan wilayah
            $wilayah = explode(',', $this->request->getVar('wilayah'));
            $id_kelurahan = $wilayah[0];
            $id_kecamatan = $wilayah[1];
            $id_kabupaten = $wilayah[2];
            $id_provinsi = $wilayah[3];
            $data = [
                'id_kafe' => $id_kafe,
                'id_provinsi'  => $id_provinsi,
                'id_kabupaten'  => $id_kabupaten,
                'id_kecamatan'  => $id_kecamatan,
                'id_kelurahan'  => $id_kelurahan,
                'nama_kafe' => $this->request->getVar('nama_kafe'),
                'alamat_kafe'  => $this->request->getVar('alamat_kafe'),
                'longitude'  => $this->request->getVar('longitude'),
                'latitude'  => $this->request->getVar('latitude'),
                'instagram_kafe'  => $this->request->getVar('instagram_kafe'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        } else {
            // jika wilayah tetap
            $data = [
                'id_kafe' => $id_kafe,
                'nama_kafe' => $this->request->getVar('nama_kafe'),
                'alamat_kafe'  => $this->request->getVar('alamat_kafe'),
                'longitude'  => $this->request->getVar('longitude'),
                'latitude'  => $this->request->getVar('latitude'),
                'instagram_kafe'  => $this->request->getVar('instagram_kafe'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }
        $updateKafe = $this->kafe->updateKafe($data, $id_kafe);
        // echo '<pre>';
        // var_dump($data);

        $opens = $this->request->getVar('open-time[]');
        $open = [];
        foreach ($opens as $item) {
            if ($item == '') {
                $open[] = null;
            } else {
                $open[] = $item;
            }
        }
        // print_r($open);
        $closes = $this->request->getVar('close-time[]');
        $close = [];
        foreach ($closes as $item) {
            if ($item == '') {
                $close[] = null;
            } else {
                $close[] = $item;
            }
        }
        // print_r($close);
        $day = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

        // Mengambil id kafe
        $id = [];
        foreach ($open as $key) {
            $id[] = $id_kafe;
        }
        // print_r($id);
        $datas = [
            'kafe_id' => $id,
            'hari' => $day,
            'open_time' => $open,
            'close_time' => $close
        ];
        $data = [];
        $i = 0;
        foreach ($datas as $key => $val) {
            $i = 0;
            foreach ($val as $k => $v) {
                $data[$i][$key] = $v;
                $i++;
            }
        }
        foreach ($data as $time) {
            $update = $time;
            $hari = $update['hari'];
            $updateTime = $this->kafe->updateTime($update, $id_kafe, $hari);
        }
        // var_dump($update);
        // die;

        $files = $this->request->getFiles();
        foreach ($files['foto_kafe'] as $key => $img) {
            if ($img->isValid() && !$img->hasMoved()) {
                $newName = $img->getRandomName();
                $dataF = [
                    'id_kafe' => $id_kafe,
                    'nama_file_foto' => $newName,
                ];
                $this->fotoKafe->addFoto($dataF);
                $img->move('img/kafe/', $newName);
            }
        }
        if ($updateKafe && $updateTime) {
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
        $this->kafe->delete(['id_kafe' => $id_kafe]);
        session()->setFlashdata('alert', "Data Berhasil dihapus.");
        return $this->response->redirect(site_url('/admin/data/kafe'));
    }

    // side server delete image
    public function deleteImage()
    {
        // Menerima ID data yang akan dihapus dari permintaan POST
        $id = $this->request->getPost('id');
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

    // Pending Data
    public function pending()
    {
        $data = [
            'title' => 'PENDING LIST',
            'tampilKafe' => $this->kafe->callPendingData(),
        ];
        return view('admin/pendingList', $data);
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

    // reject data
    public function rejectKafe($id_kafe)
    {
        $data = $this->kafe->callKafe($id_kafe)->getRow();
        $file = $data->foto_kafe;
        unlink("img/sekolah/" . $file);

        $this->kafe->delete(['id_kafe' => $id_kafe]);
        session()->setFlashdata('alert', "Data Berhasil dihapus.");
        return $this->response->redirect(site_url('/admin/data/kafe'));
    }



    // side server preview image
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
