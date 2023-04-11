<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DataTables;
use Validator;
use App\Models\balance_of_business; 
use App\Models\customer;
use DB;
use App\Models\User; 
use Illuminate\Support\Facades\Hash;
class balanceofbusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	  public function index(Request $request)
    {
    
	$dealer = Auth()->user()->balance_of_business_id;
	
	if ($dealer != 1)
	{
		abort(404);
		
	}
	
	  $subdealer =  balance_of_business::where('softdelete',0)->orderBy('shopname')->get();
	
	
	
	
	  
	        if ($request->ajax()) {
					
	  $subdealer =  balance_of_business::where('softdelete',0)->orderBy('shopname')->get();

		 
            return Datatables::of($subdealer)
                   ->addIndexColumn()
				   

                    ->addColumn('action', function( balance_of_business $data){ 
   
                          $button = '<button type="button" name="edit" id="'.$data->id.'" class="edit btn btn-primary btn-sm">Edit</button>';
                        $button .= '&nbsp;&nbsp;';
                        $button .= '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm">Delete</button>';
                        return $button;
                    })  

					
					
                    ->rawColumns(['action'])
                    ->make(true);
        }
		

		return view('business_and_sub_dealer.subdealer', compact('subdealer'));   
	
	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
	 public function dropdown_list()
	 {
	$customer = customer::where('balance_of_business_id',1)->where('dealer_id', null )->where('softdelete',0)->get();	 
		 
	return response()->json(['customer' => $customer ]);
	 
	 }
	 
	 
	 
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		   DB::transaction(function () use ($request) {
			$dealer = Auth()->user()->balance_of_business_id;
	
	if ($dealer != 1)
	{
		abort(404);
		
	}
		
$customer = customer::findOrFail($request->customer_id);		
		
		
		
		
		
       $balance_of_business = new balance_of_business();
	 $balance_of_business->customer_id = $request->customer_id;  
	   $balance_of_business->shopname = $customer->name;
	    $balance_of_business->mobile = $customer->mobile;
		 $balance_of_business->address = $customer->address;
		  $balance_of_business->openingbalance = $customer->openingbalance;
		    $balance_of_business->balance = $customer->presentduebalance; 
			
			
			
		  $balance_of_business->save(); 
		  
		  
		  
	$user = new User();

$user->name = $customer->name;
$user->email = $request->email;
$user->mobile = $customer->mobile;
$user->password = Hash::make($request->password);  
 $user->balance_of_business_id = $balance_of_business->id;
$user->role=1; 
$user->save();



                           	   	customer::whereId($request->customer_id)
  ->update(['dealer_id' => $balance_of_business->id]);
    	  
		  
});			  
		  
		  
		  
		  
     return response()->json(['success' => 'Data Added successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
	
	
	
    {
		
		
			$dealer = Auth()->user()->balance_of_business_id;
	
	if ($dealer != 1)
	{
		abort(404);
		
	}
		
                    $data = balance_of_business::findOrFail($id);
			

            return response()->json(['data' => $data  ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
      public function update(Request $request)
    {
  
  
  
  
  
  	$dealer = Auth()->user()->balance_of_business_id;
	
	if ($dealer != 1)
	{
		abort(404);
		
	}
  
  
        
    $rules = array(
            'name'    =>  'required',
            'address'     =>  'required',
            'mobile'         =>  'required',
			'ob'   =>  'required',
			
			
			
        );

            $error = Validator::make($request->all(), $rules);

            if($error->fails())
            {
                return response()->json(['errors' => $error->errors()->all()]);
            }
       

        $form_data = array(
            'shopname'        =>  $request->name,
            'address'         =>  $request->address,
           'mobile' =>$request->mobile,
		  
		   		   'openingbalance' =>$request->ob,

								   
        );
        balance_of_business::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
		
			$dealer = Auth()->user()->balance_of_business_id;
	
	if ($dealer != 1)
	{
		abort(404);
		
	}
		
		
		
                           	   	balance_of_business::whereId($id)
  ->update(['softdelete' => '1']);
    }
}
