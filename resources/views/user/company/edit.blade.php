@extends('layouts.app')

@section('content')
<main class="professional-theme">
    @isset($flash)
        <div class="alert alert-{{ $flash['type'] }} alert-dismissible fade show animate__animated animate__shakeX mt-3 shadow-sm rounded" role="alert">
            {{ $flash['message'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endisset

    <div class="box-info mt-4">
        <div class="card shadow-lg border-0 rounded-lg animate__animated animate__zoomIn">
            <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0 fw-semibold"><i class='bx bxs-building me-2'></i>Company Details</h3>
            </div>
            <div class="card-body">
                <form id="companyForm" action="{{ route('user.company.update') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    <ul class="nav nav-tabs mb-4" id="companyTabs" role="tablist">
                        <li class="nav-item"><button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-tab-pane" type="button">Basic Information</button></li>
                        <li class="nav-item"><button class="nav-link" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates-tab-pane" type="button">Certificates</button></li>
                        <li class="nav-item"><button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial-tab-pane" type="button">Financial Details</button></li>
                        <li class="nav-item"><button class="nav-link" id="experience-tab" data-bs-toggle="tab" data-bs-target="#experience-tab-pane" type="button">Work Experience</button></li>
                    </ul>

                    <div class="tab-content" id="companyTabsContent">
                        <!-- Basic Info Tab -->
                        <div class="tab-pane fade show active" id="basic-tab-pane" role="tabpanel">
                            <div class="row g-3">
                                @php $delay = 0; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-detail me-1 text-primary'></i>Name of the Company/Firm/Organization <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="company_description" name="company_description" rows="3" required>{{ old('company_description', $company->company_description ?? '') }}</textarea>
                                    @error('company_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-tag me-1 text-primary'></i>Business Category (in Keywords)</label>
                                    <div id="keyword-container">
                                        @foreach (old('keywords', $company->keywords ?? ['']) as $keyword)
                                            <div class="keyword-field mb-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="keywords[]" value="{{ $keyword }}">
                                                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remove</button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addMoreField('keyword')">Add More</button>
                                </div>
                                <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay += 0.1 }}s;">
                                    <h6 class="section-header text-dark mt-4"><i class='bx bx-info-circle me-2'></i>Company Details</h6>
                                    <div class="row g-3">
                                        @php $detailDelay = 0; @endphp
                                        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $detailDelay }}s;">
                                            <label class="form-label fw-medium text-dark">Company Registration Type</label>
                                            <select class="form-select select2-checkbox" id="company_registration_type" name="company_registration_type[]" multiple>
                                                <option value="">-- Select Type --</option>
                                                @foreach (config('constants.COMPANY_REGISTRATION_TYPES', []) as $type)
                                                    <option value="{{ $type }}" {{ in_array($type, old('company_registration_type', array_filter(explode(',', $company->company_registration_type ?? '')))) ? 'selected' : '' }}>{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @error('company_registration_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @php $detailDelay += 0.1; @endphp
                                        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $detailDelay }}s;">
                                            <label class="form-label fw-medium text-dark">Registered Year (YYYY)</label>
                                            <input type="number" class="form-control" id="company_registered_year" name="company_registered_year" min="1900" max="{{ date('Y') }}" value="{{ old('company_registered_year', $company->company_registered_year ?? '') }}">
                                            @error('company_registered_year')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @php $detailDelay += 0.1; @endphp
                                        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $detailDelay }}s;">
                                            <label class="form-label fw-medium text-dark">Sector Type</label>
                                            <select class="form-select select2-checkbox" id="company_sector_type" name="company_sector_type[]" multiple>
                                                <option value="">-- Select Type --</option>
                                                @foreach (config('constants.COMPANY_SECTOR_TYPES', []) as $type)
                                                    <option value="{{ $type }}" {{ in_array($type, old('company_sector_type', array_filter(explode(',', $company->company_sector_type ?? '')))) ? 'selected' : '' }}>{{ $type }}</option>
                                                @endforeach
                                            </select>
                                            @error('company_sector_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @php $detailDelay += 0.1; @endphp
                                        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $detailDelay }}s;">
                                            <label class="form-label fw-medium text-dark">Nature of Business</label>
                                            <select class="form-select select2-checkbox" id="nature_of_business" name="nature_of_business[]" multiple>
                                                <option value="">-- Select Option --</option>
                                                @foreach (config('constants.NATURE_OF_BUSINESS_OPTIONS', []) as $option)
                                                    <option value="{{ $option }}" {{ in_array($option, old('nature_of_business', array_filter(explode(',', $company->nature_of_business ?? '')))) ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                            @error('nature_of_business')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        @php $detailDelay += 0.1; @endphp
                                        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $detailDelay }}s;">
                                            <label class="form-label fw-medium text-dark">Business Specialization</label>
                                            <select class="form-select select2-checkbox" id="business_specialization" name="business_specialization[]" multiple>
                                                <option value="">-- Select Option --</option>
                                                @foreach (config('constants.BUSINESS_SPECIALIZATION_OPTIONS', []) as $option)
                                                    <option value="{{ $option }}" {{ in_array($option, old('business_specialization', array_filter(explode(',', $company->business_specialization ?? '')))) ? 'selected' : '' }}>{{ $option }}</option>
                                                @endforeach
                                            </select>
                                            @error('business_specialization')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Certificates Tab -->
                        <div class="tab-pane fade" id="certificates-tab-pane" role="tabpanel">
                            <ul class="nav nav-tabs mb-3" id="certificatesTabs" role="tablist">
                                @foreach (config('constants.CERTIFICATE_CATEGORIES', []) as $key => $name)
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link {{ $key === 'incorporation' ? 'active' : '' }}" id="{{ $key }}-tab" data-bs-toggle="tab" href="#{{ $key }}" role="tab">
                                            {{ $name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content" id="certificatesTabContent">
                                @foreach (config('constants.CERTIFICATE_CATEGORIES', []) as $key => $name)
                                    <div class="tab-pane {{ $key === 'incorporation' ? 'show active' : '' }}" id="{{ $key }}" role="tabpanel">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body">
                                                <h6 class="mb-4 text-dark">
                                                    <i class='bx bx-certification me-2'></i> {{ $name }} Certificates
                                                </h6>
                                                <div id="{{ $key }}-certificates">
                                                    @foreach (config('constants.CERTIFICATE_LISTS')[$key] as $cert)
                                                        <div class="row g-3 certificate-field mb-3 align-items-center">
                                                            <div class="col-md-4">
                                                                <label class="form-label fw-medium text-dark">{{ $cert }}</label>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <div class="d-flex gap-3">
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="certificates[{{ $key }}][{{ $cert }}][status]" id="{{ $key }}-{{ $cert }}-yes" value="Yes" {{ (old("certificates.{$key}.{$cert}.status", $company->certificates[$key][$cert]['status'] ?? 'No')) === 'Yes' ? 'checked' : '' }}>
                                                                        <label class="form-check-label text-success" for="{{ $key }}-{{ $cert }}-yes">Yes</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="certificates[{{ $key }}][{{ $cert }}][status]" id="{{ $key }}-{{ $cert }}-no" value="No" {{ (old("certificates.{$key}.{$cert}.status", $company->certificates[$key][$cert]['status'] ?? 'No')) === 'No' ? 'checked' : '' }}>
                                                                        <label class="form-check-label text-danger" for="{{ $key }}-{{ $cert }}-no">No</label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="radio" name="certificates[{{ $key }}][{{ $cert }}][status]" id="{{ $key }}-{{ $cert }}-na" value="N/A" {{ (old("certificates.{$key}.{$cert}.status", $company->certificates[$key][$cert]['status'] ?? 'No')) === 'N/A' ? 'checked' : '' }}>
                                                                        <label class="form-check-label text-secondary" for="{{ $key }}-{{ $cert }}-na">N/A</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i class='bx bx-calendar'></i></span>
                                                                    <input type="date" class="form-control" name="certificates[{{ $key }}][{{ $cert }}][valid_up_to]" value="{{ old("certificates.{$key}.{$cert}.valid_up_to", $company->certificates[$key][$cert]['valid_up_to'] ?? '') }}" placeholder="Valid Until">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Financial Tab -->
                        <div class="tab-pane fade" id="financial-tab-pane" role="tabpanel">
                            <ul class="nav nav-tabs mb-3" id="financialTabs" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link active" id="annual-statement-tab" data-bs-toggle="tab" data-bs-target="#annual-statement" type="button" role="tab">Annual Financial Statement</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="itr-tab" data-bs-toggle="tab" data-bs-target="#itr" type="button" role="tab">ITR</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="turnover-tab" data-bs-toggle="tab" data-bs-target="#turnover" type="button" role="tab">Annual Turnover</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="networth-tab" data-bs-toggle="tab" data-bs-target="#networth" type="button" role="tab">Net Worth</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="financialTabContent">
                                <!-- Annual Financial Statement -->
                                <div class="tab-pane fade show active" id="annual-statement" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="financial-statement-table">
                                            <thead class="bg-gradient-primary text-white">
                                                <tr>
                                                    <th>From Year</th>
                                                    <th>To Year</th>
                                                    <th>Total Revenue</th>
                                                    <th>Total Expenses</th>
                                                    <th>Profit/Loss</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $financialStatements = old('financial_statements', $company['financial_statements'] ?? []);
                                                    $currentYear = date('Y');
                                                @endphp
                                                @foreach($financialStatements as $yearRange => $statement)
                                                    @php
                                                        $years = explode('-', $yearRange);
                                                        $fromYear = is_numeric($years[0] ?? '') ? (int)$years[0] : $currentYear - 1;
                                                        $toYear = is_numeric($years[1] ?? '') ? (int)$years[1] : $fromYear + 1;
                                                        $validYearRange = "{$fromYear}-{$toYear}";
                                                    @endphp
                                                    <tr data-year="{{ $fromYear }}" data-year-range="{{ $validYearRange }}">
                                                        <td>
                                                            <input type="number" class="form-control from-year" 
                                                                   name="financial_statements[{{ $validYearRange }}][from_year]" 
                                                                   value="{{ $fromYear }}" 
                                                                   min="2000" max="{{ $currentYear + 1 }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control to-year" 
                                                                   name="financial_statements[{{ $validYearRange }}][to_year]" 
                                                                   value="{{ $toYear }}" 
                                                                   min="2001" max="{{ $currentYear + 2 }}" required>
                                                        </td>
                                                        <td><input type="checkbox" name="financial_statements[{{ $validYearRange }}][revenue]" {{ $statement['revenue'] ?? false ? 'checked' : '' }}></td>
                                                        <td><input type="checkbox" name="financial_statements[{{ $validYearRange }}][expenses]" {{ $statement['expenses'] ?? false ? 'checked' : '' }}></td>
                                                        <td><input type="checkbox" name="financial_statements[{{ $validYearRange }}][profit_loss]" {{ $statement['profit_loss'] ?? false ? 'checked' : '' }}></td>
                                                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-financial-row">Remove</button></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3" id="add-financial-statement-row">Add Row</button>
                                </div>
                                <!-- ITR Tab -->
                                <div class="tab-pane fade" id="itr" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="itr-table">
                                            <thead class="bg-gradient-primary text-white">
                                                <tr>
                                                    <th>From Year</th>
                                                    <th>To Year</th>
                                                    <th>ITR Filed</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $itrData = old('financials.itr', $company['financials']['itr'] ?? []);
                                                @endphp
                                                @foreach($itrData as $yearRange => $value)
                                                    @php
                                                        $years = explode('-', $yearRange);
                                                        $fromYear = is_numeric($years[0] ?? '') ? (int)$years[0] : $currentYear - 1;
                                                        $toYear = is_numeric($years[1] ?? '') ? (int)$years[1] : $fromYear + 1;
                                                        $validYearRange = "{$fromYear}-{$toYear}";
                                                    @endphp
                                                    <tr data-year="{{ $fromYear }}" data-year-range="{{ $validYearRange }}">
                                                        <td>
                                                            <input type="number" class="form-control from-year" 
                                                                   name="itr_years[{{ $validYearRange }}][from_year]" 
                                                                   value="{{ $fromYear }}" 
                                                                   min="2000" max="{{ $currentYear + 1 }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control to-year" 
                                                                   name="itr_years[{{ $validYearRange }}][to_year]" 
                                                                   value="{{ $toYear }}" 
                                                                   min="2001" max="{{ $currentYear + 2 }}" required>
                                                        </td>
                                                        <td>
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" role="switch" 
                                                                       name="financials[itr][{{ $validYearRange }}]" value="1" 
                                                                       {{ $value ? 'checked' : '' }}>
                                                            </div>
                                                        </td>
                                                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-financial-row">Remove</button></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3" id="add-itr-row">Add Row</button>
                                </div>
                                <!-- Annual Turnover Tab -->
                                <div class="tab-pane fade" id="turnover" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="turnover-table">
                                            <thead class="bg-gradient-primary text-white">
                                                <tr>
                                                    <th>From Year</th>
                                                    <th>To Year</th>
                                                    <th>Annual Turnover (In ₹)</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $turnoverData = old('financials.turnover', $company['financials']['turnover'] ?? []);
                                                @endphp
                                                @foreach($turnoverData as $yearRange => $value)
                                                    @php
                                                        $years = explode('-', $yearRange);
                                                        $fromYear = is_numeric($years[0] ?? '') ? (int)$years[0] : $currentYear - 1;
                                                        $toYear = is_numeric($years[1] ?? '') ? (int)$years[1] : $fromYear + 1;
                                                        $validYearRange = "{$fromYear}-{$toYear}";
                                                    @endphp
                                                    <tr data-year="{{ $fromYear }}" data-year-range="{{ $validYearRange }}">
                                                        <td>
                                                            <input type="number" class="form-control from-year" 
                                                                   name="turnover_years[{{ $validYearRange }}][from_year]" 
                                                                   value="{{ $fromYear }}" 
                                                                   min="2000" max="{{ $currentYear + 1 }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control to-year" 
                                                                   name="turnover_years[{{ $validYearRange }}][to_year]" 
                                                                   value="{{ $toYear }}" 
                                                                   min="2001" max="{{ $currentYear + 2 }}" required>
                                                        </td>
                                                        <td>
                                                            <div class="input-group">
                                                                <span class="input-group-text">₹</span>
                                                                <input type="number" class="form-control" 
                                                                       name="financials[turnover][{{ $validYearRange }}]" 
                                                                       value="{{ $value ?? '' }}" 
                                                                       placeholder="Enter amount">
                                                            </div>
                                                        </td>
                                                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-financial-row">Remove</button></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3" id="add-turnover-row">Add Row</button>
                                </div>
                                <!-- Net Worth Tab -->
                                <div class="tab-pane fade" id="networth" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="networth-table">
                                            <thead class="bg-gradient-primary text-white">
                                                <tr>
                                                    <th>From Year</th>
                                                    <th>To Year</th>
                                                    <th>Net Worth Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $networthData = old('financials.net_worth', $company['financials']['net_worth'] ?? []);
                                                @endphp
                                                @foreach($networthData as $yearRange => $value)
                                                    @php
                                                        $years = explode('-', $yearRange);
                                                        $fromYear = is_numeric($years[0] ?? '') ? (int)$years[0] : $currentYear - 1;
                                                        $toYear = is_numeric($years[1] ?? '') ? (int)$years[1] : $fromYear + 1;
                                                        $validYearRange = "{$fromYear}-{$toYear}";
                                                    @endphp
                                                    <tr data-year="{{ $fromYear }}" data-year-range="{{ $validYearRange }}">
                                                        <td>
                                                            <input type="number" class="form-control from-year" 
                                                                   name="networth_years[{{ $validYearRange }}][from_year]" 
                                                                   value="{{ $fromYear }}" 
                                                                   min="2000" max="{{ $currentYear + 1 }}" required>
                                                        </td>
                                                        <td>
                                                            <input type="number" class="form-control to-year" 
                                                                   name="networth_years[{{ $validYearRange }}][to_year]" 
                                                                   value="{{ $toYear }}" 
                                                                   min="2001" max="{{ $currentYear + 2 }}" required>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" 
                                                                           name="financials[net_worth][{{ $validYearRange }}]" 
                                                                           value="positive" 
                                                                           {{ $value === 'positive' ? 'checked' : '' }}>
                                                                    <label class="form-check-label">Positive</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" 
                                                                           name="financials[net_worth][{{ $validYearRange }}]" 
                                                                           value="negative" 
                                                                           {{ $value === 'negative' ? 'checked' : '' }}>
                                                                    <label class="form-check-label">Negative</label>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td><button type="button" class="btn btn-sm btn-outline-danger remove-financial-row">Remove</button></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-primary mt-3" id="add-networth-row">Add Row</button>
                                </div>
                            </div>
                        </div>

                        <!-- Work Experience Tab -->
                        <div class="tab-pane fade" id="experience-tab-pane" role="tabpanel">
                            <h5 class="text-dark"><i class='bx bx-briefcase me-2'></i>Work Experience</h5>
                            <div class="row g-3">
                                @php $expDelay = 0; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $expDelay }}s;">
                                    <label class="form-label fw-medium text-dark" for="working_experience_area">Working Experience Areas</label>
                                    <select class="form-select select2-checkbox @error('work_experience.area') is-invalid @enderror" id="working_experience_area" name="work_experience[area][]" multiple>
                                        @foreach (config('constants.WORK_EXPERIENCE_AREAS', []) as $area)
                                            <option value="{{ $area }}"
                                                {{ in_array($area, old('work_experience.area', (isset($company) && !is_null($company->work_experience) && is_array($company->work_experience) && array_key_exists('area', $company->work_experience) ? $company->work_experience['area'] : []))) ? 'selected' : '' }}>
                                                {{ $area }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('work_experience.area')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @php $expDelay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $expDelay }}s;">
                                    <label class="form-label fw-medium text-dark" for="experience_years">Experience (Years)</label>
                                    <input type="number" class="form-control @error('work_experience.years') is-invalid @enderror" id="experience_years" name="work_experience[years]"
                                        value="{{ old('work_experience.years', $company->work_experience['years'] ?? '') }}"
                                        min="0" step="1" placeholder="Enter years of experience">
                                    @error('work_experience.years')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="form-label fw-medium text-dark">Work Experience Keywords</label>
                                <div id="work-experience-keywords-container">
                                    @php
                                        $keywords = old('work_experience.work_exp_keywords', $company->work_experience['work_exp_keywords'] ?? ['']);
                                    @endphp
                                    @foreach ($keywords as $keyword)
                                        <div class="work-experience-keyword-field mb-2 animate__animated animate__fadeInUp">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="work_experience[work_exp_keywords][]" value="{{ $keyword }}" placeholder="Enter work experience keyword">
                                                <button type="button" class="btn btn-outline-danger remove-field" onclick="removeField(this)">Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addMoreField('work-experience-keyword')">Add More</button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-primary btn-action px-4 animate__animated animate__pulse animate__infinite">Save Company</button>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-secondary btn-action rounded-pill">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<!-- Select2 Styling -->
