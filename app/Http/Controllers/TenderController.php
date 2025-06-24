<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Dompdf\Dompdf;

class TenderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->except(['userIndex', 'userShow', 'certificateShow']);
        $this->middleware('user')->only(['userIndex', 'userShow', 'certificateShow']);
    }

    private function verifyCsrfToken($token)
    {
        return hash_equals(Session::token(), $token);
    }

    public function index()
    {
        $tenders = $this->getAllTenders();
        $flash = Session::get('flash');

        return view('admin.tenders.index', compact('tenders', 'flash'));
    }

    public function create()
    {
        $constants = config('constants');
        $flash = Session::get('flash');
        $tender = [
            'financials' => array_fill_keys(config('constants.FINANCIAL_YEARS', []), ['itr' => false, 'turnover' => '', 'net_worth' => ''])
        ];
        return view('admin.tenders.create', compact('constants', 'flash'));
    }

    public function edit($id)
    {
        $tender = $this->getTenderById($id);
        if (!$tender) {
            return redirect()->route('admin.tenders.index')->with('flash', ['message' => 'Tender not found', 'type' => 'danger']);
        }
        $constants = config('constants');
        return view('admin.tenders.edit', compact('tender', 'constants'));
    }

    public function store(Request $request)
    {
        if (!$this->verifyCsrfToken($request->input('csrf_token'))) {
            return redirect()->route('admin.tenders.index')->with('flash', ['message' => 'Invalid CSRF token', 'type' => 'danger']);
        }

        $data = $this->prepareTenderData($request);
        $result = $this->saveTender($data);

        return redirect()->route('admin.tenders.index')->with('flash', ['message' => $result['message'], 'type' => $result['success'] ? 'success' : 'danger']);
    }

    public function update(Request $request, $id)
    {
        if (!$this->verifyCsrfToken($request->input('csrf_token'))) {
            return redirect()->route('admin.tenders.index')->with('flash', ['message' => 'Invalid CSRF token', 'type' => 'danger']);
        }

        $data = $this->prepareTenderData($request);
        $data['id'] = $id;
        $result = $this->saveTender($data);

        return redirect()->route('admin.tenders.index')->with('flash', ['message' => $result['message'], 'type' => $result['success'] ? 'success' : 'danger']);
    }

    public function destroy($id)
    {
        try {
            $result = DB::table('tenders')->where('id', $id)->delete();

            $message = $result ? 'Tender deleted successfully' : 'Failed to delete tender';
            $type = $result ? 'success' : 'danger';

            return redirect()->route('admin.tenders.index')->with('flash', ['message' => $message, 'type' => $type]);
        } catch (\Exception $e) {
            return redirect()->route('admin.tenders.index')->with('flash', ['message' => 'Database error: ' . $e->getMessage(), 'type' => 'danger']);
        }
    }



    public function userIndex()
{
    $flash = Session::get('flash');
    $tenders = $this->getAllTenders();

    // Fetch the company for the authenticated user
    $company = DB::table('companies')
        ->where('user_id', auth()->id())
        ->first();

    // Convert company to array and decode JSON fields
    if ($company) {
        $company = (array) $company;
        $company['keywords'] = json_decode($company['keywords'], true) ?? [];
        $company['websites'] = json_decode($company['websites'], true) ?? [];
        $company['certificates'] = json_decode($company['certificates'], true) ?? [];
        $company['financial_statements'] = json_decode($company['financial_statements'], true) ?? [];
        $company['financials'] = json_decode($company['financials'], true) ?? [];
        $company['work_experience'] = json_decode($company['work_experience'], true) ?? [];
    }

    // Process tenders and filter out those with days_remaining <= 0
    $processedTenders = array_filter(array_map(function ($tender) use ($company) {
        // Calculate Days Remaining (submission_deadline - Today)
        $today = Carbon::now();
        $daysRemaining = 0;

        if (isset($tender['submission_deadline']['date']) && $tender['submission_deadline']['date']) {
            $deadlineDate = Carbon::parse($tender['submission_deadline']['date'])->startOfDay();
            $today = Carbon::today();
            $daysRemaining = $today->diffInDays($deadlineDate, false);
            $daysRemaining = max(0, $daysRemaining);
        }

        // If days remaining is 0 or negative, return null to be filtered out
        if ($daysRemaining <= 0) {
            return null;
        }

        // Calculate Criteria Match
        $criteriaMatch = 0;
        if ($company) {
            $criteriaMatch = $this->calculateCriteriaMatch($tender, $company);
        }

        // Check if $criteriaMatch is an array and has the expected structure
        if (is_array($criteriaMatch) && isset($criteriaMatch['percentages']['overall'])) {
            $tender['criteria_match'] = round($criteriaMatch['percentages']['overall'], 2);
        } elseif (is_numeric($criteriaMatch)) {
            $tender['criteria_match'] = round((float)$criteriaMatch, 2);
        } else {
            $tender['criteria_match'] = 0;
        }

        // Add new fields to the tender array
        $tender['days_remaining'] = $daysRemaining;

        // Adjust field names to match the Blade template
        $tender['tender_name'] = $tender['tender_name'] ?? '';
        $tender['tender_description'] = $tender['tender_description'] ?? '';
        $tender['tenderwiz_id'] = $tender['tenderwiz_id'] ?? '';
        $tender['tender_reference_id'] = $tender['tender_reference_id'] ?? '';
        $tender['display_date'] = Carbon::parse($tender['date'])->format('d M, Y');
        $tender['date'] = Carbon::parse($tender['date'])->format('Y-m-d');

        return $tender;
    }, $tenders));

    // Re-index the array after filtering
    $tenders = array_values($processedTenders);

    return view('user.tenders.index', compact('tenders', 'flash'));
}

    public function userShow($id)
    {

     
        $tender = $this->getTenderById($id);
        if (!$tender) {
            return redirect()->route('user.tenders.index')->with('flash', ['message' => 'Tender not found', 'type' => 'danger']);
        }
        $constants = config('constants');
        return view('user.tenders.show', compact('tender', 'constants'));
    }


    public function certificateShow($id)
    {
        $tender = $this->getTenderById($id);

        $company = DB::table('companies')
        ->where('user_id', auth()->id())
        ->first();

    // Convert company to array and decode JSON fields
    if ($company) {
        $company = (array) $company;
        $company['keywords'] = json_decode($company['keywords'], true) ?? [];
        $company['websites'] = json_decode($company['websites'], true) ?? [];
        $company['certificates'] = json_decode($company['certificates'], true) ?? [];
        $company['financial_statements'] = json_decode($company['financial_statements'], true) ?? [];
        $company['financials'] = json_decode($company['financials'], true) ?? [];
        $company['work_experience'] = json_decode($company['work_experience'], true) ?? [];
      }

      $criteriaMatch = $this->calculateCriteriaMatch($tender, $company);
      $certificateHTML = $this->generateCertificate22($tender, $company, $criteriaMatch);
    echo $certificateHTML;

    die();
      
    }


    

    private function getTenderById($id)
    {
        $tender = DB::table('tenders')->where('id', $id)->first();

        if ($tender) {
            // Convert stdClass to array for consistency
            $tender = (array) $tender;
            // Decode JSON fields
            $tender['keywords'] = json_decode($tender['keywords'], true) ?? [];
            $tender['websites'] = json_decode($tender['websites'], true) ?? [];
            $tender['pre_bid_meeting'] = json_decode($tender['pre_bid_meeting'], true) ?? [];
            $tender['submission_deadline'] = json_decode($tender['submission_deadline'], true) ?? [];
            $tender['technical_bid_opening'] = json_decode($tender['technical_bid_opening'], true) ?? [];
            $tender['work_experience'] = json_decode($tender['work_experience'], true) ?? [];
            $tender['certificates'] = json_decode($tender['certificates'], true) ?? [];
            $tender['financial_statements'] = json_decode($tender['financial_statements'], true) ?? [];
            $tender['financials'] = json_decode($tender['financials'], true) ?? [];
        }

        return $tender;
    }

    private function getAllTenders()
    {
        $results = DB::table('tenders')
            // ->where('user_id', auth()->id()) // Filter by authenticated user
            ->orderBy('created_at', 'desc')
            ->get();

        $tenders = [];
        foreach ($results as $row) {
            // Convert stdClass to array
            $row = (array) $row;
            // Decode JSON fields
            $row['keywords'] = json_decode($row['keywords'], true) ?? [];
            $row['websites'] = json_decode($row['websites'], true) ?? [];
            $row['pre_bid_meeting'] = json_decode($row['pre_bid_meeting'], true) ?? [];
            $row['submission_deadline'] = json_decode($row['submission_deadline'], true) ?? [];
            $row['technical_bid_opening'] = json_decode($row['technical_bid_opening'], true) ?? [];
            $row['work_experience'] = json_decode($row['work_experience'], true) ?? [];
            $row['certificates'] = json_decode($row['certificates'], true) ?? [];
            $row['financial_statements'] = json_decode($row['financial_statements'], true) ?? [];
            $row['financials'] = json_decode($row['financials'], true) ?? [];
            $tenders[] = $row;
        }

        return $tenders;
    }

   


    private function prepareTenderData(Request $request)
{
    // Convert multi-select arrays to comma-separated strings
    $multiSelectFields = [
        'company_registration_type',
        'company_sector_type',
        'nature_of_business',
        'business_specialization',
        'procurement_category'
    ];

    $data = [
        'id' => $request->input('id', ''),
        'tender_name' => $request->input('tender_name'),
        'tender_description' => $request->input('tender_description'),
        'state' => $request->input('state'),
        'tenderwiz_id' => $request->input('tenderwiz_id'),
        'tender_reference_id' => $request->input('tender_reference_id'),
        'tag' => $request->input('tag'),
        'tender_type' => $request->input('tender_type'),
        'tender_inviting_authority' => $request->input('tender_inviting_authority'),
        'department' => $request->input('department'),
        'date' => $request->input('date'),
        'keywords' => $request->input('keywords', []),
        'websites' => $request->input('websites', []),
        'pre_bid_meeting' => $request->input('pre_bid_meeting', []),
        'submission_deadline' => $request->input('submission_deadline', []),
        'technical_bid_opening' => $request->input('technical_bid_opening', []),
        'pre_bid_venue' => $request->input('pre_bid_venue', ''),
        'tender_value' => $request->input('tender_value', ''),
        'emd_value' => $request->input('emd_value', ''),
        'emd_payment_mode' => $request->input('emd_payment_mode', ''),
        'open_tender_list' => $request->input('open_tender_list', ''),
        'tender_type_category' => $request->input('tender_type_category', ''),
        'company_registered_year' => $request->input('company_registered_year', ''),
        'tender_nature' => $request->input('tender_nature', ''),
        'work_experience' => $request->input('work_experience', []),
        'certificates' => $request->input('certificates', []),
        'financial_statements' => $request->input('financial_statements', []),
        'financials' => $request->input('financials', []),
        'csrf_token' => $request->input('csrf_token'),
    ];

    // Handle multi-select fields
    foreach ($multiSelectFields as $field) {
        $data[$field] = is_array($request->input($field, [])) 
            ? implode(',', array_filter($request->input($field, []))) 
            : '';
    }

    return $data;
}

  



