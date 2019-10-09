<?php
// defined(BASEPATH) agar tidak bisa diakses langsung dari url
defined('BASEPATH') or exit('No direct script access allowed');

class Admin_model extends CI_Model
{
    public function getRoleById($id)
    {
        return $this->db->get_where('user_role', ['id' => $id])->row_array();
    }
    public function editRole()
    {
        $data = [
            "role" => $this->input->post('edit_role', true),
        ];

        $this->db->where('id', $this->input->post('id_role'));
        $this->db->update('user_role', $data);
    }
    public function deleteDataRole($id)
    {
        $this->db->delete('user_role', ['id' => $id]);
    }
}
