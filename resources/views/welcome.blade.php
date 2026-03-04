<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>{{ config('app.name', 'Orders API') }}</title>
	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
<main class="mx-auto flex min-h-screen max-w-5xl items-center px-6 py-16">
	<section class="w-full rounded-3xl border border-slate-800 bg-slate-900/70 p-10 shadow-2xl shadow-slate-950/70">
		<p class="mb-3 inline-flex rounded-full border border-emerald-400/40 bg-emerald-400/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-emerald-300">
			Laravel 12 + PostgreSQL 18 + Redis</p>
		<h1 class="text-4xl font-semibold tracking-tight text-white sm:text-5xl">Orders API Service</h1>
		<div class="mt-8 flex flex-wrap gap-4">
			<a href="{{ route('docs.openapi') }}"
			   class="inline-flex items-center justify-center rounded-xl bg-emerald-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-400">
				OpenAPI JSON
			</a>
			<a href="{{ route('swagger') }}"
			   class="inline-flex items-center justify-center rounded-xl border border-emerald-500/50 px-6 py-3 text-sm font-semibold text-emerald-300 transition hover:bg-emerald-500/10">
				Swagger UI
			</a>
		</div>
	</section>
</main>
</body>
</html>
