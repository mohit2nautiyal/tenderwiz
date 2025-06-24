@extends('layouts.app')

@section('content')
<main>
    @isset($flash)
        <div class="alert alert-{{ $flash['type'] }} alert-dismissible fade show" role="alert">
            {{ $flash['message'] }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endisset

    <div class="box-info">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3>{{ isset($tender) ? 'Edit Tender' : 'Add New Tender' }}</h3>
                <form id="tenderForm" action="{{ isset($tender) ? route('admin.tenders.update', $tender['id']) : route('admin.tenders.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf
                    @if (isset($tender))
                        <input type="hidden" name="id" value="{{ $tender['id'] }}">
                    @endif
                    <input type="hidden" name="csrf_token" value="{{ csrf_token() }}">

                    <ul class="nav nav-tabs mb-2" id="tenderTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="basic-tab" data-bs-toggle="tab" data-bs-target="#basic-tab-pane" type="button" role="tab">Basic Information</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="certificates-tab" data-bs-toggle="tab" data-bs-target="#certificates-tab-pane" type="button" role="tab">Certificates</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="financial-tab" data-bs-toggle="tab" data-bs-target="#financial-tab-pane" type="button" role="tab">Financial Details</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="experience-tab" data-bs-toggle="tab" data-bs-target="#experience-tab-pane" type="button" role="tab">Work Experience</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="tenderTabsContent">
                        <!-- Basic Info Tab -->
                        <div class="tab-pane fade show active" id="basic-tab-pane" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Tender Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="tender_description" name="tender_description" rows="3" required>{{ old('tender_description', $tender['tender_description'] ?? '') }}</textarea>
                                    @error('tender_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Keywords</label>
                                    <div id="keyword-container">
                                        @foreach (old('keywords', $tender['keywords'] ?? ['']) as $keyword)
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
                                <div class="col-md-4">
                                    <label class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="date" name="date" value="{{ old('date', $tender['date'] ?? '') }}" required>
                                    @error('date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">TenderWiz ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tenderwiz_id" name="tenderwiz_id" value="{{ old('tenderwiz_id', $tender['tenderwiz_id'] ?? '') }}" required>
                                    @error('tenderwiz_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tender Reference ID <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tender_reference_id" name="tender_reference_id" value="{{ old('tender_reference_id', $tender['tender_reference_id'] ?? '') }}" required>
                                    @error('tender_reference_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tag</label>
                                    <select class="form-select" id="tag" name="tag">
                                        <option value="">-- Select Tag --</option>
                                        @foreach (config('constants.TAGS', []) as $tag)
                                            <option value="{{ $tag }}" {{ old('tag', $tender['tag'] ?? '') === $tag ? 'selected' : '' }}>{{ $tag }}</option>
                                        @endforeach
                                    </select>
                                    @error('tag')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tender Type</label>
                                    <select class="form-select" id="tender_type" name="tender_type">
                                        <option value="">-- Select Type --</option>
                                        @foreach (config('constants.TENDER_TYPES', []) as $type)
                                            <option value="{{ $type }}" {{ old('tender_type', $tender['tender_type'] ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    @error('tender_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">State <span class="text-danger">*</span></label>
                                    <select class="form-select" id="state" name="state" required>
                                        <option value="">-- Select State/UT --</option>
                                        @foreach (config('constants.INDIAN_STATES_AND_UTS', []) as $state)
                                            <option value="{{ $state }}" {{ old('state', $tender['state'] ?? '') === $state ? 'selected' : '' }}>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Tender Inviting Authority <span class="text-danger">*</span></label>
                                    <select class="form-select" id="tender_inviting_authority" name="tender_inviting_authority" required>
                                        <option value="">-- Select Authority --</option>
                                        @foreach (config('constants.TENDER_INVITING_AUTHORITIES', []) as $authority)
                                            <option value="{{ $authority }}" {{ old('tender_inviting_authority', $tender['tender_inviting_authority'] ?? '') === $authority ? 'selected' : '' }}>{{ $authority }}</option>
                                        @endforeach
                                    </select>
                                    @error('tender_inviting_authority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Department <span class="text-danger">*</span></label>
                                    <select class="form-select" id="department" name="department" required>
                                        <option value="">-- Select Department --</option>
                                        @foreach (config('constants.DEPARTMENTS', []) as $dept)
                                            <option value="{{ $dept }}" {{ old('department', $tender['department'] ?? '') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                        @endforeach
                                    </select>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Website 1</label>
                                    <div id="website-container-1">
                                        <div class="website-field">
                                            <input type="url" class="form-control" name="websites[]" value="{{ old('websites.0', $tender['websites'][0] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Website 2</label>
                                    <div id="website-container-2">
                                        <div class="website-field">
                                            <input type="url" class="form-control" name="websites[]" value="{{ old('websites.1', $tender['websites'][1] ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <h6 class="section-header">Important Events and Dates</h6>
                                    <div class="event-section">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Event</th>
                                                        <th>Mode</th>
                                                        <th>Date</th>
                                                        <th>Time</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>Pre-bid Meeting</td>
                                                        <td>
                                                            <select class="form-select" name="pre_bid_meeting[mode]">
                                                                <option value="">-- Select Mode --</option>
                                                                @foreach (config('constants.EVENT_MODES', []) as $mode)
                                                                    <option value="{{ $mode }}" {{ old('pre_bid_meeting.mode', $tender['pre_bid_meeting']['mode'] ?? '') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="date" class="form-control" name="pre_bid_meeting[date]" value="{{ old('pre_bid_meeting.date', $tender['pre_bid_meeting']['date'] ?? '') }}">
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control" name="pre_bid_meeting[time]" value="{{ old('pre_bid_meeting.time', $tender['pre_bid_meeting']['time'] ?? '') }}">
                                                        </td>
                                                        <td>
                                                            <select class="form-select" name="pre_bid_meeting[status]">
                                                                <option value="">-- Select Status --</option>
                                                                @foreach (config('constants.EVENT_STATUSES', []) as $status)
                                                                    <option value="{{ $status }}" {{ old('pre_bid_meeting.status', $tender['pre_bid_meeting']['status'] ?? '') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Deadline for Submission</td>
                                                        <td>
                                                            <select class="form-select" name="submission_deadline[mode]">
                                                                <option value="">-- Select Mode --</option>
                                                                @foreach (config('constants.EVENT_MODES', []) as $mode)
                                                                    <option value="{{ $mode }}" {{ old('submission_deadline.mode', $tender['submission_deadline']['mode'] ?? '') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="date" class="form-control" name="submission_deadline[date]" value="{{ old('submission_deadline.date', $tender['submission_deadline']['date'] ?? '') }}">
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control" name="submission_deadline[time]" value="{{ old('submission_deadline.time', $tender['submission_deadline']['time'] ?? '') }}">
                                                        </td>
                                                        <td>
                                                            <select class="form-select" name="submission_deadline[status]">
                                                                <option value="">-- Select Status --</option>
                                                                @foreach (config('constants.EVENT_STATUSES', []) as $status)
                                                                    <option value="{{ $status }}" {{ old('submission_deadline.status', $tender['submission_deadline']['status'] ?? '') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Opening of Technical Bid</td>
                                                        <td>
                                                            <select class="form-select" name="technical_bid_opening[mode]">
                                                                <option value="">-- Select Mode --</option>
                                                                @foreach (config('constants.EVENT_MODES', []) as $mode)
                                                                    <option value="{{ $mode }}" {{ old('technical_bid_opening.mode', $tender['technical_bid_opening']['mode'] ?? '') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="date" class="form-control" name="technical_bid_opening[date]" value="{{ old('technical_bid_opening.date', $tender['technical_bid_opening']['date'] ?? '') }}">
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control" name="technical_bid_opening[time]" value="{{ old('technical_bid_opening.time', $tender['technical_bid_opening']['time'] ?? '') }}">
                                                        </td>
                                                        <td>
                                                            <select class="form-select" name="technical_bid_opening[status]">
                                                                <option value="">-- Select Status --</option>
                                                                @foreach (config('constants.EVENT_STATUSES', []) as $status)
                                                                    <option value="{{ $status }}" {{ old('technical_bid_opening.status', $tender['technical_bid_opening']['status'] ?? '') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="row g-3 mt-2">
                                            <div class="col-md-12">
                                                <label class="form-label">Pre-Bid Venue</label>
                                                <input type="text" class="form-control" id="pre_bid_venue" name="pre_bid_venue" value="{{ old('pre_bid_venue', $tender['pre_bid_venue'] ?? '') }}">
                                                @error('pre_bid_venue')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <h6 class="section-header">Value/Earnest Money Deposit (EMD)</h6>
                                    <div class="event-section">
                                        <div class="row g-3">
                                            <div class="col-md-3">
                                                <label class="form-label">Tender Value (INR)</label>
                                                <input type="text" class="form-control" id="tender_value" name="tender_value" value="{{ old('tender_value', $tender['tender_value'] ?? '') }}">
                                                @error('tender_value')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">EMD Value (INR)</label>
                                                <input type="text" class="form-control" id="emd_value" name="emd_value" value="{{ old('emd_value', $tender['emd_value'] ?? '') }}">
                                                @error('emd_value')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">EMD Payment Mode</label>
                                                <select class="form-select" id="emd_payment_mode" name="emd_payment_mode">
                                                    <option value="">-- Select Mode --</option>
                                                    @foreach (config('constants.EMD_PAYMENT_MODES', []) as $mode)
                                                        <option value="{{ $mode }}" {{ old('emd_payment_mode', $tender['emd_payment_mode'] ?? '') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                                                    @endforeach
                                                </select>
                                                @error('emd_payment_mode')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Open Tender List</label>
                                                <select class="form-select" id="open_tender_list" name="open_tender_list">
                                                    <option value="">-- Select Option --</option>
                                                    @foreach (config('constants.OPEN_TENDER_LIST_OPTIONS', []) as $option)
                                                        <option value="{{ $option }}" {{ old('open_tender_list', $tender['open_tender_list'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                                @error('open_tender_list')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Tender Type (Category)</label>
                                                <select class="form-select" id="tender_type_category" name="tender_type_category">
                                                    <option value="">-- Select Category --</option>
                                                    @foreach (config('constants.ADDITIONAL_TENDER_TYPE_OPTIONS', []) as $option)
                                                        <option value="{{ $option }}" {{ old('tender_type_category', $tender['tender_type_category'] ?? '') === $option ? 'selected' : '' }}>{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                                @error('tender_type_category')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Company Registration Type</label>
                                                <select class="form-select select2-checkbox" id="company_registration_type" name="company_registration_type[]" multiple>
                                                    <option value="">-- Select Type --</option>
                                                    @foreach (config('constants.COMPANY_REGISTRATION_TYPES', []) as $type)
                                                        <option value="{{ $type }}" {{ in_array($type, old('company_registration_type', explode(',', $tender['company_registration_type'] ?? ''))) ? 'selected' : '' }}>{{ $type }}</option>
                                                    @endforeach
                                                </select>
                                                @error('company_registration_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Company Registered Year</label>
                                                <input type="number" class="form-control" id="company_registered_year" name="company_registered_year" min="1900" max="{{ date('Y') }}" value="{{ old('company_registered_year', $tender['company_registered_year'] ?? '') }}">
                                                @error('company_registered_year')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Company Sector Type</label>
                                                <select class="form-select select2-checkbox" id="company_sector_type" name="company_sector_type[]" multiple>
                                                    <option value="">-- Select Type --</option>
                                                    @foreach (config('constants.COMPANY_SECTOR_TYPES', []) as $type)
                                                        <option value="{{ $type }}" {{ in_array($type, old('company_sector_type', explode(',', $tender['company_sector_type'] ?? ''))) ? 'selected' : '' }}>{{ $type }}</option>
                                                    @endforeach
                                                </select>
                                                @error('company_sector_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Nature of Business</label>
                                                <select class="form-select select2-checkbox" id="nature_of_business" name="nature_of_business[]" multiple>
                                                    <option value="">-- Select Option --</option>
                                                    @foreach (config('constants.NATURE_OF_BUSINESS_OPTIONS', []) as $option)
                                                        <option value="{{ $option }}" {{ in_array($option, old('nature_of_business', explode(',', $tender['nature_of_business'] ?? ''))) ? 'selected' : '' }}>{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                                @error('nature_of_business')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Business Specialization</label>
                                                <select class="form-select select2-checkbox" id="business_specialization" name="business_specialization[]" multiple>
                                                    <option value="">-- Select Option --</option>
                                                    @foreach (config('constants.BUSINESS_SPECIALIZATION_OPTIONS', []) as $option)
                                                        <option value="{{ $option }}" {{ in_array($option, old('business_specialization', explode(',', $tender['business_specialization'] ?? ''))) ? 'selected' : '' }}>{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                                @error('business_specialization')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Procurement Category</label>
                                                <select class="form-select select2-checkbox" id="procurement_category" name="procurement_category[]" multiple>
                                                    <option value="">-- Select Category --</option>
                                                    @foreach (config('constants.PROCUREMENT_CATEGORIES', []) as $category)
                                                        <option value="{{ $category }}" {{ in_array($category, old('procurement_category', explode(',', $tender['procurement_category'] ?? ''))) ? 'selected' : '' }}>{{ $category }}</option>
                                                    @endforeach
                                                </select>
                                                @error('procurement_category')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Tender Nature</label>
                                                <select class="form-select" id="tender_nature" name="tender_nature">
                                                    <option value="">-- Select Nature --</option>
                                                    @foreach (config('constants.TENDER_NATURE_OPTIONS', []) as $nature)
                                                        <option value="{{ $nature }}" {{ old('tender_nature', $tender['tender_nature'] ?? '') === $nature ? 'selected' : '' }}>{{ $nature }}</option>
                                                    @endforeach
                                                </select>
                                                @error('tender_nature')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Certificates Tab -->
                        <div class="tab-pane fade" id="certificates-tab-pane" role="tabpanel">
                            <ul class="nav nav-tabs mb-3" id="certificatesTabs" role="tablist">
                                @foreach (config('constants.CERTIFICATE_CATEGORIES', []) as $key => $name)
                                    <li class="nav-item">
                                        <button class="nav-link {{ $key === 'incorporation' ? 'active' : '' }}" id="{{ $key }}-tab" data-bs-toggle="tab" data-bs-target="#{{ $key }}" type="button" role="tab">{{ $name }}</button>
                                    </li>
                                @endforeach
                            </ul>
                            <div class="tab-content" id="certificatesTabContent">
                                @foreach (config('constants.CERTIFICATE_CATEGORIES', []) as $key => $name)
                                    <div class="tab-pane fade {{ $key === 'incorporation' ? 'show active' : '' }}" id="{{ $key }}" role="tabpanel">
                                        <!-- <h6 class="mt-4">Certificates</h6> -->
                                        <div id="{{ $key }}-certificates">
                                            @php
                                                $certList = config('constants.CERTIFICATE_LISTS.' . $key, []);
                                                $certs = old("certificates.$key", $tender['certificates'][$key] ?? []);
                                            @endphp
                                            @foreach ($certList as $cert)
                                                <div class="row g-3 certificate-field mb-5 align-items-end">
                                                    <div class="col-md-4">
                                                        <label class="form-label">{{ $cert }}</label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="certificates[{{ $key }}][{{ $cert }}][status]" value="Yes" {{ old("certificates.$key.$cert.status", $certs[$cert]['status'] ?? '') === 'Yes' ? 'checked' : '' }}>
                                                            <label class="form-check-label">Yes</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="certificates[{{ $key }}][{{ $cert }}][status]" value="No" {{ old("certificates.$key.$cert.status", $certs[$cert]['status'] ?? '') === 'No' ? 'checked' : '' }}>
                                                            <label class="form-check-label">No</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="certificates[{{ $key }}][{{ $cert }}][status]" value="N/A" {{ old("certificates.$key.$cert.status", $certs[$cert]['status'] ?? '') === 'N/A' ? 'checked' : '' }}>
                                                            <label class="form-check-label">N/A</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="date" class="form-control" name="certificates[{{ $key }}][{{ $cert }}][valid_up_to]" value="{{ old("certificates.$key.$cert.valid_up_to", $certs[$cert]['valid_up_to'] ?? '') }}" placeholder="Valid Up To">
                                                    </div>
                                                    <div class="col-md-4"></div>
                                                </div>
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
                                    <button class="nav-link active" id="annual-statement-tab" data-bs-toggle="tab" data-bs-target="#annual-statement" type="button" role="tab">Annual Financial Statement</button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link" id="turnover-tab" data-bs-toggle="tab" data-bs-target="#turnover" type="button" role="tab">Annual Turnover & ITR</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="financialTabContent">
                                <div class="tab-pane fade show active" id="annual-statement" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Financial Years</th>
                                                    <th>Total Revenue</th>
                                                    <th>Total Expenses</th>
                                                    <th>Profit/Loss</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (config('constants.FINANCIAL_YEARS', []) as $year)
                                                    @php
                                                        $statement = array_merge(
                                                            ['revenue' => false, 'expenses' => false, 'profit_loss' => false],
                                                            old("financial_statements.$year", $tender['financial_statements'][$year] ?? [])
                                                        );
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $year }}</td>
                                                        <td><input type="checkbox" name="financial_statements[{{ $year }}][revenue]" {{ $statement['revenue'] ? 'checked' : '' }}></td>
                                                        <td><input type="checkbox" name="financial_statements[{{ $year }}][expenses]" {{ $statement['expenses'] ? 'checked' : '' }}></td>
                                                        <td><input type="checkbox" name="financial_statements[{{ $year }}][profit_loss]" {{ $statement['profit_loss'] ? 'checked' : '' }}></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="turnover" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Financial Years</th>
                                                    <th>ITR (Last 5 FY)</th>
                                                    <th>Annual Turnover (In ₹)</th>
                                                    <th>Annual Net Worth</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach (config('constants.FINANCIAL_YEARS', []) as $year)
                                                    @php
                                                        $financial = array_merge(
                                                            ['itr' => false, 'turnover' => '', 'net_worth' => ''],
                                                            old("financials.$year", $tender['financials'][$year] ?? [])
                                                        );
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $year }}</td>
                                                        <td>
                                                            <input type="checkbox" name="financials[{{ $year }}][itr]" value="1" {{ $financial['itr'] ? 'checked' : '' }}>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="financials[{{ $year }}][turnover]" value="{{ $financial['turnover'] }}">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control" name="financials[{{ $year }}][net_worth]" value="{{ $financial['net_worth'] }}">
                                                        </td>
                                                    </tr>
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
            <button class="nav-link active" id="annual-statement-tab" data-bs-toggle="tab" data-bs-target="#annual-statement" type="button" role="tab">Annual Financial Statement</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="turnover-tab" data-bs-toggle="tab" data-bs-target="#turnover" type="button" role="tab">Annual Turnover & ITR</button>
        </li>
    </ul>
    <div class="tab-content" id="financialTabContent">
        <!-- Annual Financial Statement -->
        <div class="tab-pane fade show active" id="annual-statement" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-bordered" id="financial-statement-table">
                    <thead>
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
                                array_keys($tender['financial_statements'] ?? []),
                                array_keys($tender['financials'] ?? [])
                            ));
                            if (!in_array($currentYear, $allYears)) {
                                $allYears[] = $currentYear;
                            }
                            rsort($allYears); // Sort descending
                        @endphp
                        @foreach ($allYears as $year)
                            @php
                                $statement = array_merge(
                                    ['revenue' => false, 'expenses' => false, 'profit_loss' => false],
                                    old("financial_statements.$year", $tender['financial_statements'][$year] ?? [])
                                );
                            @endphp
                            <tr data-year="{{ explode('-', $year)[0] }}">
                                <td class="year">{{ $year }}</td>
                                <td><input type="checkbox" name="financial_statements[{{ $year }}][revenue]" {{ $statement['revenue'] ? 'checked' : '' }}></td>
                                <td><input type="checkbox" name="financial_statements[{{ $year }}][expenses]" {{ $statement['expenses'] ? 'checked' : '' }}></td>
                                <td><input type="checkbox" name="financial_statements[{{ $year }}][profit_loss]" {{ $statement['profit_loss'] ? 'checked' : '' }}></td>
                            </tr>
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
        <div class="tab-pane fade" id="turnover" role="tabpanel">
            <div class="table-responsive">
                <table class="table table-bordered" id="turnover-table">
                    <thead>
                        <tr>
                            <th>Financial Years</th>
                            <th>ITR (Last 5 FY)</th>
                            <th>Annual Turnover (In ₹)</th>
                            <th>Annual Net Worth</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($allYears as $year)
                            @php
                                $financial = array_merge(
                                    ['itr' => false, 'turnover' => '', 'net_worth' => ''],
                                    old("financials.$year", $tender['financials'][$year] ?? [])
                                );
                            @endphp
                            <tr data-year="{{ explode('-', $year)[0] }}">
                                <td class="year">{{ $year }}</td>
                                <td><input type="checkbox" name="financials[{{ $year }}][itr]" value="1" {{ $financial['itr'] ? 'checked' : '' }}></td>
                                <td><input type="text" class="form-control" name="financials[{{ $year }}][turnover]" value="{{ $financial['turnover'] }}"></td>
                                <td><input type="text" class="form-control" name="financials[{{ $year }}][net_worth]" value="{{ $financial['net_worth'] }}"></td>
                            </tr>
                        @endforeach
                        <!-- Template Row (Hidden) -->
                        <tr id="turnover-template" style="display: none;" data-year="YEAR">
                            <td class="year"></td>
                            <td><input type="checkbox" name="financials[YEAR][itr]" value="1"></td>
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
                            <h5>Work Experience</h5>
                         


    <div class="row g-3">
    <div class="col-md-6">
        <label class="form-label" for="working_experience_area">Working Experience Areas</label>
        <select class="form-select select2-checkbox @error('work_experience.area') is-invalid @enderror" id="working_experience_area" name="work_experience[area][]" multiple>
            @foreach (config('constants.WORK_EXPERIENCE_AREAS', []) as $area)
                <option value="{{ $area }}"
                    {{ in_array($area, old('work_experience.area', (is_array($tender['work_experience'] ?? []) && isset($tender['work_experience']['area']) ? (array)($tender['work_experience']['area']) : []))) ? 'selected' : '' }}>
                    {{ $area }}
                </option>
            @endforeach
        </select>
        @error('work_experience.area')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-md-6">
        <label class="form-label" for="experience_years">Experience (Years)</label>
        <input type="number" class="form-control @error('work_experience.years') is-invalid @enderror" id="experience_years" name="work_experience[years]"
            value="{{ old('work_experience.years', isset($tender['work_experience']['years']) ? $tender['work_experience']['years'] : '') }}"
            min="0" step="1" placeholder="Enter years of experience">
        @error('work_experience.years')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
                            <div id="work-experience-container" class="mt-4">
                                @foreach (old('work_experience.details', $tender['work_experience']['details'] ?? []) as $detail)
                                    <div class="row g-3 work-experience-field mb-3">
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="work_experience[details][][project]" value="{{ $detail['project'] ?? '' }}" placeholder="Project Name">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="work_experience[details][][client]" value="{{ $detail['client'] ?? '' }}" placeholder="Client">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control" name="work_experience[details][][year]" value="{{ $detail['year'] ?? '' }}" placeholder="Year">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addMoreWorkExperience()">Add More</button>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-primary px-4">Save Tender</button>
                        <a href="{{ route('admin.tenders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection

<style>
/* Professional Select2 Styling - Enhanced (Checkbox Only Selection) */

/* Base container styling */
.select2-container--bootstrap-5 {
    width: 100% !important;
    box-sizing: border-box;
}

/* Main selection area */
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

/* Focus state */
.select2-container--bootstrap-5 .select2-selection--multiple:focus-within {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Rendered selected items */
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    margin: 0;
    padding: 0;
    flex-grow: 1;
    gap: 0.25rem;
}

/* Selection counter */
.select2-selection__counter {
    font-size: 0.8125rem;
    color: #495057;
    padding: 0.25rem 0.5rem;
    margin-left: auto;
    white-space: nowrap;
}

/* Dropdown menu */
.select2-container--bootstrap-5 .select2-dropdown {
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    z-index: 1050;
}

/* Individual options */
.select2-results__option {
    padding: 0.5rem 0.75rem;
    font-size: 0.9375rem;
    display: flex;
    align-items: center;
    line-height: 1.5;
    color: #212529;
    position: relative;
}

/* Checkbox styling */
.select2-checkbox-option {
    margin-right: 0.5rem;
    width: 1rem;
    height: 1rem;
    accent-color: #0d6efd;
    flex-shrink: 0;
    cursor: pointer;
}

/* Highlighted option */
.select2-container--bootstrap-5 .select2-results__option--highlighted {
    background-color: #e9ecef;
    color: #212529;
}

/* Selected option (remove background) */
.select2-container--bootstrap-5 .select2-results__option[aria-selected=true] {
    background-color: transparent;
    color: #212529;
}

/* Search input */
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

/* Scrollable options */
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

/* Selected tags */
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
    background-color: #e2e6ea;
    border: 1px solid #dae0e5;
    border-radius: 0.2rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    color: #343a40;
    display: flex;
    align-items: center;
}

/* Remove button for tags */
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

/* Disabled state */
.select2-container--bootstrap-5.select2-container--disabled .select2-selection--multiple {
    background-color: #e9ecef;
    cursor: not-allowed;
    opacity: 0.7;
}

/* Error state */
.select2-container--bootstrap-5 .select2-selection--multiple.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

/* Loading state */
.select2-results__option--loading {
    color: #6c757d !important;
    background-color: transparent !important;
}

/* No results message */
.select2-results__message {
    padding: 0.5rem 0.75rem;
    color: #6c757d;
    font-style: italic;
}

/* Ensure proper spacing for options with checkboxes */
.select2-results__option .select2-checkbox-option + span {
    flex-grow: 1;
}
</style>
@section('scripts')
<script>
$(document).ready(function() {
    // Initialize all Select2 instances with the 'select2-checkbox' class
    $('.select2-checkbox').each(function() {
        const $select = $(this);
        
        // Store original options for search functionality
        const originalOptions = $select.find('option').map(function() {
            return {
                id: $(this).val(),
                text: $(this).text(),
                element: this
            };
        }).get();

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
            },
            matcher: function(params, data) {
                // Always return the original data if no search term
                if ($.trim(params.term) === '') {
                    return data;
                }
                
                // Check if the text contains the search term
                if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                    return data;
                }
                
                // Return null if no match
                return null;
            }
        }).on('select2:select select2:unselect', function(e) {
            // Update the counter after selection/unselection
            updateSelectionCounter($(this));
            
            // Ensure the dropdown stays open
            if ($select.attr('multiple') !== undefined) {
                $select.select2('open');
            }
        }).on('select2:open', function() {
            // When dropdown opens, ensure checkboxes reflect current selection
            const selectedIds = $select.val() || [];
            const $dropdown = $select.data('select2').$dropdown;
            
            // Wait for options to render
            setTimeout(function() {
                $dropdown.find('.select2-results__option').each(function() {
                    const $option = $(this);
                    const optionId = $option.data('data').id;
                    
                    if (optionId) {
                        const isSelected = selectedIds.includes(optionId.toString());
                        $option.find('.select2-checkbox-option').prop('checked', isSelected);
                    }
                });
            }, 50);
        });

        // Initial update of the counter when the page loads
        updateSelectionCounter($select);
    });

    function formatOption(option) {
        // Don't format loading or other non-option entries
        if (option.loading || !option.id) {
            return option.text;
        }

        // Create the option element with checkbox
        const $option = $(
            `<div class="d-flex align-items-center">
                <input type="checkbox" class="select2-checkbox-option form-check-input me-2" 
                    ${option.element && option.element.selected ? 'checked' : ''}>
                <span class="select2-option-text">${option.text}</span>
            </div>`
        );

        // Add click handler for the checkbox
        $option.find('.select2-checkbox-option').on('click', function(e) {
            e.stopPropagation();
            
            const $selectElement = $(this).closest('.select2-container').prev('select');
            const currentValue = $selectElement.val() || [];
            const optionId = option.id.toString();
            
            if ($(this).is(':checked')) {
                // Add to selection if not already present
                if (!currentValue.includes(optionId)) {
                    $selectElement.val([...currentValue, optionId]).trigger('change');
                }
            } else {
                // Remove from selection
                const newValue = currentValue.filter(id => id !== optionId);
                $selectElement.val(newValue).trigger('change');
            }
        });

        return $option;
    }

    function formatSelection() {
        // Empty selection template (we'll handle display via counter)
        return '';
    }

    function updateSelectionCounter($select) {
        const $container = $select.siblings('.select2-container');
        const $rendered = $container.find('.select2-selection__rendered');
        const selectedCount = $select.select2('data').length;

        // Clear existing content
        $rendered.empty();

        if (selectedCount > 0) {
            // Add counter
            $rendered.append(
                `<span class="select2-selection__counter">
                    ${selectedCount} item${selectedCount !== 1 ? 's' : ''} selected
                </span>`
            );
        }
    }
}); 
       

        function addMoreField(type) {
            const container = document.getElementById(`${type}-container`);
            const field = document.createElement('div');
            field.className = `${type}-field mb-2 animate__animated animate__fadeIn`;
            field.innerHTML = `
                <div class="input-group">
                    <input type="text" class="form-control" name="${type}[]">
                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remove</button>
                </div>
            `;
            container.appendChild(field);
        }

        function removeField(element) {
            const field = element.closest('.keyword-field, .website-field, .certificate-field, .work-experience-field');
            $(field).addClass('animate__animated animate__fadeOut');
            setTimeout(() => field.remove(), 500);
        }

        function addMoreCertificate(category) {
            const container = document.getElementById(`${category}-certificates`);
            const field = document.createElement('div');
            field.className = 'row g-3 certificate-field mb-3 align-items-end animate__animated animate__fadeIn';
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
                    <input type="date" class="form-control" name="certificates[${category}][new_${Date.now()}][valid_up_to]" placeholder="Valid Up To">
                </div>
            `;
            container.appendChild(field);
        }

        function addMoreWorkExperience() {
            const container = document.getElementById('work-experience-container');
            const field = document.createElement('div');
            field.className = 'row g-3 work-experience-field mb-3 animate__animated animate__fadeIn';
            field.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-control" name="work_experience[details][][project]" placeholder="Project Name">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="work_experience[details][][client]" placeholder="Client">
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="work_experience[details][][year]" placeholder="Year">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-danger" onclick="removeField(this)">Remove</button>
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