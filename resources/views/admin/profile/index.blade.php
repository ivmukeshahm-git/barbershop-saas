@extends('admin.layout.layout')
@section('content')
    <script>
        page_active_id = "Profile";
    </script>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="bc-page-header">
            <div>
                <h4>My Profile</h4>
                <p>View and update your account details</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card rounded-3 shadow-sm p-4 h-100">
                    <div class="text-center">
                        <img id="profilePreview"
                            src="{{ $user->profile_img ? asset('storage/' . $user->profile_img) : asset('assets/img/user.png') }}"
                            alt="{{ $user->name }}" class="rounded-circle mb-3"
                            style="width:120px;height:120px;object-fit:cover;border:3px solid #136207;">
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-2">{{ $user->role }}</p>
                        <p class="mb-1"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</p>
                        <p class="mb-0"><i class="bi bi-telephone me-1"></i>{{ $user->phone ?: '—' }}</p>
                    </div>
                    @if ($serviceNames->isNotEmpty())
                        <hr>
                        <h6 class="fw-semibold mb-2">Assigned Services</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($serviceNames as $name)
                                <span class="badge" style="background:#136207;">{{ html_entity_decode($name, ENT_QUOTES, 'UTF-8') }}</span>
                            @endforeach
                        </div>
                    @endif
                    @if (strcasecmp((string) $user->role, 'Staff') === 0)
                        <a href="{{ route('admin.staff.staff-profile', $user->id) }}" class="btn btn-outline-primary w-100 mt-4">
                            View full stats
                        </a>
                    @endif
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card rounded-3 shadow-sm p-4">
                    <h5 class="mb-4 fw-semibold">Edit Profile</h5>
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-500">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-500">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-500">Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-500">Role</label>
                                <input type="text" class="form-control" value="{{ $user->role }}" disabled>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-500">Profile Photo</label>
                                <input type="file" name="profile_image" id="profile_image" class="form-control" accept="image/*">
                                <small class="text-muted">JPG, PNG or GIF. Max 2MB.</small>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check2 me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @if ($stats)
            <div class="row g-4 mt-1">
                <div class="col-lg-3 col-sm-6">
                    <div class="card rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Completed Services</p>
                            <h4 class="mb-0">{{ $stats->totalAppointments }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Total Visits</p>
                            <h4 class="mb-0">{{ $stats->totalVisit }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Tips</p>
                            <h4 class="mb-0">{{ $shopCurrencySymbol ?? '₹' }}{{ number_format((float) $stats->total_service_tip, 2) }}</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card rounded-3 shadow-sm h-100">
                        <div class="card-body">
                            <p class="text-muted mb-1">Total Earning</p>
                            <h4 class="mb-0">{{ $shopCurrencySymbol ?? '₹' }}{{ number_format((float) $stats->total_earning, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <script>
        document.getElementById('profile_image')?.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;
            const preview = document.getElementById('profilePreview');
            preview.src = URL.createObjectURL(file);
        });
    </script>
@endsection
