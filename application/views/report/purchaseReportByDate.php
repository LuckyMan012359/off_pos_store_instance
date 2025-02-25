<link rel="stylesheet" href="<?php echo base_url(); ?>frequent_changing/css/report.css">

<div class="main-content-wrapper">

    <section class="content-header">
        <div class="row justify-content-between">
            <div class="col-6 p-0">
                <h3 class="top-left-header mt-2"><?php echo lang('purchase_report'); ?></h3>
            </div>
            <?php $this->view('updater/breadcrumb', ['firstSection'=> lang('report'), 'secondSection'=> lang('purchase_report')])?>
        </div>
    </section>
    
    
    <div class="box-wrapper"> 
        <!-- Report Header Start -->
        <div class="report_header">
            <h3 class="company_name"><?php echo escape_output($this->session->userdata('business_name'));?> </h3>
            <h5 class="outlet_info">
                <strong><?php echo lang('purchase_report'); ?></strong>
            </h5>
            <?php if(isset($outlet_id) && $outlet_id){
                $outlet_info = getOutletInfoById($outlet_id); 
            }?>
            <h5 class="outlet_info">
                <?php if(isset($outlet_id) && $outlet_id){ ?>
                    <strong><?php echo lang('outlet'); ?>: </strong> <?= escape_output($outlet_info->outlet_name); ?>
                <?php }?>
            </h5>
            <h5 class="outlet_info">
                <?php if(isset($outlet_id) && $outlet_id){ ?>
                    <strong><?php echo lang('address'); ?>: </strong> <?= escape_output($outlet_info->address); ?>
                <?php } ?>
            </h5>
            <h5 class="outlet_info">
                <?php if(isset($outlet_id) && $outlet_id){ ?>
                    <strong><?php echo lang('email'); ?>: </strong> <?= escape_output($outlet_info->email); ?>
                <?php } ?>
            </h5>
            <h5 class="outlet_info">
                <?php if(isset($outlet_id) && $outlet_id){ ?>
                    <strong><?php echo lang('phone'); ?>: </strong> <?= escape_output($outlet_info->phone); ?>
                <?php } ?>
            </h5>
            <?php if(isset($start_date) && $start_date != '' && $start_date != '1970-01-01' || isset($end_date) && $end_date != '' && $end_date != '1970-01-01'){ ?>
            <h5 class="outlet_info">
                <strong><?php echo lang('date');?>:</strong>
                <?php
                    if(!empty($start_date) && $start_date != '1970-01-01') {
                        echo dateFormat($start_date);
                    }
                    if((isset($start_date) && isset($end_date)) && ($start_date != '1970-01-01' && $end_date != '1970-01-01')){
                        echo ' - ';
                    }
                    if(!empty($end_date) && $end_date != '1970-01-01') {
                        echo dateFormat($end_date);
                    }
                ?>
            </h5>
            <?php } ?>
            <h5 class="outlet_info">
                <?php if(isset($report_generate_time) && $report_generate_time){
                    echo $report_generate_time;
                } ?>
            </h5>
        </div>
        <!-- Report Header End -->



        <div class="table-box"> 
            <div class="box-body">
                <div class="table-responsive">
                    
                    <input type="hidden" class="datatable_name"  data-filter="yes" data-title="<?php echo lang('purchase_report'); ?>" data-id_name="datatable">
                    <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th class="w-5"><?php echo lang('sn'); ?></th>
                                        <th class="w-10"><?php echo lang('ref_no'); ?></th>
                                        <th class="w-10"><?php echo lang('date_and_time'); ?></th>
                                        <th class="w-10"><?php echo lang('purchase_date'); ?></th>
                                        <th class="w-10"><?php echo lang('supplier'); ?></th>
                                        <th class="w-20"><?php echo lang('items'); ?></th>
                                        <th class="w-10 text-center"><?php echo lang('grand_total'); ?></th>
                                        <th class="w-10 text-center"><?php echo lang('paid'); ?></th>
                                        <th class="w-10 text-center"><?php echo lang('due'); ?></th>
                                        <th class="w-15 text-right"><?php echo lang('purchased_by'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sum_of_grand_total = 0;
                                    $sum_of_paid = 0;
                                    $sum_of_due = 0;
                                    if (isset($purchaseReportByDate)):
                                        foreach ($purchaseReportByDate as $key => $value) {
                                            $sum_of_grand_total += $value->grand_total;
                                            $sum_of_paid += $value->paid;
                                            $sum_of_due += $value->due_amount;
                                            $key++;
                                            ?>
                                            <tr>
                                                <td><?php echo $key; ?></td>
                                                <td><?php echo escape_output($value->reference_no); ?></td>
                                                <td><?php echo dateFormat($value->added_date) ?></td>
                                                <td><?php echo dateFormat($value->date) ?></td>
                                                <td><?php echo escape_output($value->supplier_name); ?></td>
                                                <td><?php 
                                                $items = getPurchaseItemsByPurchaseId($value->id);
                                                if($items){
                                                    echo "<strong>Name(Code)-Qty(Unit)-Price</strong><br>";
                                                    foreach($items as $item){
                                                        echo escape_output($item->name). '('. $item->code . ')-' . $item->quantity_amount . '(' .$item->unit_name . ')-' . getAmtCustom($item->unit_price) . "<br>";
                                                    }
                                                }
                                                ?></td>
                                                <td class="text-center"><?php echo getAmtCustom($value->grand_total) ?></td>
                                                <td class="text-center"><?php echo getAmtCustom($value->paid) ?></td>
                                                <td class="text-center"><?php echo getAmtCustom($value->due_amount) ?></td>
                                                <td><?php echo escape_output($value->user_name) ?></td>
                                            </tr>
                                            <?php
                                        }
                                    endif;
                                    ?>
                                <tr>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th class="op_right"><?php echo lang('total'); ?> </th>
                                        <th class="text-center"><?php echo getAmtCustom($sum_of_grand_total) ?></th>
                                        <th class="text-center"><?php echo getAmtCustom($sum_of_paid) ?></th>
                                        <th class="text-center"><?php echo getAmtCustom($sum_of_due) ?></th>
                                        <th></th>
                                    </tr>
                                </tbody>
                                
                    </table>
                </div>
            </div>
        </div> 
    </div>   
</div>







<div class="filter-overlay"></div>
<div id="product-filter" class="filter-modal">
    <div class="filter-modal-body">
        <header>
                <h3 class="filter-modal-title"><span><?php echo lang('FilterOptions'); ?></span></h3>
                <button type="button" class="close-filter-modal" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        <i data-feather="x"></i>
                    </span>
                </button>
        </header>
        <?php echo form_open(base_url() . 'Report/purchaseReportByDate') ?>
        <div class="row">
            <div class="col-sm-12 col-md-6 mb-2">
                <div class="form-group">
                    <input  autocomplete="off" type="text" name="startDate" readonly class="form-control customDatepicker" placeholder="<?php echo lang('start_date'); ?>" value="<?php echo set_value('startDate'); ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-2">
                <div class="form-group">
                    <input  autocomplete="off" type="text" id="endMonth" name="endDate" readonly class="form-control customDatepicker" placeholder="<?php echo lang('end_date'); ?>" value="<?php echo set_value('endDate'); ?>">
                </div>
            </div>
            <div class="col-sm-12 col-md-6 mb-2">
                <div class="form-group">
                    <select  class="form-control select2" id="supplier_id" name="supplier_id">
                        <?php
                            $role = $this->session->userdata('role');
                            if($role == '1'){
                        ?>
                        <option value=""><?php echo lang('select'); ?> <?php echo lang('supplier') ?></option>
                        <?php } ?>
                        <?php
                        foreach ($suppliers as $value):
                            ?>
                            <option <?php isset($supplier_id) && $supplier_id && $supplier_id == $value->id ? 'selected' : '' ?> <?= set_select('supplier_id',$value->id)?>  value="<?php echo escape_output($value->id) ?>"><?php echo escape_output($value->name) ?></option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>
            <?php
                if(isLMni()):
            ?>
            <div class="col-sm-12 col-md-6 mb-2">
                <div class="form-group">
                    <select  class="form-control select2 ir_w_100" id="outlet_id" name="outlet_id">
                        <?php
                            $role = $this->session->userdata('role');
                            if($role == '1'){
                        ?>
                        <option value=""><?php echo lang('select_outlet') ?></option>
                        <?php } ?>
                        <?php
                        $outlets = getOutletsForReport();
                        foreach ($outlets as $value):
                            ?>
                            <option <?= set_select('outlet_id',$value->id)?>  value="<?php echo escape_output($value->id) ?>"><?php echo escape_output($value->outlet_name) ?></option>
                            <?php
                        endforeach;
                        ?>
                    </select>
                </div>
            </div>
            <?php
                endif;
            ?>
            <div class="clear-fix"></div>
            <div class="col-12 mb-2">
                <button type="submit" name="submit" value="submit" class="new-btn">
                    <iconify-icon icon="solar:hourglass-broken" width="22"></iconify-icon>
                    <?php echo lang('submit'); ?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>





<?php $this->view('updater/reuseJs_w_pagination'); ?>