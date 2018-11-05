<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
	public function __construct(){
        parent::__construct();
        $this->load->model('m_logbook');
        $this->load->helper('url');
        $this->load->library('session');
    }
	public function index(){
		$this->load->view('admin/header');
		$this->load->view('admin/home');
		$this->load->view('admin/footer');
	}
	public function view($data){
		//radar
		$radar = "radar";
		$kategoriradar = "kategoriradar";
		$getIdRadar = $this->m_logbook->getIdRadar();
		$data1['getIdRadar'] = $getIdRadar;
		foreach ($getIdRadar as $getIdRadar) {
			$id = $getIdRadar->id_radar;
			$x= 'chart'.$id;
			$data1[$x] = $this->m_logbook->pembacaanChartz($id);
		}
		$data1['kategoriradar'] = $this->m_logbook->get($kategoriradar);
		$data1['radar'] = $this->m_logbook->get($radar);
		$data1['kategori_radar'] = $this->m_logbook->getKategoriRadar();
		$data1['tanggal_radar'] = $this->m_logbook->grup_tanggal_radar();
		

//harian Alat dan radar
		if($this->input->get('tanggal')){
			$tgl = $this->input->get('tanggal');
			$data1['harianradar'] = $this->m_logbook->pembacaanJoinByDate($tgl);
			$data1['tgl'] = $tgl;
		}
		if($this->input->get('edit')){
			$tgl = $this->input->get('edit');
			$data1['editharianradar'] = $this->m_logbook->pembacaanJoinByDate($tgl);
			$data1['tgl'] = $tgl;
		}
		$this->load->view('admin/header');
		$this->load->view('admin/view/'.$data, $data1);
		$this->load->view('admin/footer');	
		
		
	}


	public function cek($data){
		//CEK RADAR
		$data1['haloradar'] = $this->m_logbook->cekTodayRadar();
		if($data1['haloradar'] == 0){
			$this->session->set_flashdata('message_harian_radar', '
				<div class="alert alert-danger alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong>Peringatan!</strong> Anda Belum Mengisi Data Radar hari ini!
				</div>');
		}else{
			$this->session->set_flashdata('message_harian_radar', '
				<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong>Terima Kasih</strong> Hari Ini sudah Input Data Radar Hari Ini, jika ada kesalahakan input langsung ubah dan klik <strong>Update</strong> di bagian bawah.
				</div>');
		}
		$kategoriradar = "kategoriradar";
		$radar = "radar";
		$data1['pembacaan'] = $this->m_logbook->getWhere('pembacaan', array('tanggal' => date('Y-m-d')));
		$data1['radar'] = $this->m_logbook->get($radar);
		$data1['pembacaanjoin'] = $this->m_logbook->pembacaanJoin();
		$data1['kategoriradar'] = $this->m_logbook->get($kategoriradar);
		$data1['kategoriradar'] = $this->m_logbook->getKategoriRadar();
		
		$data1['radar'] = $this->m_logbook->get('radar');
		$this->load->view('admin/header');
		$this->load->view('admin/cek/'.$data, $data1);
		$this->load->view('admin/footer');
	}
	//RADAR
	public function tambah_kategori_radar(){
		$namakategoriradar = $this->input->post('namakategoriradar');
		$input = array(
			'nama_kategori' => $namakategoriradar
		);
		$this->m_logbook->tambahKategoriRadar($input);
		header('location: view/radar');
		$this->session->set_flashdata('message', '
			<div class="alert alert-success alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong>Success!</strong> Kategori Radar Baru ditambahkan.
			</div>');
	}
	public function updatekategoriradar(){
		$id_kategoriradar = $this->input->post('idkategoriradar');
		$nama_kategoriradar = $this->input->post('namakategoriradar');
		$where = array(
			'id_kategori' => $id_kategoriradar
		);
		$data = array(
			'nama_kategori' => $nama_kategoriradar
		);
		if(empty($nama_kategoriradar)){
			header('location: view/radar');
		}else{
			$this->session->set_flashdata('message', '
				<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong>Success!</strong> Update Data Radar Kategori Alat.
				</div>');
			$this->m_logbook->update_data($where,$data,'kategoriradar');
			header('location: view/radar');
		}
	}
	public function tambahradar(){
		$id = $this->input->post('id_kategori_radar');
		$namaradar = $this->input->post('nama_radar');
		$standar = $this->input->post('standar');
		$data = array(
			'id_kategoriradar' => $id,
			'nama_radar' => $namaradar,
			'standar' => $standar
		);
		$this->session->set_flashdata('message', '
			<div class="alert alert-success alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong>Success!</strong> Radar Baru Ditambahkan.
			</div>');
		$this->m_logbook->tambahRadar($data);
		header('location: view/radar');
	}
	public function updateRadar(){
		$id_radar = $this->input->post('id_radar');
		$nama_radar = $this->input->post('nama_radar');
		$standar = $this->input->post('standar');
		$where = array(
			'id_radar' => $id_radar
			
		);
		$data = array(
			'nama_radar' => $nama_radar,
			'standar' => $standar
		);
		if(empty($nama_radar)){
			header('location: view/radar');
		}else{
			$this->m_logbook->update_data($where,$data,'radar');
			$this->session->set_flashdata('message', '
				<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong>Success!</strong>  Data Radar Berhasil di update.
				</div>');
			header('location: view/radar');
		}
	}
	public function hapusKategoriRadar($data){
		$this->m_logbook->hapusKategoriRadar($data);
		$this->session->set_flashdata('message', '
			<div class="alert alert-success alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong>Success!</strong> Kategori Radar Berhasil di Hapus.
			</div>');
		header('location: ../view/radar');
	}
	public function hapusRadar($data){
		$this->m_logbook->hapusRadar($data);
		$this->session->set_flashdata('message', '
			<div class="alert alert-success alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong>Success!</strong> Data Radar berhasil di Hapus .
			</div>');
		header('location: ../view/radar');
	}
	public function cekharianradar()
	{
		
		$data = $this->m_logbook->get('radar');
		$date = date('Y-m-d');
		foreach ($data as $radar) {
			$var = "pembacaan".$id = $radar->id_radar;
			$pembacaan = $this->input->post($var);
			if(empty($pembacaan)){
				$pembacaan = "-";
			}
			$data = array(
				'id_radar' => $id,
				'tanggal' => $date,
				'pembacaan' => $pembacaan
			);
			$table='pembacaan';			
			$this->m_logbook->inputHarian($table, $data);
			$this->session->set_flashdata('message_harian_sukses', '
				<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong>Success!</strong> Data Radar Agroklimat Berhasil di Input.
				</div>');
			header('location: cek/cek_radar');
		}
	}
	public function updateharianradar()
	{
		$data = $this->m_logbook->get('radar');
		$date = date('Y-m-d');
		foreach ($data as $radar) {
			$var = "pembacaan".$id = $radar->id_radar;
			$var1 = "id_pembacaan".$radar->id_radar;
			$pembacaan = $this->input->post($var);
			$id_pembacaan = $this->input->post($var1);
			if(empty($pembacaan)){
				$pembacaan = "-";
			}
			//echo $id_pembacaan."-".$pembacaan."<br>";
			$where = array(
				'id_pembacaan' => $id_pembacaan, 
				'tanggal' => $date
			);
			$data = array(
				'pembacaan' => $pembacaan
			);
			$this->m_logbook->update_data($where,$data,'pembacaan');
			$this->session->set_flashdata('message_harian_sukses', '
				<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong>Success!</strong> Data Radar Agroklimat Berhasil di Update.
				</div>');
			header('location: cek/cek_radar');
		}
	}
	public function updateharianradarByTanggal(){
			$data = $this->m_logbook->get('radar');
			$date = date('Y-m-d');
			foreach ($data as $radar) {
				$var = "pembacaan".$id = $radar->id_radar;
				$var1 = "id_pembacaan".$radar->id_radar;
				$var2 = "tanggal".$radar->id_radar;
				$pembacaan = $this->input->post($var);
				$id_pembacaan = $this->input->post($var1);
				$tanggal = $this->input->post($var2);
			if(empty($pembacaan)){
				$pembacaan = "-";
			}
			$where = array(
				'id_pembacaan' => $id_pembacaan
			);
			$data = array(
				'pembacaan' => $pembacaan
			);
			
			$this->m_logbook->update_data($where,$data,'pembacaan');
			$this->session->set_flashdata('message_harian_sukses', '
				<div class="alert alert-success alert-dismissible">
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
				<strong>Success!</strong> Data Radar Agroklimat Berhasil di Update.
				</div>');
			header('location: view/tampil_data_radar');
		}
	}
			
}