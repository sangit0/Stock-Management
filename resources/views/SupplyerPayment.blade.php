@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <script>
        var supplyerID=0;
        var paidOrginal=0;
        function getProducts(r,x,price,IDsupp) {
            document.getElementById('display_box').innerHTML = 0;

            document.getElementById('display_supplyer').innerHTML = "Not found"
            document.getElementById('display_bal').innerHTML = 0;
            document.getElementById('display_paid').innerHTML = 0;

            document.getElementById('display_total').innerHTML  =price;
            document.getElementById('display_due').innerHTML = 0;
            document.getElementById('display_InvoicePaid').innerHTML =  0;


            $.ajax({
                url: '/supplyerPaymentDetails/' + r,
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    var t = $('#paymentDetails').DataTable();
                    t.clear().draw();
                    if (x == 2) {
                        //console.log(x);
                        document.getElementById('IDinvoice').disabled = true;
                        document.getElementById('amountBAL').disabled = true;
                        document.getElementById('amountpaid').disabled = true;

                        document.getElementById('IDinvoice').value = r;
                        document.getElementById('amountBAL').value = price;
                        document.getElementById('amountpaid').value = 0;
                        document.getElementById('amountpaidNew').value=0;
                        document.getElementById('remarks').value = "";
                        supplyerID= IDsupp;

                        // console.log(response);

                    }



                    paidOrginal=0;

                    var totalINVOICEPaid =0;

                    $.each(response, function (i, data) {
                        //console.log(x);
                        paidOrginal = parseInt(document.getElementById('amountpaid').value)+parseInt(data.amount);


                        document.getElementById('amountpaid').value = paidOrginal;

                        totalINVOICEPaid=parseInt(data.amount)+parseInt(totalINVOICEPaid);

                        document.getElementById('display_box').innerHTML = data.boxID;
                        document.getElementById('display_supplyer').innerHTML = data.supplyer.name;
                        document.getElementById('display_bal').innerHTML = data.supplyer.total_balance;
                        document.getElementById('display_paid').innerHTML = data.supplyer.paid;

                        //console.log(data);
                        t.row.add([
                            data.created_at,
                            data.amount,
                            data.payment_method.Type,
                            data.remarks

                        ]).draw(true);


                    });
                    var dueInvoice = parseInt(price)-parseInt(totalINVOICEPaid);
                    if(paidOrginal!=0) {
                        var unpayBtn = '<button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#addPayment" onclick="getProducts(' + r + ',' + 2 + ',' + price + ',' + IDsupp + ')"><i class="fa fa-minus"></i> Unpay invoice</button>';
                        document.getElementById('unpay').innerHTML = unpayBtn;
                    }
                    else {
                        document.getElementById('unpay').innerHTML = "No payment done yet";

                    }


                    //
                    document.getElementById('display_due').innerHTML = dueInvoice;
                    document.getElementById('display_InvoicePaid').innerHTML =  totalINVOICEPaid;


                }
            });
        }
        function  calculatePayment() {
            var newpaid =document.getElementById('amountpaidNew').value;
            var bal =document.getElementById('amountBAL').value;

            var paid =0;
            if(newpaid=="");
            else if(parseInt(newpaid)<0){
                paid = parseInt(paidOrginal) + parseInt(newpaid);
                document.getElementById('amountpaid').value = paid;

            }
            else {
                paid = parseInt(paidOrginal) + parseInt(newpaid);
                //console.log(paid);
                if (paid <= parseInt(bal) )
                    document.getElementById('amountpaid').value = paid;
                else{
                    alert("Can't greater than total invoice Price");
                    document.getElementById('amountpaidNew').value = 0;
                    document.getElementById('amountpaid').value = paidOrginal;

                }
            }

        }

        function payToSupplyer(){

            var ID =document.getElementById('IDinvoice').value;
            var balance = document.getElementById('amountBAL').value;
            var paid =paidOrginal;
            var newpaid =document.getElementById('amountpaidNew').value;
            var typePayment =document.getElementById('paymentMethod').value;
            var remarks =document.getElementById('remarks').value;





            var totalPaid=parseInt(paidOrginal)+parseInt(newpaid);
            //console.log(totalPaid);


            if(parseInt(balance)>=totalPaid) {
                if(newpaid!=0) {

                    var paymentDetails = {
                        supplyerID: supplyerID,
                        ID: ID,
                        total: balance,
                        paid: paid,
                        newpaid: newpaid,
                        paymentMethod: typePayment,
                        remarks: remarks
                    };
                    // console.log(paymentDetails);
                    $.ajax({
                        data: {data: paymentDetails},
                        url: '/save-supplyer-payment',
                        type: 'POST',

                        beforeSend: function (request) {
                            return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                        },
                        success: function (response) {
                            //console.log(response);
                            getProducts(ID, 1, balance, supplyerID);


                        }
                    });
                }
                else{
                    alert("Zero balance can't be paid.");

                }
            }
            else{
                alert("Paid amount can't greater than total amount.");

            }


        }



    </script>

    <h2>
        Supplier payment
        <small>Manage payment information</small>
    </h2>
    <hr class="alert-info">
    <div class="row">
        <div class="col-md-7">
            <div class="box  box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Supplier</h3>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table  id="productsTBL" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th><i class="fa fa-sort"></i> Status</th>
                            <th><i class="fa fa-sort"></i> Invoice </th>
                            <th><i class="fa fa-sort"></i> Total(Tk)</th>
                            <th><i class="fa fa-sort"></i> Name</th>


                            <th><i class=""></i> Action</th>

                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($stock as $stock)
                            <tr>
                                @if($stock->statusPaid==-1)
                                    <td><label class="label label-danger">unpaid</label></td>
                                @else
                                    <td><label class="label label-success">paid</label></td>

                                @endif
                                <td>{{ $stock->boxID}}</td>

                                <td>{{ $stock->price}}</td>
                                <td>{{ $stock->supplyer->name}}</td>


                                <td>
                                    @if($stock->statusPaid==-1)
                                        <button class="btn btn-primary btn-sm" onclick="getProducts({{$stock->boxID}},1,{{$stock->price}},{{$stock->supplyer->id}})"><i class="fa fa-eye-slash"></i> Details</button>
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#addPayment" onclick="getProducts({{$stock->boxID}},2,{{$stock->price}},{{$stock->supplyer->id}})"><i class="fa fa-plus-circle"></i> Pay</button>
                                        <a href="{{URL::to('/makepdfpurchase/'.$stock->boxID)}}" target="_blank" class="btn btn-info btn-sm" ><i class="fa fa-print"></i></a>

                                    @else
                                        <button class="btn btn-primary btn-sm" onclick="getProducts({{$stock->boxID}},1,{{$stock->price}},{{$stock->supplyer->id}})"><i class="fa fa-eye-slash"></i> Details</button>
                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#addPayment" onclick="getProducts({{$stock->boxID}},2,{{$stock->price}},{{$stock->supplyer->id}})" disabled><i class="fa fa-plus-circle"></i> Pay</button>
                                        <a href="{{URL::to('/makepdfpurchase/'.$stock->boxID)}}" target="_blank"  class="btn btn-info btn-sm" ><i class="fa fa-print"></i></a>


                                    @endif
                                </td>

                            </tr>
                        @endforeach

                        </tbody>

                    </table>


                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-5">
            <div class="box box-solid">
                <div class="box-header with-border">

                    <h3 class="box-title">Particular payment information</h3>
                    <!-- /.box-tools -->
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                   <span class="alert">




                <p class="alert pull-left" style="color:black; border:1px solid black;">
                    <span class="callout callout-default">Supplier information </span>

                              <br>
                              <br>
                               <span  style="font-size:15px;">Customer: </span>
                            <label id="display_supplyer" class="label" style="font-size:13px; color:black; "> Not selected </label>
                           <br>

                            <label style="font-size:13px;">Total balance: </label>
                            <label id="display_bal" class="label label-danger" style="font-size:13px; color:black; border:1px solid red; border-radious: 10/8px;">0 </label>
                            <br>
                              <label  style="font-size:13px;">Paid: </label>
                            <label id="display_paid" class="label" style="font-size:13px; color:black;border:1px solid red; border-radious: 10/8px;"> 0 </label>
                           <br>
                             </p>
                           <p class="alert pull-right" style="color:black; border:1px solid black;">Invoice ID:
                            <label id="display_box" class="label" style="font-size:13px; color:red;border:1px solid black; border-radious: 10/8px;"> 0 </label>

                              <br>
                              <br>
                               <span  style="font-size:15px;">Total invoice: </span>
                            <label id="display_total" class="label" style="font-size:13px; color:black; "> 0 </label>
                           <br>

                            <label style="font-size:13px;">Total paid: </label>
                            <label id="display_InvoicePaid" class="label label-danger" style="font-size:13px; color:black; border:1px solid red; border-radious: 10/8px;">0 </label>
                            <br>
                              <label  style="font-size:13px;">Due: </label>
                            <label id="display_due" class="label" style="font-size:13px; color:black;border:1px solid red; border-radious: 10/8px;"> 0 </label>
                           <br>


                             </p>
                        </span>

                    <br><br><br>

                    <table  id="paymentDetails" class="table table-bordered table-hover">

                        <thead>
                        <tr>
                            <th><i class="fa fa-sort"></i> Date</th>
                            <th><i class="fa fa-sort"></i> Amount </th>
                            <th><i class="fa fa-sort"></i> PaymentMethod </th>
                            <th><i class="fa fa-sort"></i> Remarks </th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>
                    <label  id="unpay"></label>



                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>


    </div>

    <!-- /.modal -->
    <div class="modal fade" id="addPayment">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add new payment</h4>
                </div>
                <div class="modal-body">
                    <table  class="table">

                        <tbody>
                        <tr>
                            <td></td>
                            <td>Invoice ID</td>


                            <td><input type="number" name="ID" id="IDinvoice"/></td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Total</td>


                            <td><input type="number" name="amount" id="amountBAL"/></td>

                        </tr>
                        <tr>
                            <td></td>
                            <td>Paid</td>


                            <td><input type="number" name="paid" id="amountpaid"/></td>

                        </tr>
                        <tr>
                            <td></td>
                            <td><label class="control-label">New paid:</label></td>


                            <td><input type="number" onkeyup="calculatePayment()" name="newpaid" id="amountpaidNew"/></td>

                        </tr>
                        <tr>
                            <td></td>
                            <td><label class="control-label">Payment:</label></td>
                            <td> <select class="form-control select2" id="paymentMethod" style="width: 50%">
                                    <?php
                                    $method= \App\PaymentMethod::all()->where('publication_status',1);
                                    foreach($method as $value)
                                    {
                                    ?>
                                    <option value="{{$value->ID}}">{{$value->Type}}</option>
                                    <?php } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><label class="control-label">Remarks:</label></td>


                            <td><textarea name="remarks" id="remarks"> </textarea></td>

                        </tr>



                        </tbody>

                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="payToSupplyer();">Pay</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <script>
        $(function () {

            $('#productsTBL').DataTable({
                'paging'      : true,
                'lengthChange': false,
                'searching'   : true,
                'ordering'    : false,
                'info'        : true,
                'autoWidth'   : true
            })
        });

    </script>


@endsection
