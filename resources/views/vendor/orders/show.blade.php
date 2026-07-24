@extends('vendor.layout')
@section('content')
<div class="content">

<div class="row">
     <!-- Order Details Section -->
<div class="col-md-4">
        <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #18bebd; color: white; display: flex; justify-content: space-between; align-items: center;">
                    <span>{{ trans('global.orders') }} {{ trans('global.detail') }}</span>
                    <button id="generatePDF" class="btn btn-success btn-sm">
                        Generate PDF
                    </button>
                </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>{{ trans('global.id') }}</th>
                        <td>{{ $Data->id }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('global.reservation_code') }}</th>
                        <td>{{ $Data->token }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('global.booked') }} {{ trans('global.date') }}</th>
                        <td>{{ \Carbon\Carbon::parse($Data->created_at)->format('h:i A, Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('global.itemid') }}</th>
                        <td>{{ $Data->item->title }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('global.check_in') }}</th>
                        <td>{{ $Data->start_time }}, {{ $Data->check_in }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('global.check_out') }}</th>
                        <td>{{ $Data->end_time }}, {{ $Data->check_out }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('global.address') }}</th>
                        <td>{{ $Data->item->address }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('global.status') }}</th>
                        <td><strong>{{ $Data->status }} </strong></td>
                    </tr>

                    @if($Data->status === 'Cancelled')
                    <tr>
                        <th>{{ trans('global.cancellation_reasion') }}</th>
                        <td>{{ $Data->cancellation_reasion }}</td>
                    </tr>
                    @endif
                    
                </table>
            </div>
        </div>
    </div>


    <div class="col-md-4">
        <div class="panel panel-default" style = "min-height: 435px;">
            <div class="panel-heading" style="background-color: #18bebd; color: white;">
            {{ trans('global.customer') }} {{ trans('global.detail') }}
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>{{ trans('global.name') }}</th>
                        <td>{{ $Data->user->first_name ?? '' }} {{ $Data->user->last_name ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('global.email') }}</th>
                        <td>{{ $Data->user->email ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('global.phone') }}</th>
                        <td>{{ $Data->user->phone_country ?? '' }} {{ $Data->user->phone ?? '' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel panel-default" style = "min-height: 435px;">
            <div class="panel-heading" style="background-color: #18bebd; color: white;">
                {{ trans('global.Payments_title') }} {{ trans('global.detail') }}
            </div>
            <div class="panel-body">
                <table class="table table-bordered table-striped">
                    <tr>
                        <th>{{ trans('global.payment_method') }}</th>
                        <td>{{ $Data->payment_method }}</td>
                    </tr>
                    <tr>
                        <th>{{ trans('global.duration') }}</th>
                        <td>{{ $Data->total_night }} Days</td>
                    </tr>
                    <tr>
                        <th>{{ trans('global.base_price') }}</th>
                        <td>{{ $general_default_currency->meta_value ?? ''}} {{ $Data->base_price }} </td>
                    </tr>
                    
                    @if(isset($Data->extension->doorStep_price) && $Data->extension->doorStep_price > 0)
                    <tr>
                        <th>{{ trans('global.doorstep_delivery_price') }}</th>
                        <td>+ {{ $general_default_currency->meta_value ?? ''}} {{ $Data->extension->doorStep_price }}</td>
                    </tr>
                    @endif

                    @if($Data->discount_price > 0)
                    <tr>
                        <th>{{ trans('global.discount') }}</th>
                        <td>- {{ $general_default_currency->meta_value ?? ''}} {{ $Data->discount_price }}</td>
                    </tr>
                    @endif

                    
                    @if($Data->coupon_discount > 0)
                    <tr>
                        <th> {{ trans('global.coupons') }} {{ trans('global.discount') }}</th>
                        <td>- {{ $general_default_currency->meta_value ?? ''}} {{ $Data->coupon_discount }}</td>
                    </tr>
                    @endif

                    @if($Data->iva_tax > 0)
                    <tr>
                        <th>{{ trans('global.iva_tax') }}</th>
                        <td>+ {{ $general_default_currency->meta_value ?? ''}} {{ $Data->iva_tax }}</td>
                    </tr>
                    @endif

                    @if($Data->security_money > 0)
                    <tr>
                        <th>{{ trans('global.security_money') }}</th>
                        <td>+ {{ $general_default_currency->meta_value ?? ''}} {{ $Data->security_money }}</td>
                    </tr>
                    @endif

                    @if($Data->wall_amt > 0)
                    <tr>
                        <th>{{ trans('global.wall_amt') }}</th>
                        <td>{{ $general_default_currency->meta_value ?? ''}} {{ $Data->wall_amt }}</td>
                    </tr>
                    @endif

                    <tr>
                        <th>{{ trans('global.total') }}</th>
                        <td><strong>{{ $general_default_currency->meta_value ?? ''}} {{ $Data->total }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>



</div>
@endsection

@section('scripts')
@parent
<script>
     document.getElementById('generatePDF').addEventListener('click', function () {

// Booking details
var bookingDetails = [
{
table: {
    widths: ['30%', '70%'], // Define column widths
    body: [
        [
            { text: 'Booking ID:', bold: true },
            { text: "{{ $Data->id }}" }
        ],
        [
            { text: 'Reservation Code:', bold: true },
            { text: "{{ $Data->token }}" }
        ],
        [
            { text: 'Booked Date:', bold: true },
            { text: "{{ \Carbon\Carbon::parse($Data->created_at)->format('h:i A, Y-m-d') }}" }
        ],
        [
            { text: 'Item:', bold: true },
            { text: "{{ $Data->item->title }}" }
        ],
        [
            { text: 'Check-in:', bold: true },
            { text: "{{ $Data->check_in }}" }
        ],
        [
            { text: 'Check-out:', bold: true },
            { text: "{{ $Data->check_out }}" }
        ],
        [
            { text: 'Address:', bold: true },
            { text: "{{ $Data->item->address }}" }
        ],
        [
            { text: 'Status:', bold: true },
            { text: "{{ $Data->status }}" }
        ]
    ]
},
style: 'content'
}
];


// Customer details
        // Customer details
var customerDetails = [
    {
        table: {
            widths: ['30%', '70%'], // Define column widths to match booking and payment details
            body: [
                [
                    { text: 'Name:', bold: true },
                    { text: "{{ $Data->user->first_name ?? '' }} {{ $Data->user->last_name ?? '' }}" }
                ],
                [
                    { text: 'Email:', bold: true },
                    { text: "{{ $Data->user->email ?? 'N/A' }}" }
                ],
                [
                    { text: 'Phone:', bold: true },
                    { text: "{{ $Data->user->phone_country ?? '' }} {{ $Data->user->phone ?? 'N/A' }}" }
                ]
            ]
        },
        style: 'content'
    }
];


// Payment details
var paymentDetails = [
{
table: {
    widths: ['30%', '70%'], // Define column widths to match booking details
    body: [
        [
            { text: 'Payment Method:', bold: true },
            { text: "{{ $Data->payment_method ?? 'N/A' }}" }
        ],
        [
            { text: 'Duration:', bold: true },
            { text: "{{ $Data->total_night ?? 'N/A' }} Days" }
        ],
        [
            { text: 'Base Price:', bold: true },
            { text: "{{ $general_default_currency->meta_value ?? '' }} {{ $Data->base_price ?? 'N/A' }}" }
        ],
        
        @if(isset($Data->extension->doorStep_price) && $Data->extension->doorStep_price > 0)
        [
            { text: 'Doorstep Price:', bold: true },
            { text: "{{ $general_default_currency->meta_value ?? '' }} {{ $Data->extension->doorStep_price ?? 'N/A' }}" }
        ],
        @endif
        
        @if($Data->discount_price > 0)
        [
            { text: 'Discount:', bold: true },
            { text: "- {{ $general_default_currency->meta_value ?? '' }} {{ $Data->discount_price ?? 'N/A' }}" }
        ],
        @endif
        
        @if($Data->iva_tax > 0)
        [
            { text: 'IVA Tax:', bold: true },
            { text: "+ {{ $general_default_currency->meta_value ?? '' }} {{ $Data->iva_tax ?? 'N/A' }}" }
        ],
        @endif
        
        @if($Data->security_money > 0)
        [
            { text: 'Security Money:', bold: true },
            { text: "+ {{ $general_default_currency->meta_value ?? '' }} {{ $Data->security_money ?? 'N/A' }}" }
        ],
        @endif
        
        @if($Data->wall_amt > 0)
        [
            { text: 'Wall Amount:', bold: true },
            { text: "{{ $general_default_currency->meta_value ?? '' }} {{ $Data->wall_amt ?? 'N/A' }}" }
        ],
        @endif
        
        [
            { text: 'Total:', bold: true },
            { text: "{{ $general_default_currency->meta_value ?? '' }} {{ $Data->total ?? 'N/A' }}" }
        ]
    ]
},
style: 'content'
}
];


// Define the document content
var docDefinition = {
    content: [
        { text: 'Booking Details', style: 'headerCenter' },
        ...bookingDetails,
        { text: 'Customer Details', style: 'headerCenter' },
        ...customerDetails,
        { text: 'Payments Details', style: 'headerCenter' },
        ...paymentDetails
    ],
    styles: {
        header: {
            fontSize: 14,
            bold: true,
            margin: [0, 10, 0, 10]
        }, 
        headerCenter: {
            fontSize: 14,
            bold: true,
            alignment: 'center',
            margin: [0, 20, 0, 10]
        },
        content: {
            fontSize: 10,
            margin: [0, 5, 0, 5]
        }
    }
};

// Generate the PDF
pdfMake.createPdf(docDefinition).download('booking-summary-' + '{{ $Data->token }}' + '.pdf');
});
</script>
@endsection