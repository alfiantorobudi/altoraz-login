<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        is_logged_in();
    }

    public function index()
    {
        // ngambil data dari user, berdasarkan email yang diinput do form login
        $data['title'] = 'My Profile';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('user/index', $data);
        $this->load->view('templates/footer');
    }

    public function edit()
    { // ngambil data dari user, berdasarkan email yang diinput do form login
        $data['title'] = 'Edit Profile';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->form_validation->set_rules('name', 'Full name', 'required|trim');

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('user/edit', $data);
            $this->load->view('templates/footer');
        } else {
            $name = $this->input->post('name');
            $email = $this->input->post('email');

            // cek jika ada gambar yang akan diupload
            $upload_image = $_FILES['image']['name'];

            if ($upload_image) {
                // configurasi untuk path, size dllnya
                $config['upload_path'] = './assets/img/profile';
                $config['allowed_types'] = 'gif|jpg|png';
                $config['max_size'] = '2048';

                // load library
                $this->load->library('upload', $config);

                // cek menggunakan library do_upload
                // cek dari input image di views
                if ($this->upload->do_upload('image')) {
                    // cek gambar lama, ambil dari $data[user][image] di database 

                    $old_image = $data['user']['image'];
                    //cek jika old_imagenya bukan default maka jalankan delete/unlink

                    if ($old_image != 'default.jpg') {
                        //hapus data lamanya tidak bisa pakai delete
                        // buat path gak bisa pakai baseurl, harus pakai FCPATH kemudian digabung dengan lokasi path folder, dan datanya
                        unlink(FCPATH . 'assets/img/profile/' . $old_image);
                    }



                    $new_image = $this->upload->data('file_name');
                    $this->db->set('image', $new_image);
                } else {
                    echo $this->upload->display_errors();
                }
            }



            $this->db->set('name', $name);
            $this->db->where('email', $email);
            $this->db->update('user');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Your profile has been updated</div>');
            redirect('user');
        }
    }

    public function changePassword()
    {
        // ngambil data dari user, berdasarkan email yang diinput do form login
        $data['title'] = 'Change Password';
        $data['user'] = $this->db->get_where('user', ['email' => $this->session->userdata('email')])->row_array();

        $this->form_validation->set_rules('current_password', 'Current Password', 'required|trim');
        $this->form_validation->set_rules('new_password1', 'New Password', 'required|trim|min_length[3]|matches[new_password2]');
        $this->form_validation->set_rules('new_password2', 'Confirm New Password', 'required|trim|min_length[3]|matches[new_password1]');



        if ($this->form_validation->run() == false) {
            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('user/changepassword', $data);
            $this->load->view('templates/footer');
        } else {
            $current_password = $this->input->post('current_password');

            $new_password = $this->input->post('new_password1');

            // cek password apakah sesuai dengan password lamanya?
            if (!password_verify($current_password, $data['user']['password'])) {

                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                Wrong Current Password
                </div>');
                redirect('user/changepassword');
            } else {
                // maka cek apakah new password sama dengan password yg current?
                if ($current_password == $new_password) {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                New Password cannot be the same as current password
                </div>');
                    redirect('user/changepassword');
                } else {
                    // password sudah oke 
                    // lalu di hash
                    // kemudian di update
                    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                    $this->db->set('password', $password_hash);
                    $this->db->where('email', $this->session->userdata('email'));
                    $this->db->update('user');

                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
                Password Changed
                </div>');
                    redirect('user/changepassword');
                }
            }
        }
    }
}
