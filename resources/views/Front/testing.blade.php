@extends('layouts.admin')
@section('content')
<div class="content">


<!--/*seaction 1 start here*/-->
  <div class="box">
    <div class="panel-body">
      <div class="nav-tabs-custom">
        <ul class="cus nav nav-tabs f-14" role="tablist">
         <li class="active"> <a href="">Over View</a> </li>
          <li class=""> <a href="">Edit Customer</a> </li>
          <li class=""> <a href="#">Properties</a> </li>
          <li class=""> <a href="#">Bookings</a> </li>
          <li class=""> <a href="#">Payouts</a> </li>
          <li class=""> <a href="#">Payment Methods</a> </li>
          <li class=""> <a href="#">Wallet</a> </li>
        </ul>
        <div class="clearfix"></div>
      </div>
    </div>
  </div>
  
    <!--/*seaction 1  end*/-->
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
      <!--/*seaction 4 start here*/-->
  
  <div class="row g-3 coenr-capitalize">
<div class="col-md-4">
<div class="cardbg-1">
<div class="card-body text-center d-flex flex-column justify-content-center align-items-center">
<h5 class="">
Collected cash by store
</h5>
<div class="d-flex align-items-center justify-content-center mt-3">
<div class="">
<img src="https://onlinemart.cssrender.com/public/images/icon/cash-new.png" alt="img">
</div>
<h2 class="cash--title text-white">$ 0.00</h2>
</div>
</div>
<div class="">
<button class="btn" id="collect_cash" type="button">Collect cash from store
</button>
</div>
</div>
</div>
<div class="col-md-8">
<div class="row g-3">
<div class="col-sm-6">
<div class="caredoane cardbg-2">
<h4 class="title">$ 0.00</h4>
<div class="subtitle">Pending Withdraw</div>
<img class="sweicon" src="https://onlinemart.cssrender.com/public/images/icon/cash-withdrawal.png" alt="transaction">
</div>
</div>
<div class="col-sm-6">
<div class="caredoane  cardbg-3">
<h4 class="title">$ 0.00</h4>
<div class="subtitle">Total withdrawal amount</div>
<img class="sweicon" src="https://onlinemart.cssrender.com/public/images/icon/atm.png" alt="transaction">
</div>
</div>
<div class="col-sm-6 mt-3 mt-3">
<div class="caredoane cardbg-4">
<h4 class="title">$ 0.00</h4>
<div class="subtitle">Withdrawable balance</div>
<img class="sweicon" src="https://onlinemart.cssrender.com/public/images/icon/atm.png" alt="transaction">
</div>
</div>
<div class="col-sm-6 mt-3">
<div class="caredoane cardbg-5">
<h4 class="title">$ 0.00</h4>
<div class="subtitle">Total earning</div>
<img class="sweicon" src="https://onlinemart.cssrender.com/public/images/icon/earning.png" alt="transaction">
</div>
</div>
</div>
</div>
</div>
  
  
   <!--/*seaction 4  here*/-->
  
  
  
  
  

  
  
  
  <!--/*seaction 2 start here*/-->
  <div class="panel panel-default">
    <div class="panel-heading"> test user </div>
    <div class="panel-body">
      <form class="form-horizontal" action="" id="edit_customer" method="post" name="signup_form" accept-charset="UTF-8" novalidate="novalidate">
        <p class="text-black text-center mb-0 f-18 mt-1 ">Update Customer Information</p>
        <input type="hidden" name="_token" value="WVhpMmKLgLUgtog4tbFOrogMzZQ6IgVWE0VuEVFe">
        <input type="hidden" name="customer_id" id="user_id" value="1">
        <input type="hidden" name="default_country" id="default_country" value="us" class="form-control">
        <input type="hidden" name="carrier_code" id="carrier_code" value="1" class="form-control">
        <input type="hidden" name="formatted_phone" id="formatted_phone" value="+1123" class="form-control">
        <div class="box-body">
          <div class="form-group row mt-2">
            <label for="exampleInputPassword1" class="control-label col-sm-3 fw-bold text-md-end mb-2 mb-md-0">First Name<span class="text-danger">*</span></label>
            <div class="col-sm-5">
              <input type="text" class="form-control" name="first_name" id="first_name" value="test" placeholder="">
              <span id="first_name-error" class="text-danger"></span> </div>
          </div>
          <div class="form-group row mt-3">
            <label for="exampleInputPassword1" class="control-label col-sm-3 fw-bold text-md-end mb-2 mb-md-0">Last Name<span class="text-danger">*</span></label>
            <div class="col-sm-5">
              <input type="text" class="form-control" name="last_name" id="last_name" value="user" placeholder="">
              <span id="last_name-error" class="text-danger"></span> </div>
          </div>
          <div class="form-group row mt-3">
            <label for="exampleInputPassword1" class="control-label col-sm-3 fw-bold text-md-end mb-2 mb-md-0">Email<span class="text-danger">*</span></label>
            <div class="col-sm-5">
              <input type="text" class="form-control error" name="email" id="email" value="test@tukki.com" placeholder="">
              <span id="email-error" class="text-danger"></span>
              <div id="emailError"></div>
            </div>
          </div>
          <div class="form-group row mt-3">
            <label for="exampleInputPassword1" class="control-label col-sm-3 fw-bold text-md-end mb-2 mb-md-0">Password</label>
            <div class="col-sm-5">
              <input type="password" class="form-control" name="password" id="password" placeholder="">
              <span id="password-error" class="text-danger"></span> </div>
            <div class="col-sm-3"> <small class="f-12">Enter new password only. Leave blank to use existing
              password.</small> </div>
          </div>
          <div class="form-group row mt-3">
            <label for="exampleInputPassword1" class="control-label col-sm-3 fw-bold text-md-end mb-2 mb-md-0">Phone</label>
            <div class="col-sm-5">
              <div class="intl-tel-input allow-dropdown separate-dial-code iti-sdc-2">
                <input type="tel" class="form-control" id="phone" name="phone" value="+134546677" autocomplete="off" placeholder="201-555-0123">
              </div>
              <span id="phone-error" class="text-danger"></span> <span id="tel-error" class="text-danger"></span> </div>
          </div>
          <div class="form-group row mt-3">
            <label for="exampleInputPassword1" class="control-label col-sm-3 fw-bold text-md-end mb-2 mb-md-0">Status</label>
            <div class="col-sm-5">
              <select class="form-control" name="status" id="status">
                <option value="Active" selected=""> Active</option>
                <option value="Inactive"> Inactive</option>
              </select>
            </div>
          </div>
        </div>
        <div class="box-footer newdpe">
          <button type="submit" class="btn btn-info f-14 text-white nekr mt-4" id="submitBtn">Submit</button>
          <button type="reset" class="btn btn-danger f-14 mt-4">Reset</button>
        </div>
      </form>
    </div>
  </div>
  
  
   <!--/*seaction 2  here*/-->
  
  
  
  
  
    <!--/*seaction 3 start here*/-->

  <div class="box">
    <div class="box-body">
      <form class="form-horizontal" enctype="" action="" method="GET" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="WVhpMmKLgLUgtog4tbFOrogMzZQ6IgVWE0VuEVFe">
        <input class="form-control" id="startfrom" type="hidden" name="from" value="">
        <input class="form-control" id="endto" type="hidden" name="to" value="">
        <div class="row align-items-center date-parent">
          <div class="col-md-3 col-sm-12 col-xs-12">
            <label>Date Range</label>
            <div class="input-group col-xs-12">
              <input type="text" class="form-control" id="daterange-btn">
              <span class="input-group-addon"><i class="fa fa-calendar"></i></span> </div>
          </div>
          <div class="col-md-3 col-sm-3 col-xs-12">
            <label>Status</label>
            <select class="form-control" name="status" id="status">
              <option value="">All</option>
              <option value="Listed">Listed</option>
              <option value="Unlisted">Unlisted</option>
            </select>
          </div>
          <div class="col-md-3 col-sm-3 col-xs-12">
            <label>Space Type</label>
            <select class="form-control" name="space_type" id="space_type">
              <option value="">All</option>
              <option value="1">Entire home/apt</option>
              <option value="2">Private room</option>
              <option value="3">Shared room</option>
            </select>
          </div>
          <div class="col-md-3 col-sm-3 col-xs-4 d-flex gap-2 mt-4">
            <button type="submit" name="btn" class="btn btn-primary btn-flat f-14 rounded">Filter</button>
            <button type="button" name="reset_btn" id="reset_btn" class="btn btn-primary btn-flat f-14 rounded">Reset</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <div class="panel panel-default">
    <div class="panel-heading"> Property List </div>
    <div class="panel-body">
      <div class="table-responsive">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
          <div class="dataTables_length" id="DataTables_Table_0_length">
            <label>Show
            <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0" class="form-control input-sm">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            entries</label>
          </div>
          <div class="dt-buttons"><a class="btn buttons-select-all btn-primary" tabindex="0" aria-controls="DataTables_Table_0" href="#"><span>Select all</span></a><a class="btn buttons-select-none btn-primary disabled" tabindex="0" aria-controls="DataTables_Table_0" href="#"><span>Deselect all</span></a><a class="btn buttons-copy buttons-html5 btn-default" tabindex="0" aria-controls="DataTables_Table_0" href="#"><span>Copy</span></a><a class="btn buttons-csv buttons-html5 btn-default" tabindex="0" aria-controls="DataTables_Table_0" href="#"><span>CSV</span></a><a class="btn buttons-excel buttons-html5 btn-default" tabindex="0" aria-controls="DataTables_Table_0" href="#"><span>Excel</span></a><a class="btn buttons-pdf buttons-html5 btn-default" tabindex="0" aria-controls="DataTables_Table_0" href="#"><span>PDF</span></a><a class="btn buttons-print btn-default" tabindex="0" aria-controls="DataTables_Table_0" href="#"><span>Print</span></a><a class="btn buttons-collection buttons-colvis btn-default" tabindex="0" aria-controls="DataTables_Table_0" href="#"><span>Columns</span></a><a class="btn btn-danger" tabindex="0" aria-controls="DataTables_Table_0" href="#"><span>Delete selected</span></a></div>
          <div id="DataTables_Table_0_filter" class="dataTables_filter">
            <label>Search:
            <input type="search" class="form-control input-sm" placeholder="" aria-controls="DataTables_Table_0">
            </label>
          </div>
          <div class="dataTables_scroll">
            <div class="dataTables_scrollHead" style="overflow: hidden; position: relative; border: 0px; width: 100%;">
              <div class="dataTables_scrollHeadInner" style="box-sizing: content-box; width: 1057px; padding-right: 0px;">
                <table class="table table-bordered table-striped table-hover datatable datatable-Property dataTable no-footer" role="grid" style="margin-left: 0px; width: 1057px;">
                  <thead>
                    <tr role="row">
                      <th width="10" class="select-checkbox sorting_disabled" rowspan="1" colspan="1" style="width: 9.48438px;" aria-label="