private function saveTender($data)
{

    // dd($data);
    $fields = [
        'tender_name','tender_description', 'state', 'tenderwiz_id', 'tender_reference_id', 'tag', 'tender_type',
        'tender_inviting_authority', 'department', 'date', 'pre_bid_venue', 'tender_value', 'emd_value',
        'emd_payment_mode', 'open_tender_list', 'tender_type_category', 'company_registration_type',
        'company_registered_year', 'company_sector_type', 'nature_of_business', 'business_specialization',
        'procurement_category', 'tender_nature'
    ];

    $jsonFields = [
        'keywords', 'websites', 'pre_bid_meeting', 'submission_deadline', 'technical_bid_opening',
        'work_experience', 'certificates', 'financial_statements', 'financials'
    ];
    unset($data['financials']['YEAR']);

    // Prepare data for insertion or update
    $tenderData = [];
    foreach ($fields as $field) {
        $tenderData[$field] = $data[$field] ?? '';
    }
    foreach ($jsonFields as $field) {
        $tenderData[$field] = json_encode($data[$field] ?? []);
    }


    try {
        if ($data['id']) {
            // Update existing tender
            DB::table('tenders')->where('id', $data['id'])->update($tenderData);
            return ['success' => true, 'message' => 'Tender updated successfully'];
        } else {
            // Insert new tender
            // $tenderData['user_id'] = auth()->id(); // Add user_id for new tenders
            DB::table('tenders')->insert($tenderData);
            return ['success' => true, 'message' => 'Tender created successfully'];
        }
    } catch (\Exception $e) {
        return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
    }
}



  
// public function generateCertificate22($tender, $company, $matchResults) {



//     // Helper function to safely display values (handles arrays and strings)
//     $safeDisplay = function($value) {
//         if (is_array($value)) {
//             return htmlspecialchars(implode(', ', $value));
//         }
//         return htmlspecialchars((string) $value);
//     };

//     // Certificate HTML
//     $html = '<div class="certificate-container" style="border: 2px solid #000; padding: 20px; max-width: 900px; margin: 0 auto; font-family: Arial, sans-serif;">';
    
//     // Header
//     $html .= '<div style="text-align: center; margin-bottom: 30px;">';
//     $html .= '<h1 style="color: #2c3e50;">Tender Eligibility Certificate</h1>';
//     $html .= '<p style="font-size: 16px;">Generated on: ' . date('Y-m-d') . '</p>';
//     $html .= '</div>';
    
//     // Company and Tender Info
//     $html .= '<div style="display: flex; justify-content: space-between; margin-bottom: 30px;">';
//     $html .= '<div style="width: 48%; border: 1px solid #ddd; padding: 10px;">';
//     $html .= '<h3 style="background-color: #f2f2f2; padding: 5px; margin-top: 0;">Company Details</h3>';
//     $html .= '<p><strong>Name:</strong> ' . $safeDisplay($company['name'] ?? 'N/A') . '</p>';
//     $html .= '<p><strong>Registration Type:</strong> ' . $safeDisplay($company['company_registration_type'] ?? []) . '</p>';
//     $html .= '<p><strong>Registered Year:</strong> ' . $safeDisplay($company['company_registered_year'] ?? 'N/A') . '</p>';
//     $html .= '<p><strong>Sector Type:</strong> ' . $safeDisplay($company['company_sector_type'] ?? []) . '</p>';
//     $html .= '<p><strong>Nature of Business:</strong> ' . $safeDisplay($company['nature_of_business'] ?? []) . '</p>';
//     $html .= '<p><strong>Business Specialization:</strong> ' . $safeDisplay($company['business_specialization'] ?? []) . '</p>';
//     $html .= '</div>';
    
//     $html .= '<div style="width: 48%; border: 1px solid #ddd; padding: 10px;">';
//     $html .= '<h3 style="background-color: #f2f2f2; padding: 5px; margin-top: 0;">Tender Requirements</h3>';
//     $html .= '<p><strong>Tender ID:</strong> ' . $safeDisplay($tender['tenderwiz_id'] ?? 'N/A') . '</p>';
//     $html .= '<p><strong>Registration Type Required:</strong> ' . $safeDisplay($tender['company_registration_type'] ?? 'N/A') . '</p>';
//     $html .= '<p><strong>Registered Before:</strong> ' . $safeDisplay($tender['company_registered_year'] ?? 'N/A') . '</p>';
//     $html .= '<p><strong>Sector Type Required:</strong> ' . $safeDisplay($tender['company_sector_type'] ?? 'N/A') . '</p>';
//     $html .= '<p><strong>Nature of Business Required:</strong> ' . $safeDisplay($tender['nature_of_business'] ?? 'N/A') . '</p>';
//     $html .= '<p><strong>Business Specialization Required:</strong> ' . $safeDisplay($tender['business_specialization'] ?? 'N/A') . '</p>';
//     $html .= '</div>';
//     $html .= '</div>';
    
//     // Matching Results
//     $html .= '<div style="margin-bottom: 30px;">';
//     $html .= '<h3 style="background-color: #f2f2f2; padding: 5px;">Detailed Matching Results</h3>';
    
//     // Certificates Section
//     $html .= '<div style="margin-bottom: 20px;">';
//     $html .= '<h4>Certificates & Licenses Match: ' . $matchResults['percentages']['certificates'] . '%</h4>';
    
//     $certificateSections = ['incorporation', 'regulatory', 'hospital', 'environmental', 'other'];
//     foreach ($certificateSections as $section) {
//         if (isset($tender['certificates'][$section])) {
//             $html .= '<h5>' . ucfirst($section) . ' Certificates</h5>';
//             $html .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">';
//             $html .= '<tr style="background-color: #f2f2f2;">';
//             $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Certificate</th>';
//             $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Tender Requires</th>';
//             $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Company Has</th>';
//             $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Status</th>';
//             $html .= '</tr>';
            
//             foreach ($tender['certificates'][$section] as $certName => $certDetails) {
//                 $tenderStatus = $certDetails['status'];
//                 $companyStatus = isset($company['certificates'][$section][$certName]['status']) ? 
//                     $company['certificates'][$section][$certName]['status'] : 'No';
//                 $result = $matchResults['match_results']['certificates'][$certName] ?? 'N/A';
                
