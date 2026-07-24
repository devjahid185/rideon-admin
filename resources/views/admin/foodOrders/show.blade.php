@extends('layouts.admin')
@section('content')
    <div class="content">
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Food Order: {{ $order->order_number }}
                    </div>
                    <div class="panel-body">
                        <div class="row" style="margin-bottom: 16px;">
                            <div class="col-md-6">
                                <h4>Order Summary</h4>
                                <p><strong>Status:</strong> <span class="label label-info">{{ $order->status }}</span></p>
                                <p><strong>Payment:</strong> {{ $order->payment_method }} / {{ $order->payment_status }}</p>
                                <p><strong>Total:</strong> {{ number_format((float)$order->total_amount, 2) }}</p>
                                <p><strong>Address:</strong> {{ $order->delivery_address }}</p>
                                <p><strong>Customer Note:</strong> {{ $order->customer_note ?? '-' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h4>Party</h4>
                                <p><strong>Customer:</strong>
                                    {{ optional($order->customer)->first_name }} {{ optional($order->customer)->last_name }}
                                    ({{ optional($order->customer)->phone_country }}{{ optional($order->customer)->phone }})
                                </p>
                                <p><strong>Driver:</strong>
                                    @if($order->driver)
                                        {{ trim(optional($order->driver)->first_name.' '.optional($order->driver)->last_name) ?: 'Driver #'.$order->driver->id }}
                                        ({{ optional($order->driver)->phone_country }}{{ optional($order->driver)->phone }})
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </p>
                                <p><strong>Restaurant:</strong> {{ optional($order->restaurant)->name }}</p>
                                <p><strong>Branch:</strong> {{ optional($order->branch)->name }} - {{ optional($order->branch)->address }}</p>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">Order Items</div>
                            <div class="panel-body table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Variant</th>
                                            <th>Qty</th>
                                            <th>Unit Price</th>
                                            <th>Line Total</th>
                                            <th>Addons</th>
                                            <th>Instruction</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td>{{ optional($item->foodItem)->name }}</td>
                                                <td>{{ optional($item->variant)->name ?? '-' }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ number_format((float)$item->unit_price, 2) }}</td>
                                                <td>{{ number_format((float)$item->line_total, 2) }}</td>
                                                <td>
                                                    @if(!empty($item->addons))
                                                        <pre style="margin:0; white-space: pre-wrap;">{{ json_encode($item->addons, JSON_PRETTY_PRINT) }}</pre>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $item->special_instruction ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Live Driver Tracking
                                <small class="pull-right text-muted" id="food-tracking-last-updated">Waiting for driver location</small>
                            </div>
                            <div class="panel-body">
                                <div class="food-tracking-shell">
                                    <div class="food-tracking-topbar">
                                        <div>
                                            <strong>Driver Route Monitor</strong>
                                            <span id="food-tracking-point-count">No route points yet</span>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-primary" id="food-tracking-refresh-btn">
                                            Refresh
                                        </button>
                                    </div>
                                    <div id="food-order-tracking-map"></div>
                                    <div class="food-tracking-empty" id="food-tracking-empty">
                                        <div class="food-tracking-empty-icon">&#8982;</div>
                                        <strong>Waiting for driver live GPS</strong>
                                        <span>Tracking starts after pickup and updates every few seconds.</span>
                                    </div>
                                </div>
                                <p class="help-block" style="margin-top: 10px;">
                                    Shows driver route points from pickup to delivery. It refreshes every 8 seconds while the order is active.
                                </p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Update Status</div>
                                    <div class="panel-body">
                                        @if(count($nextStatuses))
                                            <form method="POST" action="{{ route('admin.food-orders.status', $order->id) }}">
                                                @csrf
                                                <div class="form-group">
                                                    <label>Next Status</label>
                                                    <select name="status" class="form-control" required>
                                                        @foreach($nextStatuses as $nextStatus)
                                                            <option value="{{ $nextStatus }}">{{ strtoupper($nextStatus) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Note</label>
                                                    <textarea name="note" class="form-control" rows="3" placeholder="Optional note"></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Update Status</button>
                                            </form>
                                        @else
                                            <p>No further status transition allowed from <strong>{{ $order->status }}</strong>.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-default">
                                    <div class="panel-heading">Timeline</div>
                                    <div class="panel-body">
                                        <ul class="list-group">
                                            @forelse($order->statusLogs->sortBy('id') as $log)
                                                <li class="list-group-item">
                                                    <div><strong>{{ $log->from_status ?? 'start' }}</strong> → <strong>{{ $log->to_status }}</strong></div>
                                                    <div>{{ optional($log->changedBy)->first_name }} {{ optional($log->changedBy)->last_name }} ({{ optional($log->changedBy)->user_type }})</div>
                                                    <div>{{ $log->note ?? '-' }}</div>
                                                    <small class="text-muted">{{ optional($log->created_at)->format('Y-m-d H:i:s') }}</small>
                                                </li>
                                            @empty
                                                <li class="list-group-item">No timeline data</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('admin.food-orders.index') }}" class="btn btn-default">Back to list</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    @php
        $trackingPointsPayload = $trackingPoints->map(function ($point) {
            return [
                'lat' => (float) $point->latitude,
                'lng' => (float) $point->longitude,
                'recorded_at' => optional($point->recorded_at ?: $point->created_at)->toIso8601String(),
            ];
        })->values();
    @endphp
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <style>
        .food-tracking-shell {
            position: relative;
            overflow: hidden;
            min-height: 520px;
            border: 1px solid #d8e6f8;
            border-radius: 18px;
            background: linear-gradient(135deg, #f7fbff 0%, #eef6ff 100%);
            box-shadow: 0 18px 35px rgba(29, 86, 165, .10);
        }
        #food-order-tracking-map {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 520px;
            min-height: 520px;
            border-radius: 18px;
            background: #eef6ff;
        }
        .food-tracking-topbar {
            position: absolute;
            z-index: 700;
            top: 14px;
            left: 14px;
            right: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 14px;
            border: 1px solid rgba(216, 230, 248, .9);
            border-radius: 14px;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 12px 28px rgba(16, 24, 39, .12);
        }
        .food-tracking-topbar strong {
            display: block;
            color: #101827;
            font-size: 14px;
            line-height: 1.2;
        }
        .food-tracking-topbar span {
            display: block;
            color: #64748b;
            font-size: 12px;
            margin-top: 2px;
        }
        .food-tracking-empty {
            position: absolute;
            z-index: 650;
            left: 50%;
            top: 54%;
            width: min(360px, calc(100% - 32px));
            transform: translate(-50%, -50%);
            padding: 22px;
            border: 1px solid #d8e6f8;
            border-radius: 20px;
            background: rgba(255, 255, 255, .95);
            text-align: center;
            box-shadow: 0 16px 34px rgba(16, 24, 39, .14);
        }
        .food-tracking-empty.is-hidden {
            display: none;
        }
        .food-tracking-empty-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 10px;
            border-radius: 18px;
            background: #eaf3ff;
            color: #1d56a5;
            font-size: 30px;
            line-height: 48px;
            font-weight: 900;
        }
        .food-tracking-empty strong,
        .food-tracking-empty span {
            display: block;
        }
        .food-tracking-empty strong {
            color: #101827;
            font-size: 16px;
        }
        .food-tracking-empty span {
            color: #64748b;
            margin-top: 5px;
        }
        .leaflet-container,
        .leaflet-container * {
            box-sizing: content-box;
        }
        .leaflet-container img,
        .leaflet-container .leaflet-tile,
        .leaflet-container .leaflet-marker-icon,
        .leaflet-container .leaflet-marker-shadow {
            max-width: none !important;
            max-height: none !important;
        }
        .leaflet-control-attribution {
            font-size: 10px;
        }
    </style>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        (function () {
            const orderId = {{ (int) $order->id }};
            const initialPoints = @json($trackingPointsPayload);
            const pickup = {
                lat: {{ optional($order->branch)->latitude !== null ? (float) optional($order->branch)->latitude : 'null' }},
                lng: {{ optional($order->branch)->longitude !== null ? (float) optional($order->branch)->longitude : 'null' }},
            };
            const drop = {
                lat: {{ $order->delivery_latitude !== null ? (float) $order->delivery_latitude : 'null' }},
                lng: {{ $order->delivery_longitude !== null ? (float) $order->delivery_longitude : 'null' }},
            };

            const refreshButton = document.getElementById('food-tracking-refresh-btn');
            const emptyState = document.getElementById('food-tracking-empty');
            const pointCount = document.getElementById('food-tracking-point-count');
            const lastUpdated = document.getElementById('food-tracking-last-updated');
            const validPoint = point => point
                && point.lat !== null
                && point.lng !== null
                && point.lat !== ''
                && point.lng !== ''
                && Number.isFinite(Number(point.lat))
                && Number.isFinite(Number(point.lng));
            const center = validPoint(initialPoints[0])
                ? initialPoints[0]
                : (validPoint(pickup) ? pickup : (validPoint(drop) ? drop : {lat: 23.685, lng: 90.3563}));
            const map = L.map('food-order-tracking-map', {
                zoomControl: true,
                scrollWheelZoom: true,
            }).setView([center.lat, center.lng], 14);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            let polyline = L.polyline([], {color: '#1D56A5', weight: 5, opacity: .9}).addTo(map);
            let driverMarker = null;

            const pickupIcon = L.divIcon({
                className: 'food-tracking-marker',
                html: '<div style="width:28px;height:28px;border-radius:50%;background:#1D56A5;border:3px solid #fff;box-shadow:0 8px 16px rgba(29,86,165,.35);"></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });
            const dropIcon = L.divIcon({
                className: 'food-tracking-marker',
                html: '<div style="width:28px;height:28px;border-radius:50%;background:#E11D48;border:3px solid #fff;box-shadow:0 8px 16px rgba(225,29,72,.35);"></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });
            const driverIcon = L.divIcon({
                className: 'food-tracking-marker',
                html: '<div style="width:34px;height:34px;border-radius:50%;background:#16A34A;border:4px solid #fff;box-shadow:0 10px 20px rgba(22,163,74,.35);display:flex;align-items:center;justify-content:center;color:#fff;font-size:16px;">➤</div>',
                iconSize: [34, 34],
                iconAnchor: [17, 17]
            });

            const baseBoundsPoints = [];
            if (validPoint(pickup)) {
                baseBoundsPoints.push([pickup.lat, pickup.lng]);
                L.marker([pickup.lat, pickup.lng], {icon: pickupIcon}).addTo(map).bindPopup('Restaurant pickup');
            }
            if (validPoint(drop)) {
                baseBoundsPoints.push([drop.lat, drop.lng]);
                L.marker([drop.lat, drop.lng], {icon: dropIcon}).addTo(map).bindPopup('Customer drop-off');
            }

            function render(points) {
                const latLngs = points
                    .filter(validPoint)
                    .map(point => [Number(point.lat), Number(point.lng)]);
                pointCount.innerText = latLngs.length
                    ? `${latLngs.length} route point${latLngs.length === 1 ? '' : 's'} captured`
                    : 'No route points yet';
                emptyState.classList.toggle('is-hidden', latLngs.length > 0);
                if (!latLngs.length) {
                    if (baseBoundsPoints.length > 1) {
                        map.fitBounds(L.latLngBounds(baseBoundsPoints).pad(.25));
                    } else {
                        map.setView([center.lat, center.lng], 14);
                    }
                    setTimeout(() => map.invalidateSize(true), 150);
                    return;
                }

                polyline.setLatLngs(latLngs);
                const latest = latLngs[latLngs.length - 1];
                if (!driverMarker) {
                    driverMarker = L.marker(latest, {icon: driverIcon}).addTo(map).bindPopup('Driver live location');
                } else {
                    driverMarker.setLatLng(latest);
                }
                map.fitBounds(L.latLngBounds(baseBoundsPoints.concat(latLngs)).pad(.2));
                const last = points[points.length - 1];
                lastUpdated.innerText =
                    last.recorded_at ? `Last update: ${last.recorded_at}` : 'Live tracking active';
                setTimeout(() => map.invalidateSize(true), 150);
            }

            async function refreshTracking() {
                try {
                    const response = await fetch(`{{ route('admin.food-orders.tracking', $order->id) }}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (!response.ok) return;
                    const json = await response.json();
                    const points = (((json || {}).data || {}).route_points || []).map(point => ({
                        lat: Number(point.latitude),
                        lng: Number(point.longitude),
                        recorded_at: point.recorded_at || point.created_at,
                    }));
                    render(points);
                } catch (e) {
                    // Keep existing map visible if polling fails.
                }
            }

            refreshButton.addEventListener('click', refreshTracking);
            render(initialPoints);
            setTimeout(() => map.invalidateSize(true), 250);
            setTimeout(() => map.invalidateSize(true), 1000);
            setInterval(refreshTracking, 8000);
        })();
    </script>
@endsection
