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
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-detail me-1 text-primary'></i>Name of the Company/Firm/Organization
 <span class="text-danger">*</span></label>
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
                              <!--   @php $delay += 0.1; @endphp
                                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-hash me-1 text-primary'></i>Company ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="company_id" name="company_id" value="{{ old('company_id', $company->company_id ?? '') }}" required>
                                    @error('company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-id-card me-1 text-primary'></i>Reference ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="reference_id" name="reference_id" value="{{ old('reference_id', $company->reference_id ?? '') }}" required>
                                    @error('reference_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> -->
                               <!--  @php $delay += 0.1; @endphp
                                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-category me-1 text-primary'></i>Company Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="company_type" name="company_type" required>
                                        @foreach (config('constants.COMPANY_TYPES', ["Online (e-tender)", "Open Tender", "Short Tender", "Offline"]) as $type)
                                            <option value="{{ $type }}" {{ old('company_type', $company->company_type ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> -->
                                <!-- @php $delay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-map me-1 text-primary'></i>State <span class="text-danger">*</span></label>
                                    <select class="form-select" id="state" name="state" required>
                                        <option value="">-- Select State/UT --</option>
                                        @foreach (config('constants.INDIAN_STATES_AND_UTS', []) as $state)
                                            <option value="{{ $state }}" {{ old('state', $company->state ?? '') === $state ? 'selected' : '' }}>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-building me-1 text-primary'></i>Department <span class="text-danger">*</span></label>
                                    <select class="form-select" id="department" name="department" required>
                                        @foreach (config('constants.DEPARTMENTS', ["State Health Society, Bihar (SHSB)", "State Ayush Society, Bihar (SASB)", "Health CE"]) as $dept)
                                            <option value="{{ $dept }}" {{ old('department', $company->department ?? '') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                        @endforeach
                                    </select>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> -->
                                <!-- @php $delay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-globe me-1 text-primary'></i>Website 1</label>
                                    <div id="website-container-1">
                                        <div class="website-field">
                                            <input type="url" class="form-control" name="websites[]" value="{{ old('websites.0', $company->websites[0] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-globe me-1 text-primary'></i>Website 2</label>
                                    <div id="website-container-2">
                                        <div class="website-field">
                                            <input type="url" class="form-control" name="websites[]" value="{{ old('websites.1', $company->websites[1] ?? '') }}">
                                        </div>
                                    </div>
                                </div> -->
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
                                            <label class="form-label fw-medium text-dark"> Sector Type</label>
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
                                       <!--  @php $detailDelay += 0.1; @endphp
                                        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $detailDelay }}s;">
                                            <label class="form-label fw-medium text-dark">Procurement Category</label>
                                            <select class="form-select select2-checkbox" id="procurement_category" name="procurement_category[]" multiple>
                                                <option value="">-- Select Category --</option>
                                                @foreach (config('constants.PROCUREMENT_CATEGORIES', []) as $category)
                                                    <option value="{{ $category }}" {{ in_array($category, old('procurement_category', array_filter(explode(',', $company->procurement_category ?? '')))) ? 'selected' : '' }}>{{ $category }}</option>
                                                @endforeach
                                            </select>
                                            @error('procurement_category')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div> -->
                                       <!--  @php $detailDelay += 0.1; @endphp
                                        <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $detailDelay }}s;">
                                            <label class="form-label fw-medium text-dark">Tender Nature</label>
                                            <select class="form-select" id="tender_nature" name="tender_nature">
                                                @foreach (config('constants.TENDER_NATURE_OPTIONS', []) as $nature)
                                                    <option value="{{ $nature }}" {{ old('tender_nature', $company->tender_nature ?? '') === $nature ? 'selected' : '' }}>{{ $nature }}</option>
                                                @endforeach
                                            </select>
                                            @error('tender_nature')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Certificates Tab -->
                        <div class="tab-pane fade" id="certificates-tab-pane" role="tabpanel">
                            <ul class="nav nav-tabs mb-3" id="certificatesTabs" role="tablist">
                                @foreach (config('constants.CERTIFICATE_CATEGORIES', []) as $key => $name)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $key === 'incorporation' ? 'active' : '' }}" id="{{ $key }}-tab" data-bs-toggle="tab" href="#{{ $key }}" role="tab">{{ $name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content" id="certificatesTabContent">
                                @foreach (config('constants.CERTIFICATE_CATEGORIES', []) as $key => $name)
                                    <div class="tab-pane {{ $key === 'incorporation' ? 'show active' : '' }}" id="{{ $key }}" role="tabpanel">
                                        <h6 class="mt-4 text-dark"><i class='bx bx-certification me-2'></i>Certificates</h6>
                                        <div id="{{ $key }}-certificates">
                                            @php $certDelay = 0; @endphp
                                            @foreach (config('constants.CERTIFICATE_LISTS')[$key] as $cert)
                                                <div class="row g-3 certificate-field mb-3 align-items-end animate__animated animate__fadeInUp" style="animation-delay: {{ $certDelay }}s;">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium text-dark">{{ $cert }}</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="certificates[{{ $key }}][{{ $cert }}][status]" value="Yes" {{ (old("certificates.{$key}.{$cert}.status", $company->certificates[$key][$cert]['status'] ?? 'No')) === 'Yes' ? 'checked' : '' }}>
                                                            <label class="form-check-label">Yes</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="certificates[{{ $key }}][{{ $cert }}][status]" value="No" {{ (old("certificates.{$key}.{$cert}.status", $company->certificates[$key][$cert]['status'] ?? 'No')) === 'No' ? 'checked' : '' }}>
                                                            <label class="form-check-label">No</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="certificates[{{ $key }}][{{ $cert }}][status]" value="N/A" {{ (old("certificates.{$key}.{$cert}.status", $company->certificates[$key][$cert]['status'] ?? 'No')) === 'N/A' ? 'checked' : '' }}>
                                                            <label class="form-check-label">N/A</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="date" class="form-control" name="certificates[{{ $key }}][{{ $cert }}][valid_up_to]" value="{{ old("certificates.{$key}.{$cert}.valid_up_to", $company->certificates[$key][$cert]['valid_up_to'] ?? '') }}" placeholder="Valid Up to">
                                                    </div>
                                                    <div class="col-md-4"></div>
                                                </div>
                                                @php $certDelay += 0.1; @endphp
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addMoreCertificate('{{ $key }}')">Add More</button>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Financial Tab -->
                        <!-- <div class="tab-pane fade" id="financial-tab-pane" role="tabpanel">
                            <ul class="nav nav-tabs mb-3" id="financialTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="annual-statement-tab" data-bs-toggle="tab" href="#annual-statement" role="tab">Annual Financial Statement</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="turnover-tab" data-bs-toggle="tab" href="#turnover" role="tab">Annual Turnover & ITR</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="financialTabContent">
                                <div class="tab-pane show active" id="annual-statement" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="bg-gradient-primary text-white">
                                                <tr>
                                                    <th>Financial Years</th>
                                                    <th>Total Revenue</th>
                                                    <th>Total Expenses</th>
                                                    <th>Profit/Loss</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $finDelay = 0; @endphp
                                                @foreach (config('constants.FINANCIAL_YEARS', []) as $year)
                                                    @php
                                                        $defaultStatement = ['revenue' => false, 'expenses' => false, 'profit_loss' => false];
                                                        $statementData = $company->financial_statements[$year] ?? [];
                                                        $statement = old("financial_statements.{$year}", array_merge($defaultStatement, is_array($statementData) ? $statementData : []));
                                                    @endphp
                                                    <tr class="animate__animated animate__fadeIn" style="animation-delay: {{ $finDelay }}s;">
                                                        <td>{{ $year }}</td>
                                                        <td><input type="checkbox" name="financial_statements[{{ $year }}][revenue]" {{ isset($statement['revenue']) && $statement['revenue'] ? 'checked' : '' }}></td>
                                                        <td><input type="checkbox" name="financial_statements[{{ $year }}][expenses]" {{ isset($statement['expenses']) && $statement['expenses'] ? 'checked' : '' }}></td>
                                                        <td><input type="checkbox" name="financial_statements[{{ $year }}][profit_loss]" {{ isset($statement['profit_loss']) && $statement['profit_loss'] ? 'checked' : '' }}></td>
                                                    </tr>
                                                    @php $finDelay += 0.1; @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="turnover" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="bg-gradient-primary text-white">
                                                <tr>
                                                    <th>Financial Years</th>
                                                    <th>ITR (Last 5 FY)</th>
                                                    <th>Annual Turnover (In ₹)</th>
                                                    <th>Annual Net Worth</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $turnDelay = 0; @endphp
                                                @foreach (config('constants.FINANCIAL_YEARS', []) as $year)
                                                    @php
                                                        $defaultFinancial = ['itr' => false, 'turnover' => '', 'net_worth' => ''];
                                                        $financialData = $company->financials[$year] ?? [];
                                                        $financial = old("financials.{$year}", array_merge($defaultFinancial, is_array($financialData) ? $financialData : []));
                                                    @endphp
                                                    <tr class="animate__animated animate__fadeIn" style="animation-delay: {{ $turnDelay }}s;">
                                                        <td>{{ $year }}</td>
                                                        <td><input type="checkbox" name="financials[{{ $year }}][itr]" {{ isset($financial['itr']) && $financial['itr'] ? 'checked' : '' }}></td>
                                                        <td><input type="text" class="form-control" name="financials[{{ $year }}][turnover]" value="{{ $financial['turnover'] ?? '' }}"></td>
                                                        <td><input type="text" class="form-control" name="financials[{{ $year }}][net_worth]" value="{{ $financial['net_worth'] ?? '' }}"></td>
                                                    </tr>
                                                    @php $turnDelay += 0.1; @endphp
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> -->


                       
<!-- Financial Tab -->
<div class="tab-pane fade" id="financial-tab-pane" role="tabpanel">
    <ul class="nav nav-tabs mb-3" id="financialTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="annual-statement-tab" data-bs-toggle="tab" href="#annual-statement" role="tab">Annual Financial Statement</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="turnover-tab" data-bs-toggle="tab" href="#turnover" role="tab">Annual Turnover & ITR</a>
        </li>
    </ul>
    <div class="tab-content" id="financialTabContent">
        <!-- Annual Financial Statement -->
        <div class="tab-pane show active" id="annual-statement" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="financial-statement-table">
                    <thead class="bg-gradient-primary text-white">
                        <tr>
                            <th>Financial Years</th>
                            <th>Total Revenue</th>
                            <th>Total Expenses</th>
                            <th>Profit/Loss</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $currentYear = (date('Y')-1) . '-' . date('Y'); // e.g., 2024-2025
                            // Merge and deduplicate years
                            $allYears = array_unique(array_merge(
                                array_keys($company['financial_statements'] ?? []),
                                array_keys($company['financials'] ?? [])
                            ));
                            if (!in_array($currentYear, $allYears)) {
                                $allYears[] = $currentYear;
                            }
                            rsort($allYears); // Sort descending
                            $finDelay = 0;

                           
                        @endphp
                        @foreach ($allYears as $year)
                            @php
                                $defaultStatement = ['revenue' => false, 'expenses' => false, 'profit_loss' => false];
                                $statementData = $company['financial_statements'][$year] ?? [];
                                $statement = old("financial_statements.{$year}", array_merge($defaultStatement, is_array($statementData) ? $statementData : []));
                            @endphp
                            <tr data-year="{{ explode('-', $year)[0] }}" class="animate__animated animate__fadeIn" style="animation-delay: {{ $finDelay }}s;">
                                <td class="year">{{ $year }}</td>
                                <td><input type="checkbox" name="financial_statements[{{ $year }}][revenue]" {{ isset($statement['revenue']) && $statement['revenue'] ? 'checked' : '' }}></td>
                                <td><input type="checkbox" name="financial_statements[{{ $year }}][expenses]" {{ isset($statement['expenses']) && $statement['expenses'] ? 'checked' : '' }}></td>
                                <td><input type="checkbox" name="financial_statements[{{ $year }}][profit_loss]" {{ isset($statement['profit_loss']) && $statement['profit_loss'] ? 'checked' : '' }}></td>
                            </tr>
                            @php $finDelay += 0.1; @endphp
                        @endforeach
                        <!-- Template Row (Hidden) -->
                        <tr id="statement-template" style="display: none;" data-year="YEAR">
                            <td class="year"></td>
                            <td><input type="checkbox" name="financial_statements[YEAR][revenue]"></td>
                            <td><input type="checkbox" name="financial_statements[YEAR][expenses]"></td>
                            <td><input type="checkbox" name="financial_statements[YEAR][profit_loss]"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Annual Turnover & ITR -->
        <div class="tab-pane" id="turnover" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="turnover-table">
                    <thead class="bg-gradient-primary text-white">
                        <tr>
                            <th>Financial Years</th>
                            <th>ITR (Last 5 FY)</th>
                            <th>Annual Turnover (In ₹)</th>
                            <th>Annual Net Worth</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $turnDelay = 0; @endphp
                        @foreach ($allYears as $year)
                            @php
                                $defaultFinancial = ['itr' => false, 'turnover' => '', 'net_worth' => ''];
                                $financialData = $company['financials'][$year] ?? [];
                                $financial = old("financials.{$year}", array_merge($defaultFinancial, is_array($financialData) ? $financialData : []));
                            @endphp
                            <tr data-year="{{ explode('-', $year)[0] }}" class="animate__animated animate__fadeIn" style="animation-delay: {{ $turnDelay }}s;">
                                <td class="year">{{ $year }}</td>
                                <td><input type="checkbox" name="financials[{{ $year }}][itr]" {{ isset($financial['itr']) && $financial['itr'] ? 'checked' : '' }}></td>
                                <td><input type="text" class="form-control" name="financials[{{ $year }}][turnover]" value="{{ $financial['turnover'] ?? '' }}"></td>
                                <td><input type="text" class="form-control" name="financials[{{ $year }}][net_worth]" value="{{ $financial['net_worth'] ?? '' }}"></td>
                            </tr>
                            @php $turnDelay += 0.1; @endphp
                        @endforeach
                        <!-- Template Row (Hidden) -->
                        <tr id="turnover-template" style="display: none;" data-year="YEAR">
                            <td class="year"></td>
                            <td><input type="checkbox" name="financials[YEAR][itr]"></td>
                            <td><input type="text" class="form-control" name="financials[YEAR][turnover]"></td>
                            <td><input type="text" class="form-control" name="financials[YEAR][net_worth]"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Single Add More Button -->
    <div class="mt-3">
        <button type="button" class="btn btn-primary" id="add-financial-row">Add More</button>
    </div>
</div>

                        <!-- Work Experience Tab -->
                        <div class="tab-pane fade" id="experience-tab-pane" role="tabpanel">
                            <h5 class="text-dark"><i class='bx bx-briefcase me-2'></i>Work Experience</h5>
                           <!--  <div class="row g-3">
                                @php $expDelay = 0; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $expDelay }}s;">
                                    <label class="form-label fw-medium text-dark">Working Experience Areas</label>
                                    <select class="form-select" id="working_experience_area" name="work_experience[area]">
                                        @foreach (config('constants.WORK_EXPERIENCE_AREAS', []) as $area)
                                            <option value="{{ $area }}" {{ old('work_experience.area', $company->work_experience['area'] ?? '') === $area ? 'selected' : '' }}>{{ $area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @php $expDelay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $expDelay }}s;">
                                    <label class="form-label fw-medium text-dark">Experience (Years)</label>
                                    <select class="form-select" id="experience_years" name="work_experience[years]">
                                        @foreach (config('constants.EXPERIENCE_YEARS', []) as $y)
                                            <option value="{{ $y }}" {{ old('work_experience.years', $company->work_experience['years'] ?? '') === $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> -->


    <div class="row g-3">
    @php $expDelay = 0; @endphp
   <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $expDelay ?? 0 }}s;">
    <label class="form-label fw-medium text-dark" for="working_experience_area">Working Experience Areas</label>
    <select class="form-select select2-checkbox" id="working_experience_area" name="work_experience[area][]" multiple>
        @foreach (config('constants.WORK_EXPERIENCE_AREAS', []) as $area)
            <option value="{{ $area }}"
                {{ in_array($area, old('work_experience.area', (isset($company) && !is_null($company->work_experience) && is_array($company->work_experience) && array_key_exists('area', $company->work_experience) ? $company->work_experience['area'] : []))) ? 'selected' : '' }}>
                {{ $area }}
            </option>
        @endforeach
    </select>