//                 $status = ($result === 1) ? '✅ Matched' : 
//                          (($result === 0) ? '❌ Not Matched' : '⚪ Not Applicable');
                
//                 $html .= '<tr>';
//                 $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($certName) . '</td>';
//                 $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($tenderStatus) . '</td>';
//                 $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($companyStatus) . '</td>';
//                 $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $status . '</td>';
//                 $html .= '</tr>';
//             }
            
//             $html .= '</table>';
//         }
//     }
//     $html .= '</div>';
    
//     // Work Experience Section
//     $html .= '<div style="margin-bottom: 20px;">';
//     $html .= '<h4>Work Experience Match: ' . $matchResults['percentages']['work_experience'] . '%</h4>';
//     $html .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">';
//     $html .= '<tr style="background-color: #f2f2f2;">';
//     $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Criteria</th>';
//     $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Tender Requires</th>';
//     $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Company Has</th>';
//     $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Status</th>';
//     $html .= '</tr>';
    
//     // Work Experience Area
//     $tenderArea = $tender['work_experience']['area'] ?? 'Not Specified';
//     $companyArea = $company['work_experience']['area'] ?? 'None';
//     $areaResult = $matchResults['match_results']['work_experience']['area'] ?? 'N/A';
//     $areaStatus = ($areaResult === 1) ? '✅ Matched' : 
//                  (($areaResult === 0) ? '❌ Not Matched' : '⚪ Not Applicable');
    
//     $html .= '<tr>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">Work Area</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($tenderArea) . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($companyArea) . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $areaStatus . '</td>';
//     $html .= '</tr>';
    
//     // Work Experience Years
//     $tenderYears = $tender['work_experience']['years'] ?? 0;
//     $companyYears = $company['work_experience']['years'] ?? 0;
//     $yearsResult = $matchResults['match_results']['work_experience']['years'] ?? 'N/A';
//     $yearsStatus = ($yearsResult === 1) ? '✅ Matched' : 
//                   (($yearsResult === 0) ? '❌ Not Matched' : '⚪ Not Applicable');
    
//     $html .= '<tr>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">Experience (Years)</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($tenderYears) . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($companyYears) . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $yearsStatus . '</td>';
//     $html .= '</tr>';
    
//     $html .= '</table>';
//     $html .= '</div>';
    

//     // Financial Statements Section
// $html .= '<div style="margin-bottom: 20px;">';
// $html .= '<h4>Financial Statements Match: ' . $matchResults['percentages']['financials'] . '%</h4>';
// $html .= '<table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">';
// $html .= '<tr style="background-color: #f2f2f2;">';
// $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Year</th>';
// $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Criteria</th>';
// $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Tender Requires</th>';
// $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Company Has</th>';
// $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Status</th>';
// $html .= '</tr>';

// // Loop through each financial year in the match results
// foreach ($matchResults['match_results']['financials'] as $year => $criteria) {
//     // ITR Row
//     $tenderITR = isset($tender['financials']['itr'][$year]) ? 'Required' : 'Not Required';
//     $companyITR = isset($company['financials']['itr'][$year]) ? 'Submitted' : 'Not Submitted';
//     $itrStatus = ($criteria['itr'] === 1) ? '✅ Matched' : 
//                 (($criteria['itr'] === 0) ? '❌ Not Matched' : '⚪ Not Applicable');
    
//     $html .= '<tr>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($year) . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">ITR</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $tenderITR . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $companyITR . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $itrStatus . '</td>';
//     $html .= '</tr>';
    
//     // Turnover Row
//     $tenderTurnover = $tender['financials']['turnover'][$year] ?? 'Not Specified';
//     $companyTurnover = $company['financials']['turnover'][$year] ?? 'Not Available';
//     $turnoverStatus = ($criteria['turnover'] === 1) ? '✅ Matched' : 
//                      (($criteria['turnover'] === 0) ? '❌ Not Matched' : '⚪ Not Applicable');
    
//     $html .= '<tr>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($year) . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">Turnover</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($tenderTurnover) . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($companyTurnover) . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $turnoverStatus . '</td>';
//     $html .= '</tr>';
    
//     // Net Worth Row
//     $tenderNetWorth = $tender['financials']['net_worth'][$year] ?? 'Not Specified';
//     $companyNetWorth = $company['financials']['net_worth'][$year] ?? 'Not Available';
//     $netWorthStatus = ($criteria['net_worth'] === 1) ? '✅ Matched' : 
//                      (($criteria['net_worth'] === 0) ? '❌ Not Matched' : '⚪ Not Applicable');
    
//     $html .= '<tr>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($year) . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">Net Worth</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($tenderNetWorth) . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $safeDisplay($companyNetWorth) . '</td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $netWorthStatus . '</td>';
//     $html .= '</tr>';
// }

// $html .= '</table>';
// $html .= '</div>';



    
//     // Summary Section
//     $html .= '<div style="margin-top: 30px; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd;">';
//     $html .= '<h3 style="margin-top: 0;">Summary</h3>';
//     $html .= '<table style="width: 100%; border-collapse: collapse;">';
//     $html .= '<tr style="background-color: #e6e6e6;">';
//     $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Section</th>';
//     $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Match Percentage</th>';
//     $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Details</th>';
//     $html .= '</tr>';
    
//     foreach ($matchResults['percentages'] as $section => $percentage) {
//         if ($section === 'overall') continue;
        
//         $details = '';
//         switch ($section) {
//             case 'incorporation':
//                 $details = 'Registration, Sector, Business details';
//                 break;
//             case 'certificates':
//                 $details = count($matchResults['match_results']['certificates']) . ' certificates checked';
//                 break;
//             case 'financials':
//                 $details = '5 years financial data checked';
//                 break;
//             case 'work_experience':
//                 $details = 'Work area and years of experience';
//                 break;
//         }
        
//         $html .= '<tr>';
//         $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . ucfirst($section) . '</td>';
//         $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $percentage . '%</td>';
//         $html .= '<td style="border: 1px solid #ddd; padding: 8px;">' . $details . '</td>';
//         $html .= '</tr>';
//     }
    
//     $html .= '<tr style="background-color: #d4edda;">';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;"><strong>Overall Match</strong></td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;"><strong>' . $matchResults['percentages']['overall'] . '%</strong></td>';
//     $html .= '<td style="border: 1px solid #ddd; padding: 8px;">Weighted average of all sections</td>';
//     $html .= '</tr>';
    
//     $html .= '</table>';
//     $html .= '</div>';
    
//     // Footer
//     $html .= '<div style="text-align: center; margin-top: 30px; font-size: 12px; color: #777;">';
//     $html .= '<p>This certificate is generated automatically based on the matching criteria between tender requirements and company profile.</p>';
//     $html .= '</div>';
    
//     $html .= '</div>'; // Close container
    
//     return $html;
// }

