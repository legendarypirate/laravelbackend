@extends('admin.master')

@section('mainContent')
    <style>
        #map {
            height: 600px;
            width: 100%;
            border-radius: 8px;
        }
        .driver-info {
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 10px;
        }
        .driver-info h4 {
            margin: 0 0 5px 0;
            color: #333;
        }
        .driver-info p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .status-online {
            background-color: #28a745;
        }
        .status-offline {
            background-color: #dc3545;
        }
        .refresh-btn {
            margin-bottom: 15px;
        }
        /* Style for Google Maps marker labels */
        .driver-marker-label {
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        }
    </style>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>ЖОЛООЧИЙН БАЙРШИЛ</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Байршил</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Жолоочийн байршлын мэдээлэл</h3>
                                <button type="button" class="btn btn-primary btn-sm refresh-btn float-right" id="refreshLocations">
                                    <i class="fas fa-sync-alt"></i> Шинэчлэх
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="map"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Жолоочийн жагсаалт</h3>
                            </div>
                            <div class="card-body" id="driverList">
                                <p class="text-center">Ачааллаж байна...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Google Maps API -->
    @php
        $googleMapsKey = env('GOOGLE_MAPS_API_KEY', '');
    @endphp
    @if($googleMapsKey)
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&callback=initMap" async defer></script>
    @else
        <div class="alert alert-warning">
            <strong>Анхаар:</strong> Google Maps API түлхүүр тохируулаагүй байна. .env файлд GOOGLE_MAPS_API_KEY нэмнэ үү.
        </div>
        <script>
            function initMap() {
                document.getElementById('map').innerHTML = '<div class="alert alert-danger text-center p-5"><h4>Google Maps API түлхүүр шаардлагатай</h4><p>.env файлд GOOGLE_MAPS_API_KEY нэмнэ үү.</p></div>';
            }
        </script>
    @endif
    
    <script>
        let map;
        let markers = {};
        let updateInterval;

        function initMap() {
            // Default center (Ulaanbaatar, Mongolia)
            const defaultCenter = { lat: 47.8864, lng: 106.9057 };
            
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 8, // Zoomed out to show more area by default
                center: defaultCenter,
                mapTypeId: 'roadmap'
            });

            // Load initial locations
            loadDriverLocations();

            // Auto-refresh every 10 seconds
            updateInterval = setInterval(loadDriverLocations, 10000);
        }

        function loadDriverLocations() {
            fetch('{{ url('/api/v1/driver/locations') }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('API Response:', data); // Debug log
                    console.log('Response type check:', {
                        isObject: typeof data === 'object',
                        hasSuccess: data && 'success' in data,
                        successValue: data && data.success,
                        hasData: data && 'data' in data,
                        dataValue: data && data.data,
                        dataType: data && data.data ? typeof data.data : 'undefined',
                        isArray: data && data.data ? Array.isArray(data.data) : false,
                        dataLength: data && Array.isArray(data.data) ? data.data.length : 'N/A'
                    });
                    
                    // Handle response - ensure data is always an array
                    if (data && data.success !== undefined) {
                        // If data exists but is not an array, convert it or use empty array
                        let driverData = [];
                        if (data.data !== undefined) {
                            if (Array.isArray(data.data)) {
                                driverData = data.data;
                            } else if (typeof data.data === 'number' && data.data === 0) {
                                // Handle case where backend returns 0 instead of empty array
                                driverData = [];
                            } else if (data.data && typeof data.data === 'object') {
                                // If it's an object, try to convert to array
                                driverData = Object.values(data.data);
                            } else {
                                console.warn('Unexpected data type, using empty array:', typeof data.data, data.data);
                                driverData = [];
                            }
                        }
                        
                        updateMap(driverData);
                        updateDriverList(driverData);
                    } else {
                        console.error('Invalid response format - missing success field:', data);
                        document.getElementById('driverList').innerHTML = '<p class="text-center text-muted">Мэдээлэл ачаалахад алдаа гарлаа.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading driver locations:', error);
                    document.getElementById('driverList').innerHTML = '<p class="text-center text-danger">Мэдээлэл ачаалахад алдаа гарлаа: ' + error.message + '</p>';
                });
        }

        function updateMap(drivers) {
            // Ensure drivers is an array
            if (!Array.isArray(drivers)) {
                console.error('Drivers is not an array:', drivers);
                document.getElementById('driverList').innerHTML = '<p class="text-center text-muted">Одоогоор байршил мэдээлэл байхгүй байна.</p>';
                // Reset map to default view when error occurs
                const defaultCenter = { lat: 47.8864, lng: 106.9057 };
                map.setCenter(defaultCenter);
                map.setZoom(8);
                return;
            }

            // Remove old markers
            Object.values(markers).forEach(marker => marker.setMap(null));
            markers = {};

            // Default center (Ulaanbaatar, Mongolia)
            const defaultCenter = { lat: 47.8864, lng: 106.9057 };

            if (drivers.length === 0) {
                // No drivers - show default view with zoomed out map
                map.setCenter(defaultCenter);
                map.setZoom(8);
                document.getElementById('driverList').innerHTML = '<p class="text-center text-muted">Одоогоор байршил мэдээлэл байхгүй байна.</p>';
                return;
            }

            // Create markers for each driver
            drivers.forEach(driver => {
                const position = { lat: driver.latitude, lng: driver.longitude };
                
                const marker = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: driver.name,
                    label: {
                        text: driver.name,
                        color: '#20853b',
                        fontSize: '13px',
                        fontWeight: 'bold'
                    },
                    icon: {
                        url: '{{ asset('car.svg') }}',
                        scaledSize: new google.maps.Size(50, 50),
                        anchor: new google.maps.Point(25, 25),
                        labelOrigin: new google.maps.Point(25, -10)
                    }
                });

                // Info window for marker
                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div class="driver-info">
                            <h4>${driver.name}</h4>
                            <p><strong>Утас:</strong> ${driver.phone || 'N/A'}</p>
                            <p><strong>Сүүлд шинэчлэгдсэн:</strong> ${driver.updated_at ? new Date(driver.updated_at).toLocaleString('mn-MN') : 'N/A'}</p>
                        </div>
                    `
                });

                marker.addListener('click', () => {
                    infoWindow.open(map, marker);
                });

                markers[driver.id] = marker;
            });

            // Fit map to show all markers with padding and max zoom limit
            if (drivers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                drivers.forEach(driver => {
                    bounds.extend({ lat: driver.latitude, lng: driver.longitude });
                });
                
                // Fit bounds with padding (in pixels) to ensure markers aren't at the edge
                map.fitBounds(bounds, {
                    top: 50,
                    right: 50,
                    bottom: 50,
                    left: 50
                });
                
                // Set a maximum zoom level to prevent zooming in too much
                // This ensures the map stays visible even if all drivers are very close together
                google.maps.event.addListenerOnce(map, 'bounds_changed', function() {
                    if (map.getZoom() > 15) {
                        map.setZoom(15);
                    }
                    // Ensure minimum zoom to keep map visible
                    if (map.getZoom() < 8) {
                        map.setZoom(8);
                    }
                });
            }
        }

        function updateDriverList(drivers) {
            // Ensure drivers is an array
            if (!Array.isArray(drivers)) {
                console.error('Drivers is not an array in updateDriverList:', drivers);
                return;
            }

            const driverListHtml = drivers.length > 0 
                ? drivers.map(driver => {
                    const isOnline = driver.updated_at && 
                        (new Date() - new Date(driver.updated_at)) < 300000; // 5 minutes
                    
                    return `
                        <div class="driver-info">
                            <h4>
                                <span class="status-indicator ${isOnline ? 'status-online' : 'status-offline'}"></span>
                                ${driver.name}
                            </h4>
                            <p><strong>Утас:</strong> ${driver.phone || 'N/A'}</p>
                            <p><strong>Байршил:</strong> ${driver.latitude.toFixed(6)}, ${driver.longitude.toFixed(6)}</p>
                            <p><strong>Сүүлд шинэчлэгдсэн:</strong> ${driver.updated_at ? new Date(driver.updated_at).toLocaleString('mn-MN') : 'N/A'}</p>
                        </div>
                    `;
                }).join('')
                : '<p class="text-center text-muted">Одоогоор байршил мэдээлэл байхгүй байна.</p>';

            document.getElementById('driverList').innerHTML = driverListHtml;
        }

        // Manual refresh button
        document.getElementById('refreshLocations').addEventListener('click', () => {
            loadDriverLocations();
        });

        // Clean up interval on page unload
        window.addEventListener('beforeunload', () => {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
        });
    </script>
@endsection

