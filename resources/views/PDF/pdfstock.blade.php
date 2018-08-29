<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>INVOICE {{$Invoice[0]->id}}</title>
  <style>
    @include('PDF.style')
  </style>
</head>
<body>
<header class="clearfix">
  <div id="logo">
    {{--<img src="logo.png">--}}
  </div>
  <h3>INVOICE {{$Invoice[0]->boxID}}</h3>

  <div id="company" class="clearfix" style="padding-right: 50px;">

    <div>Hello dummy name<br> Trading LLC</div>
    <div>Bashundhara 40, Road 7/C, sector 9
    </div>
    <div>Bashundhara, Dhaka</div>

    {{--<div><a href="mailto:company@example.com">company@example.com</a></div>--}}
  </div>


</header>

<hr>


<div id="project" >
  <div style="font-size: 15px;">Bill To:</div><br>
  <div style="font-size: 15px;"> Supplier: {{$Invoice[0]->supplyer->name}}</div>
  <span>
      <div> {{$Invoice[0]->supplyer->Adress}}</div>
      <div>{{$Invoice[0]->supplyer->contact}}</div>
      </span>
</div>
@if($Invoice[0]->statusPaid==0)
  <div id="project-right" style="padding-right: 50px;">
 <span style="color: green; border:1px solid green; border-radius: 10% ;width: 200px;
	height: 200px; font-size: 20px; ">PAID</span>
    @else
      <div id="project-right" style="padding-right: 50px;">
 <span style="color: red; border:1px solid red; border-radius: 10% ;width: 200px;
	height: 200px; font-size: 19px; ">UNPAID</span>
        @endif
        <div style="font-size: 15px;">Invoice ID: {{$Invoice[0]->boxID}}</div><br>
        <div style="font-size: 15px;">Created at: {{$Invoice[0]->products[0]->created_at}}</div>
        <div style="font-size: 15px;">Total price: {{$Invoice[0]->price}}</div><br>



      </div>

      <table style="padding-top: 100px;">
        <thead>
        <tr>
          <th class="service">No</th>
          <th class="desc">Product</th>
          <th class="desc">Style</th>

          <th>Qty</th>
          <th>Unit price</th>
          <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        @php
          $i=0;
        @endphp
        @foreach($Invoice[0]->products as $value)
          <tr>
            <td class="service">{{++$i}}</td>
            <td class="desc">{{$value->pName}}</td>
            <td class="desc">{{$value->styles->name}}</td>
            <td class="qty">{{$value->quantity}}</td>
            <td class="unit">{{$value->price}}</td>
            <td class="total"> {{$value->quantity*$value->price}}</td>
          </tr>
        @endforeach

        @if($Invoice[0]->statusPaid==0)
          <tr>
            <td class="sum" colspan="5">SUBTOTAL</td>
            <td class="total"> {{$Invoice[0]->price}} </td>
          </tr>
          <tr>
              <td  class="sum" colspan="5">PAID</td>
              <td class="total"> {{$paymenthist}}</td>
          </tr>
          <tr>
              <td colspan="5" class="grand total" style="text-align: right;"> TOTAL</td>
              <td class="grand total"> {{$Invoice[0]->price-$paymenthist}}</td>
          </tr>
          <tr>
            <td  class="sum" colspan="5">Previous Due</td>
            <td class="total"> {{($Invoice[0]->supplyer->total_balance-$Invoice[0]->supplyer->paid)}}</td>
          </tr>
          <tr>
            <td colspan="5" class="grand total" style="text-align: right;">GRAND TOTAL</td>
            <td class="grand total"> {{($Invoice[0]->supplyer->total_balance-$Invoice[0]->supplyer->paid)}}</td>
          </tr>
        @else
          <tr>
            <td  class="sum" colspan="5">SUBTOTAL</td>
            <td class="total"> {{$Invoice[0]->price}}</td>
          </tr>
          <tr>
              <td  class="sum" colspan="5">PAID</td>
              <td class="total"> {{$paymenthist}}</td>
          </tr>
          <tr>
              <td colspan="5" class="grand total" style="text-align: right;"> TOTAL</td>
              <td class="grand total"> {{$Invoice[0]->price-$paymenthist}}</td>
          </tr>

          <tr>
            <td  class="sum" colspan="5">Previous Due</td>
              <td class="total"> {{($Invoice[0]->supplyer->total_balance-($Invoice[0]->supplyer->paid-$paymenthist)-$Invoice[0]->price)}}</td>
          </tr>
          <tr>
            <td colspan="5" class="grand total" style="text-align: right;">GRAND TOTAL</td>
              <td class="grand total"> {{$Invoice[0]->supplyer->total_balance-$Invoice[0]->supplyer->paid}}</td>
          </tr>
        @endif


        </tbody>
      </table>
      <br>
      <div id="notices" style="float: left">
        <div class="notice">________________</div>
        <div>Authorise signature</div>
      </div>

      <div style="float: right; padding-right: 50px">
        <div class="notice">__________________</div>
        <div>Manager signature</div>
      </div>

</body>
</html>