public function generateCertificate22($tender, $company, $matchResults) {
    // Helper function to safely display values
    $safeDisplay = function($value) {
        if (is_array($value)) {
            return htmlspecialchars(implode(', ', $value));
        }
        return htmlspecialchars((string) $value);
    };

    // Certificate HTML with exact PDF structure
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Tender Compatibility Certificate</title>
        <style>
            @import url("https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap");
            
            body {
                font-family: "Roboto", sans-serif;
                margin: 0;
                padding: 0;
                color: #333;
                line-height: 1.5;
                background-color: #f5f5f5;
            }
            .certificate-container {
                max-width: 900px;
                margin: 20px auto;
                padding: 40px;
                background: white;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            }
            .header-section {
                margin-bottom: 30px;
                border-bottom: 2px solid #0056b3;
                padding-bottom: 20px;
            }
            .company-header {
                font-size: 22px;
                font-weight: 700;
                color: #0056b3;
                margin-bottom: 5px;
            }
            .tender-meta {
                margin: 15px 0;
            }
            .tender-meta p {
                margin: 5px 0;
                font-size: 14px;
            }
            .tender-meta strong {
                font-weight: 500;
                color: #555;
                min-width: 344px;
                display: inline-block;
            }
            .report-title {
                font-size: 20px;
                font-weight: 700;
                color: #0056b3;
                text-align: center;
                margin: 25px 0;
                text-transform: uppercase;
            }
            .match-summary {
                margin-bottom: 30px;
            }
            .match-summary table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            .match-summary th {
                background-color: #0056b3;
                color: white;
                text-align: left;
                padding: 10px 15px;
                font-weight: 500;
            }
            .match-summary td {
                padding: 10px 15px;
                border-bottom: 1px solid #eee;
            }
            .match-summary .indented {
                padding-left: 30px;
            }
            .match-percentage {
                font-weight: 700;
                color: #0056b3;
            }
            .detailed-results {
                margin-top: 30px;
            }
            .detailed-results table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 30px;
            }
            .detailed-results th {
                background-color: #f2f2f2;
                text-align: left;
                padding: 10px 15px;
                font-weight: 500;
                border-bottom: 2px solid #ddd;
            }
            .detailed-results td {
                padding: 10px 15px;
                border-bottom: 1px solid #eee;
                vertical-align: top;
            }
            .section-title {
                background-color: #f8f8f8;
                font-weight: 600;
            }
            .status {
                font-weight: 500;
            }
            .matched {
                color: #28a745;
            }
            .not-matched {
                color: #dc3545;
            }
            .not-applicable {
                color: #6c757d;
                font-style: italic;
            }
            .footer {
                margin-top: 40px;
                font-size: 12px;
                color: #777;
                border-top: 1px solid #eee;
                padding-top: 20px;
            }
            .footer p {
                margin: 5px 0;
            }
            .disclaimer {
                font-style: italic;
                margin-top: 20px;
            }
            .download-btn {
                display: inline-block;
                background-color: #0056b3;
                color: white;
                padding: 10px 20px;
                text-decoration: none;
                border-radius: 4px;
                margin-top: 20px;
                font-weight: 500;
                border: none;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
    <div class="certificate-container">
        <div class="header-section">
           <!-- <div class="company-header">' . $safeDisplay($company['company_description'] ?? 'Company Name') . ', ' . $safeDisplay($company['state'] ?? 'State') . '</div> -->


            <div class="company-header">' . $safeDisplay($company['company_description'] ?? 'Company Name') . '</div>
            
            <div class="tender-meta">
                <p><strong>TenderWiz ID:</strong> ' . $safeDisplay($tender['tenderwiz_id'] ?? 'N/A') . '</p>
                <p><strong>Tender Reference ID:</strong> ' . $safeDisplay($tender['tender_reference_id'] ?? 'N/A') . '</p>
                <p><strong>Tender/Bid Description ID:</strong> ' . $safeDisplay($tender['tender_name'] ?? 'N/A') . '</p>
                <p><strong>Tender/Bid Inviting Authority:</strong> ' . $safeDisplay($tender['tender_inviting_authority'] ?? 'N/A') . '</p>
                <p><strong>Department:</strong> ' . $safeDisplay($tender['department'] ?? 'N/A') . '</p>
                <p><strong>Pre-bid Meeting Date:</strong> ' . $safeDisplay($tender['pre_bid_meeting']['date'] ?? 'N/A') . '</p>
                <p><strong>Deadline for Submission of Bidding Document:</strong> ' . $safeDisplay($tender['submission_deadline']['date'] ?? 'N/A') . '</p>
            </div>
        </div>

        <div class="report-title">Tender/ Bid Compatibility Assessment Report</div>

        <div class="match-summary">
            <table>
                <tr>
                    <th style="width: 70%;">Criteria</th>
                    <th style="width: 30%;">Match Percentage</th>
                </tr>
                <tr>
                    <td>Overall Match (In %)</td>
                    <td class="match-percentage">' . $matchResults['percentages']['overall'] . '%</td>
                </tr>
                <tr>
                    <td>Incorporation</td>
                    <td class="match-percentage">' . $matchResults['percentages']['incorporation'] . '%</td>
                </tr>
                <tr>
                    <td>Certificates and Licenses</td>
                    <td class="match-percentage">' . $matchResults['percentages']['certificates'] . '%</td>
                </tr>
                <tr>
                    <td>Financials</td>
                    <td class="match-percentage">' . $matchResults['percentages']['financials'] . '%</td>
                </tr>
                <tr>
                    <td class="indented">Annual Financial Statement</td>
                    <td class="match-percentage">' . $matchResults['percentages']['financials'] . '%</td>
                </tr>
                <tr>
                    <td class="indented">Annual Turnover</td>
                    <td class="match-percentage">' . $matchResults['percentages']['financials'] . '%</td>
                </tr>
                <tr>
                    <td class="indented">ITR</td>
                    <td class="match-percentage">' . $matchResults['percentages']['financials'] . '%</td>
                </tr>
                <tr>
                    <td class="indented">Networth</td>
                    <td class="match-percentage">' . $matchResults['percentages']['financials'] . '%</td>
                </tr>
                <tr>
                    <td>*Work Experience</td>
                    <td class="match-percentage">' . $matchResults['percentages']['work_experience'] . '%</td>
                </tr>
            </table>
        </div>

        <div class="detailed-results">
            <table>
                <tr>
                    <th style="width: 35%;">Tender/Bid Fields</th>
                    <th style="width: 45%;">Description</th>
                    <th style="width: 20%;">Match Status</th>
                </tr>
                
                <!-- Incorporation Section -->
                <tr class="section-title">
                    <td colspan="3">Incorporation</td>
                </tr>
                <tr>
                    <td>Incorporation</td>
                    <td>Company/Firm/Organization Registration Type</td>
                    <td class="status ' . $this->getStatusClass($matchResults['match_results']['incorporation']['company_registration_type']) . '">' . $this->getStatusText($matchResults['match_results']['incorporation']['company_registration_type']) . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Company Registration (Duration)</td>
                    <td class="status ' . $this->getStatusClass($matchResults['match_results']['incorporation']['company_registered_year']) . '">' . $this->getStatusText($matchResults['match_results']['incorporation']['company_registered_year']) . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Company Sector Type</td>
                    <td class="status ' . $this->getStatusClass($matchResults['match_results']['incorporation']['company_sector_type']) . '">' . $this->getStatusText($matchResults['match_results']['incorporation']['company_sector_type']) . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Nature of Business</td>
                    <td class="status ' . $this->getStatusClass($matchResults['match_results']['incorporation']['nature_of_business']) . '">' . $this->getStatusText($matchResults['match_results']['incorporation']['nature_of_business']) . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Business Specialization</td>
                    <td class="status ' . $this->getStatusClass($matchResults['match_results']['incorporation']['business_specialization']) . '">' . $this->getStatusText($matchResults['match_results']['incorporation']['business_specialization']) . '</td>
                </tr>
               
                
                <!-- Certificates Section -->
                <tr class="section-title">
                    <td colspan="3">Certificate & Licenses</td>
                </tr>';
                
                // Certificate rows
                $certificateSections = ['incorporation', 'regulatory', 'hospital', 'environmental', 'other'];
                foreach ($certificateSections as $section) {
                    if (isset($tender['certificates'][$section])) {
                        foreach ($tender['certificates'][$section] as $certName => $certDetails) {
                            $result = $matchResults['match_results']['certificates'][$certName] ?? 'N/A';
                            $html .= '<tr>
                                <td></td>
                                <td>' . $safeDisplay($certName) . '</td>
                                <td class="status ' . $this->getStatusClass($result) . '">' . $this->getStatusText($result) . '</td>
                            </tr>';
                        }
                    }
                }
                
                // Financials Section
                $html .= '<tr class="section-title">
                    <td colspan="3">Annual Financial Statement</td>
                </tr>
                <tr>
                    <td>Annual Financial Statement</td>
                    <td>Annual Balance Sheet</td>
                    <td class="status matched">Match</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Audited P&L Account Statement (Last 5 FY)</td>
                    <td class="status matched">Match</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Audited Income & Expenditure Accounts for the Entity Registered Under Society or Trust (Last 5 FY)</td>
                    <td class="status matched">Match</td>
                </tr>
                <tr class="section-title">
                    <td colspan="3">ITR</td>
                </tr>
                <tr>
                    <td>ITR</td>
                    <td>ITR (Last 5 AY)</td>
                    <td class="status matched">Match</td>
                </tr>
                <tr class="section-title">
                    <td colspan="3">Annual Turnover</td>
                </tr>
                <tr>
                    <td>Annual Turnover</td>
                    <td>Average Annual Turnover (Last 5 FY) (in $)</td>
                    <td class="status matched">Match</td>
                </tr>
                <tr class="section-title">
                    <td colspan="3">Networth</td>
                </tr>
                <tr>
                    <td>Networth</td>
                    <td>Annual Net Worth (Negative or Positive)</td>
                    <td class="status matched">Match</td>
                </tr>
                
                <!-- Work Experience Section -->
                <tr class="section-title">
                    <td colspan="3">Work Experience</td>
                </tr>
                <tr>
                    <td>Work Experience</td>
                    <td>Work Experience Area</td>
                    <td class="status ' . $this->getStatusClass($matchResults['match_results']['work_experience']['area']) . '">' . $this->getStatusText($matchResults['match_results']['work_experience']['area']) . '</td>
                </tr>
                <tr>
                    <td></td>
                    <td>Experience (in Years) from the current FY</td>
                    <td class="status ' . $this->getStatusClass($matchResults['match_results']['work_experience']['years']) . '">' . $this->getStatusText($matchResults['match_results']['work_experience']['years']) . '</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <div class="disclaimer">
               
                ' . html_entity_decode($safeDisplay($tender['tender_description'] ?? 'N/A')) .'
            </div>
            
            <button class="download-btn" onclick="window.print();">Download as PDF</button>
        </div>
    </div>
    </body>
    </html>';
    
    return $html;


    // <div class="footer">
    //         <p>*Note: In the event that the work experience provided by an applicant does not strictly conform to the eligibility criteria set forth in the tender documentation, it shall be the sole responsibility of the client to undertake appropriate verification measures.</p>
            
    //         <div class="disclaimer">
    //             <p><strong>Disclaimer</strong></p>
    //             <p>This Tender/ Bid Compatibility Assessment Report has been generated by TenderWiz solely for the internal reference and convenience of the client. The contents of this report are automatically generated by the system based on the company profile, credentials, documents, and other relevant information as last updated and submitted by the client on the TenderWiz platform.</p>


    //             ' . html_entity_decode($safeDisplay($tender['tender_description'] ?? 'N/A')) .'
    //         </div>
            
    //         <button class="download-btn" onclick="window.print();">Download as PDF</button>
    //     </div>
}

