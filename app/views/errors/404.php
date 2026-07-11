<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página No Encontrada - 404</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        coffee: {
                            dark: '#3D1C02',
                            medium: '#7B4F2E',
                            light: '#A0714F',
                        },
                        cream: {
                            DEFAULT: '#FFF8F0',
                            dark: '#F5E6D3',
                        },
                        accent: {
                            DEFAULT: '#E07B39',
                            dark: '#C96525',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-cream min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center bg-white rounded-2xl shadow-xl p-8 border border-cream-dark">
        <div class="text-6xl mb-4">🍔🔍</div>
        <h1 class="text-3xl font-bold text-coffee-dark mb-2">404 - No Encontrado</h1>
        <p class="text-coffee-light mb-6">Lo sentimos, la página que buscas no existe o ha sido movida.</p>
        <a href="/" class="inline-block bg-accent hover:bg-accent-dark text-white font-semibold px-6 py-3 rounded-xl transition duration-200">
            Volver al Inicio
        </a>
    </div>
</body>
</html>