"> </th>
                      <th class="sorting_desc" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 13.9531px;" aria-sort="descending" aria-label="
ID
: activate to sort column ascending"> ID </th>
                      <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 75.7969px;" aria-label="
Title
: activate to sort column ascending"> Title </th>
                      <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 96.9688px;" aria-label="
Host Name
: activate to sort column ascending"> Host Name </th>
                      <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 63.6719px;" aria-label="
Property Rating
: activate to sort column ascending"> Property Rating </th>
                      <th width="50" class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 49.1406px;" aria-label="
Price
: activate to sort column ascending"> Price </th>
                      <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 58.4375px;" aria-label="
City / Town / District
: activate to sort column ascending"> City / Town / District </th>
                      <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 53.9062px;" aria-label="
Is Verified
: activate to sort column ascending"> Is Verified </th>
                      <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 60.9531px;" aria-label="
Featured
: activate to sort column ascending"> Featured </th>
                      <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 43.9688px;" aria-label="
Status
: activate to sort column ascending"> Status </th>
                      <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 14.7188px;" aria-label="&amp;nbsp;
">&nbsp; </th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
            <div class="dataTables_scrollBody" style="position: relative; overflow: auto; width: 100%;">
              <table class="table table-bordered table-striped table-hover datatable datatable-Property dataTable no-footer" id="DataTables_Table_0" role="grid" aria-describedby="DataTables_Table_0_info" style="width: 1059px;">
                <thead>
                  <tr role="row" style="height: 0px;">
                    <th width="10" class="select-checkbox sorting_disabled" rowspan="1" colspan="1" style="width: 9.48438px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="