private function getStatusClass($result) {
    if ($result === 'Not Applicable') {
        return 'not-applicable';
    }
    return $result === 1 ? 'matched' : 'not-matched';
}

private function getStatusText($result) {
    if ($result === 'Not Applicable') {
        return 'Not Applicable';
    }
    return $result === 1 ? 'Match' : 'No match';
}


public function calculateCriteriaMatch($tender, $company) {
    $matchResults = [
        'incorporation' => [],
        'certificates' => [],
        'financials' => [],
        'work_experience' => []
    ];

    // 1. Incorporation Matching
    $matchResults['incorporation'] = [
        'company_registration_type' => $this->checkArrayMatch(
            $tender['company_registration_type'], 
            $company['company_registration_type']
        ),
        'company_registered_year' => $this->checkYearMatch(
            $tender['company_registered_year'], 
            $company['company_registered_year']
        ),
        'company_sector_type' => $this->checkArrayMatch(
            $tender['company_sector_type'], 
            $company['company_sector_type']
        ),
        'nature_of_business' => $this->checkArrayMatch(
            $tender['nature_of_business'], 
            $company['nature_of_business']
        ),
        'business_specialization' => $this->checkArrayMatch(
            $tender['business_specialization'], 
            $company['business_specialization']
        )
        // 'procurement_category' => $this->checkArrayMatch(
        //     $tender['procurement_category'], 
        //     $company['procurement_category']
        // )
    ];



    // 2. Certificates Matching - Updated for exact Yes/No match
    $certificateSections = ['incorporation', 'regulatory', 'hospital', 'environmental', 'other'];
    foreach ($certificateSections as $section) {
        if (isset($tender['certificates'][$section])) {
            foreach ($tender['certificates'][$section] as $certName => $certDetails) {
                
                $tenderStatus = isset($certDetails['status'])?$certDetails['status']:'N/A';
                $companyStatus = isset($company['certificates'][$section][$certName]['status']) ? 
                    $company['certificates'][$section][$certName]['status'] : 'No';
                
                $matchResults['certificates'][$certName] = $this->checkExactCertificateMatch(
                    $tenderStatus, 
                    $companyStatus
                );
            }
        }
    }

//     // 3. Financials Matching - Dynamic year comparison
// $matchResults['financials'] = [];

// // Get all financial years from tender data
// $tenderFinancialYears = array_keys($tender['financials'] ?? []);

// foreach ($tenderFinancialYears as $tenderYear) {
//     // Check if company has data for this year
//     if (isset($company['financials'][$tenderYear])) {
//         $matchResults['financials'][$tenderYear] = [
//             'turnover' => $this->checkTurnoverMatch(
//                 $tender['financials'][$tenderYear]['turnover'] ?? null,
//                 $company['financials'][$tenderYear]['turnover'] ?? null
//             ),
//             'net_worth' => $this->checkNetWorthMatch(
//                 $tender['financials'][$tenderYear]['net_worth'] ?? null,
//                 $company['financials'][$tenderYear]['net_worth'] ?? null
//             ),
//             'itr' => $this->checkExactITRMatch(
//                 $tender['financials'][$tenderYear]['itr'] ?? null,
//                 $company['financials'][$tenderYear]['itr'] ?? null
//             )
//         ];
//     } else {
//         // Company doesn't have data for this tender year - mark all as not matched
//         $matchResults['financials'][$tenderYear] = [
//             'turnover' => 0,
//             'net_worth' => 0,
//             'itr' => 0
//         ];
//     }
// }


      $matchResults['financials'] = [];
    
    // Check ITR requirements
    if (isset($tender['financials']['itr'])) {
        foreach ($tender['financials']['itr'] as $year => $tenderValue) {
            $companyValue = $company['financials']['itr'][$year] ?? 0;
            $matchResults['financials'][$year]['itr'] = $this->checkExactITRMatch($tenderValue, $companyValue);
        }
    }
    
    // Check Turnover requirements
    if (isset($tender['financials']['turnover'])) {
        foreach ($tender['financials']['turnover'] as $year => $tenderValue) {
            $companyValue = $company['financials']['turnover'][$year] ?? 0;
            $matchResults['financials'][$year]['turnover'] = $this->checkTurnoverMatch($tenderValue, $companyValue);
        }
    }
    
    // Check Net Worth requirements
    if (isset($tender['financials']['net_worth'])) {
        foreach ($tender['financials']['net_worth'] as $year => $tenderValue) {
            $companyValue = $company['financials']['net_worth'][$year] ?? 'negative';
            $matchResults['financials'][$year]['net_worth'] = $this->checkNetWorthMatch($tenderValue, $companyValue);
        }
    }


    // 4. Work Experience Matching
    $matchResults['work_experience'] = [
        'area' => $this->checkWorkExperienceAreaMatch(
            $tender['work_experience']['area'] ?? '',
            $company['work_experience']['area'] ?? ''
        ),
        'years' => $this->checkWorkExperienceYearsMatch(
            $tender['work_experience']['years'] ?? 0,
            $company['work_experience']['years'] ?? 0
        )
    ];

    // Calculate percentages for each section
    $percentages = [
        'incorporation' => $this->calculateSectionPercentage($matchResults['incorporation']),
        'certificates' => $this->calculateSectionPercentage($matchResults['certificates']),
        'financials' => $this->calculateFinancialsPercentage($matchResults['financials']),
        'work_experience' => $this->calculateSectionPercentage($matchResults['work_experience'])
    ];

    // Calculate overall percentage (weighted average if needed)
    $percentages['overall'] = array_sum($percentages) / count($percentages);

    return [
        'match_results' => $matchResults,
        'percentages' => $percentages
    ];
}

// Updated helper functions
private function checkArrayMatch($tenderValue, $companyValue) {
    if (empty($tenderValue) || $tenderValue === 'N/A' || $tenderValue === 'NA') {
        return 'Not Applicable';
    }
    
    $tenderArray = is_array($tenderValue) ? $tenderValue : explode(',', $tenderValue);
    $companyArray = is_array($companyValue) ? $companyValue : explode(',', $companyValue);
    
    return !empty(array_intersect($tenderArray, $companyArray)) ? 1 : 0;
}

private function checkYearMatch($tenderYear, $companyYear) {
    if (empty($tenderYear) || $tenderYear === 'N/A' || $tenderYear === 'NA') {
        return 'Not Applicable';
    }
    if (empty($companyYear)) return 0;
    
    return ($companyYear <= $tenderYear) ? 1 : 0;
}

// Updated for exact Yes/No match
private function checkExactCertificateMatch($tenderStatus, $companyStatus) {
    $tenderStatus = strtoupper($tenderStatus);
    if ($tenderStatus === 'N/A' || $tenderStatus === 'NA') {
        return 'Not Applicable';
    }
    
    $companyStatus = strtoupper($companyStatus);
    return ($tenderStatus === $companyStatus) ? 1 : 0;
}


private function checkTurnoverMatch($tenderValue, $companyValue) {
    if (empty($tenderValue) || $tenderValue === 'N/A' || $tenderValue === 'NA') {
        return 'Not Applicable';
    }
    if (empty($companyValue)) return 0;
    
    $tenderValue = is_numeric($tenderValue) ? (float)$tenderValue : null;
    $companyValue = is_numeric($companyValue) ? (float)$companyValue : null;
    
    if ($tenderValue === null || $companyValue === null) {
        return 0;
    }
    
    return ($companyValue >= $tenderValue) ? 1 : 0;
}

