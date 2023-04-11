<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>

table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid black;
  text-align: left;
  padding: 2px;
}



#c{




margin: 0 auto;
position:relative;

}





#c img {
width:100%;
}

#c::before {
content:'';
position:absolute;
top:50px;
left:0;
background-image: url("img/watermark.jpg");
background-position:center;
background-repeat:no-repeat;
width: 100%;
height: 100%;;
opacity: .1;
}

#m{
  
 background-color:red;;

}





</style>

</head>
<body style="font-family: Times New Roman;">
<div id="c" >
<div id="head" >
<img width="500px;"   src="img/logo.jpg" >
<hr>
</div>



    <div style="height:10px;" id="one" >
    <div style="width:30%; float:left;" >

      <b><u>Purchase Report:</u></b>

	</div>




  </div>


    <div style="height:10px;" id="two" >
    <div style="width:40%; float:left;" >
      <b>Company Name :  {{$company}}    </b> 
    </div>

	


	


  </div>
     


  
 <b>Transition: From  <?php echo date('d/m/Y ', strtotime($start)); ?> to <?php echo date('d/m/Y ', strtotime($e)); ?> </b><p>

  
  
  
 <br> 


<b>  Purchase Report </b>







<table>
    
  <thead>	
    <tr>
    <th> No.</th>
     <th style="width:40px;" >Voucher No.</th>
   <th style="width:40px;" >Company Name</th>
    <th style="width:40px;" > Date </th> 
    <th style="width:40px;" > Type </th> 
      <th style="width:150px;" >
    
      Products
      
     </th>
  
      <th style="width:100px;"  >Gross Amnt(TK)</th>
      <th style="width:100px;"  >Total Gr.(TK)</th>
        <th style="width:100px;"  >Discount </th>
        <th style="width:100px;"  > Total Dis </th>
        <th style="width:100px;"  >Payable Amnt. </th>
        <th style="width:100px;"  >Total Payable Amnt. </th>
       <th style="width:100px;"  >Paid(TK)</th>
       <th style="width:100px;"  > Total Paid  </th>  
          <th style="width:100px;"  >Due(TK)</th>
       

  <?php $i=1; $sum=0; $discount=0; $gross_amnt=0; $recv_amnt=0; $due=0; $paid=0; $pay_in_cash=0; $pay_id_adv = 0;  $ref=0; ?>
    </tr>
    </thead>
   @foreach ( $order as $t )
  
  
    <tr>
    
  <td> {{$i}}</td>
   <td> {{$t->id}} </td>
   <td> {{$t->productcompany->name}} </td>
   <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $t->created_at)->format('d/m/y') }}</td>
   <td>

<?php if( $t->type == 1)
{ ?>
  Purchase
<?php }elseif( $t->type == 2 )
{ ?>

Paid to Company
<?php }elseif( $t->type == 4 )
{ ?>
Return Money from Company 
<?php }
elseif( $t->type == 3 )
{ ?>
  Return Product
<?php }
?>

   </td>
   <?php $i++ ;?>
      <td> 
  
  <table>
    <tr>
      <th style="width:180px;" > Product Name </th>
      <th>Qun.</th>
    <th>Unit Pr.</th>
    <th>Unit</th>
      <th>Gross Pr.(TK)</th>
     <th>Dis.(TK) </th>
     
     <th>Payable Amount(TK)</th> 
    </tr>
    
  
   
    @foreach ( $t->productcompanytransition as $tr )
    <tr>
      <td> {{$tr->product->name}}</td>
     <td><?php echo round($tr->quantity,2); ?> </td>
     <td><?php echo round($tr->unirprice,2); ?> </td>
   <td>{{$tr->unitname }} </td> 
      <td><?php echo round($tr->amount,2); ?></td>
     <td><?php echo round($tr->discount,2); ?></td>
     
     <td><?php echo round($tr->finalamountafterdiscount,2); ?></td>  
     <?php    
     ?>   
    </tr>
  @endforeach  
  </table>
  
  </td>
    
  
  
  
  
   <td><?php if($t->type== 1) {$gross_amnt = $gross_amnt+ $t->amount;} elseif($t->type== 3){$gross_amnt = $gross_amnt- $t->amount;} echo round($t->amount,2); ?> </td>
   <td><?php echo round($gross_amnt,2); ?></td>
   <td><?php if($t->type== 1) { $discount= $discount+ $t->discount;} elseif($t->type== 3){$discount= $discount- $t->discount;}    echo round($t->discount,2); ?> </td>
   <td><?php echo round($discount,2); ?></td>
  <td><?php if($t->type== 1) { $recv_amnt= $recv_amnt+ $t->amountafterdiscount; } elseif($t->type== 3){$recv_amnt= $recv_amnt - $t->amountafterdiscount; }  echo round($t->amountafterdiscount,2); ?></td>
  <td><?php   echo round($recv_amnt,2); ?></td>   
  <td><?php if($t->type== 1) {$paid= $paid + $t->credit; echo round($t->credit,2);}    ?></td> 
  <td><?php echo round($paid,2); ?></td> 
  <td><?php echo round($t->debit,2); ?></td>    
    </tr>
  
  @endforeach 
  
  
    {{-- <tr>
    <th style="width:40px;" > Total</th>
     <th  >NA</th>
      <th  >NA</th>
   <th style="width:40px;" >NA</th>
    
      <th style="width:150px;" >
    
     NA
      
     </th>
  
      <th style="width:100px;"  >{{$gross_amnt}}</th>
        <th style="width:100px;"  >{{$discount}} </th>
            <th style="width:100px;"  >{{$gross_amnt - $discount}} </th>
       <th style="width:100px;"  >{{$pay_in_cash}}</th>
          <th style="width:100px;"  >{{$pay_id_adv}}</th>
          <th style="width:100px;"  >{{$due}}</th>
       
     <th style="width:100px;"  > Total Balance  </th>
  
    </tr> --}}
  
  
  
  
  
  
  
  
  
  
  
  </table>






