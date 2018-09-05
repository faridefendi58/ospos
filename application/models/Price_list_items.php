<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Price_list_items class
 */

class Price_list_items extends CI_Model
{
    /*
    Gets item kit items for a particular item kit
    */
    public function get_info($price_list_id)
    {
        $this->db->select('t.id, t.price_list_id, t.item_id AS item_id, v.name AS list_name, v.code, t.unit_price, u.name AS item_name');
        $this->db->from('price_list_items as t');
        $this->db->join('items as u', 'u.item_id = t.item_id');
        $this->db->join('price_lists as v', 'v.id = t.price_list_id');
        $this->db->where('t.id', $price_list_id);
        $this->db->order_by('t.id', 'asc');

        $query = $this->db->get();

        if($query->num_rows()==1) {
            return $query->row();
        } else {
            //Get empty base parent object, as $price_list_id is NOT an item kit
            $item_obj = new stdClass();

            //Get all the fields from items table
            foreach($this->db->list_fields('price_list_items') as $field)
            {
                $item_obj->$field = '';
            }

            return $item_obj;
        }
    }

    /*
    Inserts or updates an price list items
    */
    public function save(&$data, $id)
    {
        if(!$id || !$this->exists($id)) {
            if($this->db->insert('price_list_items', $data)) {
                $data['id'] = $this->db->insert_id();

                return TRUE;
            }

            return FALSE;
        }

        $this->db->where('id', $id);

        return $this->db->update('price_list_items', $data);
    }

    /*
    Deletes item kit items given an item kit
    */
    public function delete($price_list_id)
    {
        return $this->db->delete('price_list_items', array('price_list_id' => $price_list_id));
    }

    public function delete_list($ids)
    {
        $this->db->where_in('id', $ids);
        $del1 = $this->db->delete('price_list_items');

        return $del1;
    }

    public function exists($id) {
        $this->db->from('price_list_items');
        $this->db->where('id', $id);

        return ($this->db->get()->num_rows() == 1);
    }

    public function get_total_rows() {
        $this->db->from('price_list_items');

        return $this->db->count_all_results();
    }

    public function get_found_rows($search) {
        return $this->search($search, 0, 0, 'item_id', 'asc', TRUE);
    }

    public function search(
        $search,
        $rows = 0,
        $limit_from = 0,
        $sort = 'item_id',
        $order = 'asc',
        $count_only = false,
        $price_list_id = 0) {
        // get_found_rows case
        if($count_only == TRUE) {
            $this->db->select('COUNT(t.id) as count');
        } else {
            $this->db->select('t.*, t.item_id, i.name AS item_name, l.code AS price_list_code, l.name AS price_list_name');
        }

        $this->db->from('price_list_items AS t');
        $this->db->join('items as i', 'i.item_id = t.item_id');
        $this->db->join('price_lists as l', 'l.id = t.price_list_id');

        if ($price_list_id > 0) {
            $this->db->where('t.price_list_id', $price_list_id);
        }

        if (!empty($search)) {
            $this->db->like('i.name', $search);
            $this->db->or_like('i.description', $search);
        }

        // get_found_rows case
        if($count_only == TRUE) {
            return $this->db->get()->row()->count;
        }

        $this->db->order_by($sort, $order);

        if($rows > 0) {
            $this->db->limit($rows, $limit_from);
        }

        return $this->db->get();
    }

    public function find_all_by_price_list_id($price_list_id) {
        $this->db->select('t.id, t.price_list_id, t.item_id AS item_id, v.name AS list_name, v.code, t.unit_price, u.name AS item_name');
        $this->db->from('price_list_items as t');
        $this->db->join('items as u', 'u.item_id = t.item_id');
        $this->db->join('price_lists as v', 'v.id = t.price_list_id');
        $this->db->where('t.price_list_id', $price_list_id);
        $this->db->order_by('t.id', 'asc');

        $query = $this->db->get();

        return $query->result();
    }

    public function find_one_by($price_list_id, $item_id) {
        $this->db->select('t.id');
        $this->db->from('price_list_items as t');
        $query = $this->db->get();

        $row = $query->row();
        return $row;
    }
}
?>