private function checkNetWorthMatch($tenderValue, $companyValue) {
    if (empty($tenderValue) || $tenderValue === 'N/A' || $tenderValue === 'NA') {
        return 'Not Applicable';
    }
    if (empty($companyValue)) return 0;
    
    $tenderValue = strtolower($tenderValue);
    $companyValue = strtolower($companyValue);
    
    return ($tenderValue === $companyValue) ? 1 : 0;
}

private function checkExactITRMatch($tenderValue, $companyValue) {
    if ($tenderValue === null || $tenderValue === 'N/A' || $tenderValue === 'NA') {
        return 'Not Applicable';
    }
    
    return ($companyValue == $tenderValue) ? 1 : 0;
}
private function checkWorkExperienceAreaMatch($tenderAreas, $companyAreas) {
    // Convert to arrays if they aren't already
    $tenderAreas = is_array($tenderAreas) ? $tenderAreas : explode(',', $tenderAreas);
    $companyAreas = is_array($companyAreas) ? $companyAreas : explode(',', $companyAreas);
    
    // Clean up the arrays (trim whitespace, remove empty values)
    $tenderAreas = array_filter(array_map('trim', $tenderAreas));
    $companyAreas = array_filter(array_map('trim', $companyAreas));
    
    // If tender has no areas or all are N/A, return Not Applicable
    if (empty($tenderAreas) || in_array('N/A', $tenderAreas) || in_array('NA', $tenderAreas)) {
        return 'Not Applicable';
    }
    
    // Check if ALL tender areas exist in company areas (case-insensitive)
    foreach ($tenderAreas as $tenderArea) {
        $found = false;
        foreach ($companyAreas as $companyArea) {
            if (strcasecmp($tenderArea, $companyArea) === 0) {
                $found = true;
                break;
            }
        }
        if (!$found) {
            return 0; // At least one tender area not found in company
        }
    }
    
    return 1; // All tender areas found in company
}

private function checkWorkExperienceYearsMatch($tenderYears, $companyYears) {

   
    if (empty($tenderYears) || $tenderYears === 'N/A' || $tenderYears === 'NA') {
        return 'Not Applicable';
    }
    return ($companyYears >= $tenderYears) ? 1 : 0;
}

private function calculateSectionPercentage($sectionResults) {
    $total = 0;
    $matched = 0;
    
    foreach ($sectionResults as $result) {
        if ($result === 'Not Applicable') continue;
        $total++;
        $matched += $result;
    }
    
    return $total > 0 ? round(($matched / $total) * 100, 2) : 0;
}

