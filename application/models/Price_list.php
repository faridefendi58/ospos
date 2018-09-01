<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Price_list class
 */

class Price_list extends CI_Model
{
    /*
    Determines if a given item_id is an item kit
    */
    public function exists($id)
    {
        $this->db->from('price_lists');
        $this->db->where('id', $id);

        return ($this->db->get()->num_rows() == 1);
    }

    /*
    Gets total of rows
    */
    public function get_total_rows()
    {
        $this->db->from('price_lists');

        return $this->db->count_all_results();
    }

    /*
    Gets information about a particular item kit
    */
    public function get_info($id)
    {
        $this->db->select('
		id,
		name as name,
		code,
		description,
		created_at,
		updated_at');

        $this->db->from('price_lists');
        $this->db->where('id', $id);

        $query = $this->db->get();

        if($query->num_rows()==1) {
            return $query->row();
        } else {
            //Get empty base parent object, as $price_list_id is NOT an item kit
            $item_obj = new stdClass();

            //Get all the fields from items table
            foreach($this->db->list_fields('price_lists') as $field)
            {
                $item_obj->$field = '';
            }

            return $item_obj;
        }
    }

    /*
    Gets information about multiple price lists
    */
    public function get_multiple_info($price_list_ids)
    {
        $this->db->from('price_lists');
        $this->db->where_in('price_list_id', $price_list_ids);
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
            if($this->db->insert('price_lists', $data))
            {
                $price_list_data['id'] = $this->db->insert_id();

                return TRUE;
            }

            return FALSE;
        }

        $this->db->where('id', $id);

        return $this->db->update('price_lists', $data);
    }

    /*
    Delete one price list
    */
    public function delete($id){
        if (!$this->exists($id)) {
            return false;
        }

        $del1 = $this->db->delete('price_lists', array('id' => $id));
        if ($del1) {
            $this->db->where_in('price_list_id', $id);
            $del2 = $this->db->delete('price_list_items');
            return true;
        }

        return false;
    }

    /*
	Deletes a list of price list
	*/
    public function delete_list($ids)
    {
        $this->db->where_in('id', $ids);
        $del1 = $this->db->delete('price_lists');

        $this->db->where_in('price_list_id', $ids);
        $del2 = $this->db->delete('price_list_items');

        return $del1;
    }

    /*public function get_search_suggestions($search, $limit = 25)
    {
        $suggestions = array();

        $this->db->from('price_lists');

        //KIT #
        if(stripos($search, 'KIT ') !== FALSE)
        {
            $this->db->like('price_list_id', str_ireplace('KIT ', '', $search));
            $this->db->order_by('price_list_id', 'asc');

            foreach($this->db->get()->result() as $row)
            {
                $suggestions[] = array('value' => 'KIT '. $row->price_list_id, 'label' => 'KIT ' . $row->price_list_id);
            }
        }
        else
        {
            $this->db->like('name', $search);
            $this->db->order_by('name', 'asc');

            foreach($this->db->get()->result() as $row)
            {
                $suggestions[] = array('value' => $row->price_list_id, 'label' => $row->name);
            }
        }

        //only return $limit suggestions
        if(count($suggestions) > $limit) {
            $suggestions = array_slice($suggestions, 0, $limit);
        }

        return $suggestions;
    }*/

    /*
   Gets rows
   */
    public function get_found_rows($search)
    {
        return $this->search($search, 0, 0, 'name', 'asc', TRUE);
    }

    /*
    Perform a search on items
    */
    public function search($search, $rows = 0, $limit_from = 0, $sort = 'name', $order = 'asc', $count_only = FALSE)
    {
        // get_found_rows case
        if($count_only == TRUE)
        {
            $this->db->select('COUNT(price_list.id) as count');
        }

        $this->db->from('price_lists AS price_list');
        $this->db->like('name', $search);
        $this->db->or_like('description', $search);

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
}
?>
