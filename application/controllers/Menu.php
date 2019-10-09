<?php
// defined(BASEPATH) agar tidak bisa diakses langsung dari url
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends CI_Controller
{
    public function __construct()
    {
        // bisa menggunakan seperti ini, tapi hanya session yang dicek, apakah ada session atau tidak, jadi kurang tepat, maka harus membuat function sendiri (sebagai function helper, di autoload.php)dengan nama is_logged_in() yang dapat dipanggil di manapun, yang di instance dengan get_instance() dari function CI

        parent::__construct();
        // if (!$this->session->userdata('email')) {
        //     redirect('auth');
        // }


        // cara pembuatan helper -> ke folder helper -> 
        // 1. buat file dengan nama xxx_helper.php (selalu diakhiri dengan _helper) 

        // 2. config/autoload/helper/xxx 
        // ditambahkan file xxx didalam autoload/helper

        // 3. buat function e.g is_logged_in()

        // 4. kemudian di instance CI dengan get_instance() di dalam variabel e.g $ci di function is_logged_in() agar dikenal function $sessionnya 

        // cek dengan menggunakan function sendiri is_logged_in()
        is_logged_in();
    }

    public function index()
    // Add Menu gak pakai model, langsung insert di dalam controller index(), bisa juga pakai model di model->tambahmenu()
    {
        $data['title'] = 'Menu Management';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // query untuk get menu dari database, agar $menu bisa dipakai
        $data['menu'] = $this->db->get('user_menu')->result_array();

        // validasi add menu, make form_validation di autoload.php/library

        // buat rules untuk validasi menu 
        $this->form_validation->set_rules('menu', 'Menu', 'required');

        if ($this->form_validation->run() == false) {

            // menampilkan view
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('templates/footer');
        } else {
            $this->db->insert('user_menu', ['menu' => $this->input->post('menu')]);

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            New Menu Added! </div>');
            redirect('menu');
        }
    }

    public function edit($id)
    // edit role make model
    {
        $this->load->model('Menu_model', 'menu');

        $data['title'] = 'Menu Management';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // ambil data menu berdasarkan id di menu model, agar bisa dipakai di view
        $data['menu'] = $this->menu->getMenuById($id);

        $this->form_validation->set_rules('editmenu', 'Menu', 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/index', $data);
            $this->load->view('templates/footer');
        } else {

            $this->menu->editMenu();

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Menu Has Been Updated! </div>');
            redirect('menu');
        }
    }

    public function delete($id)
    {
        $this->load->model('Menu_model', 'menu');
        $this->menu->deleteMenu($id);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Menu has been deleted! </div>');

        redirect('menu');
    }

    public function submenu()
    {
        // authentication title dan user
        $data['title'] = 'Submenu Management';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // query submenu harus di join untuk mendapatkan nama menu dari table user_menu
        // make model untuk melakukan join agar rapih

        // load model 
        $this->load->model('Menu_model', 'menu');

        // masukin data ke submenu dari model/Menu_model/getSubmenu() dan get data untuk menu agar bisa dipakai di view
        $data['submenu'] = $this->menu->getSubmenu();
        $data['menu'] = $this->db->get('user_menu')->result_array();

        // buat rules untuk validasi submenu 
        $this->form_validation->set_rules('title', 'Title', 'required');
        $this->form_validation->set_rules('menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('url', 'Url', 'required');
        $this->form_validation->set_rules('icon', 'Icon', 'required');

        if ($this->form_validation->run() == false) {
            // menampilkan views
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/submenu', $data);
            $this->load->view('templates/footer');
        } else {

            $data = [
                'title' => $this->input->post('title'),
                'menu_id' => $this->input->post('menu_id'),
                'url' => $this->input->post('url'),
                'icon' => $this->input->post('icon'),
                'is_active' => $this->input->post('is_active')
            ];

            $this->db->insert('user_sub_menu', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            New Submenu Added! </div>');

            redirect('menu/submenu');
        }
    }

    public function edit_submenu($id)
    {
        $this->load->model('Menu_model', 'menu');

        // authentication title dan user
        $data['title'] = 'Submenu Management';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        // query submenu harus di join untuk mendapatkan nama menu dari table user_menu
        // make model untuk melakukan join agar rapih

        $data['submenu'] = $this->menu->getSubmenuById($id);

        // buat rules untuk validasi submenu 
        $this->form_validation->set_rules('edit_title', 'Title', 'required');
        $this->form_validation->set_rules('edit_menu_id', 'Menu', 'required');
        $this->form_validation->set_rules('edit_url', 'Url', 'required');
        $this->form_validation->set_rules('edit_icon', 'Icon', 'required');

        if ($this->form_validation->run() == false) {
            // menampilkan views
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('menu/submenu', $data);
            $this->load->view('templates/footer');
        } else {

            $data = [
                'title' => $this->input->post('edit_title'),
                'menu_id' => $this->input->post('edit_menu_id'),
                'url' => $this->input->post('edit_url'),
                'icon' => $this->input->post('edit_icon'),
                'is_active' => $this->input->post('is_active')
            ];

            $this->db->where('id', $this->input->post('id'));
            $this->db->update('user_sub_menu', $data);


            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Submenu Has Been Updated! </div>');

            redirect('menu/submenu');
        }
    }
    public function delete_submenu($id)
    {
        $this->load->model('Menu_model', 'menu');
        $this->menu->deleteSubmenu($id);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Submenu has been deleted! </div>');

        redirect('menu/submenu');
    }
}
