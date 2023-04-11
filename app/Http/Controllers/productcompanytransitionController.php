<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;




use DataTables;
use Validator;
use App\Models\balance_of_business; 
use App\Models\go_down_stock;


use App\Models\cashtransition; 
use App\Models\unitcoversion;
use App\Models\Productcompany;
use App\Models\Product;  
use App\Models\productcompanyorder;
use App\Models\productcompanytransition;
use App\Models\User;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Redirect;
use PDF;
$jsonmessage=0;
$status=0;

class productcompanytransitionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	       public function index(Request $request)
    {
     
	 $productcompanyorder=  productcompanyorder::with('productcompanytransition','Productcompany','user')->latest()->get();
	  
	
	  
	        if ($request->ajax()) {
                $productcompanyorder=  productcompanyorder::with('productcompanytransition','Productcompany','user')->latest()->get();
            return Datatables::of($productcompanyorder)
                   ->addIndexColumn()
                    ->addColumn('action', function( productcompanyorder $data){ 
   
   if( ($data->type == 1) or ($data->type == 2) or ($data->type == 3)  )
   {
                          $button = '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm">Delete</button>';
                        $button .= '&nbsp;&nbsp;';
                        //$button .= '<button type="button" name="delete" id="'.$data->id.'" class="delete btn btn-danger btn-sm">Delete</button>';
                        return $button;
   }
//     elseif ($data->type == 3)
//    {
// 	 return " Return order.";  
	   
//    }
//  elseif ($data->type == 10)
//    {
// 	 return "Reverse Entry";  
	   
//    }
//  elseif ($data->type == 5)
//    {
// 	 return "Deleted Entry";  
	   
//    }  
   
                    })  
                              


							  ->addColumn('companyname', function (productcompanyorder $productcompanyorder) {
                    return $productcompanyorder->Productcompany->name;
                })
				
									  ->addColumn('entryby', function (productcompanyorder $productcompanyorder) {
                    return $productcompanyorder->user->name;
                })
					

                 ->editColumn('created', function(productcompanyorder $data) {
					
					 return date('d/m/y h:i A', strtotime($data->created_at) );
                    
                })
				
				->editColumn('pdf', function ($productcompanyorder) {
					return '<a   target="_blank"      href="'.route('productcompanytrans.pdf', $productcompanyorder->id).'">Print</a>';
				})
				

					
					
                    ->rawColumns(['action','created','pdf' ])

                    ->make(true);
					
					

        }
      
        return view('productcompanytransition.productcompanytransition', compact('productcompanyorder'));   

    }




	    public function  dropdownlist()
    {
		
		
	 $shopid = Auth()->user()->balance_of_business_id;
     
     
	   $productdata = Product::where('balance_of_business_id',$shopid)->where('softdelete', 0)->orderBy('name')->get(); 
	   
	   $unit = unitcoversion::where('softdelete', 0)->orderBy('name')->get();
	  
		 
		 $Productcompany = Productcompany::where('balance_of_business_id',$shopid)->where('softdelete', 0)->orderBy('name')->get();

			
            return response()->json(['Productcompany' => $Productcompany , 'unit'=>$unit,  'productdata' => $productdata]);

	   
	   
	   
    } 





	public function printvoucher($id)
	{
		
		$order= productcompanyorder::findOrFail($id);
			
		$data= Productcompany::findOrFail($order->productcompany_id);
	
		 $pdf = PDF::loadView('productcompanytransition.slip', compact('data','order' ),
	   [], [
	 'mode'                     => '',
		'format'                   => 'A5',
		'default_font_size'        => '7',
		'default_font'             => 'Times-New-Roman',
		'margin_left'              => 7,
		'margin_right'             => 7,
		'margin_top'               => 7,
		'margin_bottom'            => 7,
	]
	   
	   
	   );
		
		
		 return $pdf->stream('document.pdf');
	
	}





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
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
		
		
  
	$validated = $request->validate([
	
	 	'company_Id',
		'unit_price',
		'quantity',
		'stock',
		'vat',
		'vattk',
		'discount',
		'totaldiscount',
		'amount',
		'adjust',
		'percentofdicountontaotal',
		'grossamount',
		'discountatend',
		'paid',
		'due',
		'totalamount',
'statusvalue',
'medicine_name',
'type',
		
		
    ]);
		
	global $status;	
	$company = Productcompany::findOrFail($request->company_Id);	
	
	$dueamount = $company->due + $request->due;
		

		
		//////////////////////////////////////////////////// insert shuru ///////////////////////
		
		
		 $serialno = productcompanyorder::where('productcompany_id',$request->company_Id)->orderBy('id', 'desc')->first();

 if ($serialno== '')
 {
	 $serial=1;
 }
 else{
$serial= $serialno->serialno+1;
 }		
		
		
		
		
		if($request->type == 1)
		{
		$productorder = new productcompanyorder(); 
		$productorder->user_id  = auth()->user()->id ; //$request->sellerid;
	$productorder->productcompany_id  = $request->company_Id;
	$productorder->serialno  = $serial;
	$productorder->amount  = $request->grossamount; 
	$productorder->balance_of_business_id  = Auth()->user()->balance_of_business_id; 	
	
	
	
	$productorder->discount  = $request->discountatend;
		$productorder->amountafterdiscount	  = $request->totalamount;
	$productorder->comment  =$request->comment; 
	$productorder->debit  = $request->due;
	$productorder->credit  = $request->paid;
	$productorder->balance  =  $request->due + $company->due; 
	$productorder->type  = 1;
	
		

	
	
	
	$due = $request->due;
	$id= $request->company_Id;
	

	$dueamount = $company->due + $request->due;


//// update patient due 
Productcompany::where('id', $request->company_Id )
       ->update([
           'due' => $dueamount
        ]);

/////////// update company balance 
  
    $shopid = Auth()->user()->balance_of_business_id;
  
  $balance =  balance_of_business::findOrFail($shopid);
  

  
  
   balance_of_business::where('id',  $shopid)
  ->update(['balance' =>(   $balance->balance - $request->paid )  ]);	

	$productorder->save();
	
	
// cashtransition

	




		$cashtransition = new cashtransition();
$cashtransition->balance_of_business_id = Auth()->user()->balance_of_business_id;

$cashtransition->User_id = Auth()->user()->id;
$cashtransition->productcompanyorder_id = $productorder->id;

$cashtransition->productcompany_id = $request->company_Id;

$cashtransition->amount = $request->grossamount;
$cashtransition->withdrwal = $request->paid;	

$cashtransition->transtype = 9;

$cashtransition->description = "Buy Product from Company:"  .$company->name ;

$cashtransition->type = 2;
$cashtransition->save(); 













	
	
	
	
	
	

    $order_id = $productorder->id;

    for ($product_id = 0; $product_id < count($request->medicine_name); $product_id++ ) 

	{

		
		
		
		       
		
		
		
       $producttransition = new productcompanytransition; 
	   $producttransition->productcompanyorder_id = $order_id;
	    $producttransition->productcompany_id = $request->company_Id;
	 $producttransition->user_id =  auth()->user()->id ;



$producttransition->balance_of_business_id  = Auth()->user()->balance_of_business_id; 	

	   $producttransition->product_id = $request->medicine_name[$product_id]; // asole medicine_name[] er vetore id neya hoyeche. form bananor somoy name lekha hoyechecilo pore ar change kora hoy nai 





	   $producttransition->unitcoversion_id = $request->unit[$product_id];
	   $producttransition->type = 1;
$producttransition->quantityinbase = $request->quantity[$product_id];











	    
  $producttransition->unirprice =  $request->unit_price[$product_id]; 
		$producttransition->quantity = $request->quantity[$product_id];
		
		 $unitconverter= unitcoversion::findOrFail($request->unit[$product_id])->coversionamount;
		  $quantity = $unitconverter * $request->quantity[$product_id];
				 $unitname=unitcoversion::findOrFail($request->unit[$product_id])->name;
 $producttransition->unitname =  $unitname;
	 
 $producttransition->buyingunit = $request->unit[$product_id];	
		
		
		
		 product::where('id',$request->medicine_name[$product_id] )->increment('stock',$quantity );
		 
		$godownstock = go_down_stock::where('product_id',$request->medicine_name[$product_id] )->where('unitcoversion_id', $request->unit[$product_id] )->where('balance_of_business_id', Auth()->user()->balance_of_business_id )->first();
		 
		 if($godownstock   != null )
		 {
	 go_down_stock::where('id', $godownstock->id )
  ->update(['stock' =>(   $godownstock->stock + $request->quantity[$product_id] )  ]);		 
		 }	else{

$go_down_stock = new go_down_stock();
$go_down_stock->product_id= $request->medicine_name[$product_id];
$go_down_stock->unitcoversion_id = $request->unit[$product_id];
$go_down_stock->user_id= Auth()->user()->id; 

$go_down_stock->balance_of_business_id	= Auth()->user()->balance_of_business_id;
$go_down_stock->stock= $request->quantity[$product_id];
$go_down_stock->save();



		 }			 
	



	
	
		 
		 $qun= $request->quantity[$product_id];
		 
		
		
				 
		
		// $producttransition->amount =  $request->adjust[$product_id];
		


	  $qun= $request->quantity[$product_id];
		 		 if ($request->percentofdicountontaotal > 0)
		 {
			  
			  
			  
			  	 $discount_amount =   $request->grossamount - $request->totalamount;
			 
			 $percentage =      (   $discount_amount * 100)/ $request->grossamount   ; 
			 
			 
			 
			  
			 $discount = (($request->unit_price[$product_id] * $qun)* ($percentage/100) ) ; 
			  
			  
			  
			  
			  
			  
			  
			  
		//	 $discount = (($request->unit_price[$product_id] * $qun)* ($request->percentofdicountontaotal/100) ) ; 
			
		
             $amount = ($request->unit_price[$product_id] * $qun) - $discount; 
			 
			// $producttransition->discountpercentage = $request->percentofdicountontaotal;
			 
			 $producttransition->discountpercentage = $discount;
			 
			 $producttransition->discount	 = $discount;
			
			
		     $producttransition->amount = $request->amount[$product_id];
		 $producttransition->finalamountafterdiscount = $amount;
			 
		}	 else {
				 
		 $producttransition->discountpercentage = $request->discount[$product_id];
		 $producttransition->discount	 = $request->totaldiscount[$product_id];
		 $producttransition->amount = $request->amount[$product_id];
		  $producttransition->finalamountafterdiscount	 = $request->adjust[$product_id];
		 }



		 

		  $producttransition->save(); 
		 
	


	}		
		
				
		}	
		
		
		
	
