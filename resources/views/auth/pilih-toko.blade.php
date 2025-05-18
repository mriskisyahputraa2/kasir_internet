<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title')</title>

    <!-- Bootstrap & FontAwesome -->
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" />
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />

    <!-- Fonts & Icons -->
    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Nunito:300,400,600,700|Poppins:300,400,500,600,700"
        rel="stylesheet" />
    <link href="../assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet" />
    <link href="../assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet" />
    <link href="../assets/vendor/remixicon/remixicon.css" rel="stylesheet" />

    <!-- Template CSS -->
    <link href="../assets/css/style.css" rel="stylesheet" />

    <style>
        body {
            background-color: rgba(99, 59, 72, 0.15);
            font-family: 'Poppins', sans-serif;
        }

        .container {
            max-width: 1200px;
        }

        .page-title {
            text-align: center;
            margin: 50px 0 30px;
        }

        .page-title h1 {
            color: #633B48;
            font-weight: 600;
            font-size: 2.2rem;
        }

        .store-card {
            background: #fff;
            border: none;
            border-radius: 12px;
            padding: 30px 20px 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
            cursor: pointer;
            position: relative;
            height: 100%;
        }

        .store-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.15);
        }

        .store-icon {
            background: #633B48;
            color: white;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 1.5rem;
            position: absolute;
            top: -30px;
            left: calc(50% - 30px);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .store-title {
            margin-top: 40px;
            color: #633B48;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .store-location {
            color: #666;
            font-size: 0.9rem;
        }

        @media (max-width: 767px) {
            .store-card {
                margin-bottom: 30px;
            }
        }
    </style>

    <script>
        function pilihToko(tokoId) {
            const form = document.createElement("form");
            form.method = "POST";
            form.action = "{{ route('set-toko') }}";

            const csrfInput = document.createElement("input");
            csrfInput.type = "hidden";
            csrfInput.name = "_token";
            csrfInput.value = "{{ csrf_token() }}";

            const tokoInput = document.createElement("input");
            tokoInput.type = "hidden";
            tokoInput.name = "toko_id";
            tokoInput.value = tokoId;

            form.appendChild(csrfInput);
            form.appendChild(tokoInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</head>

<body>

    <div class="container">
        <div class="page-title">
            <h1>Pilih Toko</h1>
        </div>

        <div class="row">
            @foreach ($tokoList as $toko)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="store-card text-center" onclick="pilihToko('{{ $toko->id }}')">
                        <div class="store-icon">
                            <i class="fa fa-home"></i>
                        </div>
                        <h5 class="store-title mt-4">{{ $toko->nama }}</h5>
                        <p class="store-location">{{ $toko->lokasi }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</body>

</html>
