<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Swagger UI</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
	<link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
<main class="mx-auto w-full max-w-7xl px-4 py-8">
	<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
		<h1 class="text-2xl font-semibold text-white">Swagger UI</h1>
		<div class="flex gap-3">
			<a href="{{ route('docs.openapi') }}"
			   class="inline-flex items-center rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400">
				OpenAPI JSON
			</a>
			<a href="/"
			   class="inline-flex items-center rounded-lg border border-slate-700 px-4 py-2 text-sm font-medium hover:bg-slate-800">
				На главную
			</a>
		</div>
	</div>
	<div class="overflow-hidden rounded-2xl border border-slate-800 bg-white">
		<div id="swagger-ui"></div>
	</div>
</main>
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-standalone-preset.js"></script>
<script>
    window.ui = SwaggerUIBundle({
        url: @json(route('docs.openapi')),
        dom_id: '#swagger-ui',
        deepLinking: true,
        persistAuthorization: true,
        presets: [
            SwaggerUIBundle.presets.apis,
            SwaggerUIStandalonePreset,
        ],
        layout: 'BaseLayout',
    });
</script>
</body>
</html>
