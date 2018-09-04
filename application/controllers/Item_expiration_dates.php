<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Item_expiration_dates extends Secure_Controller
{
    public function __construct()
    {
        parent::__construct('item_expiration_dates');
        $this->load->model('Item_expiration_date');
        $this->load->model('Notifications');
        $this->load->model('Item_quantity');
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
        $exps = $this->Item_expiration_date->get_expired_soon();
        $messages = [];
        if (is_array($exps) && count($exps) > 0) {
            foreach ($exps as $i => $exp) {
                $msg = [
                    'id' => $exp->notification_id,
                    'msg' => $exp->quantity.' '.$exp->item_name.' akan expired pada '. date("d/m/Y", strtotime($exp->expired_at)),
                    'href' => 'item_expiration_dates?id='.$exp->id,
                    'class_name' => 'alert-warning'
                ];
                array_push($messages, $msg);
                $data = [
                    'exp_date_id' => $exp->id,
                    'item_id' => 0,
                    'noticed_at' => date("Y-m-d H:i:s"),
                    'person_id' => $this->session->userdata('person_id')
                ];
                if ($this->Notifications->get_noticed_id($exp->id, 0) == 0)
                    $this->Notifications->save($data, -1);
            }
        }
        $out_of_stocks = $this->Item_quantity->get_out_of_stock_soon();
        if (is_array($out_of_stocks) && count($out_of_stocks) > 0) {
            foreach ($out_of_stocks as $i => $out_of_stock) {
                $msg = [
                    'id' => $out_of_stock->notification_id,
                    'msg' => 'Stok '.$out_of_stock->item_name.' hanya tersedia '. (int)$out_of_stock->quantity,
                    'href' => '#'.$out_of_stock->item_id,
                    'class_name' => 'alert-info'
                ];
                array_push($messages, $msg);
                $data = [
                    'exp_date_id' => 0,
                    'item_id' => $out_of_stock->item_id,
                    'noticed_at' => date("Y-m-d H:i:s"),
                    'person_id' => $this->session->userdata('person_id')
                ];
                if ($this->Notifications->get_noticed_id(0, $out_of_stock->item_id) == 0)
                    $this->Notifications->save($data, -1);
            }
        }

        echo json_encode(array('success' => 1, 'messages' => $messages));
    }

    public function close_notification($id) {
        if ($this->input->post("id") != $id) {
            echo json_encode(array('success' => 0));
        }

        $data = [
            'is_closed' => 1,
        ];

        $this->Notifications->save($data, (int)$id);

        echo json_encode(array('success' => 1));
    }
}
?>
