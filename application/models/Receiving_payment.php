<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Receiving class
 */

class Receiving_payment extends CI_Model
{
    public function get_last_payment_info($receiving_id)
    {
        $this->db->from('receivings_payments');
        $this->db->join('receivings', 'receivings.receiving_id = receivings_payments.id', 'LEFT');
        $this->db->where('receivings_payments.receiving_id', $receiving_id);
        $this->db->order_by('receivings_payments.id', 'desc');

        return $this->db->get();
    }

    public function save(&$data, $id = FALSE)
    {
        if(!$id || !$this->exists($id))
        {
            if($this->db->insert('receivings_payments', $data))
            {
                $data['id'] = $this->db->insert_id();

                return TRUE;
            }

            return FALSE;
        }

        $this->db->where('id', $id);

        return $this->db->update('receivings_payments', $data);
    }
}