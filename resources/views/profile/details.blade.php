@extends('layouts.app')

@section('content')
<main>
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="box-info">
        <div class="card shadow-sm animate__animated animate__fadeIn">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">
                    <i class="fas fa-user-circle me-2"></i> Account Holder Details
                </h3>
            </div>
            <div class="card-body">
                <form id="profileDetailsForm" action="{{ route('profile.details.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    <!-- Account Holder Information -->
                    <h5 class="section-header">
                        <i class="fas fa-user me-2 text-muted"></i> Account Holder Information
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="account_holder_name" class="form-label">
                                <i class="fas fa-user me-2 text-muted"></i> Name of the Account Holder <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('account_holder_name') is-invalid @enderror" id="account_holder_name" name="account_holder_name" value="{{ old('account_holder_name', $user->account_holder_name ?? '') }}" required>
                            @error('account_holder_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="company_name" class="form-label">
                                <i class="fas fa-building me-2 text-muted"></i> Name of the Company/Firm/Organization
                            </label>
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $user->company_name ?? '') }}">
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2 text-muted"></i> E-mail ID <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->pending_email ?? $user->email ?? '') }}" required>
                                <span class="input-group-text">
                                    @if ($user->hasVerifiedEmail() && !$user->pending_email)
                                        <span class="text-success">Verified</span>
                                    @else
                                        <span class="text-danger">Not Verified</span>
                                        <button type="button" class="btn btn-link p-0 ms-2 resend-otp" title="Resend OTP">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    @endif
                                </span>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="mobile_number" class="form-label">
                                <i class="fas fa-phone me-2 text-muted"></i> Mobile Number <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('mobile_number') is-invalid @enderror" id="mobile_number" name="mobile_number" value="{{ old('mobile_number', $user->mobile_number ?? '') }}" required>
                            @error('mobile_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Registered Address -->
                    <h5 class="section-header">
                        <i class="fas fa-map-marker-alt me-2 text-muted"></i> Registered Address
                    </h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="reg_address_line1" class="form-label">
                                <i class="fas fa-address-card me-2 text-muted"></i> Line 1 <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('reg_address_line1') is-invalid @enderror" id="reg_address_line1" name="reg_address_line1" value="{{ old('reg_address_line1', $user->reg_address_line1 ?? '') }}" required>
                            @error('reg_address_line1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="reg_address_line2" class="form-label">
                                <i class="fas fa-address-card me-2 text-muted"></i> Line 2
                            </label>
                            <input type="text" class="form-control @error('reg_address_line2') is-invalid @enderror" id="reg_address_line2" name="reg_address_line2" value="{{ old('reg_address_line2', $user->reg_address_line2 ?? '') }}">
                            @error('reg_address_line2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="reg_district" class="form-label">
                                <i class="fas fa-city me-2 text-muted"></i> District <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('reg_district') is-invalid @enderror" id="reg_district" name="reg_district" value="{{ old('reg_district', $user->reg_district ?? '') }}" required>
                            @error('reg_district')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="reg_state" class="form-label">
                                <i class="fas fa-map me-2 text-muted"></i> State <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('reg_state') is-invalid @enderror" id="reg_state" name="reg_state" value="{{ old('reg_state', $user->reg_state ?? '') }}" required>
                            @error('reg_state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="reg_country" class="form-label">
                                <i class="fas fa-globe me-2 text-muted"></i> Country <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('reg_country') is-invalid @enderror" id="reg_country" name="reg_country" value="{{ old('reg_country', $user->reg_country ?? '') }}" required>
                            @error('reg_country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="reg_pincode" class="form-label">
                                <i class="fas fa-map-pin me-2 text-muted"></i> Pincode <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('reg_pincode') is-invalid @enderror" id="reg_pincode" name="reg_pincode" value="{{ old('reg_pincode', $user->reg_pincode ?? '') }}" required>
                            @error('reg_pincode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Billing Address -->
                    <h5 class="section-header">
                        <i class="fas fa-file-invoice-dollar me-2 text-muted"></i> Billing Address
                    </h5>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="same_as_registered" name="same_as_registered" value="1" {{ old('same_as_registered', 0) ? 'checked' : '' }}>
                        <label class="form-check-label" for="same_as_registered">
                            <i class="fas fa-copy me-2 text-muted"></i> Same as Registered Address
                        </label>
                    </div>
                    <div id="billingAddressFields" style="{{ old('same_as_registered', 0) ? 'display: none;' : '' }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="bill_address_line1" class="form-label">
                                    <i class="fas fa-address-card me-2 text-muted"></i> Line 1 <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('bill_address_line1') is-invalid @enderror" id="bill_address_line1" name="bill_address_line1" value="{{ old('bill_address_line1', $user->bill_address_line1 ?? '') }}">
                                @error('bill_address_line1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="bill_address_line2" class="form-label">
                                    <i class="fas fa-address-card me-2 text-muted"></i> Line 2
                                </label>
                                <input type="text" class="form-control @error('bill_address_line2') is-invalid @enderror" id="bill_address_line2" name="bill_address_line2" value="{{ old('bill_address_line2', $user->bill_address_line2 ?? '') }}">
                                @error('bill_address_line2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="bill_district" class="form-label">
                                    <i class="fas fa-city me-2 text-muted"></i> District <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('bill_district') is-invalid @enderror" id="bill_district" name="bill_district" value="{{ old('bill_district', $user->bill_district ?? '') }}">
                                @error('bill_district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="bill_state" class="form-label">
                                    <i class="fas fa-map me-2 text-muted"></i> State <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('bill_state') is-invalid @enderror" id="bill_state" name="bill_state" value="{{ old('bill_state', $user->bill_state ?? '') }}">
                                @error('bill_state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="bill_country" class="form-label">
                                    <i class="fas fa-globe me-2 text-muted"></i> Country <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('bill_country') is-invalid @enderror" id="bill_country" name="bill_country" value="{{ old('bill_country', $user->bill_country ?? '') }}">
                                @error('bill_country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="bill_pincode" class="form-label">
                                    <i class="fas fa-map-pin me-2 text-muted"></i> Pincode <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('bill_pincode') is-invalid @enderror" id="bill_pincode" name="bill_pincode" value="{{ old('bill_pincode', $user->bill_pincode ?? '') }}">
                                @error('bill_pincode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-save me-2"></i> Save Details
                        </button>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- OTP Verification Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="otpModalLabel">Verify Email with OTP</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="otpForm" action="{{ route('profile.verify.otp') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="otp" class="form-label">
                                <i class="fas fa-key me-2 text-muted"></i> Enter OTP
                            </label>
                            <input type="text" class="form-control @error('otp') is-invalid @enderror" id="otp" name="otp" required>
                            @error('otp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i> Verify OTP
                            </button>
                            <button type="button" class="btn btn-outline-secondary resend-otp">
                                <i class="fas fa-redo me-2"></i> Resend OTP
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Bootstrap form validation
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

            // jQuery Validate for profile details form
            $('#profileDetailsForm').validate({
                rules: {
                    'account_holder_name': { required: true },
                    'email': { required: true, email: true },
                    'mobile_number': { required: true, pattern: /^[0-9]{10,15}$/ },
                    'reg_address_line1': { required: true },
                    'reg_district': { required: true },
                    'reg_state': { required: true },
                    'reg_country': { required: true },
                    'reg_pincode': { required: true, pattern: /^[0-9]{5,10}$/ },
                    'bill_address_line1': { required: '#same_as_registered:not(:checked)' },
                    'bill_district': { required: '#same_as_registered:not(:checked)' },
                    'bill_state': { required: '#same_as_registered:not(:checked)' },
                    'bill_country': { required: '#same_as_registered:not(:checked)' },
                    'bill_pincode': { required: '#same_as_registered:not(:checked)', pattern: /^[0-9]{5,10}$/ },
                },
                messages: {
                    'account_holder_name': 'Please enter the account holder name.',
                    'email': {
                        required: 'Please enter an email address.',
                        email: 'Please enter a valid email address.'
                    },
                    'mobile_number': {
                        required: 'Please enter a mobile number.',
                        pattern: 'Mobile number must be between 10 and 15 digits.'
                    },
                    'reg_address_line1': 'Please enter address line 1.',
                    'reg_district': 'Please enter the district.',
                    'reg_state': 'Please enter the state.',
                    'reg_country': 'Please enter the country.',
                    'reg_pincode': {
                        required: 'Please enter the pincode.',
                        pattern: 'Pincode must be between 5 and 10 digits.'
                    },
                    'bill_address_line1': 'Please enter address line 1 for billing address.',
                    'bill_district': 'Please enter the district for billing address.',
                    'bill_state': 'Please enter the state for billing address.',
                    'bill_country': 'Please enter the country for billing address.',
                    'bill_pincode': {
                        required: 'Please enter the pincode for billing address.',
                        pattern: 'Pincode must be between 5 and 10 digits.'
                    },
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.col-md-6').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                submitHandler: function(form) {
                    $(form).find('button[type="submit"]').addClass('animate__animated animate__pulse');
                    form.submit();
                }
            });

            // jQuery Validate for OTP form
            $('#otpForm').validate({
                rules: {
                    'otp': { required: true, minlength: 6, maxlength: 6 }
                },
                messages: {
                    'otp': {
                        required: 'Please enter the OTP.',
                        minlength: 'OTP must be 6 characters.',
                        maxlength: 'OTP must be 6 characters.'
                    }
                },
                errorElement: 'div',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.mb-3').append(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });

            // Handle "Same as Registered Address" checkbox
            document.getElementById('same_as_registered').addEventListener('change', function () {
                const billingFields = document.getElementById('billingAddressFields');
                if (this.checked) {
                    billingFields.style.display = 'none';
                    document.getElementById('bill_address_line1').value = document.getElementById('reg_address_line1').value;
                    document.getElementById('bill_address_line2').value = document.getElementById('reg_address_line2').value;
                    document.getElementById('bill_district').value = document.getElementById('reg_district').value;
                    document.getElementById('bill_state').value = document.getElementById('reg_state').value;
                    document.getElementById('bill_country').value = document.getElementById('reg_country').value;
                    document.getElementById('bill_pincode').value = document.getElementById('reg_pincode').value;
                    $(billingFields).find('input').removeClass('is-invalid').addClass('is-valid');
                } else {
                    billingFields.style.display = 'block';
                    $(billingFields).find('input').removeClass('is-valid');
                }
            });

            // Sync registered address fields with billing address fields if checkbox is checked
            const regFields = ['reg_address_line1', 'reg_address_line2', 'reg_district', 'reg_state', 'reg_country', 'reg_pincode'];
            regFields.forEach(field => {
                document.getElementById(field).addEventListener('input', function () {
                    if (document.getElementById('same_as_registered').checked) {
                        const billField = document.getElementById(field.replace('reg_', 'bill_'));
                        billField.value = this.value;
                        if (this.classList.contains('is-valid')) {
                            billField.classList.remove('is-invalid');
                            billField.classList.add('is-valid');
                        }
                    }
                });
            });

            // Handle resend OTP via AJAX
            $('.resend-otp').on('click', function () {
                $.ajax({
                    url: '{{ route('profile.resend.otp') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            alert(response.message);
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function () {
                        alert('An error occurred while resending the OTP.');
                    }
                });
            });

            // Show OTP modal if needed
            @if (session('showOtpModal'))
                $('#otpModal').modal('show');
            @endif
        });
    </script>
@endsection