</div>



    @php $expDelay += 0.1; @endphp
    <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $expDelay }}s;">
        <label class="form-label fw-medium text-dark" for="experience_years">Experience (Years)</label>
        <input type="number" class="form-control" id="experience_years" name="work_experience[years]"
            value="{{ old('work_experience.years', $company->work_experience['years'] ?? '') }}"
            min="0" step="1" placeholder="Enter years of experience">
    </div>
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
            $('#companyTabs button[data-bs-toggle="tab"]').each(function () {
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
                    company_description: "required",
                    state: "required",
                    company_id: "required",
                    reference_id: "required",
                    company_type: "required",
                    department: "required"
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
            $('#companyTabs button, #certificatesTabs a, #financialTabs a').on('shown.bs.tab', function (e) {
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

        function addMoreField(type) {
            const container = document.getElementById(`${type}-container`);
            const field = document.createElement('div');
            field.className = `${type}-field mb-2`;
            field.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control" name="${type}s[]">
                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remove</button>
                </div>
            `;
            container.appendChild(field);
        }

        function removeField(element) {
            element.closest('.keyword-field, .website-field, .work-experience-field, .certificate-field').remove();
        }

        function addMoreCertificate(category) {
            const container = document.getElementById(`${category}-certificates`);
            const field = document.createElement('div');
            field.className = 'row g-3 certificate-field mb-3 align-items-end animate__animated animate__fadeInUp';
            field.style.animationDelay = '0s';
            field.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-control" name="certificates[${category}][new_${Date.now()}][name]" placeholder="Certificate Name">
                </div>
                <div class="col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="certificates[${category}][new_${Date.now()}][status]" value="Yes">
                        <label class="form-check-label">Yes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="certificates[${category}][new_${Date.now()}][status]" value="No" checked>
                        <label class="form-check-label">No</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="certificates[${category}][new_${Date.now()}][status]" value="N/A">
                        <label class="form-check-label">N/A</label>
                    </div>
                </div>
                <div class="col-md-4">
                    <input type="date" class="form-control" name="certificates[${category}][new_${Date.now()}][valid_up_to]" placeholder="Valid Up to">
                </div>
            `;
            container.appendChild(field);
        }

       



        $(document).ready(function () {
    // Initialize lastYear and existingYears
   let lastYear = new Date().getFullYear() -1;
    const existingYears = [];
    $('#financial-statement-table tbody tr:not(#statement-template), #turnover-table tbody tr:not(#turnover-template)').each(function () {
        const year = parseInt($(this).data('year'));
        if (!isNaN(year)) existingYears.push(year);
    });
    console.log('Initial existing years:', existingYears);
    if (existingYears.length) {
        lastYear = Math.min(...existingYears);
    }
    console.log('Initial lastYear:', lastYear);

    // Add rows to both tables
    $('#add-financial-row').click(function () {
        if (lastYear <= 2015) {
            console.log('Year limit reached: 2015-2016');
            return;
        }

        const newYear = lastYear - 1;
        const yearStr = `${newYear}-${lastYear}`;
        
        // Check if year already exists
        if (existingYears.includes(newYear)) {
            console.log(`Year ${yearStr} already exists, skipping`);
            return;
        }
        existingYears.push(newYear);
        console.log('Adding year:', yearStr, 'New existing years:', existingYears);

        // Add to Financial Statement table
        const $statementTemplate = $('#statement-template').clone().removeAttr('id').show();
        $statementTemplate.attr('data-year', newYear);
        $statementTemplate.find('.year').text(yearStr);
        $statementTemplate.find('input').each(function () {
            const name = $(this).attr('name').replace('YEAR', yearStr);
            $(this).attr('name', name);
        });
        $('#financial-statement-table tbody').append($statementTemplate);

        // Add to Turnover table
        const $turnoverTemplate = $('#turnover-template').clone().removeAttr('id').show();
        $turnoverTemplate.attr('data-year', newYear);
        $turnoverTemplate.find('.year').text(yearStr);
        $turnoverTemplate.find('input').each(function () {
            const name = $(this).attr('name').replace('YEAR', yearStr);
            $(this).attr('name', name);
        });
        $('#turnover-table tbody').append($turnoverTemplate);

        lastYear = newYear;
        console.log('Updated lastYear:', lastYear);
    });
});
    </script>
@endsection