"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;"> </div></th>
                    <th class="sorting_desc" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 13.9531px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-sort="descending" aria-label="
ID
: activate to sort column ascending"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;"> ID </div></th>
                    <th class="sorting" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 75.7969px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="
Title
: activate to sort column ascending"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;"> Title </div></th>
                    <th class="sorting" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 96.9688px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="
Host Name
: activate to sort column ascending"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;"> Host Name </div></th>
                    <th class="sorting" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 63.6719px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="
Property Rating
: activate to sort column ascending"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;"> Property Rating </div></th>
                    <th width="50" class="sorting" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 49.1406px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="
Price
: activate to sort column ascending"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;"> Price </div></th>
                    <th class="sorting" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 58.4375px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="
City / Town / District
: activate to sort column ascending"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;"> City / Town / District </div></th>
                    <th class="sorting" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 53.9062px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="
Is Verified
: activate to sort column ascending"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;"> Is Verified </div></th>
                    <th class="sorting" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 60.9531px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="
Featured
: activate to sort column ascending"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;"> Featured </div></th>
                    <th class="sorting" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 43.9688px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="
Status
: activate to sort column ascending"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;"> Status </div></th>
                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 14.7188px; padding-top: 0px; padding-bottom: 0px; border-top-width: 0px; border-bottom-width: 0px; height: 0px;" aria-label="&amp;nbsp;
