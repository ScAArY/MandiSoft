<?php
if (!defined('BASEPATH'))exit('No direct script access allowed');
if(!isset($multiple)){
?>
<style>
    hr{
        background:#000;
        border-color:#000;
        margin:1px;
    }
    h1,h2,h3,h4,h5,h6{
        margin:1px;
    }
    td,th{
        padding:5px 20px 5px 20px;
    }
    th{
        font-size:15px;
        text-align:center;
    }
    td{vertical-align: top;}
    tr.total{
        text-align: center;
        font-weight: 700;
        font-size: 15px;
    }

    table ,tr, th , td {
        border-style: dashed;
        border-color: #000;
        /*border-width:1.5px;*/
    }
    b{
       font-size: 95%;
    }
    span{
        font-size: 90%;
    }
    small{
        font-size: 70%;
    }
   .footer td{
        padding:4px;
        border: none;
    }

</style>
<script>
    function _print (){
        window.print();
    };
</script>
<a href="javascript:_print();" class="no-print">Print Ledger</a>
<?php
}
?>
<table id="LEDGER_TABLE" align="center" border="1px" cellspacing="0" width="100%">
<?php
if(!isset($multiple)){
?>
    <tr>
        <td align="center" colspan="2">
            <?php $this->load->view("slip/heading"); ?>
        </td>
    </tr>
<?php
}
?>
    <tr>
        <td width="45%">
            <span>दिनांक :- <?php echo date("d/m/Y",time()); ?></span><hr/>
            <span>लेजर क्रo :- <?php echo $ledger_info ->ledger_number; ?></span>
            <span>अकाउंट क्रo :- <?php echo $ledger_info ->acc_id; ?></span>
        </td>
        <td width="55%">
            <span>नाम :- <?php echo ($ledger_info ->acc_name_ll?$ledger_info ->acc_name_ll:$ledger_info ->acc_name).($ledger_info ->acc_name_ll?("(".$ledger_info->acc_name.")"):"");/*($ledger_info->acc_area?"($ledger_info->acc_area)":"");*/ ?></span>
            <hr />
            <?php if($ledger_info ->mobile_number1 or $ledger_info ->mobile_number2){ ?><span>मोबाइल नo :- <?php echo $ledger_info ->mobile_number1?$ledger_info ->mobile_number1:$ledger_info ->mobile_number2; ?></span><hr/>
                <?php } ?>
            <span>पता :- <?php echo $ledger_info->acc_area.", ".($ledger_info ->acc_address1?$ledger_info ->acc_address1:$ledger_info ->acc_city); ?></span>
        </td>
    </tr>
           <tr class="total">
                <td align="left">
                    Opening Balance
                </td>
                <td align="right">
                    <?php
                    echo ($start_balance ->new_balance<0)?(-1*$start_balance ->new_balance)."Dr":$start_balance ->new_balance."Cr";
                    ?>
                </td>
            </tr>
                <?php
/*            $payments=array();*
            $sales = array();*/
            $prev_sale=NULL;
            $total_amount=0;
            $total_payment=0;
            $total_rebate=0;
