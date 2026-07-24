@extends('layouts.admin')
@section('content')

<style>

.btn-flat{width: 100%;}
.mb-3 {
    margin-bottom: 20px;
}

.padde{ padding:90px 0px; text-align:center;}
.padea{ padding:24px 20px;overflow:hidden}
.padea h5{ font-size:18px; text-align:left; float:left; }
.padea span{ float: right;
    font-size: 21px;
    margin-top: 8px; font-weight:bold}
</style>


<div class="content">


<div class="panel panel-default">
<div class="panel-heading">
Transection Report
</div>
<div class="panel-body">
<div class="col-md-12 col-sm-12 col-xs-12">
<h4>Search data</h4>
</div>



<div class="col-md-3 col-sm-12 col-xs-12 mb-3">

<select class="form-control" name="status" id="status">
<option value="">Please Select Status </option>
<option value="pending">Pending</option>
<option value="confirmed">Confirmed</option>
<option value="cancelled">Cancelled</option>
<option value="declined">Declined</option>
<option value="completed">Completed</option>
<option value="refunded">Refunded</option>
</select>
</div>

<div class="col-md-3 col-sm-12 col-xs-12 mb-3">

<select class="form-control" name="status" id="status">
<option value="">Please Select Status </option>
<option value="pending">Pending</option>
<option value="confirmed">Confirmed</option>
<option value="cancelled">Cancelled</option>
<option value="declined">Declined</option>
<option value="completed">Completed</option>
<option value="refunded">Refunded</option>
</select>
</div>


<div class="col-md-3 col-sm-12 col-xs-12 mb-3">

<select class="form-control" name="status" id="status">
<option value="">Please Select Status </option>
<option value="pending">Pending</option>
<option value="confirmed">Confirmed</option>
<option value="cancelled">Cancelled</option>
<option value="declined">Declined</option>
<option value="completed">Completed</option>
<option value="refunded">Refunded</option>
</select>
</div>



<div class="col-md-3 col-sm-12 col-xs-12 mb-3">

<select class="form-control" name="status" id="status">
<option value="">Please Select Status </option>
<option value="pending">Pending</option>
<option value="confirmed">Confirmed</option>
<option value="cancelled">Cancelled</option>
<option value="declined">Declined</option>
<option value="completed">Completed</option>
<option value="refunded">Refunded</option>
</select>
</div>


<div class="col-md-9 col-sm-9 col-xs-9 ">


</div>
<div class="col-md-3 col-sm-12 col-xs-12 mb-3"><button type="submit" name="btn" class="btn btn-primary btn-flat">Filter</button></div>








<div class="col-md-4 col-sm-4 col-xs-4 ">
<div class="small-box bg-yellow text-center padde"><h1><i class="fas fa-calculator"></i> $170/.93k</h1>
<h4>Completed</h4>
<h4>Transaction</h4>
</div>

</div>

<div class="col-md-4 col-sm-4 col-xs-4  "><div class="small-box bg-maroon padde"><h1> <i class="fas fa-calculator"></i> $180/.93k</h1>
<h4>Refunded</h4>
<h4>Transaction</h4></div></div>




<div class="col-md-4 col-sm-4 col-xs-4 ">


<div class="row"><div class="col-md-12 col-sm-12 col-xs-12 "><div class="small-box bg-aqua text-center padea"><h5><i class="fas fa-users-cog"></i> Admin Earning</h5>  <span>$4545</span></div></div>
<div class="col-md-12 col-sm-12 col-xs-12 "><div class="small-box bg-primary text-center padea"><h5><i class="fas fa-store"></i> Store Earning</h5> <span>$4545</span></div></div>
<div class="col-md-12 col-sm-12 col-xs-12"><div class="small-box bg-success text-white text-center padea"><h5><i class="fas fa-truck"></i> Deliveryman Earning</h5> <span>$4545</span></div></div></div>



</div>















</div>
</div>





</div>






@endsection