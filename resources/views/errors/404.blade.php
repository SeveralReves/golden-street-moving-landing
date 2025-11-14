@extends('layouts.default')

@section('content')

@section('title', 'Página no encontrada')

<section class="error">
    <div class="error__container container">
        <img src="{{ asset('images/404.png') }}" width="400" loading="lazy" alt="Camión de mudanza" class="error__image">
        <h1 class="error__title">
            Oops, This page not found
        </h1>
        <p class="error__description">
            Sorry, the page you are looking for does not exist.
        </p>
        <div class="error__buttons">
            <a href="/" class="error__button button__primary">
                Back to Home
            </a>
        </div>
    </div>
</section>
@stop