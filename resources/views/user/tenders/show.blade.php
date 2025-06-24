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
                <h3 class="mb-0 fw-semibold"><i class='bx bxs-file me-2'></i>Tender #{{ $tender['id'] ?? 'N/A' }}</h3>
                <span class="badge bg-light text-primary rounded-pill shadow-sm">{{ $tender['state'] ?? 'N/A' }}</span>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs mb-4" id="tenderTabs" role="tablist">
                    <li class="nav-item"><button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-tab-pane" type="button">Basic Information</button></li>
                    <li class="nav-item"><button class="nav-link" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates-tab-pane" type="button">Certificates</button></li>
                    <li class="nav-item"><button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial-tab-pane" type="button">Financial Details</button></li>
                    <li class="nav-item"><button class="nav-link" id="experience-tab" data-bs-toggle="tab" data-bs-target="#experience-tab-pane" type="button">Work Experience</button></li>
                </ul>

                <div class="tab-content" id="tenderTabsContent">
                    <!-- Basic Info Tab -->
                    <div class="tab-pane fade show active" id="basic-tab-pane" role="tabpanel">
                        @if (!empty($tender))
                            <div class="row g-3">
                                @php $delay = 0; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-detail me-1 text-primary'></i>Tender Name</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ $tender['tender_name'] ?? 'N/A' }}</p>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-tag me-1 text-primary'></i>Keywords</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ !empty($tender['keywords']) && is_array($tender['keywords']) ? implode(', ', $tender['keywords']) : 'None' }}</p>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-calendar me-1 text-primary'></i>Date</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ !empty($tender['date']) ? \Carbon\Carbon::parse($tender['date'])->format('d M, Y') : 'N/A' }}</p>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-hash me-1 text-primary'></i>TenderWiz ID</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ $tender['tenderwiz_id'] ?? 'N/A' }}</p>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-id-card me-1 text-primary'></i>Tender Reference ID</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ $tender['tender_reference_id'] ?? 'N/A' }}</p>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-bookmark me-1 text-primary'></i>Tag</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ $tender['tag'] ?? 'N/A' }}</p>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-category me-1 text-primary'></i>Tender Type</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ $tender['tender_type'] ?? 'N/A' }}</p>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-map me-1 text-primary'></i>State</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ $tender['state'] ?? 'N/A' }}</p>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-user me-1 text-primary'></i>Tender Inviting Authority</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ $tender['tender_inviting_authority'] ?? 'N/A' }}</p>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-building me-1 text-primary'></i>Department</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ $tender['department'] ?? 'N/A' }}</p>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-globe me-1 text-primary'></i>Website 1</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ $tender['websites'][0] ?? 'None' }}</p>
                                </div>
                                @php $delay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-globe me-1 text-primary'></i>Website 2</label>
                                    <p class="text-muted bg-light p-2 rounded">{{ $tender['websites'][1] ?? 'None' }}</p>
                                </div>
                                <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay += 0.1 }}s;">
                                    <h6 class="section-header text-dark mt-4"><i class='bx bx-calendar-event me-2'></i>Important Events and Dates</h6>
                                    <div class="event-section animate__animated animate__zoomIn">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="bg-gradient-primary text-white">
                                                    <tr>
                                                        <th>Event</th>
                                                        <th>Mode</th>
                                                        <th>Date</th>
                                                        <th>Time</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $rowDelay = 0; @endphp
                                                    <tr class="animate__animated animate__fadeIn" style="animation-delay: {{ $rowDelay }}s;">
                                                        <td>Pre-bid Meeting</td>
                                                        <td>{{ $tender['pre_bid_meeting']['mode'] ?? 'N/A' }}</td>
                                                        <td>{{ !empty($tender['pre_bid_meeting']['date']) ? \Carbon\Carbon::parse($tender['pre_bid_meeting']['date'])->format('d M, Y') : 'N/A' }}</td>
                                                        <td>{{ $tender['pre_bid_meeting']['time'] ?? 'N/A' }}</td>
                                                        <td>{{ $tender['pre_bid_meeting']['status'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    @php $rowDelay += 0.1; @endphp
                                                    <tr class="animate__animated animate__fadeIn" style="animation-delay: {{ $rowDelay }}s;">
                                                        <td>Deadline for Submission</td>
                                                        <td>{{ $tender['submission_deadline']['mode'] ?? 'N/A' }}</td>
                                                        <td>{{ !empty($tender['submission_deadline']['date']) ? \Carbon\Carbon::parse($tender['submission_deadline']['date'])->format('d M, Y') : 'N/A' }}</td>
                                                        <td>{{ $tender['submission_deadline']['time'] ?? 'N/A' }}</td>
                                                        <td>{{ $tender['submission_deadline']['status'] ?? 'N/A' }}</td>
                                                    </tr>
                                                    @php $rowDelay += 0.1; @endphp
                                                    <tr class="animate__animated animate__fadeIn" style="animation-delay: {{ $rowDelay }}s;">
                                                        <td>Opening of Technical Bid</td>
                                                        <td>{{ $tender['technical_bid_opening']['mode'] ?? 'N/A' }}</td>
                                                        <td>{{ !empty($tender['technical_bid_opening']['date']) ? \Carbon\Carbon::parse($tender['technical_bid_opening']['date'])->format('d M, Y') : 'N/A' }}</td>
                                                        <td>{{ $tender['technical_bid_opening']['time'] ?? 'N/A' }}</td>
                                                        <td>{{ $tender['technical_bid_opening']['status'] ?? 'N/A' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            @php $delay += 0.1; @endphp
                                            <div class="col-md-12 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay }}s;">
                                                <label class="form-label fw-medium text-dark"><i class='bx bx-location-plus me-1 text-primary'></i>Pre-Bid Venue</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['pre_bid_venue'] ?? 'None' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: {{ $delay += 0.1 }}s;">
                                    <h6 class="section-header text-dark mt-4"><i class='bx bx-money me-2'></i>Value/Earnest Money Deposit (EMD)</h6>
                                    <div class="event-section animate__animated animate__zoomIn">
                                        <div class="row g-3">
                                            @php $emdDelay = 0; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">Tender Value (INR)</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['tender_value'] ?? 'None' }}</p>
                                            </div>
                                            @php $emdDelay += 0.1; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">EMD Value (INR)</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['emd_value'] ?? 'None' }}</p>
                                            </div>
                                            @php $emdDelay += 0.1; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">EMD Payment Mode</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['emd_payment_mode'] ?? 'None' }}</p>
                                            </div>
                                            @php $emdDelay += 0.1; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">Open Tender List</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['open_tender_list'] ?? 'None' }}</p>
                                            </div>
                                            @php $emdDelay += 0.1; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">Tender Type (Category)</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['tender_type_category'] ?? 'None' }}</p>
                                            </div>
                                            @php $emdDelay += 0.1; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">Company Registration Type</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['company_registration_type'] ?? 'None' }}</p>
                                            </div>
                                            @php $emdDelay += 0.1; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">Company Registered Year</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['company_registered_year'] ?? 'None' }}</p>
                                            </div>
                                            @php $emdDelay += 0.1; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">Company Sector Type</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['company_sector_type'] ?? 'None' }}</p>
                                            </div>
                                            @php $emdDelay += 0.1; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">Nature of Business</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['nature_of_business'] ?? 'None' }}</p>
                                            </div>
                                            @php $emdDelay += 0.1; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">Business Specialization</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['business_specialization'] ?? 'None' }}</p>
                                            </div>
                                            @php $emdDelay += 0.1; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">Procurement Category</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['procurement_category'] ?? 'None' }}</p>
                                            </div>
                                            @php $emdDelay += 0.1; @endphp
                                            <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: {{ $emdDelay }}s;">
                                                <label class="form-label fw-medium text-dark">Tender Nature</label>
                                                <p class="text-muted bg-light p-2 rounded">{{ $tender['tender_nature'] ?? 'None' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center">No tender data available.</p>
                        @endif
                    </div>

                    <!-- Certificates Tab -->
                    <div class="tab-pane fade" id="certificates-tab-pane" role="tabpanel">
                        @if (!empty($tender['certificates']))
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
                                        @php
                                            $certList = config('constants.CERTIFICATE_LISTS.' . $key, []);
                                        @endphp
                                        @if (!empty($certList) && !empty($tender['certificates'][$key]))
                                            @php $certDelay = 0; @endphp
                                            @foreach ($certList as $cert)
                                                <div class="row g-3 mb-3 certificate-field animate__animated animate__fadeInUp" style="animation-delay: {{ $certDelay }}s;">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium text-dark">{{ $cert }}</label>
                                                        <p class="text-muted bg-light p-2 rounded">{{ $tender['certificates'][$key][$cert]['status'] ?? 'No' }}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-medium text-dark">Valid Up To</label>
                                                        <p class="text-muted bg-light p-2 rounded">{{ !empty($tender['certificates'][$key][$cert]['valid_up_to']) ? \Carbon\Carbon::parse($tender['certificates'][$key][$cert]['valid_up_to'])->format('d M, Y') : 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                @php $certDelay += 0.1; @endphp
                                            @endforeach
                                        @else
                                            <p class="text-muted">No certificates available for this category.</p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted text-center">No certificate data available.</p>
                        @endif
                    </div>

                    <!-- Financial Details Tab -->
                    <div class="tab-pane fade" id="financial-tab-pane" role="tabpanel">
                        @if (!empty($tender['financial_statements']) || !empty($tender['financials']))
                            <ul class="nav nav-tabs mb-3" id="financialTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="annual-statement-tab" data-bs-toggle="tab" href="#annual-statement" role="tab">Annual Financial Statement</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="itr-tab" data-bs-toggle="tab" href="#itr" role="tab">ITR</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="turnover-tab" data-bs-toggle="tab" href="#turnover" role="tab">Annual Turnover</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="networth-tab" data-bs-toggle="tab" href="#networth" role="tab">Net Worth</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="financialTabContent">
                                <!-- Annual Financial Statement -->
                                <div class="tab-pane fade show active" id="annual-statement" role="tabpanel">
                                    @if (!empty($tender['financial_statements']))
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="bg-gradient-primary text-white">
                                                    <tr>
                                                        <th>From Year</th>
                                                        <th>To Year</th>
                                                        <th>Total Revenue</th>
                                                        <th>Total Expenses</th>
                                                        <th>Profit/Loss</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $finDelay = 0; @endphp
                                                    @foreach ($tender['financial_statements'] as $yearRange => $statement)
                                                        @php
                                                            $years = explode('-', $yearRange);
                                                            $fromYear = is_numeric($years[0] ?? '') ? (int)$years[0] : 'N/A';
                                                            $toYear = is_numeric($years[1] ?? '') ? (int)$years[1] : 'N/A';
                                                            $statement = array_merge(['revenue' => false, 'expenses' => false, 'profit_loss' => false], $statement ?? []);
                                                        @endphp
                                                        <tr class="animate__animated animate__fadeIn" style="animation-delay: {{ $finDelay }}s;">
                                                            <td>{{ $fromYear }}</td>
                                                            <td>{{ $toYear }}</td>
                                                            <td>{{ $statement['revenue'] ? 'Yes' : 'No' }}</td>
                                                            <td>{{ $statement['expenses'] ? 'Yes' : 'No' }}</td>
                                                            <td>{{ $statement['profit_loss'] ? 'Yes' : 'No' }}</td>
                                                        </tr>
                                                        @php $finDelay += 0.1; @endphp
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No financial statements available.</p>
                                    @endif
                                </div>
                                <!-- ITR -->
                                <div class="tab-pane fade" id="itr" role="tabpanel">
                                    @if (!empty($tender['financials']['itr']))
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="bg-gradient-primary text-white">
                                                    <tr>
                                                        <th>From Year</th>
                                                        <th>To Year</th>
                                                        <th>ITR Filed</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $itrDelay = 0; @endphp
                                                    @foreach ($tender['financials']['itr'] as $yearRange => $value)
                                                        @php
                                                            $years = explode('-', $yearRange);
                                                            $fromYear = is_numeric($years[0] ?? '') ? (int)$years[0] : 'N/A';
                                                            $toYear = is_numeric($years[1] ?? '') ? (int)$years[1] : 'N/A';
                                                        @endphp
                                                        <tr class="animate__animated animate__fadeIn" style="animation-delay: {{ $itrDelay }}s;">
                                                            <td>{{ $fromYear }}</td>
                                                            <td>{{ $toYear }}</td>
                                                            <td>{{ $value ? 'Yes' : 'No' }}</td>
                                                        </tr>
                                                        @php $itrDelay += 0.1; @endphp
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No ITR data available.</p>
                                    @endif
                                </div>
                                <!-- Annual Turnover -->
                                <div class="tab-pane fade" id="turnover" role="tabpanel">
                                    @if (!empty($tender['financials']['turnover']))
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="bg-gradient-primary text-white">
                                                    <tr>
                                                        <th>From Year</th>
                                                        <th>To Year</th>
                                                        <th>Annual Turnover (In ₹)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $turnDelay = 0; @endphp
                                                    @foreach ($tender['financials']['turnover'] as $yearRange => $value)
                                                        @php
                                                            $years = explode('-', $yearRange);
                                                            $fromYear = is_numeric($years[0] ?? '') ? (int)$years[0] : 'N/A';
                                                            $toYear = is_numeric($years[1] ?? '') ? (int)$years[1] : 'N/A';
                                                        @endphp
                                                        <tr class="animate__animated animate__fadeIn" style="animation-delay: {{ $turnDelay }}s;">
                                                            <td>{{ $fromYear }}</td>
                                                            <td>{{ $toYear }}</td>
                                                            <td>{{ $value ? '₹' . number_format($value, 2) : 'N/A' }}</td>
                                                        </tr>
                                                        @php $turnDelay += 0.1; @endphp
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No turnover data available.</p>
                                    @endif
                                </div>
                                <!-- Net Worth -->
                                <div class="tab-pane fade" id="networth" role="tabpanel">
                                    @if (!empty($tender['financials']['net_worth']))
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover">
                                                <thead class="bg-gradient-primary text-white">
                                                    <tr>
                                                        <th>From Year</th>
                                                        <th>To Year</th>
                                                        <th>Net Worth Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $netDelay = 0; @endphp
                                                    @foreach ($tender['financials']['net_worth'] as $yearRange => $value)
                                                        @php
                                                            $years = explode('-', $yearRange);
                                                            $fromYear = is_numeric($years[0] ?? '') ? (int)$years[0] : 'N/A';
                                                            $toYear = is_numeric($years[1] ?? '') ? (int)$years[1] : 'N/A';
                                                        @endphp
                                                        <tr class="animate__animated animate__fadeIn" style="animation-delay: {{ $netDelay }}s;">
                                                            <td>{{ $fromYear }}</td>
                                                            <td>{{ $toYear }}</td>
                                                            <td>{{ $value ? ucfirst($value) : 'N/A' }}</td>
                                                        </tr>
                                                        @php $netDelay += 0.1; @endphp
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <p class="text-muted text-center">No net worth data available.</p>
                                    @endif
                                </div>
                            </div>
                        @else
                            <p class="text-muted text-center">No financial data available.</p>
                        @endif
                    </div>

                    <!-- Work Experience Tab -->
                    <div class="tab-pane fade" id="experience-tab-pane" role="tabpanel">
                        @if (!empty($tender['work_experience']))
                            <h5 class="text-dark"><i class='bx bx-briefcase me-2'></i>Work Experience</h5>
                            <div class="row g-3">
                                @php $expDelay = 0; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $expDelay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-briefcase-alt me-1 text-primary'></i>Working Experience Areas</label>
                                    <p class="text-muted bg-light p-2 rounded">
                                        {{ !empty($tender['work_experience']['area']) && is_array($tender['work_experience']['area']) 
                                            ? implode(', ', $tender['work_experience']['area']) 
                                            : 'N/A' }}
                                    </p>
                                </div>
                                @php $expDelay += 0.1; @endphp
                                <div class="col-md-6 animate__animated animate__fadeInUp" style="animation-delay: {{ $expDelay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-time me-1 text-primary'></i>Experience (Years)</label>
                                    <p class="text-muted bg-light p-2 rounded">
                                        {{ !empty($tender['work_experience']['years']) ? $tender['work_experience']['years'] : 'N/A' }}
                                    </p>
                                </div>
                                @php $expDelay += 0.1; @endphp
                                <div class="col-md-12 animate__animated animate__fadeInUp" style="animation-delay: {{ $expDelay }}s;">
                                    <label class="form-label fw-medium text-dark"><i class='bx bx-tag me-1 text-primary'></i>Work Experience Keywords</label>
                                    <p class="text-muted bg-light p-2 rounded">
                                        {{ !empty($tender['work_experience']['work_exp_keywords']) && is_array($tender['work_experience']['work_exp_keywords']) 
                                            ? implode(', ', $tender['work_experience']['work_exp_keywords']) 
                                            : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <!-- <div class="mt-4">
                                <h6 class="text-dark">Experience Details</h6>
                                @if (!empty($tender['work_experience']['details']) && is_array($tender['work_experience']['details']))
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="bg-gradient-primary text-white">
                                                <tr>
                                                    <th>Project Name</th>
                                                    <th>Client</th>
                                                    <th>Year</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tender['work_experience']['details'] as $detail)
                                                    <tr>
                                                        <td>{{ $detail['project'] ?? 'N/A' }}</td>
                                                        <td>{{ $detail['client'] ?? 'N/A' }}</td>
                                                        <td>{{ $detail['year'] ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">No work experience details available.</p>
                                @endif
                            </div> -->
                        @else
                            <p class="text-muted text-center">No work experience data available.</p>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('user.tenders.index') }}" class="btn btn-outline-secondary shadow-sm"><i class='bx bx-arrow-back me-1'></i>Back to List</a>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
/* Custom styles for better button alignment and responsiveness */
.btn-outline-secondary {
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
}
.btn-outline-secondary:hover {
    background-color: #f8f9fa;
    transform: translateX(-3px);
}
@media (max-width: 576px) {
    .d-flex {
        flex-direction: column;
        gap: 10px;
    }
    .btn-outline-secondary {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            // Debugging: Check if jQuery is loaded
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded!');
            } else {
                console.log('jQuery is loaded:', jQuery.fn.jquery);
            }

            // Debugging: Check if Bootstrap is loaded
            if (typeof bootstrap === 'undefined') {
                console.error('Bootstrap JavaScript is not loaded!');
            } else {
                console.log('Bootstrap is loaded.');
            }

            // Initialize tabs
            $('#tenderTabs button[data-bs-toggle="tab"], #certificatesTabs a[data-bs-toggle="tab"], #financialTabs a[data-bs-toggle="tab"]').each(function () {
                const tabTrigger = new bootstrap.Tab(this);
                $(this).on('click', function (e) {
                    e.preventDefault();
                    console.log('Tab clicked:', $(this).attr('id'));
                    tabTrigger.show();
                });
            });

            // Tab animation
            $('#tenderTabs button, #certificatesTabs a, #financialTabs a').on('shown.bs.tab', function (e) {
                const target = $(this).attr('href') || $(this).data('bs-target');
                const pane = $(target);
                console.log('Tab shown:', pane.attr('id'));
                pane.addClass('animate__animated animate__fadeIn').css('animation-duration', '0.5s');
                setTimeout(() => pane.removeClass('animate__animated animate__fadeIn'), 500);
            });

            // Nav link animation
            $('.nav-tabs .nav-link').on('click', function () {
                $(this).addClass('animate__animated animate__bounce');
                setTimeout(() => $(this).removeClass('animate__animated animate__bounce'), 500);
            });
        });
    </script>
@endsection