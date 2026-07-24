<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Buttons</title>
  <!-- Add Bootstrap CSS link here -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
 <style>.loader,
        .loader:after {
            border-radius: 50%;
            width: 10em;
            height: 10em;
        }
        .loader {            
            margin: 60px auto;
            font-size: 10px;
            position: relative;
            text-indent: -9999em;
            border-top: 1.1em solid rgba(255, 255, 255, 0.2);
            border-right: 1.1em solid rgba(255, 255, 255, 0.2);
            border-bottom: 1.1em solid rgba(255, 255, 255, 0.2);
            border-left: 1.1em solid #ffffff;
            -webkit-transform: translateZ(0);
            -ms-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-animation: load8 1.1s infinite linear;
            animation: load8 1.1s infinite linear;
        }
        @-webkit-keyframes load8 {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        @keyframes load8 {
            0% {
                -webkit-transform: rotate(0deg);
                transform: rotate(0deg);
            }
            100% {
                -webkit-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
        #loadingDiv {
            position:absolute;;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background-color:#111;
        }
</style>
</head>
<body>
<style>
.icons-payment{text-align: center;
    width: 100%;
    margin-top: 40px; margin:0px 15px;}
.icons-payment ul{ margin:0px; padding:0px;}
.icons-payment ul li{     text-align: center;
    margin: 0px;
    padding: 0px;
    list-style: none;
    border: 1px solid #ccc;
    margin-bottom: 1p;
    margin-bottom: 22px;
    border-radius: 8px;
    box-shadow: 1px 1px 7px #ccc;
    padding: 7px 0px;}

.btn{box-shadow: 1px 1px 7px #ccc;
    font-size: 26px;
    padding: 13px 0px;  border-radius: 8px;font-weight: bold;}
</style>
<div class="container">
  <div class="row">
    <div class="col-12 text-center" style="margin-top:30px;">
      <h2 class="mb-4">Choose a Payment Option:</h2>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6 mb-3">

      <a href="{{ route('payment_success',['bookingId'=>$bookingId]) }}" class="btn btn-success btn-block">Success Payment</a>
    </div>
    <div class="col-md-6 mb-3">
      <a href="{{ route('payment_fail',['bookingId'=>$bookingId])}}" class="btn btn-danger btn-block">Fail Payment</a>
    </div>
    


<!-- Add Bootstrap JS and jQuery scripts here -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>$('body').append('<div style="" id="loadingDiv"><div class="loader">Loading...</div></div>');
$(window).on('load', function(){
  setTimeout(removeLoader, 2000); //wait for page load PLUS two seconds.
});
function removeLoader(){
    $( "#loadingDiv" ).fadeOut(500, function() {
      // fadeOut complete. Remove the loading div
      $( "#loadingDiv" ).remove(); //makes page more lightweight 
  });  
}</script>
</body>
</html>
