<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
  public function edit()
    {
        $company = Company::where('user_id', Auth::id())->first();
        $constants = config('constants');

        return view('user.company.edit', compact('company', 'constants'));
    }

    public function update(Request $request)
    {

        // $validated = $request->validate([
        //     'company_description' => 'required|string',
        //     'company_id' => 'required|string',
        //     'reference_id' => 'required|string',
        //     'company_type' => 'required|string',
        //     'state' => 'required|string',
        //     'department' => 'required|string',
        //     'keywords' => 'nullable|array',
        //     'websites' => 'nullable|array',
        //     'company_registration_type' => 'nullable|array',
        //     'company_registration_type.*' => 'string|in:' . implode(',', config('constants.COMPANY_REGISTRATION_TYPES', [])),
        //     'company_registered_year' => 'nullable|integer|min:1900|max:' . date('Y'),
        //     'company_sector_type' => 'nullable|array',
        //     'company_sector_type.*' => 'string|in:' . implode(',', config('constants.COMPANY_SECTOR_TYPES', [])),
        //     'nature_of_business' => 'nullable|array',
        //     'nature_of_business.*' => 'string|in:' . implode(',', config('constants.NATURE_OF_BUSINESS_OPTIONS', [])),
        //     'business_specialization' => 'nullable|array',
        //     'business_specialization.*' => 'string|in:' . implode(',', config('constants.BUSINESS_SPECIALIZATION_OPTIONS', [])),
        //     'procurement_category' => 'nullable|array',
        //     'procurement_category.*' => 'string|in:' . implode(',', config('constants.PROCUREMENT_CATEGORIES', [])),
        //     'tender_nature' => 'nullable|string',
        //     'work_experience' => 'nullable|array',
        //     'certificates' => 'nullable|array',
        //     'financial_statements' => 'nullable|array',
        //     'financials' => 'nullable|array',
        // ]);

        $data = $request->only([
            'company_description', 'state', 'company_id', 'reference_id', 'company_type', 'department',
            'keywords', 'websites', 'company_registered_year', 'tender_nature',
            'work_experience', 'certificates', 'financial_statements', 'financials',
        ]);

        // Convert multiselect arrays to comma-separated strings
        $multiselectFields = [
            'company_registration_type',
            'company_sector_type',
            'nature_of_business',
            'business_specialization',
            'procurement_category',
        ];
 
        foreach ($multiselectFields as $field) {
            $data[$field] = !empty($request->$field) ? implode(',', $request->$field) : null;
        }

        

        unset($data['financials']['YEAR']);

        $data['user_id'] = Auth::id();

        Company::updateOrCreate(
            ['user_id' => $data['user_id']],
            $data
        );

        return redirect()->route('user.company.edit')->with('flash', [
            'message' => 'Company updated successfully',
            'type' => 'success',
        ]);
    }
}