/*            foreach($payments as $pay){
                $payments[] =array(
                    "date"=>$value->payment_date,
                    "amount"=>$value->amount_received
                    );
            }*/
  /*          foreach ($ledger as $value) {
                if($prev_sale!=$value->sale_id){
/*                $sales[]=array(
                    "sale_id"=>$value->sale_id,
                    "date"=>$value->sale_date,
                    "datetime"=>$value->sale_datetime,
                    "quantity"=>$value->quantity,
                    "sale_rebate"=>$value->sale_rebate,
                    "rate"=>$value->rate);
                $prev_sale = $value->sale_id;
                }
            }*/
                ?>
    <?php
    if(count($payments) or count($sales)){
        ?>
            <tr class="heading" align="center">
                <th>जमा</th>
                <th>नावें</th>
            </tr>
    <?php
    }
    ?>
            <tr>
                    <td style="text-align: center">
                        <?php
                        //SELECT `ledger_number`,`acc_name`,`acc_area`,`acc_city`,
                        //if(account_balance<0,CONCAT("₹",(-1*account_balance)," Dr"),CONCAT("₹",account_balance," Cr")) FROM `accounts`
                        /*
                         * SELECT `ledger_number` as "Ledger Number",`acc_name` as Name,`acc_area`as Area,`acc_city`as City,
if(account_balance<0,CONCAT("₹",(-1*account_balance)," Dr"),CONCAT("₹",account_balance," Cr")) as "Closing  Balance" FROM `accounts`
                         */
                        
                        
                        foreach ($payments as $pay){
                            echo "<b>₹".$pay->amount_received.($pay->rebate?(" + ₹$pay->rebate REBATE( छुट )"):(""))."</b>&nbsp;&nbsp;&nbsp;&nbsp;<small>".date("d/m/Y",strtotime($pay->make_date)?strtotime($pay->make_date):strtotime($pay->payment_date))."</small>"
                                    ."<br/>"
                                    ."<i>पुराना:- ".($pay->old_balance?(($pay->old_balance<0)?(-1*$pay->old_balance)."Dr":($pay->old_balance)."Cr"):" N/A ")." &nbsp; नया:- ".($pay->new_balance?(($pay->new_balance<0)?(-1*$pay->new_balance)."Dr":($pay->new_balance)."Cr"):" N/A ")."</i>"
                                    . "<hr />";
                            $total_payment += $pay->amount_received;
                            }
                            ?>
                    </td>
                    <td>
                        <?php
            foreach ($sales as $value){
                        $total =$value->quantity * $value->rate;
                        $total_amount += $total;
                        $total_rebate += $value->sale_rebate;
                        echo "<span>(".($value->quantity)."$value->qnt_sign * ".$value->rate.")".($value->sale_rebate?" - ".$value->sale_rebate:"")." = </span><b>₹".($total - $value->sale_rebate)."</b>";
                        echo "&nbsp;&nbsp;&nbsp;<small>".date("d/m/Y",strtotime($value->sale_date)?strtotime($value->sale_date):strtotime($value->sale_datetime))."</small>".($value->qnt_sign=="Crt"?("<br/><i>($value->item_lot की भर्ती)</i>"):"");
                        echo "<br /><i>पुराना:- ".($value->old_balance?(($value->old_balance<0)?(-1*$value->old_balance)."Dr":($value->old_balance)."Cr"):" N/A ")." &nbsp; नया:- ".($value->new_balance?(($value->new_balance<0)?(-1*$value->new_balance)."Dr":($value->new_balance)."Cr"):" N/A ")."</i>";
                        echo "<hr />";
                        }
                        ?>
                    </td>
            </tr>
            <tr class="total">
                <td align="left">
                    <!--कुल-->Closing Balance:
                </td>
                <td align="right">
                    ₹<?php
                    //echo ($ledger_info -> account_balance<0)?(-1*$ledger_info -> account_balance)." बकाया":$ledger_info -> account_balance." जमा";
                    echo ($ledger_info -> account_balance<0)?(-1*$ledger_info -> account_balance)." Dr":$ledger_info -> account_balance." Cr";
                    ?>
                </td>
               <?php
                /*<td colspan="3">
                    <?php
                    echo $total_amount;//"$total_amount  = ".($total_amount - $total_rebate);
                    ?>
                </td>
                 */
                ?>
            </tr>
            <tr class="footer">
                <td></td>
                <td>
                    <?php $this->load->view("slip/footer"); ?>
                </td>
            </tr>
</table>
<br />
    <?php
//if(!isset($multiple)){
?>
<?php
if(!isset($multiple)){
?>
<div align="center"><small>लेज़र MANDISOFT द्वारा बनाया गया|<!--MANDISOFT Made with ♥ for MY FATHER--></small></div>
<br />
<?php
}
?>