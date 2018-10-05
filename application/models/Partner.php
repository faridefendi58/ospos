<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Partner class
 */

class Partner extends CI_Model
{
    /*
    Determines if a given partner_id is a partner
    */
    public function exists($sale_id)
    {
        $this->db->from('sales_partners');
        $this->db->where('sale_id', $sale_id);
        $this->db->where('deleted', 0);

        return ($this->db->get()->num_rows() == 1);
    }

    /*
    Gets max partner number
    */
    public function get_max_number()
    {
        $this->db->select('CAST(partner_code AS UNSIGNED) AS max');
        $this->db->from('sales_partners');
        $this->db->where('partner_code REGEXP', "'^[0-9]+$'", FALSE);
        $this->db->order_by("id","desc");
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    /*
    Gets total of rows
    */
    public function get_total_rows()
    {
        $this->db->from('sales_partners');
        $this->db->where('deleted', 0);

        return $this->db->count_all_results();
    }

    /*
    Gets information about a particular partner
    */
    public function get_info($sale_id)
    {
        $this->db->from('sales_partners');
        $this->db->where('sale_id', $sale_id);
        $this->db->where('deleted', 0);

        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            return $query->row();
        }
        else
        {
            //Get empty base parent object, as $partner_id is NOT an partner
            $partner_obj = new stdClass();

            //Get all the fields from partners table
            foreach($this->db->list_fields('sales_partners') as $field)
            {
                $partner_obj->$field = '';
            }

            return $partner_obj;
        }
    }

    /*
    Gets an partner id given a partner number
    */
    public function get_partner_id($partner_number)
    {
        $this->db->from('sales_partners');
        $this->db->where('id', $partner_number);
        $this->db->where('deleted', 0);

        $query = $this->db->get();

        if($query->num_rows() == 1)
        {
            return $query->row()->id;
        }

        return FALSE;
    }

    public function get_partner_code($doctor_name = null)
    {
        if (!empty($doctor_name)) {
            $this->db->from('sales_partners');
            $this->db->where('doctor_name', $doctor_name);
            $this->db->where('deleted', 0);
            $this->db->order_by("id","desc");
            $this->db->limit(1);

            $query = $this->db->get();

            if($query->num_rows() == 1)
            {
                return $query->row()->partner_code;
            }
        }

        $n = (int)$this->get_max_number()->max + 1;
        return str_pad($n, 5, "0", STR_PAD_LEFT);;
    }

    /*
    Gets information about multiple partners
    */
    public function get_multiple_info($partner_ids)
    {
        $this->db->from('sales_partners');
        $this->db->where_in('id', $partner_ids);
        $this->db->where('deleted', 0);
        $this->db->order_by('id', 'asc');

        return $this->db->get();
    }

    /*
    Inserts or updates a partner
    */
    public function save(&$partner_data, $partner_id = FALSE)
    {
        if(!$partner_id || !$this->exists($partner_id))
        {
            if($this->db->insert('sales_partners', $partner_data))
            {
                $partner_data['id'] = $this->db->insert_id();

                return TRUE;
            }

            return FALSE;
        }

        $this->db->where('id', $partner_id);

        return $this->db->update('sales_partners', $partner_data);
    }

    /*
    Updates multiple partners at once
    */
    public function update_multiple($partner_data, $partner_ids)
    {
        $this->db->where_in('id', $partner_ids);

        return $this->db->update('sales_partners', $partner_data);
    }

    /*
    Deletes one partner
    */
    public function delete($partner_id)
    {
        $this->db->where('id', $partner_id);

        return $this->db->update('sales_partners', array('deleted' => 1));
    }

    /*
    Deletes a list of partners
    */
    public function delete_list($partner_ids)
    {
        $this->db->where_in('id', $partner_ids);

        return $this->db->update('sales_partners', array('deleted' => 1));
    }