<style>
/* Professional Select2 Styling - Enhanced (Checkbox Only Selection) */
.select2-container--bootstrap-5 {
    width: 100% !important;
    box-sizing: border-box;
}

.select2-container--bootstrap-5 .select2-selection--multiple {
    min-height: 38px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    background-color: #fff;
    padding: 0.375rem 0.75rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.25rem;
}

.select2-container--bootstrap-5 .select2-selection--multiple:focus-within {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
    display: flex;
    align-items: center;
    margin: 0;
    padding: 0;
    flex-grow: 1;
}

.select2-selection__counter {
    font-size: 0.8125rem;
    color: #495057;
    padding: 0.25rem 0.5rem;
    margin-left: auto;
    white-space: nowrap;
}

.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__clear {
    display: none;
}

.select2-container--bootstrap-5 .select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    z-index: 1050;
}

.select2-results__option {
    padding: 0.5rem 0.75rem;
    font-size: 0.9375rem;
    display: flex;
    align-items: center;
    line-height: 1.5;
    color: #212529;
}

.select2-checkbox-option {
    margin-right: 0.5rem;
    width: 1rem;
    height: 1rem;
    accent-color: #0d6efd;
    flex-shrink: 0;
}

.select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: #e9ecef;
    color: #212529;
    cursor: pointer;
}

