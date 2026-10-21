<div class="row g-4">
    <!-- Left Column: Admin Profile Card & System Authority Overview -->
    <div class="col-lg-4 col-md-5">
        <div class="admin-card text-center p-4 mb-4 shadow-sm border-0 rounded-4">
            <!-- Profile Avatar -->
            <div style="position: relative; width: 116px; height: 116px; margin: 0 auto 16px;">
                @php
                    $admPhoto = !empty($currentUser->image) && $currentUser->image !== 'dummy.jpeg'
                        ? asset('storage/'.$currentUser->image)
                        : (session('user_image') && session('user_image') !== 'dummy.jpeg'
                            ? asset('storage/'.session('user_image'))
                            : asset('upload/dummy.jpeg'));
                @endphp
                <img src="{{ $admPhoto }}" onerror="this.onerror=null; this.src='{{ asset('upload/dummy.jpeg') }}';" 
                     alt="{{ $currentUser->name ?: 'Administrator' }}" 
                     style="width: 116px; height: 116px; object-fit: cover; border-radius: 50%; border: 4px solid #4F46E5; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.25);">
                <span style="position: absolute; bottom: 4px; right: 6px; width: 22px; height: 22px; background: #10B981; border: 3px solid #fff; border-radius: 50%;" title="Account Active"></span>
            </div>

            <h4 class="fw-bold mb-1 text-dark">{{ $currentUser->name ?: $currentUser->username }}</h4>
            <div class="d-flex justify-content-center align-items-center gap-1 mb-2">
                <span class="badge bg-danger-subtle text-danger border border-danger-subtle fw-bold px-3 py-1" style="font-size: 12px; border-radius: 99px;">
                    <i class="bi bi-shield-lock-fill me-1"></i> {{ $currentUser->role?->name ?: 'Head Administrator' }}
                </span>
            </div>
            <p class="text-muted small mb-3">Master Administrator &bull; Excellence School System</p>

            <hr style="border-color: #f1f5f9; margin: 16px 0;">

            <!-- Quick Details List -->
            <div class="text-start" style="display: flex; flex-direction: column; gap: 10px;">
                <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                    <span class="text-muted small fw-semibold"><i class="bi bi-person-badge text-primary me-2"></i>Account ID</span>
                    <strong class="text-dark small">#{{ $currentUser->id }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                    <span class="text-muted small fw-semibold"><i class="bi bi-at text-primary me-2"></i>Username</span>
                    <strong class="text-dark small">{{ $currentUser->username }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                    <span class="text-muted small fw-semibold"><i class="bi bi-envelope text-primary me-2"></i>Email</span>
                    <strong class="text-dark small text-truncate" style="max-width: 170px;" title="{{ $currentUser->email }}">{{ $currentUser->email }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                    <span class="text-muted small fw-semibold"><i class="bi bi-telephone text-primary me-2"></i>Phone</span>
                    <strong class="text-dark small">{{ $currentUser->phone ?: '0339-2266560' }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                    <span class="text-muted small fw-semibold"><i class="bi bi-calendar-check text-primary me-2"></i>Registered Since</span>
                    <strong class="text-dark small">{{ $currentUser->created_at ? $currentUser->created_at->format('M d, Y') : 'N/A' }}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center p-2 rounded-3" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                    <span class="text-muted small fw-semibold"><i class="bi bi-check-circle text-success me-2"></i>Access Level</span>
                    <span class="badge bg-success-subtle text-success fw-bold px-2 py-1">Super Authority</span>
                </div>
            </div>
        </div>

        <!-- Super Administrator Authority Notice -->
        <div class="admin-card p-3 shadow-sm border-0 rounded-4" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.08), rgba(99, 102, 241, 0.15)); border: 1px solid rgba(79, 70, 229, 0.25) !important;">
            <div class="d-flex gap-3 align-items-center">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: #4F46E5; color: #fff; display: grid; place-items: center; font-size: 20px; flex-shrink: 0; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-1 text-dark">Master Authority Active</h6>
                    <p class="small text-muted mb-0" style="font-size: 11.5px; line-height: 1.4;">This administrator profile holds full unrestricted access across all school operations, fees, salaries, attendance, results, and system users.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Edit Profile & Password Management -->
    <div class="col-lg-8 col-md-7">
        <!-- Edit Profile Information Card -->
        <div class="admin-card mb-4 shadow-sm border-0 rounded-4">
            <div class="admin-card-header d-flex align-items-center justify-content-between p-3 border-bottom">
                <h5 class="admin-card-title mb-0 fw-bold">
                    <i class="bi bi-person-gear text-primary me-2"></i> Edit My Profile Information
                </h5>
                <span class="badge bg-primary-subtle text-primary fw-bold px-2 py-1" style="font-size: 11px;">
                    Personal Account
                </span>
            </div>
            <div class="p-4">
                <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                                <input type="text" name="name" class="form-control border-start-0" value="{{ old('name', $currentUser->name ?: $currentUser->username) }}" required placeholder="Enter your full name">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">System Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-at text-muted"></i></span>
                                <input type="text" class="form-control border-start-0 bg-light" value="{{ $currentUser->username }}" disabled title="System username is permanent">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" class="form-control border-start-0" value="{{ old('email', $currentUser->email) }}" required placeholder="Enter your email address">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-telephone text-muted"></i></span>
                                <input type="text" name="phone" class="form-control border-start-0" value="{{ old('phone', $currentUser->phone) }}" placeholder="e.g. 0339-2266560">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Update Profile Photo</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Supported file formats: JPG, PNG, WEBP. Maximum file size: 2MB.</small>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="admin-btn admin-btn-primary px-4 py-2" style="font-weight: 700;">
                                <i class="bi bi-check2-circle me-1"></i> Save Profile Details
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Security & Password Change Card -->
        <div class="admin-card shadow-sm border-0 rounded-4">
            <div class="admin-card-header d-flex align-items-center justify-content-between p-3 border-bottom">
                <h5 class="admin-card-title mb-0 fw-bold">
                    <i class="bi bi-shield-lock-fill text-warning me-2"></i> Security & Password Management
                </h5>
                <span class="badge bg-warning-subtle text-warning fw-bold px-2 py-1" style="font-size: 11px;">
                    Account Security
                </span>
            </div>
            <div class="p-4">
                <form action="{{ route('admin.password.update') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">Current Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-key text-muted"></i></span>
                                <input type="password" name="current_password" class="form-control border-start-0" required placeholder="Enter current login password">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="new_password" class="form-control border-start-0" required minlength="6" placeholder="Enter new password (min. 6 chars)">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Confirm New Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock-fill text-muted"></i></span>
                                <input type="password" name="new_password_confirmation" class="form-control border-start-0" required minlength="6" placeholder="Re-enter new password">
                            </div>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="admin-btn admin-btn-outline px-4 py-2 fw-bold" style="border-color: #F59E0B; color: #D97706;">
                                <i class="bi bi-shield-check me-1"></i> Update Account Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
