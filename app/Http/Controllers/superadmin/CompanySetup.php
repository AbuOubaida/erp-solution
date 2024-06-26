<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use App\Models\company_info;
use App\Models\company_type;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rule;
use function PHPUnit\Framework\isEmpty;

class CompanySetup extends Controller
{
    private $imagePath = "image/logo/";
    //
    public function index()
    {
        try {
            $companyTypes = company_type::with(['createdBY','updatedBY'])->get();
            $companies = company_info::with(['createdBY','updatedBY','companyType'])->get();
            return view('back-end/programmer/company-setup',compact('companyTypes','companies'));
        }catch (\Throwable $exception)
        {
            return back()->with('error',$exception->getMessage())->withInput();
        }

    }
    public function companyTypeAdd(Request $request)
    {
        try {
            if ($request->isMethod('post'))
            {
                $this->companyTypeStore($request);
            }
            return $this->index();
        }catch (\Throwable $exception)
        {
            return back()->with('error',$exception->getMessage())->withInput();
        }
    }

    private function companyTypeStore(Request $request)
    {
        try {
            $request->validate([
                'company_type_title'  => ['required', 'string', 'max:255','unique:'.company_type::class],
                'company_type_status' => ['required', 'numeric'],
                'description' => ['string', 'sometimes','nullable'],
            ]);
            if ($request->isMethod('post'))
            {
                $user = Auth::user();
                extract($request->post());
                company_type::create([
                    'company_type_title'=>  $company_type_title,
                    'status'            =>  ($company_type_status == 1 || $company_type_status == 3)? $company_type_status:0,
                    'remarks'           =>  $description,
                    'created_by'        =>  $user->id,
                    'created_at'        =>  now(),
                ]);
                return back()->with('success','Data insert successfully');
            }
            return back()->withInput();
        }catch (\Throwable $exception)
        {
            return back()->with('error',$exception->getMessage())->withInput();
        }
    }

    public function companyTypeEdit(Request $request, $companyTypeID)
    {
        try {
            $id = Crypt::decryptString($companyTypeID);
            if ($request->isMethod('put'))
            {
                $this->companyTypeUpdate($request, $id);
            }
            $editID = company_type::where('id',$id)->first();
            if (!$editID)
            {
                return back()->with('error','Data Not Found!');
            }
            $companyTypes = company_type::with(['createdBY','updatedBY'])->get();
            return view('back-end/programmer/company-type-edit',compact('editID','companyTypes'));
        }catch (\Throwable $exception)
        {
            return back()->with('error',$exception->getMessage())->withInput();
        }
    }

