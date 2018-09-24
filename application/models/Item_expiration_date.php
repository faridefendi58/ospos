<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Item_expiration_date class
 */

class Item_expiration_date extends CI_Model
{
    /*
    Determines if a given item_id is an item kit
    */
    public function exists($id)
    {
        $this->db->from('item_expiration_dates');
        $this->db->where('id', $id);

        return ($this->db->get()->num_rows() == 1);
    }

    /*
    Gets total of rows
    */
    public function get_total_rows()
    {
        $this->db->from('item_expiration_dates');

        return $this->db->count_all_results();
    }

    /*
    Gets information about a particular item kit
    */
    public function get_info($id)
    {
        $this->db->select('t.*, i.name AS item_name');

        $this->db->from('item_expiration_dates AS t');
        $this->db->join('items as i', 'i.item_id = t.item_id');
        $this->db->where('t.id', $id);

        $query = $this->db->get();

        if($query->num_rows()==1) {
            return $query->row();
        } else {
            //Get empty base parent object, as $price_list_id is NOT an item kit
            $item_obj = new stdClass();

            //Get all the fields from items table
            foreach($this->db->list_fields('item_expiration_dates') as $field)
            {
                $item_obj->$field = '';
            }

            return $item_obj;
        }
    }

    /*
    Gets information about multiple price lists
    */
    public function get_multiple_info($item_id)
    {
        $this->db->from('item_expiration_dates');
        $this->db->where_in('item_id', $item_id);
        $this->db->order_by('name', 'asc');

        return $this->db->get();
    }

    /*
    Inserts or updates an item kit
    */
    public function save(&$data, $id = FALSE)
    {
        if(!$id || !$this->exists($id))
        {
            if($this->db->insert('item_expiration_dates', $data))
            {
                $insert = $this->db->insert_id();

                return TRUE;
            }

            return FALSE;
        }

        $this->db->where('id', $id);

        return $this->db->update('item_expiration_dates', $data);
    }

    /*
    Delete one price list
    */
    public function delete($id){
        if (!$this->exists($id)) {
            return false;
        }

        return $this->db->delete('item_expiration_dates', array('id' => $id));
    }

    /*
	Deletes a list of price list
	*/
    public function delete_list($ids)
    {
        $this->db->where_in('id', $ids);
        return $this->db->delete('item_expiration_dates');
    }

    /*
   Gets rows
   */
    public function get_found_rows($search)
    {
        return $this->search($search, 0, 0, 'item_id', 'asc', TRUE);
    }

    /*
    Perform a search on items
    */
    public function search($search, $rows = 0, $limit_from = 0, $sort = 'item_id', $order = 'asc', $count_only = FALSE)
    {
        // get_found_rows case
        if($count_only == TRUE) {
            $this->db->select('COUNT(t.id) as count');
        } else {
            $this->db->select('t.*, i.name AS item_name, i.item_number AS barcode');
        }

        $this->db->from('item_expiration_dates AS t');
        $this->db->join('items as i', 'i.item_id = t.item_id');
        $this->db->where('t.enabled', 1);

        // get_found_rows case
        if($count_only == TRUE)
        {
            return $this->db->get()->row()->count;
        }

        $this->db->order_by($sort, $order);

        if($rows > 0)
        {
            $this->db->limit($rows, $limit_from);
        }

        return $this->db->get();
    }

    public function get_rows() {
        $this->db->from('item_expiration_dates');
        $this->db->order_by('id', 'asc');

        return $this->db->get()->result();
    }

    public function get_expired_soon($limit = 10) {
        $max = $this->config->item("notif_max_day_before_expired");
        if (empty($max)) {
            return array();
        }
        $this->db->select('t.*, DATEDIFF(t.expired_at, NOW()) AS diff, i.name AS item_name, n.noticed_at AS noticed_at, n.id AS notification_id');
        $this->db->from('item_expiration_dates AS t');
        $this->db->join('items as i', 'i.item_id = t.item_id');
        $this->db->join('notifications as n', 'n.exp_date_id = t.id AND n.person_id = '.$this->session->userdata('person_id'), 'left');
        $this->db->where('t.enabled', 1);
        $this->db->group_start();
        $this->db->where('n.noticed_at ', null);
        $this->db->or_where('n.is_closed ', 0);
        $this->db->group_end();
        $this->db->having('diff <= ', $max);
        $this->db->having('diff >= ', 0);
        $this->db->order_by('t.expired_at', 'asc');
        $this->db->limit($limit);

        return $this->db->get()->result();
    }

    public function get_quantity_by_item($item_id) {
        $this->db->select('t.id, t.quantity');

        $this->db->from('item_expiration_dates AS t');
        $this->db->where('t.item_id', $item_id);
        $this->db->where('t.quantity >', 0);

        $query = $this->db->get();
        $row = $query->row();

        return $row;
    }

    public function update_lot_qty($item_id, $qty) {
        $item = $this->get_quantity_by_item($item_id);
        if (!is_object($item)) {
            return false;
        }
        $current_qty = $item->quantity;
        if ($current_qty > 0) {
            $new_qty = $current_qty - $qty;
            if ($new_qty < 0) {
                $new_qty = 0;
            }
            $this->db->where('id', $item->id);
            $data = [
                'quantity' => $new_qty,
                'updated_at' => date("Y-m-d H:i:s")
                ];
            if ($new_qty == 0) {
                $data['enabled'] = 0;
            }

            return $this->db->update('item_expiration_dates', $data);
        }

        return false;
    }
}
?>
