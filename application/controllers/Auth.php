<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }
    public function index()
    {
        // method untuk ke defaultpage apabila sudah pernah login dan mengetik di url ke auth
        $this->goToDefaultPage();

        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|trim');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Altoraz-Login';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            // validasi success

            $this->_login(); // buat private login() agar tidak terlalu panjang

        }
    }

    // private function diberikan tanda _ agar untuk membedakan. 
    // private function hanya dapat diakses di halaman auth saja.

    private function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();
        // apakah user ada?
        if ($user) {
            // jika user ada, maka periksa is_active
            if ($user['is_active'] == 1) {
                //jika is_active = 1, maka cek password

                if (password_verify($password, $user['password'])) {
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];
                    // set user data ke dalam session
                    // kalau sudah login, maka ada session data
                    $this->session->set_userdata($data);
                    if ($user['role_id'] == 1) {
                        redirect('admin');
                    } else {
                        redirect('user');
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Wrong password! </div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Email has not been activated, please activate your email </div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Email is not registered! </div>');
            redirect('auth');
        }
    }

    public function registration()
    {

        // method untuk ke defaultpage apabila sudah pernah login dan mengetik di url ke auth
        $this->goToDefaultPage();

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'This email has already registered'
        ]); //is_unique mengecek nama table dan nama field
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', [
            'required' => 'Password is required',
            'matches' => 'Password Dont Match!',
            'min_length' => 'Password too short!'
        ]);
        $this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');

        if ($this->form_validation->run() == FALSE) {
            // jika salah maka load ke halaman ini
            $data['title'] = 'Altoraz-Registration';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registration');
            $this->load->view('templates/auth_footer');
        } else {

            $email = $this->input->post('email', true);
            // jika benar maka masukan data ke database

            // nulis data arraynya harus urut sesuaikkan dengan database phpmyadmin

            $data = [
                'name' => htmlspecialchars($this->input->post('name', true)),
                'email' => htmlspecialchars($email),
                'image' => 'default.jpg',
                'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
                'role_id' => 2,
                'is_active' => 0,
                'date_created' => time()
            ];

            // siapkan token
            $token = base64_encode(random_bytes(32));
            $user_token = [
                'email' => $email,
                'token' => $token,
                'date_created' => time(),
            ];

            $this->db->insert('user', $data);
            // masukan ke dalam tabel user_token dengan data $user_token
            $this->db->insert('user_token', $user_token);

            // ada 2 parameter, token untuk mengecek dan ada type untuk menentukan verify or forgot password 
            $this->_sendEmail($token, 'verify');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Congratulation! your account has been created. Please activate your account</div>');
            redirect('auth');
        }
    }

    private function _sendEmail($token, $type)
    {
        // cari email class di documentation CI
        // buat config email, sebagai aturan dari library email

        // load library CI untuk email
        $this->load->library('email');
        // library sudah bisa dipakai
        $config = [
            'protocol' => 'smtp',
            'smtp_host' => 'ssl://smtp.googlemail.com',
            'smtp_user' => 'altorteorida@gmail.com',
            'smtp_pass' => 'gemesbanget',
            'smtp_port' => 465,
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        ];

        $this->email->initialize($config);


        // buat format email 
        $this->email->from('altorteorida@gmail.com', 'Altorteorida.com');
        $this->email->to($this->input->post('email'));

        // apakah tipe verify or forgot password
        if ($type == 'verify') {
            $this->email->subject('Account Verification');
            $this->email->message('Click this link to verify your account : <a href="' . base_url() . 'auth/verify?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '"> Activate </a>');
        } else if ($type == 'forgot') {
            $this->email->subject('Reset Password');
            $this->email->message('Click this link to reset your password : <a href="' . base_url() . 'auth/resetpassword?email=' . $this->input->post('email') . '&token=' . urlencode($token) . '"> Reset your Password </a>');
        }


        if ($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }

    public function verify()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');

        // query untuk mendapatkan user dengan email yg sama dengan email yg ada di database
        $user = $this->db->get_where('user', ['email' => $email])->row_array();

        // ngecek apakah ada usernya
        if ($user) {
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();

            // ngecek apakah ada tokennya
            if ($user_token) {

                // ngecek apakah masih 1 hari atau belum
                if (time() - $user_token['date_created'] < (60 * 60 * 24)) {
                    // update is activenya apabila telah lolos semua
                    $this->db->set('is_active', 1);
                    $this->db->where('email', $email);
                    $this->db->update('user');

                    // jika sudah diupdate maka delete user di table user_token where email = $email
                    $this->db->delete('user_token', ['email' => $email]);

                    $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">' . $email . ' has been activated! Please login.</div>');
                    redirect('auth');
                } else {

                    // jika lewat 1 hari, maka hapus usernya, dan token di dalam tabel user dan table user_token
                    $this->db->delete('user', ['email' => $email]);
                    $this->db->delete('user_token', ['email' => $email]);

                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
                    Account activation failed! Token Expired!</div>');
                    redirect('auth');
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Account activation failed! Wrong token!</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Account activation failed! Wrong email!</div>');
            redirect('auth');
        }
    }


    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            You have been logged out!</div>');
        redirect('auth');
    }

    public function bloked()
    {
        $this->load->view('auth/bloked');
    }

    // buat function untuk kembali ke default page, ketika sudah login, tetapi mengetikkan url auth, maka hasilnya bukan login lagi, tetapi ke default page, dipanggilnya bukan di construct(), tetapi di method index(), dan register()

    public function goToDefaultPage()
    {
        if ($this->session->userdata('role_id') == 1) {
            redirect('admin');
        } else if ($this->session->userdata('role_id') == 2) {
            redirect('user');
        } else {
            // jika ada role_id yg lain maka tambahkan disini
        }
    }

    public function forgotPassword()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Forgot Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/forgot-password');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email', true);

            $user = $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();

            // cek apakah user ada dan is activenya = 1 
            // agar yg belum aktivasi tidak bisa forgot password
            if ($user) {
                // baru kirim email jalankan method private sendEmail()
                // siapkan token
                $token = base64_encode(random_bytes(32));
                // buat diinsert ke table user token
                $user_token = [
                    'email' => $email,
                    'token' => $token,
                    'date_created' => time()
                ];

                $this->db->insert('user_token', $user_token);
                $this->_sendEmail($token, 'forgot');

                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Please check your email to reset your password</div>');
                redirect('auth/forgotpassword');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Email is not registered or activated</div>');
                redirect('auth/forgotpassword');
            }
        }
    }

    public function resetPassword()
    {
        $email = $this->input->get('email');
        $token = $this->input->get('token');
        // query dari database untuk select data, agar tidak terjadi pengisian user dan token oleh user di URL secara langsung

        $user = $this->db->get_where('user', ['email' => $email])->row_array();


        if ($user) {
            $user_token = $this->db->get_where('user_token', ['token' => $token])->row_array();
            if ($user_token) {

                // gak usah dicek ke date_created, agar bisa dipakai terus, tanpa limit waktu
                // buat session reset email, agar sessionnya ada di change password, agar tidak bisa nulis di url kalau tidak ada sessionnya

                $this->session->set_userdata('reset_email', $email);
                $this->changePassword();
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Reset password failed! Wrong token!</div>');
                redirect('auth');
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">
            Reset password failed! Wrong email!</div>');
            redirect('auth');
        }
    }

    public function changePassword()
    {
        // buat method untuk ngecek session, agar user tidak sembarangan change reset password tanpa melalui email 
        // harus ada session reset email..
        if (!$this->session->userdata('reset_email')) {
            redirect('auth');
        }
        $this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]');

        $this->form_validation->set_rules('password2', 'Repeat Password', 'required|trim|min_length[3]|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Change Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/change-password');
            $this->load->view('templates/auth_footer');
        } else {
            $password = password_hash($this->input->post('password1'), PASSWORD_DEFAULT);
            //ambil dari session yang untuk change password saja yaitu session->userdata('reset_email');

            $email = $this->session->userdata('reset_email');

            $this->db->set('password', $password);
            $this->db->where('email', $email);
            $this->db->update('user');
            // unset agar password yg telah diganti, dihapus
            $this->session->unset_userdata('reset_email');

            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Password has been changed! Please login. </div>');
            redirect('auth');
        }
    }
}