<p>






<b> Product Purchased by quantity </b> <br>





<table>

<thead>
  <tr>
     
	
 <th style="width:40px;" >	Product Name</th>
  
    <th style="width:100px;" >
	
Unit 
    
	 </th>
	  <th style="width:100px;"  > Qun.  </th>
    <th style="width:100px;"  >Qun.(Base) </th>
	<th style="width:100px;"  > Gross Amnt.(TK) </th>
<th style="width:100px;"  > Dis.(TK) </th>
<th style="width:100px;"  > Receiveable Amnt.(TK) </th>
  </tr>
  </thead>
 @foreach ( $producttransition as $p )
 <tr>
 <td>{{$p->Product->name}} </td>
 <td>{{$p->unitcoversion->name}} </td>
   <td><?php echo round($p->quantity,2); ?> </td>
   <td> <?php   $amnt=  $p->quantity * $p->unitcoversion->coversionamount ;     ?> {{ $amnt }}  {{ $p->unitcoversion->basicunit->name }}   </td>

    <td><?php echo round($p->amount,2); ?> </td>  
    <td><?php echo round($p->discount,2); ?> </td>
    <td><?php echo round($p->finalamountafterdiscount,2); ?> </td>
	 
	 
  </tr>

@endforeach 
</table>
	
	
	
	
	<p>
	
<b> Return Product to Company</b> 


<table>






<thead>
  <tr>
     
	
 <th style="width:40px;" >	Product Name</th>
  
    <th style="width:100px;" >
	
Unit 
    
	 </th>
	  <th style="width:100px;"  > Qun.  </th>
    <th style="width:100px;"  >Qun.(Base) </th>
	<th style="width:100px;"  > Gross Amnt.(TK) </th>
<th style="width:100px;"  > Dis.(TK) </th>
<th style="width:100px;"  > Return Amnt.(TK) </th>
  </tr>
  </thead>
 @foreach ( $returnproduct as $p )
 <tr>
 <td>{{$p->Product->name}} </td>
 <td>{{$p->unitcoversion->name}} </td>
   <td><?php echo round($p->quantity,2); ?> </td>
   <td> <?php   $amnt=  $p->quantity * $p->unitcoversion->coversionamount ;     ?> {{ $amnt }}  {{ $p->unitcoversion->basicunit->name }}   </td>
    <td><?php echo round($p->amount,2); ?> </td>  
    <td><?php echo round($p->discount,2); ?> </td>
    <td><?php echo round($p->finalamountafterdiscount,2); ?> </td>
	 
	 
  </tr>

@endforeach 
</table>	
	
	
	
	
	
	
	
	
	
	
	
	<p>
	
	
	
	
	
	

	
	
	
	
	
	
	
	
	
	
	<div  style="height:10px;" id="btwo" >
    <div style="width:50%;float:left;" >
 <b>Date :</b><?php echo date("d/m/y") ;  ?>
    </div>

	    <div style="width:50%;float:left;" >
		<b>Print By:{{Auth()->user()->name}}</b>

    </div>

  </div>



</div>
<p>

</div>



</body>
</html>