// retun product to company 

		if($request->type == 3)
		{
		

	    
    for ($product_id = 0; $product_id < count($request->medicine_name); $product_id++ ) 

	{
		
		
			$godownstock = go_down_stock::where('product_id',$request->medicine_name[$product_id] )->where('unitcoversion_id', $request->unit[$product_id] )->where('balance_of_business_id', Auth()->user()->balance_of_business_id )->first();


		 if (($godownstock   == null ))
		 {
			 		
$status =1;
goto flag;			 
		 }
		 else 
		 {
			 
			 if (  $request->quantity[$product_id] >  $godownstock->stock  )    
			 {

$status =1;
goto flag;	
		 }}
	 

		
		
	}
















				
		$productorder = new productcompanyorder(); 
		$productorder->user_id  = auth()->user()->id ; //$request->sellerid;
	$productorder->productcompany_id  = $request->company_Id;
	$productorder->serialno  = $serial;

	$productorder->balance_of_business_id  = Auth()->user()->balance_of_business_id;
	
		$productorder->amount  = $request->grossamount; 
	
	
	
	
	$productorder->discount  = $request->discountatend;
		$productorder->amountafterdiscount	  = $request->totalamount;
	
	
	
	
	$productorder->comment  =$request->comment; 
	$productorder->debit  = 0;
	$productorder->credit  = $request->totalamount; 
	$productorder->balance  =   $company->due - $request->totalamount; 
	$productorder->type  = 3;
	
		

	
	
	
	$due = $request->due;
	$id= $request->company_Id;
	

	$dueamount = $company->due - $request->totalamount; 


//// update company due 
Productcompany::where('id', $request->company_Id )
       ->update([
           'due' => $dueamount
        ]);


	$productorder->save();
	
	

	
	
	
	
	
	
	
	
	
	
	
	

    $order_id = $productorder->id;

    for ($product_id = 0; $product_id < count($request->medicine_name); $product_id++ ) 

	{

		
		
		
		       
		
		
		
       $producttransition = new productcompanytransition; 
	   $producttransition->productcompanyorder_id = $order_id;
	    $producttransition->productcompany_id = $request->company_Id;
	 $producttransition->user_id =  auth()->user()->id ;
	$producttransition->balance_of_business_id  = Auth()->user()->balance_of_business_id;
	
	 
	   $producttransition->product_id = $request->medicine_name[$product_id]; // asole medicine_name[] er vetore id neya hoyeche. form bananor somoy name lekha hoyechecilo pore ar change kora hoy nai 
	    
  $producttransition->unirprice =  $request->unit_price[$product_id]; 
		$producttransition->quantity = $request->quantity[$product_id];
		
		
		 $unitconverter= unitcoversion::findOrFail($request->unit[$product_id])->coversionamount;
		  $quantity = $unitconverter * $request->quantity[$product_id];
				 $unitname=unitcoversion::findOrFail($request->unit[$product_id])->name;
 $producttransition->unitname =  $unitname;
	 
 $producttransition->buyingunit = $request->unit[$product_id];			
		   $producttransition->unitcoversion_id = $request->unit[$product_id];
	   $producttransition->type = 3;
$producttransition->quantityinbase = $request->quantity[$product_id];

	
		
		
		
		
		
		
		 product::where('id',$request->medicine_name[$product_id] )->decrement('stock',$quantity );
		 
		 


		$godownstock = go_down_stock::where('product_id',$request->medicine_name[$product_id] )->where('unitcoversion_id', $request->unit[$product_id] )->where('balance_of_business_id', Auth()->user()->balance_of_business_id )->first();
		 
		 if($godownstock   != null )
		 {
	 go_down_stock::where('id', $godownstock->id )
  ->update(['stock' =>(   $godownstock->stock - $request->quantity[$product_id] )  ]);		 
		 }	
















	
		 
		
		 	  $qun= $request->quantity[$product_id];
		 		 if ($request->percentofdicountontaotal > 0)
		 {
			  
			 $discount = (($request->unit_price[$product_id] * $qun)* ($request->percentofdicountontaotal/100) ) ; 
			
		
             $amount = ($request->unit_price[$product_id] * $qun) - $discount; 
			 
			 $producttransition->discountpercentage = $request->percentofdicountontaotal;
			 $producttransition->discount	 = $discount;
			
			
		     $producttransition->amount = $request->amount[$product_id];
		 $producttransition->finalamountafterdiscount = $amount;
			 
		}	 else {
				 
		 $producttransition->discountpercentage = $request->discount[$product_id];
		 $producttransition->discount	 = $request->totaldiscount[$product_id];
		 $producttransition->amount = $request->amount[$product_id];
		  $producttransition->finalamountafterdiscount	 = $request->adjust[$product_id];
		 }
		
		
				 
		
		 $producttransition->amount =  $request->adjust[$product_id];
		

		 

		  $producttransition->save(); 
		 
	


	}		
		
				
		}	
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
		
