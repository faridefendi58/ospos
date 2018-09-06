<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Item_expiration_date class
 */

class Notifications extends CI_Model
{
    /*
    Determines if a given item_id is an item kit
    */
    public function exists($id)
    {
        $this->db->from('notifications');
        $this->db->where('id', $id);

        return ($this->db->get()->num_rows() == 1);
    }

    /*
    Gets total of rows
    */
    public function get_total_rows()
    {
        $this->db->from('notifications');

        return $this->db->count_all_results();
    }

    /*
    Gets information about a particular item kit
    */
    public function get_info($id)
    {
        $this->db->select('t.*');

        $this->db->from('notifications AS t');
        $this->db->where('t.id', $id);

        $query = $this->db->get();

        if($query->num_rows()==1) {
            return $query->row();
        } else {
            //Get empty base parent object, as $price_list_id is NOT an item kit
            $item_obj = new stdClass();

            //Get all the fields from items table
            foreach($this->db->list_fields('notifications') as $field)
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
        $this->db->from('notifications');
        $this->db->where_in('rel_id', $item_id);
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
            if($this->db->insert('notifications', $data))
            {
                $insert = $this->db->insert_id();

                return TRUE;
            }

            return FALSE;
        }

        $this->db->where('id', $id);

        return $this->db->update('notifications', $data);
    }

    /*
    Delete one price list
    */
    public function delete($id){
        if (!$this->exists($id)) {
            return false;
        }

        return $this->db->delete('notifications', array('id' => $id));
    }

    /*
	Deletes a list of price list
	*/
    public function delete_list($ids)
    {
        $this->db->where_in('id', $ids);
        return $this->db->delete('notifications');
    }

    /*
   Gets rows
   */
    public function get_found_rows($search)
    {
        return $this->search($search, 0, 0, 'id', 'asc', TRUE);
    }

    /*
    Perform a search on items
    */
    public function search($search, $rows = 0, $limit_from = 0, $sort = 'id', $order = 'asc', $count_only = FALSE)
    {
        // get_found_rows case
        if($count_only == TRUE) {
            $this->db->select('COUNT(t.id) as count');
        } else {
            $this->db->select('t.*, i.name AS item_name');
        }

        $this->db->from('notifications AS t');
        $this->db->join('item_expiration_dates as exp', 'exp.id = t.exp_date_id');
        $this->db->where('t.person_id', $this->session->userdata('person_id'));

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
        $this->db->from('notifications');
        $this->db->order_by('id', 'asc');

        return $this->db->get()->result();
    }

    public function get_noticed_id($exp_date_id = 0, $item_id = 0) {
        $this->db->select('t.id');
        $this->db->from('notifications AS t');
        $this->db->where('t.person_id', $this->session->userdata('person_id'));
        if ($exp_date_id > 0)
            $this->db->where('t.exp_date_id', $exp_date_id);
        if ($item_id > 0)
            $this->db->where('t.item_id', $item_id);

        $row = $this->db->get()->row();

        return (is_object($row))? $row->id : 0;
    }

    public function get_closed_notification($limit = 10) {
        $this->db->select('id, DATEDIFF(NOW(), noticed_at) AS diff');
        $this->db->from('notifications');
        $this->db->where('is_closed', 1);
        $this->db->where('DATEDIFF(NOW(), noticed_at)>=', 1);
        $this->db->order_by('noticed_at', 'asc');
        $this->db->limit($limit);

        $results = $this->db->get()->result();
        $items = [];
        if (is_array($results)) {
            foreach ($results as $i => $result) {
                array_push($items, $result->id);
            }
        }

        return $items;
    }
}
?>
