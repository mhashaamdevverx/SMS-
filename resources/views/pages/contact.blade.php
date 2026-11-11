@extends('layouts.public')

@section('title', __('Contact Us & Campus Location') . ' - Excellence School System')

@section('content')
<!-- Page Banner Header -->
<section class="page-banner text-center">
    <div class="container">
        <span class="page-banner-tag"><i class="bi bi-geo-alt-fill me-1"></i> {{ __('Get in Touch') }}</span>
        <h1 class="page-banner-title">{{ __('We\'d Love to Hear From You') }}</h1>
        <p class="page-banner-sub">
            {{ __('Have questions regarding admissions, fee structure, or curriculum? Reach out to our admissions secretariat or visit our campus.') }}
        </p>
    </div>
</section>

<!-- Contact Info & Message Section -->
<section class="py-5 bg-white">
    <div class="container py-4">
        <div class="row g-5">
            <!-- Contact Details -->
            <div class="col-lg-5">
                <span class="badge px-3 py-2 rounded-pill bg-primary-subtle text-primary fw-bold mb-3" style="font-size: 13px;">{{ __('CAMPUS SECRETARIAT') }}</span>
                <h2 class="fw-bold mb-4" style="color: #0F172A; font-size: 32px;">{{ __('Main Campus & Head Office') }}</h2>

                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex align-items-start gap-3 p-3 rounded-4 border bg-light">
                        <div class="rounded-3 p-2 bg-primary-subtle text-primary fs-4 flex-shrink-0">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">{{ __('Campus Address') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Plot 18, Sector H-8/4, Educational Enclave, Islamabad, Pakistan') }}</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-3 rounded-4 border bg-light">
                        <div class="rounded-3 p-2 bg-success-subtle text-success fs-4 flex-shrink-0">
                            <i class="bi bi-telephone-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">{{ __('Phone Numbers') }}</h6>
                            <p class="text-muted small mb-0">{{ __('UAN:') }} +92 51 848 2900 / 01 <br> {{ __('Mobile:') }} +92 300 555 8899</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-3 rounded-4 border bg-light">
                        <div class="rounded-3 p-2 bg-info-subtle text-info fs-4 flex-shrink-0">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">{{ __('Email Addresses') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Admissions:') }} admissions@excellenceschool.edu.pk <br> {{ __('General:') }} info@excellenceschool.edu.pk</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-3 rounded-4 border bg-light">
                        <div class="rounded-3 p-2 bg-warning-subtle text-warning fs-4 flex-shrink-0">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">{{ __('Visiting Hours') }}</h6>
                            <p class="text-muted small mb-0">{{ __('Monday – Friday: 8:00 AM – 3:30 PM') }} <br> {{ __('Saturday: 9:00 AM – 1:30 PM (Admissions Only)') }}</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('support') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4 py-2 w-100">
                        <i class="bi bi-headset me-2"></i> {{ __('Visit Support Center') }}
                    </a>
                </div>
            </div>

            <!-- Quick Inquiry Message Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-lg rounded-4 p-4 p-md-5" style="border-top: 5px solid #4F46E5 !important;">
                    <h3 class="fw-bold mb-1 text-dark">{{ __('Send Us a Direct Message') }}</h3>
                    <p class="text-muted small mb-4">{{ __('Our administrative officer will review and reply within one business day.') }}</p>

                    <form action="{{ route('inquiry.submit') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">{{ __('Your Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="parent_name" class="form-control py-2 rounded-3" placeholder="{{ __('Full name') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control py-2 rounded-3" placeholder="03xx-xxxxxxx" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">{{ __('Email Address') }}</label>
                                <input type="email" name="email" class="form-control py-2 rounded-3" placeholder="you@domain.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-dark">{{ __('Subject / Area of Inquiry') }} <span class="text-danger">*</span></label>
                                <select name="grade" class="form-select py-2 rounded-3" required>
                                    <option value="" disabled selected>{{ __('Select Subject') }}</option>
                                    <option value="Admission Inquiry">{{ __('Admission Inquiry') }}</option>
                                    <option value="Fee & Transport Details">{{ __('Fee & Transport Details') }}</option>
                                    <option value="Curriculum & Wing Transfer">{{ __('Curriculum & Wing Transfer') }}</option>
                                    <option value="Parent Teacher Meeting">{{ __('Parent Teacher Meeting') }}</option>
                                    <option value="General Campus Question">{{ __('General Campus Question') }}</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <input type="hidden" name="student_name" value="General Contact Inquiry">
                                <label class="form-label fw-bold small text-dark">{{ __('Your Message') }} <span class="text-danger">*</span></label>
                                <textarea name="message" rows="4" class="form-control rounded-3" placeholder="{{ __('How can we assist you today? Please provide relevant details...') }}" required></textarea>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow" style="background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 100%);">
                                    <i class="bi bi-send-fill me-2"></i> {{ __('Send Message') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
