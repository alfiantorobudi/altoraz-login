<?php

function is_logged_in()
{
    // gak bisa manggil $this
    // harus diinstance dulu dengan get_instance()


    $ci = get_instance();

    if (!$ci->session->userdata('email')) {
        redirect('auth');
    } else {
        // role_id sudah ada di session login tinggal dipanggil saja
        $role_id = $ci->session->userdata('role_id');
        // menu diambil dari url controller dengan uri->segment(1); 
        $menu = $ci->uri->segment(1);
        // cocokan menu/id dengan menu_id di dalam tabel user_access_menu

        $queryMenu = $ci->db->get_where('user_menu', ['menu' => $menu])->row_array();
        $menu_id = $queryMenu['id'];

        // di tahap ini maka udah dapet role_id dan menu_id untuk dicocokan
        $userAccess = $ci->db->get_where('user_access_menu', [
            'role_id' => $role_id,
            'menu_id' => $menu_id
        ]);
        // apabila ada yang sama maka jalankan fungi ini
        if ($userAccess->num_rows() < 1) { // artinya tidak ada menu_id dan role_id yang sama, maka tampilkan halaman bloked
            redirect('auth/bloked');
        }
    }
}

function check_access($role_id, $menu_id)
{
    $ci = get_instance();

    // query untuk mencari semua data yang dimana ada role_id = $role_id dan menu id = $menu_id
    $ci->db->where('role_id', $role_id);
    $ci->db->where('menu_id', $menu_id);
    $result = $ci->db->get('user_access_menu');

    // dicek resultnya
    if ($result->num_rows() > 0) {
        return "checked='checked'";
    }
}
