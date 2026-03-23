@extends('layouts.customer')

@section('title', 'About - Uyayi')
@section('description', 'Learn more about Uyayi and our gentle baby care products.')

@section('content')
<section class="py-5 hero-section">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3" style="color: var(--primary-blue-dark);">About Uyayi</h1>
                <p class="lead text-muted mb-4">Uyayi is focused on gentle, dependable baby care products designed for everyday family routines.</p>
                <div class="d-flex flex-wrap gap-3">
                    <span class="eco-badge">Gentle Care</span>
                    <span class="eco-badge">Parent Friendly</span>
                    <span class="eco-badge">Daily Essentials</span>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-4 rounded-4 shadow-sm" style="background: white; border: 1px solid rgba(212, 196, 176, 0.3);">
                    <h3 class="fw-bold mb-3">Our Story</h3>
                    <p class="text-muted mb-0">We built Uyayi to make it easier for parents to find baby essentials in one calm, trustworthy place. From bath time to skin care and hygiene basics, every product category is organized to help families shop with confidence.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="category-card text-center p-4 h-100 rounded-4">
                    <i class="bi bi-droplet-heart display-5 mb-3"></i>
                    <h4 class="fw-bold">Gentle First</h4>
                    <p class="text-muted mb-0">Our store is centered around baby-friendly care essentials suitable for delicate skin and daily use.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="category-card text-center p-4 h-100 rounded-4">
                    <i class="bi bi-grid-3x3-gap display-5 mb-3"></i>
                    <h4 class="fw-bold">Organized Collections</h4>
                    <p class="text-muted mb-0">Products are grouped into clear categories so parents can quickly find what they need.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="category-card text-center p-4 h-100 rounded-4">
                    <i class="bi bi-shield-check display-5 mb-3"></i>
                    <h4 class="fw-bold">Reliable Shopping</h4>
                    <p class="text-muted mb-0">We aim to provide a simple storefront experience with easy browsing, ordering, and account access.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: white;">
    <div class="container text-center">
        <h2 class="display-6 fw-bold mb-3" style="color: var(--primary-blue-dark);">Explore the Store</h2>
        <p class="text-muted mb-4">Browse products by category or jump into the full shop to see everything available.</p>
        <div class="d-flex justify-content-center flex-wrap gap-3">
            <a href="{{ route('collections') }}" class="btn btn-primary-custom">View Collections</a>
            <a href="{{ route('shop') }}" class="btn btn-outline-success">Go to Shop</a>
        </div>
    </div>
</section>
@endsection
