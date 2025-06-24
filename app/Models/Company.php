<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'user_id', 'company_description', 'state', 'company_id', 'reference_id', 'company_type', 'department',
        'keywords', 'websites', 'company_registration_type', 'company_registered_year', 'company_sector_type',
        'nature_of_business', 'business_specialization', 'procurement_category', 'tender_nature',
        'work_experience', 'certificates', 'financial_statements', 'financials',
    ];

    protected $casts = [
        'keywords' => 'array',
        'websites' => 'array',
        'work_experience' => 'array',
        'certificates' => 'array',
        'financial_statements' => 'array',
        'financials' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}