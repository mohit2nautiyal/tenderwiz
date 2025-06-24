@extends('layouts.app')

@section('content')
<main class="professional-theme">
    @isset($flash)
        <div class="alert alert-{{ $flash['type'] }} alert-dismissible fade show animate__animated animate__shakeX mt-3 shadow-sm rounded" role="alert">
            {{ $flash['message'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endisset

    <div class="box-info">
        <div class="card shadow-sm rounded-lg animate__animated animate__fadeIn">
            <div class="card-header bg-gradient-primary text-white">
                <h3 class="mb-0 fw-semibold"><i class='bx bxs-user me-2'></i>Edit User: {{ $user->name }}</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="subscription_end_date">Subscription End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('subscription_end_date') is-invalid @enderror" id="subscription_end_date" name="subscription_end_date" value="{{ old('subscription_end_date', $user->subscription_end_date ? \Carbon\Carbon::parse($user->subscription_end_date)->format('Y-m-d') : '') }}" required>
                            @error('subscription_end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill shadow-sm">
                            <i class='bx bxs-save me-2'></i>Save Changes
                        </button>
                        <a href="{{ route('admin.users.listing') }}" class="btn btn-outline-secondary rounded-pill">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .professional-theme .card {
            background: #fff;
            border-radius: 12px;
            transition: transform 0.3s ease;
        }

        .professional-theme .card-header {
            padding: 1rem 1.5rem;
        }

        .professional-theme .btn-primary {
            background: linear-gradient(90deg, var(--blue), #2a73c0);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .professional-theme .btn-primary:hover {
            background: linear-gradient(90deg, #2a73c0, var(--blue));
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(60, 145, 230, 0.3);
        }

        .professional-theme .btn-outline-secondary {
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }

        .professional-theme .alert {
            border-left: 4px solid;
            padding: 1rem;
        }

        .professional-theme .alert-success {
            border-color: #28a745;
        }

        .professional-theme .alert-danger {
            border-color: #dc3545;
        }
    </style>
</main>
@endsection

@section('scripts')
    <script>
        // Bootstrap form validation
        (function () {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
@endsection