flag:		

});	



global $status; 


if ($status == 1)

{
        return response()->json(['success' => 'Products are not avilable in Go-Down Stock']);	
	
}

        return response()->json(['success' => 'Data Added successfully.']);



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
	 
     */
	 
	 
	 
	 
	 
	public function purchase()
{

   $company = Productcompany::where('balance_of_business_id', Auth()->user()->balance_of_business_id )->orderBy('name')->get();
	return view('product.datepickpurchase', compact('company'));   



}	
 
	 
	 
	 
public function purchasefetch(Request $request)
{
	
			        $start = date("Y-m-d",strtotime($request->input('startdate')));
        $end = date("Y-m-d",strtotime($request->input('enddate')."+1 day"));
	 $e = date("Y-m-d",strtotime($request->input('enddate')));


if ($request->company == 99999999999999   )
{


	$order= productcompanyorder::whereBetween('created_at',[$start,$end])	
	->get();

$producttransition = productcompanytransition::with('Product','unitcoversion')	
	 ->select( 'product_id','unitcoversion_id',   \DB::raw( 'SUM(quantity) as quantity'  ) , \DB::raw( 'SUM(quantityinbase) as quantityinbase'  ) , \DB::raw( 'SUM(amount) as 	amount'  )  ,

\DB::raw( 'SUM(discount) as 	discount'),\DB::raw( 'SUM(finalamountafterdiscount	) as 	finalamountafterdiscount'),



	 )
->whereBetween('created_at',[$start,$end])	
->where('type', 1)
->where('balance_of_business_id', Auth()->user()->balance_of_business_id )
->groupBy('product_id','unitcoversion_id')
				
 ->get();	
 
 
 
 
 
 $returnproduct = productcompanytransition::with('Product','unitcoversion')	
	 ->select( 'product_id','unitcoversion_id',   \DB::raw( 'SUM(quantity) as quantity'  ) , \DB::raw( 'SUM(quantityinbase) as quantityinbase'  ) , \DB::raw( 'SUM(amount) as 	amount'  )  ,

\DB::raw( 'SUM(discount) as 	discount'),\DB::raw( 'SUM(finalamountafterdiscount	) as 	finalamountafterdiscount'),



	 )
->whereBetween('created_at',[$start,$end])	
->where('type', 3)
->where('balance_of_business_id', Auth()->user()->balance_of_business_id )
->groupBy('product_id','unitcoversion_id')
				
 ->get();
 
 

$product = product::with('productpriceaccunit')->where('balance_of_business_id', Auth()->user()->balance_of_business_id )->orderBy('name')->get();	

$company= "All";

	 $pdf = PDF::loadView('product.purchasereport', compact('producttransition','start','e','product','returnproduct','company', 'order' ),
   [], [
 'mode'                     => '',
	'format'                   => 'A5',
	'default_font_size'        => '7',
	'default_font'             => 'Times-New-Roman',
	'margin_left'              => 7,
	'margin_right'             => 7,
	'margin_top'               => 7,
	'margin_bottom'            => 7,
]
   
   
   );
	
	
	 return $pdf->stream('document.pdf');

}

else{
	

	
	$order= productcompanyorder::whereBetween('created_at',[$start,$end])->where('productcompany_id', $request->company )	
	->get();




$producttransition = productcompanytransition::with('Product','unitcoversion')	
	 ->select( 'product_id','unitcoversion_id',   \DB::raw( 'SUM(quantity) as quantity'  ) , \DB::raw( 'SUM(quantityinbase) as quantityinbase'  ) , \DB::raw( 'SUM(amount) as 	amount'  )  ,

\DB::raw( 'SUM(discount) as 	discount'),\DB::raw( 'SUM(finalamountafterdiscount	) as 	finalamountafterdiscount'),



	 )
->whereBetween('created_at',[$start,$end])	
->where('type', 1)
->where('productcompany_id', $request->company )
->where('balance_of_business_id', Auth()->user()->balance_of_business_id )
->groupBy('product_id','unitcoversion_id')
				
 ->get();	
 
 
 
 
 
 $returnproduct = productcompanytransition::with('Product','unitcoversion')	
	 ->select( 'product_id','unitcoversion_id',   \DB::raw( 'SUM(quantity) as quantity'  ) , \DB::raw( 'SUM(quantityinbase) as quantityinbase'  ) , \DB::raw( 'SUM(amount) as 	amount'  )  ,

\DB::raw( 'SUM(discount) as 	discount'),\DB::raw( 'SUM(finalamountafterdiscount	) as 	finalamountafterdiscount'),



	 )
->whereBetween('created_at',[$start,$end])	
->where('type', 3)
->where('productcompany_id', $request->company )
->where('balance_of_business_id', Auth()->user()->balance_of_business_id )
->groupBy('product_id','unitcoversion_id')
				
 ->get();
 
 

$company = productcompany::findOrFail($request->company)->name;	

$product = product::with('productpriceaccunit')->where('balance_of_business_id', Auth()->user()->balance_of_business_id )->orderBy('name')->get();	

	 $pdf = PDF::loadView('product.purchasereport', compact('producttransition','start','e','product','returnproduct','company','order' ),
   [], [
 'mode'                     => '',
	'format'                   => 'A5',
	'default_font_size'        => '7',
	'default_font'             => 'Times-New-Roman',
	'margin_left'              => 7,
	'margin_right'             => 7,
	'margin_top'               => 7,
	'margin_bottom'            => 7,
]
   
   
   );
	
	
	 return $pdf->stream('document.pdf');
	
	
	
}

	
}	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
	 
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   public function destroy($id)
    {
             
		
		
		$data = productcompanyorder::with('productcompanytransition','Productcompany','user')->findOrFail($id);

if ($data->type == 1 )
{
	  $i = $data->productcompany_id;
$presentdue = Productcompany::findOrFail($data->productcompany_id)->due;

 Productcompany::where('id', $i )
  ->update(['due' =>(   $presentdue - $data->debit )  ]);	

/////////// update company balance 
  
      $shopid = Auth()->user()->balance_of_business_id;
  
  $balance =  balance_of_business::findOrFail($shopid);
  

  
  
   balance_of_business::where('id',  $shopid)

  ->update(['balance' =>(   $balance->balance + $data->credit )  ]);	

				
				 foreach ($data->productcompanytransition as $d)
				 {
					  $unitconverter= unitcoversion::findOrFail($d->buyingunit )->coversionamount;
		  $quantity = $unitconverter * $d->quantity;
		
					 
	 product::where('id',$d->product_id )->decrement('stock',$quantity );

		$godownstock = go_down_stock::where('product_id',$d->product_id )->where('unitcoversion_id', $d->unitcoversion_id )->where('balance_of_business_id', Auth()->user()->balance_of_business_id )->first();
		 
		 if($godownstock   != null )
		 {
	 go_down_stock::where('id', $godownstock->id )
  ->update(['stock' =>(   $godownstock->stock - $d->quantity )  ]);		 
		 }
					 
				 }


		$producttransition= productcompanytransition::where('productcompanyorder_id', $id )->delete();
		
			 cashtransition::where('productcompanyorder_id', $id )->delete();
		
	
        
		$data->delete();
	   
				}


if ($data->type == 3)
{
	$i = $data->productcompany_id;
$presentdue = Productcompany::findOrFail($data->productcompany_id)->due;

Productcompany::where('id', $i )
->update(['due' =>(   $presentdue + $data->credit )  ]);	

/////////// update company balance 

	$shopid = Auth()->user()->balance_of_business_id;

$balance =  balance_of_business::findOrFail($shopid);




 balance_of_business::where('id',  $shopid)

->update(['balance' =>(   $balance->balance - $data->credit )  ]);	

			  
			   foreach ($data->productcompanytransition as $d)
			   {
					$unitconverter= unitcoversion::findOrFail($d->buyingunit )->coversionamount;
		$quantity = $unitconverter * $d->quantity;
	  
				   
   product::where('id',$d->product_id )->decrement('stock',$quantity );

	  $godownstock = go_down_stock::where('product_id',$d->product_id )->where('unitcoversion_id', $d->unitcoversion_id )->where('balance_of_business_id', Auth()->user()->balance_of_business_id )->first();
	   
	   if($godownstock   != null )
	   {
   go_down_stock::where('id', $godownstock->id )
->update(['stock' =>(   $godownstock->stock + $d->quantity )  ]);		 
	   }
				   
			   }


	  $producttransition= productcompanytransition::where('productcompanyorder_id', $id )->delete();
	  
		   cashtransition::where('productcompanyorder_id', $id )->delete();
	  
  
	  
	  $data->delete();
	 
			  }


	   

	   
	  return response()->json(['success' => 'Reverse entry added successfully .']);   
    }
}

