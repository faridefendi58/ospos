<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once("Secure_Controller.php");

class Price_lists extends Secure_Controller
{
	public function __construct()
	{
		parent::__construct('price_lists');
		$this->load->model('Price_list');
		$this->load->model('Price_list_items');
	}
	
	/*
	Add the total cost and retail price to a passed items kit retrieving the data from each singular item part of the kit
	*/
	private function _add_totals_to_price_list($item_kit)
	{
		$kit_item_info = $this->Item->get_info(isset($item_kit->kit_item_id) ? $item_kit->kit_item_id : $item_kit->item_id);

		$item_kit->total_cost_price = 0;
		$item_kit->total_unit_price = (float)$kit_item_info->unit_price;

		foreach($this->Item_kit_items->get_info($item_kit->item_kit_id) as $item_kit_item)
		{
			$item_info = $this->Item->get_info($item_kit_item['item_id']);
			foreach(get_object_vars($item_info) as $property => $value)
			{
				$item_info->$property = $this->xss_clean($value);
			}

			$item_kit->total_cost_price += $item_info->cost_price * $item_kit_item['quantity'];

			if($item_kit->price_option == PRICE_OPTION_ALL || ($item_kit->price_option == PRICE_OPTION_KIT_STOCK && $item_info->stock_type == HAS_STOCK ))
			{
				$item_kit->total_unit_price += $item_info->unit_price * $item_kit_item['quantity'];
			}
		}

		$discount_fraction = bcdiv($item_kit->kit_discount_percent, 100);
		$item_kit->total_unit_price = $item_kit->total_unit_price - round(bcmul($item_kit->total_unit_price, $discount_fraction), totals_decimals(), PHP_ROUND_HALF_UP);

		return $item_kit;
	}
	
	public function index()
	{
		$data['table_headers'] = $this->xss_clean(get_price_list_manage_table_headers());

		$this->load->view('price_lists/manage', $data);
	}

	/*
	Returns Item kits table data rows. This will be called with AJAX.
	*/
	public function search()
	{
		$search = $this->input->get('search');
		$limit  = $this->input->get('limit');
		$offset = $this->input->get('offset');
		$sort   = $this->input->get('sort');
		$order  = $this->input->get('order');

		$price_lists = $this->Price_list->search($search, $limit, $offset, $sort, $order);
		$total_rows = $this->Price_list->get_found_rows($search);

		$data_rows = array();
		foreach($price_lists->result() as $price_list)
		{
			$data_rows[] = $this->xss_clean(get_price_list_data_row($price_list));
		}

		echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
	}

	public function suggest_search()
	{
		$suggestions = $this->xss_clean($this->Price_list->get_search_suggestions($this->input->post('term')));

		echo json_encode($suggestions);
	}

	public function get_row($row_id)
	{
		// calculate the total cost and retail price of the Kit so it can be added to the table refresh
		$price_list = $this->_add_totals_to_price_list($this->Price_list->get_info($row_id));

		echo json_encode(get_price_list_data_row($price_list));
	}
	
	public function view($item_kit_id = -1)
	{
		$info = $this->Price_list->get_info($item_kit_id);

		if($item_kit_id == -1)
		{
			$info->price_list_id = 0;
		}
		foreach(get_object_vars($info) as $property => $value)
		{
			$info->$property = $this->xss_clean($value);
		}

		$data['price_list_info']  = $info;

		$this->load->view("price_lists/form", $data);
	}
	
	public function save($id = -1)
	{
		$data = [
			'name' => $this->input->post('name'),
			'code' => $this->input->post('code'),
			'description' => $this->input->post('description'),
			'updated_at' => date("Y-m-d H:i:s")
		];
        if($id == -1) {
            $data['created_at'] = date("Y-m-d H:i:s");
        }
		
		if($this->Price_list->save($data, $id))
		{
			$success = true;
            $new_item = false;
            //New item kit
            if($id == -1) {
                $new_item = true;
            }

			$item_kit_data = $this->xss_clean($data);

			if($new_item) {
				echo json_encode(array('success' => $success,
					'message' => $this->lang->line('price_lists_successful_adding').' '.$item_kit_data['name'], 'id' => $id));
			}
			else
			{
				echo json_encode(array('success' => $success,
					'message' => $this->lang->line('price_lists_successful_updating').' '.$item_kit_data['name'], 'id' => $id));
			}
		}
		else//failure
		{
			$item_kit_data = $this->xss_clean($data);

			echo json_encode(array('success' => FALSE, 
								'message' => $this->lang->line('price_lists_error_adding_updating').' '.$item_kit_data['name'], 'id' => -1));
		}
	}
	
	public function delete()
	{
		$ids = $this->xss_clean($this->input->post('ids'));
        if($this->Price_list->delete_list($ids)) {
            echo json_encode(array('success' => TRUE,
                'message' => $this->lang->line('price_lists_successful_deleted').' '.$this->lang->line('price_lists_one_or_multiple')));
		} else {
            echo json_encode(array('success' => FALSE,
                'message' => $this->lang->line('price_lists_cannot_be_deleted')));
		}
	}
	
	public function generate_barcodes($item_kit_ids)
	{
		$this->load->library('barcode_lib');
		$result = array();

		$item_kit_ids = explode(':', $item_kit_ids);
		foreach($item_kit_ids as $item_kid_id)
		{		
			// calculate the total cost and retail price of the Kit so it can be added to the barcode text at the bottom
			$item_kit = $this->_add_totals_to_item_kit($this->Item_kit->get_info($item_kid_id));
			
			$item_kid_id = 'KIT '. urldecode($item_kid_id);

			$result[] = array('name' => $item_kit->name, 'item_id' => $item_kid_id, 'item_number' => $item_kid_id,
							'cost_price' => $item_kit->total_cost_price, 'unit_price' => $item_kit->total_unit_price);
		}

		$data['items'] = $result;
		$barcode_config = $this->barcode_lib->get_barcode_config();
		// in case the selected barcode type is not Code39 or Code128 we set by default Code128
		// the rationale for this is that EAN codes cannot have strings as seed, so 'KIT ' is not allowed
		if($barcode_config['barcode_type'] != 'Code39' && $barcode_config['barcode_type'] != 'Code128')
		{
			$barcode_config['barcode_type'] = 'Code128';
		}
		$data['barcode_config'] = $barcode_config;

		// display barcodes
		$this->load->view("barcodes/barcode_sheet", $data);
	}

    public function items()
    {
        $data['table_headers'] = $this->xss_clean(get_price_list_items_table_headers());

        $this->load->view('price_lists/items', $data);
    }

    public function search_items()
    {
        $search = $this->input->get('search');
        $limit  = $this->input->get('limit');
        $offset = $this->input->get('offset');
        $sort   = $this->input->get('sort');
        $order  = $this->input->get('order');

        $price_lists = $this->Price_list_items->search($search, $limit, $offset, $sort, $order);
        $total_rows = $this->Price_list_items->get_found_rows($search);

        $data_rows = array();
        foreach($price_lists->result() as $price_list) {
            $data_rows[] = $this->xss_clean(get_price_list_items_data_row($price_list));
        }

        echo json_encode(array('total' => $total_rows, 'rows' => $data_rows));
    }

    public function view_list($id = -1) {
        $info = $this->Price_list_items->get_info($id);

        if($id == -1) {
            $info->price_list_id = 0;
        }
        foreach(get_object_vars($info) as $property => $value)
        {
            $info->$property = $this->xss_clean($value);
        }

        $data['price_list_info']  = $info;

        $this->load->view("price_lists/form_items", $data);
    }
}
?>