    /*
   Get search suggestions to find partners
   */
    public function get_search_patient_suggestions($search, $limit = 25)
    {
        $suggestions = array();

        $this->db->from('sales_partners');
        $this->db->like('patient_name', $search);
        $this->db->where('deleted', 0);
        $this->db->order_by('id', 'asc');
        $this->db->group_by('patient_name');
        foreach($this->db->get()->result() as $row)
        {
            $suggestions[] = array('label' => $row->patient_name);
        }

        //only return $limit suggestions
        if(count($suggestions) > $limit)
        {
            $suggestions = array_slice($suggestions, 0, $limit);
        }

        return $suggestions;
    }

    public function get_search_doctor_suggestions($search, $limit = 25)
    {
        $suggestions = array();

        $this->db->from('sales_partners');
        $this->db->like('doctor_name', $search);
        $this->db->where('deleted', 0);
        $this->db->order_by('id', 'asc');
        $this->db->group_by('doctor_name');
        foreach($this->db->get()->result() as $row)
        {
            $suggestions[] = array(
                'label' => $row->doctor_name,
                'address' => $row->doctor_address,
                'partner_code' => $row->partner_code
            );
        }

        //only return $limit suggestions
        if(count($suggestions) > $limit)
        {
            $suggestions = array_slice($suggestions, 0, $limit);
        }

        return $suggestions;
    }

    /*
    Gets gift cards
    */
    public function get_found_rows($search)
    {
        return $this->search($search, 0, 0, 'id', 'asc', TRUE);
    }

    /*
    Performs a search on partners
    */
    public function search($search, $rows = 0, $limit_from = 0, $sort = 'partner_number', $order = 'asc', $count_only = FALSE)
    {
        // get_found_rows case
        if($count_only == TRUE)
        {
            $this->db->select('COUNT(partners.id) as count');
        }

        $this->db->from('sales_partners AS partners');
        $this->db->join('sales AS sales', 'sales.sale_id = partners.sale_id', 'left');
        $this->db->group_start();
        $this->db->like('partners.patient_name', $search);
        $this->db->or_like('partners.doctor_name', $search);
        $this->db->group_end();
        $this->db->where('partners.deleted', 0);

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

    public function store_partner_data($sale_id)
    {
        $partner_data = [
            'sale_id' => $sale_id,
            'partner_code' => $this->get_partner_code(),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (isset($_COOKIE['sales_patient'])) {
            $partner_data['patient_name'] = $_COOKIE['sales_patient'];
        }

        if (isset($_COOKIE['sales_doctor'])) {
            $partner_data['doctor_name'] = $_COOKIE['sales_doctor'];
            $partner_data['partner_code'] = $this->get_partner_code($partner_data['doctor_name']);
        }

        if (isset($_COOKIE['sales_doctor_address'])) {
            $partner_data['doctor_address'] = $_COOKIE['sales_doctor_address'];
        }

        if(!$this->exists($sale_id))
        {
            $partner_data['created_at'] = date('Y-m-d H:i:s');
            if($this->db->insert('sales_partners', $partner_data))
            {
                $partner_data['id'] = $this->db->insert_id();

                $this->remove_all_cookie();

                return TRUE;
            }

            return FALSE;
        }

        $this->db->where('sale_id', $sale_id);
        $this->db->where('deleted', 0);

        $update = $this->db->update('sales_partners', $partner_data);
        if ($update) {
            $this->remove_all_cookie();
            return true;
        }
        return false;
    }

    public function remove_all_cookie()
    {
        if (isset($_COOKIE['sales_patient'])) {
            unset($_COOKIE['sales_patient']);
            setcookie('sales_patient', null, -1, '/');
        }
        if (isset($_COOKIE['sales_doctor'])) {
            unset($_COOKIE['sales_doctor']);
            setcookie('sales_doctor', null, -1, '/');
        }
        if (isset($_COOKIE['sales_doctor_address'])) {
            unset($_COOKIE['sales_doctor_address']);
            setcookie('sales_doctor_address', null, -1, '/');
        }
        if (isset($_COOKIE['partner_code'])) {
            unset($_COOKIE['partner_code']);
            setcookie('partner_code', null, -1, '/');
        }
    }
}
?>
