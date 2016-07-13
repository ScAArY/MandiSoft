<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
Class item_purchase extends CI_Model {
    function purchase($a){
        
        $amount = ($a["quantity"] * $a["rate"]) - $a["rebate"];
        
        /*
         * 
         * *********Transaction Started If any Query Fails ....
         * * Others will also be rollbacked (Any changes made after *this* point will be discarded)***
         * 
         */
        $this->db->trans_start();
        
        /*
         * Sell is registered in `item_sale` Table
         */
        $this->db->insert("item_purchase",$a);
        $purchase_id = $this->db->insert_id();
        /*
         * The net amount of sale is registered either as lending or cash payment in accordance with PAYMENT MODE
         */
        $this->payment->new_purchase(array("acc_id"=>$a["sold_to"],"amount"=>$amount,"crates"=>$a["crates"],"purchase_id"=>$sale_id,"payment_mode"=>$a["payment_mode"]));
        
        $this->db->trans_complete();
        /*
         * Transaction is completed either all data is saved or nothing is saved
         */
        if($this->db->trans_status() == true){
            return $sale_id;
        }
        else{
            return false;
        }        
    }
    
    function makePayment($a,$b){
            $this->db->reset_query();
            $this->db->set('amount_received', 'amount_received + '.$b,false);
            $this->db->where('purchase_id',$a);
            $this->db->update('item_purchase');
            $this->db->reset_query();
    }
}