.select2-container--bootstrap-5 .select2-results__option[aria-selected=true] {
    background-color: transparent;
    color: #212529;
}

.select2-container--bootstrap-5 .select2-search--inline .select2-search__field {
    margin: 0;
    padding: 0.25rem 0.5rem;
    height: auto;
    font-size: 0.9375rem;
    min-width: 100px;
    border: 0;
    outline: 0;
    background: transparent;
    flex-grow: 1;
}

.select2-results__options {
    max-height: 250px;
    overflow-y: auto;
}

.select2-results__options::-webkit-scrollbar {
    width: 8px;
}

.select2-results__options::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 4px;
}

.select2-results__options::-webkit-scrollbar-thumb {
    background: #adb5bd;
    border-radius: 4px;
}

.select2-results__options::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
}

.select2-selection__placeholder {
    color: #6c757d;
    font-size: 0.9375rem;
    flex-grow: 1;
}

.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
    background-color: #e2e6ea;
    border: 1px solid #dae0e5;
    border-radius: 0.2rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    color: #343a40;
    display: flex;
    align-items: center;
    margin-top: 0.125rem;
    margin-bottom: 0.125rem;
}

.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
    margin-left: 0.5rem;
    color: #6c757d;
    cursor: pointer;
    font-size: 1.1em;
    font-weight: bold;
    line-height: 1;
    opacity: 0.8;
    transition: opacity 0.15s ease-in-out;
}