"><div class="dataTables_sizing" style="height: 0px; overflow: hidden;">&nbsp; </div></th>
                  </tr>
                </thead>
                <tbody>
              
                  
                  <tr data-entry-id="18" role="row" class="odd">
                    <td class=" select-checkbox"></td>
                    <td class="sorting_1"> 18 </td>
                    <td> 6 room duplex villa with swimming pool for rent in attoban </td>
                    <td> tom </td>
                    <td> 3.00 </td>
                    <td> USD 44.00 </td>
                    <td> Woroba </td>
                    <td> No </td>
                    <td> No </td>
                    <td> Active </td>
                    <td><a class="btn btn-xs btn-primary" href="https://tukki.cssrender.com/admin/properties/18"> <i class="fa fa-eye" aria-hidden="true"></i> </a> <a style="margin-bottom:5px;margin-top:5px" class="btn btn-xs btn-info" href="https://tukki.cssrender.com/admin/properties/18/edit"> <i class="fa fa-pencil" aria-hidden="true"></i> </a>
                      <form action="https://tukki.cssrender.com/admin/properties/18" method="POST" onsubmit="return confirm('Are you sure?');" style="display: inline-block;">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_method" value="DELETE">
                        <input type="hidden" name="_token" value="yVAJTYYjoYoRKI8sNODQPgNdeuoElarvr8Ps7NAU">
                        <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                      </form></td>
                  </tr>
                  
                </tbody>
              </table>
            </div>
          </div>
          <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">Showing 1 to 50 of 50 entries</div>
          <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
            <ul class="pagination">
              <li class=" previous disabled" id="DataTables_Table_0_previous"><a href="#" aria-controls="DataTables_Table_0" data-dt-idx="0" tabindex="0">Previous</a></li>
              <li class=" active"><a href="#" aria-controls="DataTables_Table_0" data-dt-idx="1" tabindex="0">1</a></li>
              <li class=" next disabled" id="DataTables_Table_0_next"><a href="#" aria-controls="DataTables_Table_0" data-dt-idx="2" tabindex="0">Next</a></li>
            </ul>
          </div>
          <div class="actions"></div>
        </div>
        
      </div>
    </div>
  </div>
   <!--/*seaction 3  here*/-->
  
  
  
  
  
  
</div>
@endsection