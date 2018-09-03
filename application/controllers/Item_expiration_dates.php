<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Item_expiration_dates extends Secure_Controller
{
    public function __construct()
    {
        parent::__construct('item_expiration_dates');
        $this->load->model('Item_expiration_date');
    }

    public function index()
    {
        $data['table_headers'] = $this->xss_clean(get_item_expiration_date_manage_table_headers());

        $this->load->view('item_expiration_dates/manage', $data);
    }
    
    public function search()
    {
        $search = $this->input->get('search');
        $limit  = $this->input->get('limit');
        $offset = $this->input->get('offset');
        $sort   = $this->input->get('sort');
        $order  = $this->input->get('order');

        $item_expiration_dates = $this->Item_expiration_date->search($search, $limit, $offset, $sort, $order);
        $total_rows = $this->Item_expiration_date->get_found_rows($search);

        $data_rows = array();
        foreach($item_expiration_dates->result() as $item_expiration_date)
        {
            $data_rows[] = $this->xss_clean(get_item_expiration_date_data_row($item_expiration_date));
        }

        echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
    }

    public function view($item_expiration_date_id = -1)
    {
        $info = $this->Item_expiration_date->get_info($item_expiration_date_id);

        if($item_expiration_date_id == -1)
        {
            $info->item_expiration_date_id = 0;
        }
        foreach(get_object_vars($info) as $property => $value)
        {
            $info->$property = $this->xss_clean($value);
        }

        $data['item_expiration_date_info']  = $info;
        $data['items'] = $this->Item->get_rows();

        $this->load->view("item_expiration_dates/form", $data);
    }

    public function save($id = -1)
    {
        $data = [
            'item_id' => $this->input->post('item_id'),
            'quantity' => $this->input->post('quantity'),
            'expired_at' => date("Y-m-d H:i:s", strtotime($this->input->post('expired_at'))),
            'notes' => $this->input->post('notes'),
            'enabled' => (empty($this->input->post('enabled')))? 0 : 1,
            'updated_at' => date("Y-m-d H:i:s")
        ];

        if($id == -1) {
            $data['base_quantity'] = $data['quantity'];
            $data['enabled'] = 1;
            $data['created_at'] = date("Y-m-d H:i:s");
        }

        if($this->Item_expiration_date->save($data, $id))
        {
            $success = true;
            $new_item = false;
            //New item kit
            if($id == -1) {
                $new_item = true;
            }

            if($new_item) {
                echo json_encode(array('success' => $success,
                    'message' => $this->lang->line('item_expiration_dates_successful_adding'), 'id' => $id));
            }
            else
            {
                echo json_encode(array('success' => $success,
                    'message' => $this->lang->line('item_expiration_dates_successful_updating'), 'id' => $id));
            }
        } else {
            echo json_encode(array('success' => FALSE,
                'message' => $this->lang->line('item_expiration_dates_error_adding_updating'), 'id' => -1));
        }
    }

    public function delete()
    {
        $ids = $this->xss_clean($this->input->post('ids'));
        if($this->Item_expiration_date->delete_list($ids)) {
            echo json_encode(array('success' => TRUE,
                'message' => $this->lang->line('item_expiration_dates_successful_deleted').' '.$this->lang->line('item_expiration_dates_one_or_multiple')));
        } else {
            echo json_encode(array('success' => FALSE,
                'message' => $this->lang->line('item_expiration_dates_cannot_be_deleted')));
        }
    }

    public function warning() {
        echo json_encode(array('success' => 1, 'message' => 'ada 10 produk yang expired!'));
    }
}
?>