.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #dc3545;
    opacity: 1;
}

.select2-container--bootstrap-5.select2-container--disabled .select2-selection--multiple {
    background-color: #e9ecef;
    cursor: not-allowed;
    opacity: 0.7;
}

.select2-container--bootstrap-5.select2-container--disabled .select2-selection--multiple .select2-selection__choice__remove {
    pointer-events: none;
}

.select2-container--bootstrap-5.select2-container--readonly .select2-selection--multiple {
    background-color: #f8f9fa;
    cursor: default;
}

.select2-container--bootstrap-5 .select2-selection--multiple.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}
</style>
@endsection

@section('scripts')
<script src="{{ asset('js/jquery.validate.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script>
$(document).ready(function () {
    // Debugging: Check if jQuery and Bootstrap are loaded
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded!');
    } else {
        console.log('jQuery is loaded:', jQuery.fn.jquery);
    }
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap JavaScript is not loaded!');
    } else {
        console.log('Bootstrap is loaded.');
    }

    // Initialize Select2 for multiselect fields
    $('.select2-checkbox').each(function() {
        const $select = $(this);
        $select.select2({
            theme: 'bootstrap-5',
            placeholder: $select.data('placeholder') || 'Select options',
            allowClear: true,
            closeOnSelect: false,
            width: '100%',
            dropdownAutoWidth: true,
            templateResult: formatOption,
            templateSelection: formatSelection,
            escapeMarkup: function(markup) {
                return markup;
            }
        }).on('select2:select select2:unselect', function(e) {
            const optionId = e.params.data.id;
            const isSelected = e.type === 'select2:select';
            const $dropdown = $select.data('select2').$dropdown;
            const $checkbox = $dropdown.find(`.select2-results__option[data-select2-id*="${optionId}"] .select2-checkbox-option`);
            if ($checkbox.length) {
                $checkbox.prop('checked', isSelected);
            }
            updateSelectionCounter($(this));
        }).on('select2:open', function() {
            const selectedIds = $select.val() || [];
            const $dropdown = $select.data('select2').$dropdown;
            $dropdown.find('.select2-results__option').each(function() {
                const $option = $(this);
                const optionData = $option.data('data');
                if (optionData && optionData.id) {
                    const isChecked = selectedIds.includes(String(optionData.id));
                    $option.find('.select2-checkbox-option').prop('checked', isChecked);
                }
            });
        });
        updateSelectionCounter($select);
    });

    // Initialize tabs manually
    $('#companyTabs button[data-bs-toggle="tab"], #certificatesTabs a[data-bs-toggle="tab"], #financialTabs button[data-bs-toggle="tab"]').each(function () {
        const tabTrigger = new bootstrap.Tab(this);
        $(this).on('click', function (e) {
            e.preventDefault();
            console.log('Tab clicked:', $(this).attr('id'));
            tabTrigger.show();
        });
    });

    // Validate form
    $('#companyForm').validate({
        rules: {
            company_description: "required"
        },
        errorElement: "div",
        errorPlacement: function(error, element) {
            error.addClass("invalid-feedback");
            element.closest('.col-md-6, .col-md-4, .col-md-3').append(error);
        },
        highlight: function(element) {
            $(element).addClass('is-invalid').removeClass('is-valid');
            if ($(element).hasClass('select2-checkbox')) {
                $(element).next('.select2-container').find('.select2-selection').addClass('is-invalid');
            }
        },
        unhighlight: function(element) {
            $(element).removeClass('is-invalid').addClass('is-valid');
            if ($(element).hasClass('select2-checkbox')) {
                $(element).next('.select2-container').find('.select2-selection').removeClass('is-invalid');
            }
        }
    });

    // Animate tab content on show
    $('#companyTabs button, #certificatesTabs a, #financialTabs button').on('shown.bs.tab', function (e) {
        const pane = $($(this).data('bs-target') || $(this).attr('href'));
        console.log('Tab shown:', pane.attr('id'));
        pane.addClass('animate__animated animate__fadeIn');
        pane.find('.animate__animated').each(function (index) {
            $(this).css('animation-delay', (index * 0.1) + 's');
        });
    });

    // Hover animations
    $('.nav-link').hover(
        function () { $(this).addClass('animate__animated animate__pulse'); },
        function () { $(this).removeClass('animate__animated animate__pulse'); }
    );
    $('.form-label').hover(
        function () { $(this).addClass('animate__animated animate__bounce'); },
        function () { $(this).removeClass('animate__animated animate__bounce'); }
    );
    $('.btn-action').hover(
        function () { $(this).addClass('animate__animated animate__pulse'); },
        function () { $(this).removeClass('animate__animated animate__pulse'); }
    );

    // Financial sub-tabs configuration
    const financialTabs = {
        'financial-statement': {
            tableId: 'financial-statement-table',
            fieldPrefix: 'financial_statements',
            existingYears: [],
            descending: false
        },
        'itr': {
            tableId: 'itr-table',
            fieldPrefix: 'financials[itr]',
            namePrefix: 'itr_years',
            specificField: 'itr',
            existingYears: [],
            descending: true
        },
        'turnover': {
            tableId: 'turnover-table',
            fieldPrefix: 'financials[turnover]',
            namePrefix: 'turnover_years',
            specificField: 'turnover',
            existingYears: [],
            descending: false
        },
        'networth': {
            tableId: 'networth-table',
            fieldPrefix: 'financials[net_worth]',
            namePrefix: 'networth_years',
            specificField: 'net_worth',
            existingYears: [],
            descending: false
        }
    };

    // Populate existing years for each financial sub-tab
    $.each(financialTabs, function(tabKey, tab) {
        $(`#${tab.tableId} [data-year]`).each(function() {
            const year = parseInt($(this).data('year'));
            if (!isNaN(year) && !tab.existingYears.includes(year)) {
                tab.existingYears.push(year);
            }
        });
        tab.existingYears.sort((a, b) => tab.descending ? b - a : a - b);
    });

    // Add row handlers
    $('#add-financial-statement-row').click(() => addFinancialRow('financial-statement'));
    $('#add-itr-row').click(() => addFinancialRow('itr'));
    $('#add-turnover-row').click(() => addFinancialRow('turnover'));
    $('#add-networth-row').click(() => addFinancialRow('networth'));

    function addFinancialRow(tabKey) {
        const tab = financialTabs[tabKey];
        const currentYear = new Date().getFullYear();
        const tempYearRange = 'temp-' + Math.random().toString(36).substr(2, 9);

        const $newRow = $(`
            <tr data-year-range="${tempYearRange}">
                <td>
                    <input type="number" class="form-control from-year" 
                           name="${tab.namePrefix || tab.fieldPrefix}[${tempYearRange}][from_year]" 
                           placeholder="From Year" 
                           min="2000" max="${currentYear + 1}" required>
                </td>
                <td>
                    <input type="number" class="form-control to-year" 
                           name="${tab.namePrefix || tab.fieldPrefix}[${tempYearRange}][to_year]" 
                           placeholder="To Year" 
                           min="2001" max="${currentYear + 2}" required>
                </td>
                ${tab.specificField ? getFieldCell(tab.fieldPrefix, tempYearRange, tab.specificField) : getStatementCells(tab.fieldPrefix, tempYearRange)}
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-financial-row">Remove</button></td>
            </tr>
        `);

        const $tbody = $(`#${tab.tableId} tbody`);
        tab.descending ? $tbody.prepend($newRow) : $tbody.append($newRow);
        $newRow.addClass('animate__animated animate__fadeIn');
    }

    function getStatementCells(fieldPrefix, yearRange) {
        return `
            <td><input type="checkbox" name="${fieldPrefix}[${yearRange}][revenue]"></td>
            <td><input type="checkbox" name="${fieldPrefix}[${yearRange}][expenses]"></td>
            <td><input type="checkbox" name="${fieldPrefix}[${yearRange}][profit_loss]"></td>
        `;
    }

    function getFieldCell(fieldPrefix, yearRange, fieldName) {
        if (fieldName === 'itr') {
            return `
                <td>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" 
                               name="${fieldPrefix}[${yearRange}]" value="1">
                    </div>
                </td>
            `;
        } else if (fieldName === 'net_worth') {
            return `
                <td>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" 
                                   name="${fieldPrefix}[${yearRange}]" 
                                   value="positive">
                            <label class="form-check-label">Positive</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" 
                                   name="${fieldPrefix}[${yearRange}]" 
                                   value="negative">
                            <label class="form-check-label">Negative</label>
                        </div>
                    </div>
                </td>
            `;
        } else {
            return `
                <td>
                    <div class="input-group">
                        <span class="input-group-text">₹</span>
                        <input type="number" class="form-control" 
                               name="${fieldPrefix}[${yearRange}]" 
                               placeholder="Enter amount">
                    </div>
                </td>
            `;
        }
    }

    function sortFinancialTable($table, descending) {
        const $tbody = $table.find('tbody');
        const $rows = $tbody.find('tr').get();

        $rows.sort((a, b) => {
            const aYear = parseInt($(a).find('.from-year').val()) || 0;
            const bYear = parseInt($(b).find('.from-year').val()) || 0;
            return descending ? bYear - aYear : aYear - bYear;
        });

        $.each($rows, (index, row) => $tbody.append(row));
    }

    $(document).on('click', '.remove-financial-row', function() {
        const $row = $(this).closest('tr');
        const tabKey = $row.closest('table').attr('id').replace('-table', '');
        const tab = financialTabs[tabKey];
        const year = parseInt($row.data('year'));

        $row.addClass('animate__animated animate__fadeOut');
        setTimeout(() => {
            if (year && tab.existingYears.includes(year)) {
                tab.existingYears = tab.existingYears.filter(y => y !== year);
                tab.existingYears.sort((a, b) => tab.descending ? b - a : a - b);
            }
            $row.remove();
        }, 500);
    });

    $(document).on('change', '.from-year', function() {
        const $row = $(this).closest('tr');
        const fromYear = parseInt($(this).val());
        const $toYearInput = $row.find('.to-year');
        const tabKey = $row.closest('table').attr('id').replace('-table', '');
        const tab = financialTabs[tabKey];

        if (!isNaN(fromYear)) {
            const toYear = fromYear + 1;
            $toYearInput.val(toYear);

            if (tab.existingYears.includes(fromYear)) {
                alert(`Year ${fromYear}-${toYear} already exists in the ${tabKey} table.`);
                $(this).val('');
                $toYearInput.val('');
                return;
            }

            const oldYear = parseInt($row.data('year'));
            if (oldYear && tab.existingYears.includes(oldYear)) {
                tab.existingYears = tab.existingYears.filter(y => y !== oldYear);
            }
            tab.existingYears.push(fromYear);
            tab.existingYears.sort((a, b) => tab.descending ? b - a : a - b);

            updateYearRange($row, fromYear, toYear);
            $row.data('year', fromYear);
            sortFinancialTable($row.closest('table'), tab.descending);
        }
    });

    $(document).on('change', '.to-year', function() {
        const $row = $(this).closest('tr');
        const fromYear = parseInt($row.find('.from-year').val());
        const toYear = parseInt($(this).val());

        if (!isNaN(fromYear) && !isNaN(toYear)) {
            if (toYear !== fromYear + 1) {
                alert('To Year must be exactly 1 year after From Year');
                $(this).val(fromYear + 1);
                updateYearRange($row, fromYear, fromYear + 1);
            } else {
                updateYearRange($row, fromYear, toYear);
            }
            sortFinancialTable($row.closest('table'), financialTabs[$row.closest('table').attr('id').replace('-table', '')].descending);
        }
    });

    function updateYearRange($row, fromYear, toYear) {
        const newYearRange = `${fromYear}-${toYear}`;
        const oldYearRange = $row.data('year-range');

        $row.find('[name]').each(function() {
            const oldName = $(this).attr('name');
            const newName = oldName.replace(oldYearRange, newYearRange);
            $(this).attr('name', newName);
        });

        $row.attr('data-year-range', newYearRange);
    }
});

