<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Huella de Carbono</title>
  
  <!-- Favicons -->
  <link href="{{ asset('modules/sica/favicon.ico') }}" rel="icon">
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  
  <!-- Font Awesome -->
  <link href="{{ asset('libs/Fontawesome6/css/fontawesome.css') }}" rel="stylesheet">
  <link href="{{ asset('libs/Fontawesome6/css/brands.css') }}" rel="stylesheet">
  <link href="{{ asset('libs/Fontawesome6/css/solid.css') }}" rel="stylesheet">
  
  <!-- Alpine.js for interactions -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  
  <!-- Custom Styles -->
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
  
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#10b981',
            secondary: '#6b7280',
          }
        }
      }
    }
  </script>

</head>

