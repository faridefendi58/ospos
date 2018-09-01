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
        $this->db->select('t.id, t.price_list_id, v.name AS list_name, t.unit_price, u.name AS item_name');
        $this->db->from('price_list_items as t');
        $this->db->join('items as u', 'u.item_id = t.item_id');
        $this->db->join('price_lists as v', 'v.id = t.price_list_id');
        $this->db->where('t.id', $price_list_id);
        $this->db->order_by('t.id', 'asc');

        //return an array of item kit items for an item
        return $this->db->get()->result_array();
    }

    /*
    Inserts or updates an item kit's items
    */
    public function save(&$item_kit_items_data, $item_kit_id)
    {
        $success = TRUE;

        //Run these queries as a transaction, we want to make sure we do all or nothing

        $this->db->trans_start();

        $this->delete($item_kit_id);

        if($item_kit_items_data != NULL)
        {
            foreach($item_kit_items_data as $row)
            {
                $row['item_kit_id'] = $item_kit_id;
                $success &= $this->db->insert('price_list_items', $row);
            }
        }

        $this->db->trans_complete();

        $success &= $this->db->trans_status();

        return $success;
    }

    /*
    Deletes item kit items given an item kit
    */
    public function delete($price_list_id)
    {
        return $this->db->delete('price_list_items', array('price_list_id' => $price_list_id));
    }
}
?>