function formatOption(option) {
    if (option.loading) {
        return option.text;
    }
    const isSelected = option.element ? option.element.selected : false;
    const $option = $(
        `<div class="d-flex align-items-center">
            <input type="checkbox" class="select2-checkbox-option form-check-input me-2" ${isSelected ? 'checked' : ''}>
            <span class="select2-option-text text-truncate">${option.text}</span>
        </div>`
    );
    $option.find('.select2-checkbox-option').on('click', function(e) {
        e.stopPropagation();
        const $originalOption = $(this).closest('.select2-results__option');
        const dataId = $originalOption.data('data').id;
        const $selectElement = $(this).closest('.select2-container--open').prev('select');
        if ($(this).is(':checked')) {
            const currentValue = $selectElement.val() || [];
            if (!currentValue.includes(dataId)) {
                $selectElement.val([...currentValue, dataId]).trigger('change');
            }
        } else {
            const currentValue = $selectElement.val() || [];
            const newValue = currentValue.filter(id => id !== dataId);
            $selectElement.val(newValue).trigger('change');
        }
    });
    return $option;
}

function formatSelection() {
    return '';
}

function updateSelectionCounter($select) {
    const $container = $select.siblings('.select2-container');
    const $rendered = $container.find('.select2-selection__rendered');
    const selectedCount = $select.select2('data').length;
    $rendered.empty();
    if (selectedCount > 0) {
        $rendered.append(
            `<span class="select2-selection__counter">
                ${selectedCount} item${selectedCount !== 1 ? 's' : ''} selected
            </span>`
        );
    }
}

