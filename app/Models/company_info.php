<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class company_info extends Model
{
    use HasFactory;
    protected $fillable = ['status','company_name','company_type_id','contract_person_name','company_code','phone','contract_person_phone','email','location','remarks','logo','logo_sm','logo_icon','cover','created_by','updated_by','created_at','updated_at'];

    public function createdBY()
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy()
    {
        return $this->belongsTo(User::class,'updated_by');
    }
    public function companyType()
    {
        return $this->belongsTo(company_type::class,'company_type_id');
    }
}
