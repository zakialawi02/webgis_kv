<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title><?= $title; ?></title>
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="/img/favicon.png" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;500&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href=" https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.min.css " rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Template Stylesheet -->
    <link href="/assets/css/style.css" rel="stylesheet">
    <link href="/css/map.css" rel="stylesheet">

    <!-- leaflet Component -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css" integrity="sha256-sA+zWATbFveLLNqWO2gtiw3HL/lh1giY/Inf1BJ0z14=" crossorigin="" />
    <link href="/leaflet/L.Control.MousePosition.css" rel="stylesheet">
    <link rel="stylesheet" href="//unpkg.com/leaflet-gesture-handling/dist/leaflet-gesture-handling.min.css" type="text/css">
    <link rel="stylesheet" href="/leaflet/leaflet-sidepanel.css" />
    <link rel="stylesheet" href="/leaflet/iconLayers.css" />
    <link rel="stylesheet" href="/leaflet/leaflet-notifications.min.css" />

</head>

<body>
    <!-- Spinner Start -->
    <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
        <div class="spinner-grow text-primary" role="status"></div>
    </div>
    <!-- Spinner End -->

    <!-- ISI CONTENT -->

    <!-- Modal only-->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="loginModalLabel">Login</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?= view('Myth\Auth\Views\_message_block') ?>
                    <form action="<?= url_to('login') ?>" method="post" name="login">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="login"><?= lang('Auth.emailOrUsername') ?></label>
                            <input type="text" class="form-control <?php if (session('errors.login')) : ?>is-invalid<?php endif ?>" name="login" placeholder="<?= lang('Auth.emailOrUsername') ?>">
                            <div class="invalid-feedback">
                                <?= session('errors.login') ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="password"><?= lang('Auth.password') ?></label>
                            <input type="password" name="password" class="form-control  <?php if (session('errors.password')) : ?>is-invalid<?php endif ?>" placeholder="<?= lang('Auth.password') ?>" aria-describedby="emailHelp">
                            <div class="invalid-feedback">
                                <?= session('errors.password') ?>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-check-label">
                                <input type="checkbox" name="remember" class="form-check-input" <?php if (old('remember')) : ?> checked <?php endif ?>>
                                <?= lang('Auth.rememberMe') ?>
                            </label>
                        </div>
                        <div class="form-group">
                            <p class="text-center">Don't have account? <a href="<?= url_to('register') ?>" id="signup">Sign up here</a></p>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <button type="submit" id="login-submit" class=" btn btn-block mybtn btn-primary tx-tfm"><?= lang('Auth.loginAction') ?></button>
                            </div>
                            <div class="col">
                                <p class="text-center">
                                    <a href="<?= url_to('forgot') ?>" class="google btn mybtn"><?= lang('Auth.forgotYourPassword') ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div id="button-section-group" class="">
        <div id="button-section" class="float-end m-1">

            <?php if (logged_in()) : ?>
                <button type="button" id="logout-btn" class="btn btn-primary">Log Out</button>
                <button id="spinners" class="btn btn-primary" type="button" disabled>
                    <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>Logout... </button>
            <?php else : ?>
                <!-- Button trigger modal -->
                <button type="button" id="login-btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
            <?php endif ?>

        </div>
    </div>

    <div class="map" id="map">

        <div id="panelID" class="sidepanel" aria-label="side panel" aria-hidden="false">
            <div class="sidepanel-inner-wrapper">
                <nav class="sidepanel-tabs-wrapper" aria-label="sidepanel tab navigation">
                    <ul class="sidepanel-tabs">
                        <li class="sidepanel-tab">
                            <a href="#" class="sidebar-tab-link" role="tab" data-tab-link="tab-1"><i class="bi bi-house-door-fill"></i></a>
                        </li>

                    </ul>
                </nav>
                <div class="sidepanel-content-wrapper">
                    <div class="sidepanel-content">
                        <div class="sidepanel-tab-content" data-tab-content="tab-1">
                            <p>Content 1.</p>
                        </div>

                    </div>
                </div>
            </div>
            <div class="sidepanel-toggle-container">
                <button class="sidepanel-toggle-button" type="button" aria-label="toggle side panel"></button>
            </div>
        </div>

    </div>


    <!-- END ISI CONTENT -->

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoTz1HIcje39Wm4jDKdf19U8gI4ddQ3GYNS7NTKfAdVQSZe" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src=" https://cdn.jsdelivr.net/npm/sweetalert2@11.7.5/dist/sweetalert2.all.min.js "></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Template Javascript -->
    <script src="/assets/js/main.js"></script>

    <script>
        $(document).ready(function() {
            $('form[name="login"]').submit(function(event) {
                event.preventDefault(); // prevent default form submit behavior
                var form = $(this);
                var url = form.attr('action');
                var method = form.attr('method');
                var data = form.serialize();
                // AJAX request
                $.ajax({
                    url: url,
                    type: method,
                    data: data,
                    success: function(response) {
                        location.reload();
                        // Swal.fire({
                        //     title: "Login Berhasil!",
                        //     icon: "success",
                        //     showConfirmButton: false,
                        //     timer: 1000
                        // }).then(() => {
                        //     $('.modal').hide();
                        //     $('.modal-backdrop').hide();
                        //     $('#button-section-group').load(location.href + ' #button-section');
                        //     location.reload();
                        // });
                    },
                });
            });

            $('#logout-btn').click(function(e) {
                e.preventDefault();
                $('#logout-btn').hide();
                $('#spinners').show();
                // tunggu 500ms sebelum menjalankan AJAX
                $.ajax({
                    url: "/logout",
                    type: "GET",
                }).done(function() {
                    // $('#spinners').hide();
                    // $('#button-section-group').load(location.href + ' #button-section');
                    // Swal.fire({
                    //     icon: 'success',
                    //     title: 'Berhasil Logout.',
                    //     showConfirmButton: false,
                    //     timer: 1000
                    // }).then(() => {
                    location.reload();
                    // });
                });
            });
        });
    </script>


    <!-- Leafleat js Component -->
    <script src="https://unpkg.com/leaflet@1.9.2/dist/leaflet.js" integrity="sha256-o9N1jGDZrf5tS+Ft4gbIK7mYMipq9lqpVJ91xHSyKhg=" crossorigin=""></script>
    <script src="https://unpkg.com/geojson-vt@3.2.0/geojson-vt.js"></script>
    <script src="/leaflet/leaflet-geojson-vt.js"></script>
    <script src="/leaflet/leaflet.ajax.min.js"></script>
    <script src="/leaflet/leaflet.ajax.js"></script>
    <script src="/leaflet/L.Control.MousePosition.js"></script>
    <script src="//unpkg.com/leaflet-gesture-handling"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-tilelayer-geojson/1.0.2/TileLayer.GeoJSON.min.js"></script>
    <script src="/leaflet/leaflet-sidepanel.min.js"></script>
    <script src="/leaflet/Leaflet.Control.Custom.js"></script>
    <script src="/leaflet/iconLayers.js"></script>
    <script src="/leaflet/leaflet-notifications.min.js"></script>

    <!-- Leafleat Setting js-->
    <!-- initialize the map on the "map" div with a given center and zoom -->
    <script>
        // Base map
        var peta1 = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiNjg2MzUzMyIsImEiOiJjbDh4NDExZW0wMXZsM3ZwODR1eDB0ajY0In0.6jHWxwN6YfLftuCFHaa1zw', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
                '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>',
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1
        });

        var peta2 = L.tileLayer('http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; <a href="https://www.google.com/maps">Google Maps</a> contributors',
        });

        var peta3 = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });

        var peta4 = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiNjg2MzUzMyIsImEiOiJjbDh4NDExZW0wMXZsM3ZwODR1eDB0ajY0In0.6jHWxwN6YfLftuCFHaa1zw', {
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
                '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
                'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            id: 'mapbox/dark-v10'
        });

        // set frame view
        <?php foreach ($tampilData as $D) : ?>
            var map = L.map('map', {
                center: [<?= $D->coordinat_wilayah; ?>],
                zoom: <?= $D->zoom_view; ?>,
                layers: [peta1],
                gestureHandling: false,
            })
        <?php endforeach ?>

        // controller
        map.removeControl(map.zoomControl);
        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);
        var baseLayers = new L.Control.IconLayers(
            [{
                    title: 'Default', // use any string
                    layer: peta1, // any ILayer
                    icon: '/leaflet/icon/here_normaldaygrey.png' // 80x80 icon
                },
                {
                    title: 'Satellite',
                    layer: peta2,
                    icon: '/leaflet/icon/here_satelliteday.png'
                },
                {
                    title: 'OSM',
                    layer: peta3,
                    icon: '/leaflet/icon/openstreetmap_mapnik.png'
                },
            ], {
                position: 'bottomright',
                maxLayersInRow: 3
            }
        );
        baseLayers.addTo(map);
        L.control.mousePosition().addTo(map);
        L.control.scale().addTo(map);








        // Tambahkan control accordion pada peta
        var legendControl = L.control({
            position: 'bottomleft'
        });
        legendControl.onAdd = function(map) {
            var div = L.DomUtil.create('div', 'legend-panel');
            div.innerHTML = `<div class="accordion" id="legendAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Legenda
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#legendAccordion">
                        <div class="accordion-body">
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: red;"></div>
                                <div class="legend-label">Layer 1</div>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: blue;"></div>
                                <div class="legend-label">Layer 2</div>
                            </div>
                            <div class="legend-item">
                                <div class="legend-color" style="background-color: green;"></div>
                                <div class="legend-label">Layer 3</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
            return div;
        };
        legendControl.addTo(map);

        // SidePanel
        const panelRight = L.control.sidepanel('panelID', {
            panelPosition: 'left',
            tabsPosition: 'left',
            pushControls: true,
            darkMode: false,
            startTab: 'tab-5'
        }).addTo(map);

        // set marker place
        const locKafe = L.icon({
            iconUrl: '<?= base_url(); ?>/leaflet/icon/restaurant_breakfast.png',
            iconSize: [30, 30],
            iconAnchor: [18.5, 30], // point of the icon which will correspond to marker's location
            popupAnchor: [0, -28] // point from which the popup should open relative to the iconAnchor
        });

        // geojson
        function popUp(f, l) {
            var out = "";
            if (f.properties) {
                const id_kafe = f.properties.id_kafe;
                const nama_foto = f.properties.nama_foto[0];
                const foto_list = nama_foto.split(","); // memecah string dengan koma sebagai delimiter menjadi array
                const foto_html = foto_list.map(foto => "<img src='/img/kafe/" + foto.trim() + "'>"); // membuat HTML tag img untuk setiap nama file foto pada array
                out += foto_html[0];
                out += "<table>";
                out += "<tr><td><b>Nama Kafe</b></td><th>:</th><td>" + f.properties.nama_kafe + "</td></tr>";
                // out += "<tr><td><b>Longitude</b></td><th>:</th><td>" + f.properties.longitude + "</td></tr>";
                // out += "<tr><td><b>Latitude</b></td><th>:</th><td>" + f.properties.latitude + "</td></tr>";
                out += "<tr><td><b>Alamat</b></td><th>:</th><td>" + f.properties.alamat_kafe + "</td></tr>";
                out += "<tr><td><b>Wilayah Administrasi</b></td><th>:</th><td>" + f.properties.nama_kelurahan + ", Kec." + f.properties.nama_kecamatan + ", " + f.properties.nama_kabupaten + "</td></tr>";
                out += "<tr><td><b>Instagram</b></td><th>:</th><td>" + "@" + f.properties.instagram_kafe + "</td></tr>";
                out += "<tr><td><b>Jam Oprasional</b></td><th>:</th><td>" + f.properties.jam_oprasional + "</td></tr>";
                out += "</table>";
                out += "<a id='tombol-viewmap' href='/kafe/" + id_kafe + "/detail' style='color:black;'>view</a>";

                l.bindPopup(out);
            }
        }
        var jsonTest = new L.GeoJSON.AJAX(["<?= base_url(); ?>/api"], {
            onEachFeature: popUp,
            pointToLayer: function(feature, latlng) {
                return L.marker(latlng, {
                    icon: locKafe,
                });
            }
        }).addTo(map);


        var controlElement = baseLayers.getContainer();
        controlElement.style.position = 'absolute';
        controlElement.style.bottom = '1.2rem';
        controlElement.style.right = '3rem';
    </script>


</body>

</html>