<?php
// app/Models/Tender.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    protected $casts = [
        'keywords' => 'array',
        'websites' => 'array',
        'pre_bid_meeting' => 'array',
        'submission_deadline' => 'array',
        'technical_bid_opening' => 'array',
        'work_experience' => 'array',
        'certificates' => 'array',
        'financial_statements' => 'array',
        'financials' => 'array',
    ];
}