<?php

namespace App\Models;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $fillable = ['name','shortened_name','name_es', 'address','address_es', 'telephone', 'tax_code','user_id','qualification_id', 'status', 'fax'];

    public static function rules($id = 0)
    {
        return [
            'name' => 'bail|required|max:255|unique:companies,name' . ($id == 0 ? '' : ',' . $id),
            'shortened_name'=>'bail|required|unique:companies,shortened_name' . ($id == 0 ? '' : ',' . $id),
            /*'telephone' => 'bail|required|min:10|regex:/(0)[0-9]{9}/|numeric|unique:companies,telephone' . ($id == 0 ? '' : ',' . $id),*/
            'telephone' => 'bail|required|min:10|unique:companies,telephone' . ($id == 0 ? '' : ',' . $id),
            'tax_code' => 'bail|required|min:10|max:14|unique:companies,tax_code' . ($id == 0 ? '' : ',' . $id),
            'address' => 'bail|required',

        ];
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function getCompaniesForOption()
    {
        return $this->pluck('name', 'id')->toArray();
    }

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public static function companysOption(){
        $departmentIDs = Department::departmentsRole();
        $companyIds = [];
        foreach ($departmentIDs as $departmentID){
            array_push($companyIds,Department::find($departmentID)->company_id);
        }
        $company = Company::whereIn('id',$companyIds)->pluck('shortened_name', 'id')->toArray();
        return $company;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    public function departmentOffice()
    {
        return $this->hasMany(Department::class)->whereIn('type', [\App\Define\Department::FUNCTIONAL_OFFICE, \App\Define\Department::DECLARATION_OFFICE]);
    }

    public function concurrentContracts()
    {
        return $this->hasMany(ConcurrentContract::class);
    }

    public static function countActiveCompanies()
    {
        return self::where('status', 1)
            ->count();
    }
}
