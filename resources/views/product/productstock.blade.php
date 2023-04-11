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

	</div>
 



  </div>


<b> Stock Report </b>
     



  
 <br> 




<table>






<thead>	
  <tr>
     
	

  
    <th style="width:150px;" >
	
	  Product Name
    
	 </th>
	  <th style="width:100px;"  >Go-Down</th>
    <th style="width:100px;"  >Sale Point</th>
  <th style="width:100px;"  >Total</th>

  </tr>
  </thead>	
 @foreach ( $product as $p )

 <?php $total_amount =0;
$total_amount_sale_point =0;
 $s=0; 
 ?>

  <tr>
 
 
 
 
 
 
  <td> {{$p->name  }} </td>
 

 
 
 
 
     <td> 

<table>
  <thead>
  <tr>
    <th style="width:100px;" >Unit Name </th>
    <th>Qun.</th>
	<th> Basic Unit  </th>
    
  </tr>
    </thead>

 
 @foreach ( $p->go_down_stock as $g )
  <tr>

 <td> {{ $g->unitcoversion->name }} </td>

   <td>{{ $g->stock }}     </td> 
   <td> <?php         $amnt=  $g->stock * $g->unitcoversion->coversionamount ; $s= $s+$amnt; $total_amount = $total_amount + $amnt ;  ?> {{ $amnt }} {{ $g->unitcoversion->basicunit->name }}  </td> 
	 

	 
	 
  </tr>
@endforeach 

  <tr>

 <td> Total </td>

   <td>NA      </td> 
   <td> {{ $total_amount }}  {{ $g->unitcoversion->basicunit->name }}   </td> 
	 

	 
	 
  </tr>













</table>

</td>
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
    <td> 

<table>
  <thead>
  <tr>
    <th style="width:100px;" >Unit Name </th>
    <th>Qun.</th>
	<th> Basic Unit ( KG , Piece ) </th>
    
  </tr>
    </thead>

 
 @foreach ( $p->productpriceaccunit as $t )
  <tr>

 <td> {{ $t->unitcoversion->name }} </td>

   <td>{{ $t->stock }}     </td> 
   <td> <?php   $amnt=  $t->stock * $t->unitcoversion->coversionamount ;    $total_amount_sale_point = $total_amount_sale_point + $amnt;      $s= $s+$amnt;  ?> {{ $amnt }}  {{ $g->unitcoversion->basicunit->name }}   </td> 
	 

	 
	 
  </tr>
@endforeach 





  <tr>

 <td> Total: </td>

   <td> NA    </td> 
   <td>  {{ $total_amount_sale_point }}  {{ $g->unitcoversion->basicunit->name }}   </td> 
	 

	 
	 
  </tr>













</table>

</td>
  
  <td><?php echo round($s ,2);?>  {{ $g->unitcoversion->basicunit->name }} </td>







	 
	 
  </tr>

@endforeach 
</table>
	
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