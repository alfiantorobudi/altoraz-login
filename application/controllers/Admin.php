<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }

    public function index()
    {
        // ngambil data dari user, berdasarkan email yang diinput do form login
        $data['title'] = 'Dashboard Admin';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }

    public function role()
    {
        // Add Role disini gak pakai model, langsung insert di dalam controller index(), bisa juga pakai model di model->tambahrole()

        // ngambil data dari user, berdasarkan email yang diinput do form login
        $data['title'] = 'Role';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // query dulu rolenya

        $data['role'] = $this->db->get('user_role')->result_array();

        // buat validasi untuk add role make form_validation di autoload.php/library

        // buat rules untuk validasi 

        $this->form_validation->set_rules('role', 'Role', 'required');

        if ($this->form_validation->run() == false) {

            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('admin/role', $data);
            $this->load->view('templates/footer');
        } else {
            $this->db->insert('user_role', ['role' => $this->input->post('role')]);

            // set konfirmasi telah diinsert ke database
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">New Role Added! </div>');

            redirect('admin/role');
        }
    }

    public function edit_role($id)
    // edit role make model
    {
        $this->load->model('Admin_model', 'admin');

        $data['title'] = 'Role';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $data['role'] = $this->admin->getRoleById($id);

        $this->form_validation->set_rules('edit_role', 'Role', 'required');

        if ($this->form_validation == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('admin/role', $data);
            $this->load->view('templates/footer');
        } else {

            $this->admin->editRole();

            // set konfirmasi telah diupdate di database
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Role Has Been Updated! </div>');

            redirect('admin/role');
        }
    }

    public function deleteRole($id)
    {
        $this->load->model('Admin_model', 'admin');
        $this->admin->deleteDataRole($id);

        // set konfirmasi telah diupdate di database
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Role Has Been Deleted! </div>');

        redirect('admin/role');
    }

    public function roleAccess($role_id)
    {
        // ngambil data dari user, berdasarkan email yang diinput do form login
        $data['title'] = 'Role Access';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // query dulu rolenya dan menunya agar dapat dipakai di view

        $data['role'] = $this->db->get_where('user_role', ['id' => $role_id])->row_array();

        // id menu 1 tidak tampil, selain itu tampil semua, karena nanti takut uncheck.. jadinya tidak ditampilkan saja
        $this->db->where('id !=', 1);
        $data['menu'] = $this->db->get('user_menu')->result_array();



        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('admin/role-access', $data);
        $this->load->view('templates/footer');
    }

    public function changeAccess()
    {
        // menuId dan roleId diambil dari ajax property data: menuId:menuId di footer 
        $menu_id = $this->input->post('menuId');
        $role_id = $this->input->post('roleId');

        // siapin datanya buat masukin ke querynyaa

        // memasukan $role_id ke 'role_id' agar datanya bisa dipanggil untuk di cek di if dengan num_rows();
        $data = [
            'role_id' => $role_id,
            'menu_id' => $menu_id
        ];

        $result = $this->db->get_where('user_access_menu', $data);

        // cari apakah ada data yang terdiri dari menu dan role id yang sama dari database, kalau tidak ada, maka ditambah (checked), kalau ada maka di apus (uncheck)
        if ($result->num_rows() < 1) {
            $this->db->insert('user_access_menu', $data);
        } else {
            $this->db->delete('user_access_menu', $data);
        }

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Access Has Been Changed</div>');
    }
}
