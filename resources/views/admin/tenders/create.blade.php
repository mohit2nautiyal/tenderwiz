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
                                    <label class="form-label">Tender Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tender_name" name="tender_name" required value="{{ old('tender_name', $tender['tender_name'] ?? '') }}">
                                    @error('tender_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Keywords</label>
                                    <div id="keyword-container">
                                        @foreach (old('keywords', $tender['keywords'] ?? ['']) as $keyword)
                                            <div class="keyword-field mb-2">
                                                <div class="input-group">
                                                    <input type="text" class="form-control" name="keywords[]" value="{{ $keyword }}" placeholder="Enter keyword">
                                                    <button type="button" class="btn btn-outline-danger remove-field" onclick="removeField(this)">Remove</button>
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
                                <div class="col-md-12">
                                    <label class="form-label">Tender Description</label>
                                    <textarea class="form-control" id="tender_description" name="tender_description" rows="3">{{ old('tender_description', $tender['tender_description'] ?? '') }}</textarea>
                                    @error('tender_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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
                                    <button class="nav-link" id="networth-tab" data-bs-toggle="tab" data-bs-target="#networth" type="button" role="tab">Networth</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="financialTabContent">
                                <!-- Annual Financial Statement -->
                                <div class="tab-pane fade show active" id="annual-statement" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="financial-statement-table">
                                            <thead>
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
                                                    $financialStatements = old('financial_statements', $tender['financial_statements'] ?? []);
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
                                            <thead>
                                                <tr>
                                                    <th>From Year</th>
                                                    <th>To Year</th>
                                                    <th>ITR Filed</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $itrData = old('financials.itr', $tender['financials']['itr'] ?? []);
                                                    $currentYear = date('Y');
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
                                            <thead>
                                                <tr>
                                                    <th>From Year</th>
                                                    <th>To Year</th>
                                                    <th>Annual Turnover (In ₹)</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $turnoverData = old('financials.turnover', $tender['financials']['turnover'] ?? []);
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

                                <!-- Networth Tab -->
                                <div class="tab-pane fade" id="networth" role="tabpanel">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="networth-table">
                                            <thead>
                                                <tr>
                                                    <th>From Year</th>
                                                    <th>To Year</th>
                                                    <th>Networth Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $networthData = old('financials.net_worth', $tender['financials']['net_worth'] ?? []);
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
                     <!-- Work Experience Tab -->
<div class="tab-pane fade" id="experience-tab-pane" role="tabpanel">
    <h5>Work Experience</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="working_experience_area">Working Experience Areas</label>
            <select class="form-select select2-checkbox @error('work_experience.area') is-invalid @enderror" 
                    id="working_experience_area" name="work_experience[area][]" multiple>
                @foreach (config('constants.WORK_EXPERIENCE_AREAS', []) as $area)
                    <option value="{{ $area }}"
                        {{ in_array($area, old('work_experience.area', $tender['work_experience']['area'] ?? [])) ? 'selected' : '' }}>
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
            <input type="number" class="form-control @error('work_experience.years') is-invalid @enderror" 
                   id="experience_years" name="work_experience[years]"
                   value="{{ old('work_experience.years', $tender['work_experience']['years'] ?? '') }}"
                   min="0" step="1" placeholder="Enter years of experience">
            @error('work_experience.years')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="mt-4">
        <label class="form-label">Work Experience Keywords</label>
        <div id="work-experience-keywords-container">
            @php
                $keywords = old('work_experience.work_exp_keywords', $tender['work_experience']['work_exp_keywords'] ?? ['']);
            @endphp
            @foreach ($keywords as $keyword)
                <div class="work-experience-keyword-field mb-2">
                    <div class="input-group">
                        <input type="text" class="form-control" 
                               name="work_experience[work_exp_keywords][]" 
                               value="{{ $keyword }}" 
                               placeholder="Enter keyword">
                        <button type="button" class="btn btn-outline-danger remove-field" 
                                onclick="removeField(this)">Remove</button>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-outline-primary btn-sm mt-2" 
                onclick="addMoreField('work-experience-keyword')">Add More</button>
    </div>
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

@section('styles')
<!-- <link rel="stylesheet" href="{{ asset('css/tenders_form.css') }}"> -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script src="{{ asset('js/tenders_form.js') }}"></script>
@endsection