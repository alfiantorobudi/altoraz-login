<?php
// defined(BASEPATH) agar tidak bisa diakses langsung dari url
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{

    public function getMenuById($id)
    {
        return $this->db->get_where('user_menu', ['id' => $id])->row_array(); // function result di CI untuk mengambil baris array, bukan semuanya, kalau semuanya make result_array
    }

    public function editMenu()
    {
        $data = [
            // post berdasarkan name yang ada di forms index
            "menu" => $this->input->post('editmenu', true),
        ];
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('user_menu', $data);
    }
    public function deleteMenu($id)
    {
        $this->db->delete('user_menu', ['id' => $id]);
    }


    // ------------- MODEL UNTUK SUBMENU ------------------ //

    public function getSubmenu()
    {
        $query = "SELECT `user_sub_menu`.*, `user_menu`.`menu` FROM `user_sub_menu` JOIN `user_menu` ON `user_sub_menu`.`menu_id` = `user_menu`.`id` 
        ";

        return $this->db->query($query)->result_array();
    }

    public function getSubmenuById($id)
    {
        $query = "SELECT `user_sub_menu`.*, `user_menu`.`menu` FROM `user_sub_menu` JOIN `user_menu` ON `user_sub_menu`.`menu_id` = `user_menu`.`id` 
        WHERE `id`= $id 
        ";
        return $this->db->query($query)->row();
    }

    public function deleteSubmenu($id)
    {
        $this->db->delete('user_sub_menu', ['id' => $id]);
    }
}