    private function companyTypeUpdate(Request $request, $companyTypeID)
    {
        try {
            $user = Auth::user();
            $request->validate([
                'company_type_title'  => ['required', 'string', 'max:255',Rule::unique('company_types')->ignore($companyTypeID)],
                'company_type_status' => ['required', 'numeric'],
                'description' => ['string', 'sometimes','nullable'],
            ]);
            extract($request->post());
            company_type::where('id',$companyTypeID)->update([
                'company_type_title'=>  $company_type_title,
                'status'            =>  ($company_type_status == 1 || $company_type_status == 3)? $company_type_status:0,
                'remarks'           =>  $description,
                'updated_by'        =>  $user->id,
                'updated_at'        =>  now(),
            ]);
            return back()->with('success','Data updated successfully.');
        }catch (\Throwable $exception)
        {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function companyTypeDelete(Request $request)
    {
        try {
            $request->validate([
                'id'    =>  ['string','required'],
            ]);
            extract($request->post());
            $deleteID = Crypt::decryptString($id);
            $company = company_type::with(['companies'])->find($deleteID);
            if(count($company->companies))
            {
                return back()->with('error','A relationship exists between other tables. Data delete not possible');
            }
            company_type::where('id',$deleteID)->delete();
            return back()->with('success','Data deleted successfully.');
        }catch (\Throwable $exception)
        {
            return back()->with('error', $exception->getMessage());
        }
    }

    public function companyAdd(Request $request)
    {
        try {
            if ($request->isMethod('post'))
            {
                $this->companyStore($request);
            }
            return $this->index();
        }catch (\Throwable $exception)
        {
            return back()->with('error',$exception->getMessage())->withInput();
        }
    }
    private function companyStore(Request $request)
    {
        $request->validate([
            'company_name'  => ['required', 'string', 'max:255','unique:company_infos,company_name'],
            'company_short_name' => ['required', 'string','regex:/(^[A-Za-z0-9 ]+$)+/','unique:company_infos,company_code'],
            'company_type_id'   =>  ['required', 'numeric','exists:company_types,id'],
            'contract_person' => ['string', 'required','max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:company_infos,email'],
            'contract_person_phone' => ['required', 'numeric'],
            'company_phone' => ['sometimes','nullable', 'numeric', 'unique:company_infos,phone'],
            'logo'    =>  ['sometimes','nullable','max:2048'],
            'logo_sm'    =>  ['sometimes','nullable','max:1024'],
            'logo_icon'    =>  ['sometimes','nullable','max:1024'],
            'cover'    =>  ['sometimes','nullable','max:2048'],
            'location'    =>  ['sometimes','nullable','string'],
            'remarks'    =>  ['sometimes','nullable','string'],
        ]);
        try {
            extract($request->post());
            $user = Auth::user();
            if ($request->hasFile('logo'))
            {
                $file = $request->file('logo');
                $logo_name = $company_short_name."_logo_".$file->getClientOriginalName();
                $logo_location = $file->move($this->imagePath,$logo_name);

            }
            if ($request->hasFile('logo_sm'))
            {
                $file = $request->file('logo_sm');
                $logo_sm_name = $company_short_name."_logo_sm_".$file->getClientOriginalName();
                $logo_sm_location = $file->move($this->imagePath,$logo_sm_name);

            }
            if ($request->hasFile('logo_icon'))
            {
                $file = $request->file('logo_icon');
                $logo_icon_name = $company_short_name."_logo_icon_".$file->getClientOriginalName();
                $logo_icon_location = $file->move($this->imagePath,$logo_icon_name);
            }
            if ($request->hasFile('cover'))
            {
                $file = $request->file('cover');
                $cover_name = $company_short_name."_cover_".$file->getClientOriginalName();
                $cover_location = $file->move($this->imagePath,$cover_name); // Adjust the

            }
            company_info::create([
                'status'=>1,
                'company_name'=>$company_name,
                'company_type_id'=>$company_type_id,
                'contract_person_name'=>$contract_person,
                'company_code'=>$company_short_name,
                'phone'=>$company_phone,
                'contract_person_phone'=>$contract_person_phone,
                'email'=>$email,
                'location'=>$location,
                'remarks'=>$remarks,
                'logo'=>isset($logo_name)?$this->imagePath.$logo_name:'',
                'logo_sm'=>isset($logo_sm_name)?$this->imagePath.$logo_sm_name:'',
                'logo_icon'=>isset($logo_icon_name)?$this->imagePath.$logo_icon_name:'',
                'cover'=>isset($cover_name)?$this->imagePath.$cover_name:'',
                'created_by'=>$user->id,
                'created_at'=>now(),
            ]);
            return back()->with('success','Data add successfully.');
        }catch (\Throwable $exception)
        {
            return back()->with('error',$exception->getMessage())->withInput();
        }
    }

    public function companyEdit(Request $request, $companyID)
    {
        try {
            if ($request->isMethod('put'))
            {
                $this->companyUpdate($request, $companyID);
            }
            $companyTypes = company_type::where('status',1)->get();
            $companies = company_info::with(['createdBY','updatedBY','companyType'])->get();
            $c_id = Crypt::decryptString($companyID);
            $edit_company = company_info::where('id',$c_id)->first();
            if (!$edit_company)
            {
                return back()->with('error','Company Not Found!');
            }
            return view('back-end/programmer/company-edit',compact('edit_company','companies','companyTypes'));
        }catch (\Throwable $exception)
        {
            return back()->with('error',$exception->getMessage())->withInput();
        }
    }

    private function companyUpdate(Request $request, $companyID)
    {
        $id = Crypt::decryptString($companyID);
        $request->validate([
            'company_name'  => ['required', 'string', 'max:255',Rule::unique('company_infos','company_name')->ignore($id)],
            'company_short_name' => ['required', 'string','regex:/(^[A-Za-z0-9 ]+$)+/',Rule::unique('company_infos','company_code')->ignore($id)],
            'company_type_id'   =>  ['required', 'numeric','exists:company_types,id'],
            'contract_person' => ['string', 'required','max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('company_infos','email')->ignore($id)],
            'contract_person_phone' => ['required', 'numeric'],
            'company_phone' => ['sometimes','nullable', 'numeric', Rule::unique('company_infos','phone')->ignore($id)],
            'logo'    =>  ['sometimes','nullable','max:2048'],
            'logo_sm'    =>  ['sometimes','nullable','max:1024'],
            'logo_icon'    =>  ['sometimes','nullable','max:1024'],
            'cover'    =>  ['sometimes','nullable','max:2048'],
            'location'    =>  ['sometimes','nullable','string'],
            'remarks'    =>  ['sometimes','nullable','string'],
        ]);
        try {
            
        }catch (\Throwable $exception)
        {
            return back()->with('error',$exception->getMessage())->withInput();
        }
    }
}