window.addMoreField = function(type) {
    let containerId, html, fieldClass, inputName, placeholder;
    
    if (type === 'keyword') {
        containerId = 'keyword-container';
        fieldClass = 'keyword-field';
        inputName = 'keywords[]';
        placeholder = 'Enter keyword';
    } else if (type === 'work-experience-keyword') {
        containerId = 'work-experience-keywords-container';
        fieldClass = 'work-experience-keyword-field';
        inputName = 'work_experience[work_exp_keywords][]';
        placeholder = 'Enter work experience keyword';
    } else {
        console.error(`Unsupported field type: ${type}`);
        alert(`Error: Unsupported field type ${type}.`);
        return;
    }

    html = `
        <div class="${fieldClass} mb-2 animate__animated animate__fadeIn">
            <div class="input-group">
                <input type="text" class="form-control" name="${inputName}" placeholder="${placeholder}">
                <button type="button" class="btn btn-outline-danger remove-field" onclick="removeField(this)">Remove</button>
            </div>
        </div>
    `;

    const container = document.getElementById(containerId);
    if (container) {
        container.insertAdjacentHTML('beforeend', html);
    } else {
        console.error(`Container with ID ${containerId} not found in DOM.`);
    }
};

window.removeField = function(element) {
    const field = element.closest('.keyword-field, .work-experience-keyword-field, .certificate-field');
    if (field) {
        $(field).addClass('animate__animated animate__fadeOut');
        setTimeout(() => field.remove(), 500);
    } else {
        console.error('Field element not found for removal.');
    }
};


</script>
@endsection