private function calculateFinancialsPercentage($financialsResults) {
    $total = 0;
    $matched = 0;
    
    foreach ($financialsResults as $yearData) {
        foreach ($yearData as $result) {
            if ($result === 'Not Applicable') continue;
            $total++;
            $matched += $result;
        }
    }
    
    return $total > 0 ? round(($matched / $total) * 100, 2) : 0;
}














 public function generateCertificate_new($tender, $company, $matchResults) {
        // Helper function to safely display values
        $safeDisplay = function($value) {
            if (is_array($value)) {
                return htmlspecialchars(implode(', ', $value));
            }
            return htmlspecialchars((string) $value);
        };

        // Certificate HTML - Matching the PDF style
        $html = '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Tender/Bid Compatibility Report</title>
            <style>
                body {
                    font-family: \'Inter\', sans-serif; /* Changed to Inter for a modern look */
                    margin: 0;
                    padding: 20px;
                    background-color: #f0f2f5; /* Lighter background */
                    color: #34495e; /* Darker grey for text */
                    line-height: 1.6;
                    font-size: 14px;
                }
                .container {
                    max-width: 850px; /* Slightly narrower for better readability */
                    margin: 0 auto;
                    background-color: #ffffff;
                    border: 1px solid #e6e9ed;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.08); /* More prominent shadow */
                    padding: 40px; /* Increased padding */
                    border-radius: 8px; /* Slightly more rounded corners */
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 3px solid #0056b3; /* Darker blue line */
                    position: relative; /* For logo positioning */
                }
                .header h1 {
                    color: #2c3e50;
                    font-size: 32px; /* Larger heading */
                    margin-bottom: 10px;
                    font-weight: 700; /* Bolder */
                }
                .header p {
                    font-size: 15px;
                    color: #7f8c8d; /* Muted grey for tagline */
                    margin-top: 0;
                }
                .logo-placeholder {
                    margin-bottom: 20px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                .logo-placeholder img {
                    width: 180px; /* Larger logo */
                    height: auto;
                    vertical-align: middle;
                }
                .section {
                    margin-bottom: 35px; /* Increased spacing between sections */
                    border: 1px solid #dce0e6;
                    padding: 25px; /* Increased padding */
                    border-radius: 6px;
                    background-color: #fdfdfe; /* Almost white background */
                }
                .section h2, .section h3 {
                    font-size: 22px; /* Adjusted heading size */
                    margin-top: 0;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #aec6e4; /* Lighter, subtle blue for section divider */
                    color: #0056b3; /* Darker blue for section titles */
                    font-weight: 600; /* Semi-bold */
                    margin-bottom: 15px; /* Spacing below heading */
                }
                .section h3 {
                    font-size: 19px;
                    color: #34495e;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                    font-size: 14px;
                }
                table th, table td {
                    border: 1px solid #ebf0f5; /* Lighter border for tables */
                    padding: 12px 15px; /* More padding in cells */
                    text-align: left;
                }
                table thead th {
                    background-color: #f7f9fc; /* Very light grey for table headers */
                    font-weight: 600;
                    color: #5d6d7e; /* Muted dark grey */
                    border-bottom: 2px solid #dce0e6;
                }
                table tbody tr:nth-child(even) {
                    background-color: #fcfdfe; /* Subtle zebra striping */
                }
                .match-percentage-table td:first-child {
                    background-color: #f7f9fc;
                    font-weight: 600;
                    color: #5d6d7e;
                }
                .match-percentage-table td:last-child {
                    text-align: center;
                    font-weight: 700; /* Bolder percentage */
                    color: #28a745; /* Green for match percentage */
                    font-size: 17px;
                }
                 .overall-match td:last-child {
                    background-color: #d4edda; /* Light green background for overall match */
                    color: #155724; /* Dark green text */
                    font-size: 20px; /* Larger overall match percentage */
                    padding: 15px;
                }
                .description-table th {
                    background-color: #f7f9fc;
                }
                .match-status {
                    font-weight: 600; /* Semi-bold */
                    text-align: center;
                    padding: 12px 15px;
                }
                .match-status.match {
                    color: #28a745; /* Green */
                }
                .match-status.no-match {
                    color: #dc3545; /* Red */
                }
                .match-status.not-applicable {
                    color: #6c757d; /* Grey */
                }
                .note-section {
                    margin-top: 40px; /* More space */
                    padding: 20px;
                    background-color: #fff8e1; /* Softer yellow background */
                    border: 1px solid #ffe082;
                    border-left: 6px solid #ffa000; /* Darker orange left border */
                    font-size: 13px;
                    line-height: 1.7; /* Increased line height */
                    border-radius: 6px;
                }
                .note-section strong {
                    color: #e65100; /* Darker orange for strong tag */
                }
                .disclaimer-section {
                    margin-top: 40px; /* More space */
                    padding: 25px;
                    background-color: #e8eaf6; /* Light blue-grey background */
                    border: 1px solid #c5cae9;
                    border-left: 6px solid #5c6bc0; /* Deeper blue-grey left border */
                    font-size: 12px;
                    line-height: 1.7; /* Increased line height */
                    border-radius: 6px;
                }
                .disclaimer-section h4 {
                    color: #3f51b5; /* Deeper blue for disclaimer heading */
                    font-size: 16px;
                    margin-top: 0;
                    margin-bottom: 12px;
                }
                .disclaimer-section ol {
                    padding-left: 25px;
                    margin-top: 10px;
                }
                .disclaimer-section li {
                    margin-bottom: 10px;
                }
                .disclaimer-section strong {
                    color: #303f9f; /* Even deeper blue for strong tag */
                }
                .footer {
                    text-align: center;
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 1px solid #e6e9ed;
                    font-size: 11px;
                    color: #9e9e9e; /* Lighter grey for footer text */
                    line-height: 1.5;
                }
                .footer p {
                    margin-bottom: 8px;
                }
                .footer a {
                    color: #0056b3; /* Darker blue for links */
                    text-decoration: none;
                }
                .footer a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>
        <div class="container">';
        
        // Header with TenderWiz logo placeholder
        $html .= '<div class="header">
                    <div class="logo-placeholder">
                        <img src="https://placehold.co/180x60/0056b3/ffffff?text=TenderWiz+Logo" alt="TenderWiz Logo">
                    </div>
                    <h1>Tender/ Bid Compatibility Assessment Report</h1>
                    <p>TenderWiz Consulting Live Tenders</p>
                </div>';
        
        // Company and Tender Info - Matching PDF layout
        $html .= '<div class="section">
                    <h2>Company & Tender Information</h2>
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr>
                            <td style="width: 35%; padding: 10px 0; font-weight: 600; color: #555;">Company Name:</td>
                            <td style="padding: 10px 0; color: #333;">'. $safeDisplay($company['name'] ?? 'ABC (India) Private Limited, Tamil Nadu') .'</td>
                        </tr>
                        <tr>
                            <td style="width: 35%; padding: 10px 0; font-weight: 600; color: #555;">TenderWiz ID:</td>
                            <td style="padding: 10px 0; color: #333;">'. $safeDisplay($tender['tenderwiz_id'] ?? 'TW000001') .'</td>
                        </tr>
                        <tr>
                            <td style="width: 35%; padding: 10px 0; font-weight: 600; color: #555;">Tender Reference ID:</td>
                            <td style="padding: 10px 0; color: #333;">'. $safeDisplay($tender['tender_reference_id'] ?? 'BMSICL/2025-26/ME-400') .'</td>
                        </tr>
                        <tr>
                            <td style="width: 35%; padding: 10px 0; font-weight: 600; color: #555;">Tender/Bid Description:</td>
                            <td style="padding: 10px 0; color: #333;">'. $safeDisplay($tender['description'] ?? 'Tender for the Procurement, Rate Contract and Supply & Installation of Medical Equipment for different Government Health Institutions of Bihar') .'</td>
                        </tr>
                        <tr>
                            <td style="width: 35%; padding: 10px 0; font-weight: 600; color: #555;">Tender/Bid Inviting Authority:</td>
                            <td style="padding: 10px 0; color: #333;">'. $safeDisplay($tender['authority'] ?? 'Bihar Medical Services and Infrastructure Limited, Bihar') .'</td>
                        </tr>
                        <tr>
                            <td style="width: 35%; padding: 10px 0; font-weight: 600; color: #555;">Department:</td>
                            <td style="padding: 10px 0; color: #333;">'. $safeDisplay($tender['department'] ?? 'Health Department, Government of Bihar') .'</td>
                        </tr>
                        <tr>
                            <td style="width: 35%; padding: 10px 0; font-weight: 600; color: #555;">Pre-bid Meeting Date:</td>
                            <td style="padding: 10px 0; color: #333;">'. $safeDisplay($tender['pre_bid_meeting_date'] ?? '13-05-2025') .'</td>
                        </tr>
                        <tr>
                            <td style="width: 35%; padding: 10px 0; font-weight: 600; color: #555;">Deadline for Submission:</td>
                            <td style="padding: 10px 0; color: #333;">'. $safeDisplay($tender['submission_deadline'] ?? '02-06-2025') .'</td>
                        </tr>
                    </table>
                </div>';
        
        // Overall Match Percentage - Matching PDF style
        $html .= '<div class="section">
                    <h3>Tender/ Bid Compatibility Assessment</h3>
                    <table class="match-percentage-table overall-match">
                        <tr>
                            <td><strong>Overall Match (in %)</strong></td>
                            <td>'. ($matchResults['percentages']['overall'] ?? '100') .'%</td>
                        </tr>
                    </table>
                    
                    <table class="match-percentage-table">
                        <tr>
                            <td><strong>Incorporation</strong></td>
                            <td>'. ($matchResults['percentages']['incorporation'] ?? '100') .'%</td>
                        </tr>
                        <tr>
                            <td><strong>Regulatory & Compliance Certificates and Licenses</strong></td>
                            <td>'. ($matchResults['percentages']['certificates'] ?? '100') .'%</td>
                        </tr>
                        <tr>
                            <td><strong>Financials</strong></td>
                            <td>'. ($matchResults['percentages']['financials'] ?? '100') .'%</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 25px;">Annual Financial Statement</td>
                            <td>100%</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 25px;">Annual Turnover</td>
                            <td>100%</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 25px;">ITR</td>
                            <td>100%</td>
                        </tr>
                        <tr>
                            <td style="padding-left: 25px;">Net Worth</td>
                            <td>100%</td>
                        </tr>
                        <tr>
                            <td><strong>Work Experience*</strong></td>
                            <td>'. ($matchResults['percentages']['work_experience'] ?? '100') .'%</td>
                        </tr>
                    </table>
                </div>';
        
        // Detailed Compatibility Assessment - Matching PDF table structure
        $html .= '<div class="section">
                    <h3>Description of Tender/ Bid Compatibility Assessment</h3>
                    <table class="description-table">
                        <thead>
                            <tr>
                                <th style="width: 25%;">Tender/Bid Fields</th>
                                <th style="width: 50%;">Description</th>
                                <th style="width: 25%; text-align: center;">TW000001/ Match</th>
                            </tr>
                        </thead>
                        <tbody>';
        
        // Helper to determine match status class
        $getMatchStatus = function($isMatch) {
            if ($isMatch === 'Not Applicable') {
                return 'not-applicable';
            }
            return ($isMatch) ? 'match' : 'no-match';
        };
        $getMatchText = function($isMatch) {
            if ($isMatch === 'Not Applicable') {
                return 'Not Applicable';
            }
            return ($isMatch) ? 'Match' : 'No match';
        };

        // Incorporation Section
        $html .= '<tr>
                        <td rowspan="6" style="font-weight: 700; color: #0056b3;">Incorporation</td>
                        <td>Company/Firm/Organization Registration Type</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['incorporation']['registration_type'] ?? false) .'">'. $getMatchText($matchResults['match_results']['incorporation']['registration_type'] ?? false) .'</td>
                    </tr>
                    <tr>
                        <td>Company Registration (Duration)</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['incorporation']['registration_duration'] ?? true) .'">'. $getMatchText($matchResults['match_results']['incorporation']['registration_duration'] ?? true) .'</td>
                    </tr>
                    <tr>
                        <td>Company Sector Type</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['incorporation']['sector_type'] ?? false) .'">'. $getMatchText($matchResults['match_results']['incorporation']['sector_type'] ?? false) .'</td>
                    </tr>
                    <tr>
                        <td>Nature of Business</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['incorporation']['nature_of_business'] ?? false) .'">'. $getMatchText($matchResults['match_results']['incorporation']['nature_of_business'] ?? false) .'</td>
                    </tr>
                    <tr>
                        <td>Business Specialization</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['incorporation']['business_specialization'] ?? false) .'">'. $getMatchText($matchResults['match_results']['incorporation']['business_specialization'] ?? false) .'</td>
                    </tr>
                    <tr>
                        <td>Procurement Category</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['incorporation']['procurement_category'] ?? true) .'">'. $getMatchText($matchResults['match_results']['incorporation']['procurement_category'] ?? true) .'</td>
                    </tr>';
        
        // Certificates & Licenses Section
        $html .= '<tr>
                        <td rowspan="18" style="font-weight: 700; color: #0056b3;">Certificate & Licenses</td>
                        <td>Company Incorporation Certificate</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['certificates']['company_incorporation'] ?? true) .'">'. $getMatchText($matchResults['match_results']['certificates']['company_incorporation'] ?? true) .'</td>
                    </tr>
                    <tr>
                        <td>Import Export Code (IEC Certificate)</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['certificates']['iec'] ?? false) .'">'. $getMatchText($matchResults['match_results']['certificates']['iec'] ?? false) .'</td>
                    </tr>
                    <tr>
                        <td>Udyam/MSME Certificate</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['certificates']['msme'] ?? false) .'">'. $getMatchText($matchResults['match_results']['certificates']['msme'] ?? false) .'</td>
                    </tr>
                    <tr>
                        <td>PAN Card</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['certificates']['pan'] ?? true) .'">'. $getMatchText($matchResults['match_results']['certificates']['pan'] ?? true) .'</td>
                    </tr>
                    <tr>
                        <td>GST Registration Certificate</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['certificates']['gst'] ?? true) .'">'. $getMatchText($matchResults['match_results']['certificates']['gst'] ?? true) .'</td>
                    </tr>
                    <tr>
                        <td>ESI Registration Certificate</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['certificates']['esi'] ?? true) .'">'. $getMatchText($matchResults['match_results']['certificates']['esi'] ?? true) .'</td>
                    </tr>
                    <tr>
                        <td>EPF Registration Certificate</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['certificates']['epf'] ?? true) .'">'. $getMatchText($matchResults['match_results']['certificates']['epf'] ?? true) .'</td>
                    </tr>
                    <tr>
                        <td>Drug License</td>
                        <td class="match-status not-applicable">Not Applicable</td>
                    </tr>
                    <tr>
                        <td>Good Manufacturing Practice Certificate (GMP)</td>
                        <td class="match-status not-applicable">Not Applicable</td>
                    </tr>
                    <tr>
                        <td>WHO-GMP or GMP Certificate</td>
                        <td class="match-status not-applicable">Not Applicable</td>
                    </tr>
                    <tr>
                        <td>FDA Approval (US)</td>
                        <td class="match-status not-applicable">Not Applicable</td>
                    </tr>
                    <tr>
                        <td>DCGI Approval (India)</td>
                        <td class="match-status not-applicable">Not Applicable</td>
                    </tr>
                    <tr>
                        <td>CE Certification (European Conformity)</td>
                        <td class="match-status not-applicable">Not Applicable</td>
                    </tr>
                    <tr>
                        <td>ISO 13485</td>
                        <td class="match-status not-applicable">Not Applicable</td>
                    </tr>
                    <tr>
                        <td>BIS Certification (India)</td>
                        <td class="match-status not-applicable">Not Applicable</td>
                    </tr>
                    <tr>
                        <td>CDSCO Registration (India)</td>
                        <td class="match-status not-applicable">Not Applicable</td>
                    </tr>
                    <tr>
                        <td>ISO 9001:2015</td>
                        <td class="match-status not-applicable">Not Applicable</td>
                    </tr>
                    <tr>
                        <td>ISO 14001</td>
                        <td class="match-status not-applicable">Not Applicable</td>
                    </tr>';
        
        // Financials Section
        $html .= '<tr>
                        <td rowspan="5" style="font-weight: 700; color: #0056b3;">Financials</td>
                        <td>Annual Balance Sheet</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['financials']['annual_balance_sheet'] ?? true) .'">'. $getMatchText($matchResults['match_results']['financials']['annual_balance_sheet'] ?? true) .'</td>
                    </tr>
                    <tr>
                        <td>Audited P&L Account Statement (Last 5 FY)</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['financials']['pnl_statement'] ?? true) .'">'. $getMatchText($matchResults['match_results']['financials']['pnl_statement'] ?? true) .'</td>
                    </tr>
                    <tr>
                        <td>Audited Income & Expenditure Accounts for the Entity Registered Under Society or Trust (Last 5 FY)</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['financials']['income_expenditure_statement'] ?? true) .'">'. $getMatchText($matchResults['match_results']['financials']['income_expenditure_statement'] ?? true) .'</td>
                    </tr>
                    <tr>
                        <td>ITR (Last 5 AY)</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['financials']['itr'] ?? true) .'">'. $getMatchText($matchResults['match_results']['financials']['itr'] ?? true) .'</td>
                    </tr>
                     <tr>
                        <td>Average Annual Turnover (Last 5 FY) (in $)</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['financials']['annual_turnover'] ?? true) .'">'. $getMatchText($matchResults['match_results']['financials']['annual_turnover'] ?? true) .'</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #0056b3;">Net Worth</td>
                        <td>Annual Net Worth (Negative or Positive)</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['financials']['net_worth'] ?? true) .'">'. $getMatchText($matchResults['match_results']['financials']['net_worth'] ?? true) .'</td>
                    </tr>';
        
        // Work Experience Section
        $html .= '<tr>
                        <td rowspan="2" style="font-weight: 700; color: #0056b3;">Work Experience</td>
                        <td>Work Experience Area</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['work_experience']['area'] ?? false) .'">'. $getMatchText($matchResults['match_results']['work_experience']['area'] ?? false) .'</td>
                    </tr>
                    <tr>
                        <td>Experience (in Years) from the current FY</td>
                        <td class="match-status '. $getMatchStatus($matchResults['match_results']['work_experience']['years'] ?? true) .'">'. $getMatchText($matchResults['match_results']['work_experience']['years'] ?? true) .'</td>
                    </tr>
                </tbody>
            </table>
        </div>';
        
        // Note section matching PDF
        $html .= '<div class="note-section">
                    <p><strong>*Note:</strong> In the event that the work experience provided by an applicant does not strictly conform to the eligibility criteria set forth in the tender documentation, it shall be the sole responsibility of the client to undertake appropriate verification measures. The client must ensure that the nature of business operations and the corresponding work experience submitted are substantially aligned with the scope of work, terms, conditions, and technical requirements as stipulated in the tender documents.</p>
                    <p>The client is advised to exercise due diligence in evaluating the relevance, authenticity, and applicability of such experience prior to submission. Any misrepresentation or non-compliance arising from unverified or misclassified credentials shall be at the client\'s sole risk, and no liability shall accrue to the consultant or advisory entity in such circumstances.</p>
                </div>';
        
        // Disclaimer section matching PDF
        $html .= '<div class="disclaimer-section">
                    <h4>Disclaimer</h4>
                    <p>This Tender/ Bid Compatibility Assessment Report has been generated by TenderWiz solely for the internal reference and convenience of the client. The contents of this report are automatically generated by the system based on the company profile, credentials, documents, and other relevant information as last updated and submitted by the client on the TenderWiz platform. While the system attempts to interpret and align key eligibility criteria, compliance clauses, and scope requirements of the tender with the client\'s declared profile, it is important to note the following:</p>
                    <ol>
                        <li><strong>Indicative Nature:</strong> The assessment presented in this report is purely indicative and must not be treated as a definitive or complete evaluation of the client\'s eligibility or qualification for the tender.</li>
                        <li><strong>Client Validation Required:</strong> All descriptions, matches, and compatibility insights provided in this report must be thoroughly reviewed and validated by the client. TenderWiz relies solely on the input provided by the client and makes no warranties regarding its accuracy, completeness, or relevance to any specific tender requirement.</li>
                        <li><strong>No Filing Basis:</strong> No tender or bid submission should be made solely on the basis of this report. This document is not a substitute for a detailed review of the official tender documents or associated corrigenda and clarifications issued by the tendering authority.</li>
                        <li><strong>No Guarantee or Liability:</strong> TenderWiz makes no representations or guarantees regarding the outcome of any bid and disclaims any liability arising from actions taken based on this report. Use of this report is entirely at the client\'s discretion and risk.</li>
                        <li><strong>Dynamic Tender Environment:</strong> Tender conditions, criteria, and deadlines are subject to change. Clients are strongly advised to refer to the original tender documents and all subsequent updates issued by the procuring entity before preparing or submitting any bid.</li>
                        <li><strong>Legal Vetting Advisory:</strong> Clients are strongly advised to consult their legal or compliance advisors for a comprehensive review of the tender documents and any interpretation of eligibility, legal clauses, or compliance requirements. This report is not legally vetted and does not constitute legal advice or opinion.</li>
                    </ol>
                </div>';
        
        // Footer matching PDF
        $html .= '<div class="footer">
                    <p>&copy;2025 TenderWiz. All rights reserved. TenderWiz is a proprietary platform developed and maintained for providing tender-related advisory and automation services. TenderWiz may refer to the technology solution and/or its associated consulting entity, each operating as a separate and independent legal entity. This content is provided for general informational purposes only and should not be construed as legal, financial or professional advice. Clients are strongly encouraged to seek independent professional consultation before acting on any information contained in this report. The Tender/ Bid Compatibility Assessment Report is system-generated based on the data and profile information input by the client and is intended solely as an indicative reference. It does not constitute a formal opinion of eligibility or compliance for any specific tender. At TenderWiz, our mission is to simplify tender participation and support clients in navigating complex procurement requirements with clarity and efficiency.</p>
                    <p>To learn more about our solutions or share your feedback at info@tenderwiz.in, visit us at <a href="http://www.tenderwiz.in" style="color: #0056b3; text-decoration: none;">www.tenderwiz.in</a>.</p>
                </div>';
        
        $html .= '</div></body></html>'; // Close container and body, html
        
        return $html;
    }



//     public function generatePDF()
// {
//     $html = "<h1>Hello PDF</h1>"; // You can also load view using view()->render()

//     $dompdf = new Dompdf();
//     $dompdf->loadHtml($html);
//     $dompdf->setPaper('A4', 'portrait');
//     $dompdf->render();

//     return $dompdf->stream("TenderWiz_Compatibility_Report.pdf", ["Attachment" => true]);
// }



public function downloadCertificate($tender, $company, $matchResults) {
    // Generate the HTML
    $html = $this->generateCertificate22($tender, $company, $matchResults);
    
    // Configure Dompdf
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Montserrat');
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    // Output the generated PDF
    $dompdf->stream('tender_eligibility_certificate.pdf', [
        'Attachment' => true
    ]);
}




}