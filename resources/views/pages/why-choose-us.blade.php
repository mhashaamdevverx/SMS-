@extends('layouts.public')

@section('title', __('Why Choose Us') . ' - Excellence School System')

@section('content')
<!-- Page Banner Header -->
<section class="page-banner text-center">
    <div class="container">
        <span class="page-banner-tag"><i class="bi bi-star-fill text-warning me-1"></i> {{ __('Distinct Advantages') }}</span>
        <h1 class="page-banner-title">{{ __('Why Excellence School System?') }}</h1>
        <p class="page-banner-sub">
            {{ __('We provide a holistic educational ecosystem where students achieve academic brilliance, master future-ready digital technologies, and cultivate upright moral character.') }}
        </p>
    </div>
</section>

<!-- 6 Pillar Advantages Grid -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge px-3 py-2 rounded-pill bg-primary-subtle text-primary fw-bold mb-2">{{ __('EDUCATIONAL EXCELLENCE') }}</span>
            <h2 class="fw-bold" style="color: #0F172A; font-size: 34px;">{{ __('6 Compelling Reasons Parents Trust Us') }}</h2>
        </div>

        <div class="row g-4">
            <!-- Feature 1 -->
            <div class="col-md-6 col-lg-4">
                <div class="card border rounded-4 p-4 h-100 shadow-sm transition-hover">
                    <div class="rounded-4 p-3 d-inline-block mb-3 bg-primary-subtle text-primary" style="width: 58px; height: 58px; text-align: center;">
                        <i class="bi bi-robot fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">{{ __('1. Hands-on STEM & AI Robotics') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Unlike traditional rote-learning schools, every child from Class 1 learns coding, sensor robotics, 3D printing, and mathematical logic in our dedicated Innovation Lab.') }}</p>
                </div>
            </div>

            <!-- Feature 2 -->
            <div class="col-md-6 col-lg-4">
                <div class="card border rounded-4 p-4 h-100 shadow-sm transition-hover">
                    <div class="rounded-4 p-3 d-inline-block mb-3 bg-success-subtle text-success" style="width: 58px; height: 58px; text-align: center;">
                        <i class="bi bi-award-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">{{ __('2. 99.8% Board Distinction') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Our structured testing sessions and individual subject diagnostic tracking ensure students consistently achieve top positions in FBISE board exams.') }}</p>
                </div>
            </div>

            <!-- Feature 3 -->
            <div class="col-md-6 col-lg-4">
                <div class="card border rounded-4 p-4 h-100 shadow-sm transition-hover">
                    <div class="rounded-4 p-3 d-inline-block mb-3 bg-warning-subtle text-warning" style="width: 58px; height: 58px; text-align: center;">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">{{ __('3. 15:1 Student-Teacher Ratio') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Small classroom sizes guarantee personalized attention, enabling teachers to identify each child\'s unique talents and address learning gaps promptly.') }}</p>
                </div>
            </div>

            <!-- Feature 4 -->
            <div class="col-md-6 col-lg-4">
                <div class="card border rounded-4 p-4 h-100 shadow-sm transition-hover">
                    <div class="rounded-4 p-3 d-inline-block mb-3 bg-info-subtle text-info" style="width: 58px; height: 58px; text-align: center;">
                        <i class="bi bi-shield-check fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">{{ __('4. Islamic Ethics & Leadership') }}</h5>
                    <p class="text-muted small mb-0">{{ __('High academic standards go hand-in-hand with empathy, honesty, public speaking confidence, and strong moral values anchored in Islamic ethos.') }}</p>
                </div>
            </div>

            <!-- Feature 5 -->
            <div class="col-md-6 col-lg-4">
                <div class="card border rounded-4 p-4 h-100 shadow-sm transition-hover">
                    <div class="rounded-4 p-3 d-inline-block mb-3 bg-purple-subtle text-purple" style="width: 58px; height: 58px; text-align: center; background: #FAF5FF; color: #8B5CF6;">
                        <i class="bi bi-phone-fill fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">{{ __('5. 24/7 Academic Parent Portal') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Real-time attendance, test marks, roll slips, fee status, and teacher communications are instantly accessible via web & mobile portals.') }}</p>
                </div>
            </div>

            <!-- Feature 6 -->
            <div class="col-md-6 col-lg-4">
                <div class="card border rounded-4 p-4 h-100 shadow-sm transition-hover">
                    <div class="rounded-4 p-3 d-inline-block mb-3 bg-danger-subtle text-danger" style="width: 58px; height: 58px; text-align: center;">
                        <i class="bi bi-dribbble fs-3"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2">{{ __('6. 2-Acre Sports & Activity Arena') }}</h5>
                    <p class="text-muted small mb-0">{{ __('Lush green grounds, professional cricket nets, football field, basketball court, and martial arts training foster physical vitality and sportsmanship.') }}</p>
                </div>
            </div>
        </div>

        <!-- Campus CTA -->
        <div class="mt-5 p-5 rounded-4 text-center text-white" style="background: linear-gradient(135deg, #1E1B4B 0%, #312E81 100%);">
            <h3 class="fw-bold mb-2">{{ __('Experience the Excellence Difference Firsthand') }}</h3>
            <p class="text-light opacity-75 max-w-600 mx-auto mb-4">{{ __('Book a private guided campus tour and meet our academic leadership team.') }}</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('admissions') }}" class="btn btn-warning px-4 py-3 fw-bold rounded-3 shadow">
                    {{ __('Apply Online for :session', ['session' => $academicSession ?? '2026-2027']) }} <i class="bi bi-arrow-right ms-2"></i>
                </a>
                <a href="{{ route('contact') }}" class="btn btn-outline-light px-4 py-3 fw-bold rounded-3">
                    {{ __('Contact Admissions Office') }}
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
