@extends('layouts.app')
@section('content') {{--Sangit is author--}}
<meta name="csrf-token" content="{{ csrf_token() }}" />

<style>
    .stepwizard-step p {
        margin-top: 10px;
    }

    .stepwizard-row {
        display: table-row;
    }

    .stepwizard {
        display: table;
        width: 50%;
        position: relative;
    }

    .stepwizard-step button[disabled] {
        opacity: 1 !important;
        filter: alpha(opacity=100) !important;
    }

    .stepwizard-row:before {
        top: 14px;
        bottom: 0;
        position: absolute;
        content: " ";
        width: 100%;
        height: 1px;
        background-color: #ccc;
        z-order: 0;
    }

    .stepwizard-step {
        display: table-cell;
        text-align: center;
        position: relative;
    }

    .btn-circle {
        color: white;
        width: 30px;
        height: 30px;
        text-align: center;
        padding: 6px 0;
        font-size: 12px;
        line-height: 1.428571429;
        border-radius: 15px;
    }
</style>
<script>
    var boxID = 0;
    var Style = -1;
    var supplyerID = 0;
    var Supplier_name;
    var alert_supplier = -1;
    var dataProduct = [];

    //brand
    var BrandID = 0;
    var BrandName;
    var alertBrand = -1;

    function checkBoxID() {
        var temp = document.getElementById('boxID').value;
        boxID = temp;
    }

    function getStyle(selTag) {

        var stName = selTag.options[selTag.selectedIndex].text;
        Style = selTag.options[selTag.selectedIndex].value;
        document.getElementById('display_style').innerHTML = stName;
        //console.log(Style);

    }

    function supplyercheck(selTag) {
        Supplier_name = selTag.options[selTag.selectedIndex].text;
        supplyerID = selTag.options[selTag.selectedIndex].value;
        document.getElementById('display_supplyer').innerHTML = Supplier_name;


        if (supplyerID < 10000)
            alert_supplier = 1;
        else
            alert_supplier = -1;

    }

    function displayStep1() {
        checkBoxID();
        document.getElementById('display_box').innerHTML = boxID;

    }

    function getBrand(selTag) {
        BrandName = selTag.options[selTag.selectedIndex].text;
        BrandID = selTag.options[selTag.selectedIndex].value;
        document.getElementById('display_brand').innerHTML = BrandName;


        if (BrandID < 10000)
            alertBrand = 1;
        else
            alertBrand = -1;

    }

    function changeDetect(r, index) {
        var i = r.parentNode.parentNode.rowIndex;
        if (index == 1)
            dataProduct[i - 1].product = document.getElementById('pName' + i).value;
        else if (index == 2)
            dataProduct[i - 1].size = document.getElementById('sizeInput' + i).value;
        else if (index == 3) {
            dataProduct[i - 1].quantity = document.getElementById('qauntityInput' + i).value;
            document.getElementById('total' + i).innerHTML = Math.ceil(dataProduct[i - 1].quantity * dataProduct[i - 1].price);
        } else if (index == 4) {
            dataProduct[i - 1].price = document.getElementById('priceInput' + i).value;
            document.getElementById('total' + i).innerHTML = Math.ceil(dataProduct[i - 1].quantity * dataProduct[i - 1].price);


        }



    }

    function delete_product(r) {
        var chk = confirm("Are You Sure to Delete This?");
        if (chk) {

            var i = r.parentNode.parentNode.rowIndex;
            document.getElementById("hist_table").deleteRow(i);

            dataProduct.splice(i - 1, 1);


        } else {}
    }

    function addData() {
        var productName = document.getElementById('product').value;
        var size = document.getElementById('size').value;
        var quantity = document.getElementById('qty').value;
        var color = document.getElementById('color').value;

        var price = document.getElementById('price').value;

        if (alertBrand == -1)
            alert("Brand not selected");
        else if (product == "" || size == "" || quantity == "" || price == "")
            alert("Please all the data in input box!");
        else if (Style == -1)
            alert("Select Style");
        else {
            var product = {
                product: productName,
                size: size,
                color: color,
                quantity: quantity,
                price: price,
                style: Style,
                Brand: BrandID
            }
            //console.log(product);
            dataProduct.push(product);
            var sizeRow = dataProduct.length;


            var table = document.getElementById("hist_table");

            var row = table.insertRow(-1);

            var cell1 = row.insertCell(0);
            var cell6 = row.insertCell(1);
            var cell4 = row.insertCell(2);
            var cell2 = row.insertCell(3);
            var cell3 = row.insertCell(4);
            var s = '<button class="btn btn-danger btn-sm" onclick="delete_product(this);"><i class="fa fa-trash-o"></i></button>'

            var cell5 = row.insertCell(5);
            cell1.innerHTML = "<input type='text'  onkeyup='changeDetect(this,1)' id='pName" + sizeRow + "' value='" + productName + "' style='width:100%';>";
            cell6.innerHTML = "<input type='text'  onkeyup='changeDetect(this,2)' id='sizeInput" + sizeRow + "' value='" + size + "' style='width:70%'>";
            cell4.innerHTML = "<input type='number'  onkeyup='changeDetect(this,3)' onclick='changeDetect(this,3)' id='qauntityInput" + sizeRow + "' value='" + quantity + "' style='width:70%'>";
            cell3.innerHTML = "<p id='total" + sizeRow + "'>" + Math.ceil(quantity * price) + "<p>";
            cell5.innerHTML = s;
            cell2.innerHTML = "<input type='number'  onkeyup='changeDetect(this,4)' onclick='changeDetect(this,3)' id='priceInput" + sizeRow + "' value='" + price + "' style='width:70%;'>";
            document.getElementById('product').value = "";
            document.getElementById('size').value = "";
            document.getElementById('qty').value = "";
            document.getElementById('price').value = "";
        }



    }

    function saveStockData() {

        if (boxID == 0 || alert_supplier == -1)
            alert("Please check 'step 1' didn't complete  properly!");
        else if (dataProduct.length == 0)
            alert("Please add some product to save data.");
        else {
            var chk = confirm("Are you sure to save all the data in the under Invoice: " + boxID + " ?");

            if (chk) {
                var dataImp = {
                    boxID: boxID,
                    supplyer: supplyerID
                }
                dataProduct.push(dataImp);
                //Dp.boxID
                //console.log(dataProduct);

                $.ajax({
                    data: {
                        data1: dataProduct
                    },
                    url: '/save-purchase',
                    type: 'POST',

                    beforeSend: function(request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function(response) {
                        // console.log(response);
                        window.location = "/add-stock";
                    }
                });
            }
        }

    }
</script>
<?php
    $all_published_supplyer = \App\Supplyer::all()
        ->where('publication_status',1);
    $all_published_style = \App\ProductCategory::all()
        ->where('status',1);
    $all_published_brand = \App\Brand::all()
        ->where('publication_status',1);

   ///Purchase ID creation
    $boxID = \App\StockPurchase::select('boxID')
        ->orderBy('boxID', 'desc')
        ->get();
    if(sizeof($boxID)==0)
        $invoice_boxID=55005;
    else
        $invoice_boxID= $boxID[0]->boxID+1;

    //var_dump($all_published_brand);
    ?>

    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
    <?php
    echo Session::put('message','');

?>

        <div class="col-md-5">
            <div class="box box-default box-solid">
                <div class="box-header with-border">

                    <h3 class="box-title">Add new products to stock</h3>
                    <a href="#addSupplyer" role="button" class="btn btn-warning btn-sm  pull-right" title="Add new supplyer" data-toggle="modal"><i class="fa fa-user-plus" ></i></a>



                    <!-- /.box-tools -->
                </div>
                <span class="callout text-danger"> Follow the steps to add new products.</span>

                <!-- /.box-header -->
                <div class="box-body">

                    <div class="stepwizard">
                        <div class="stepwizard-row setup-panel">
                            <div class="stepwizard-step">
                                <a href="#step-1" type="button" class="btn btn-primary btn-circle">1</a>
                                <p>Step 1</p>
                            </div>

                            <div class="stepwizard-step">
                                <a href="#step-3" type="button" class="btn btn-default btn-circle" disabled="disabled">2</a>
                                <p>Step 2</p>
                            </div>
                        </div>
                    </div>

                    <form role="form" action="" method="post">
                        <div class="row setup-content" id="step-1">
                            <div class="">
                                <div class="col-md-7">

                                    <table class="table table-hover">

                                        <tr>

                                            <td><label class="control-label">Purchase ID:</label></td>
                                            <td>


                                                <input type="number" id="boxID" required="required" value="{{$invoice_boxID}}" class="form-control" maxlength="50px" placeholder="Enter Box number" disabled/>
                                            </td>


                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Supplyer:</label></td>
                                            <td> <select class="form-control select2" id="supplyerId" onchange="supplyercheck(this)" style="width: 100%;">
                                                <option>Select...</option>

                                            </select>
                                            </td>
                                        </tr>


                                        <br>

                                    </table>
                                    <br>
                                    <button class="btn btn-primary btn-sm nextBtn pull-right" onclick="displayStep1();" type="button">Next</button>

                                </div>
                            </div>
                        </div>

                        <div class="row setup-content" id="step-3">
                            <div class="">
                                <div class="col-md-7">
                                    <table class="table table-hover">
                                        <tr>
                                            <td><label class="control-label">Brand:</label></td>
                                            <td> <select class="form-control select2" onchange="getBrand(this)" style="width: 100%;" ;>
                                                <option>Select...</option>
                                                <?php
                                                foreach($all_published_brand as $Pvalue)
                                                {
                                                ?>
                                                <option value="{{$Pvalue->ID}}">{{$Pvalue->name}}</option>
                                                <?php } ?>
                                            </select>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Product:</label></td>
                                            <td> <input id="product" type="text" required="required" class="form-control" maxlength="50px" placeholder="Enter Product" /></td>
                                        </tr>

                                        <tr>
                                            <td><label class="control-label">Style</label></td>
                                            <td> <select class="form-control select2" id="styleID" onchange="getStyle(this)" style="width: 100%;" ;>
                                                <option value="-1">Select...</option>
                                                <?php
                                                foreach($all_published_style as $Pvalue)
                                                {
                                                ?>
                                                <option value="{{$Pvalue->id}}">{{$Pvalue->name}}</option>
                                                <?php } ?>
                                            </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Size:</label></td>
                                            <td> <input id="size" type="text" required="required" class="form-control" maxlength="50px" placeholder="Enter size" /></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Color:</label></td>
                                            <td> <input id="color" type="text" required="required" class="form-control" maxlength="50px" placeholder="Enter color" /></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Qty:</label></td>
                                            <td> <input id="qty" type="number" required="required" class="form-control" maxlength="50px" placeholder="Enter quantity" /></td>
                                        </tr>
                                        <tr>
                                            <td><label class="control-label">Per price:</label></td>
                                            <td> <input id="price" type="number" required="required" class="form-control" maxlength="50px" placeholder="Enter price" /></td>
                                        </tr>

                                        <br>

                                    </table>
                                    <br>
                                    <button class="btn btn-primary btn-md pull-left" onclick="addData();" type="button"><i class="fa fa-plus-circle"></i> Add</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>



            </div>
            <!-- /.box-body -->
            <!-- /.box -->
        </div>
        <div class="col-md-7">
            <!-- Widget: user widget style 1 -->
            <div class="box box-widget widget-user-2">
                <!-- Add the bg color to the header using any of the bg-* classes -->
                <div class="widget-user-header">

                    <h3 class="widget-user-username">Cart</h3>
                    <h5 class="widget-user-desc">Stock invoice history</h5>
                    <span class="col-sm-4">
                             <p class="alert" style="color:black; border:1px solid black;">ID:
                            <label id="display_box" class="label" style="font-size:13px; color:red;border:1px solid black; border-radious: 10/8px;"> 0 </label>

                              <br>
                              <br>


                            <label class="text-danger" style="font-size:13px;">Supplyer: </label>
                            <label id="display_supplyer" class="label" style="font-size:13px; color:black; "> Not selected </label>
                            <br>
                              <label class="text-danger" style="font-size:13px;">Brand: </label>
                            <label id="display_brand" class="label" style="font-size:13px; color:black; "> Not selected </label>
                           <br>

                            <label class="text-danger" style="font-size:13px;">Style: </label>
                            <label id="display_style" class="label" style="font-size:13px; color:black;border: black 1px solid;border-radius: 10/8px; "> None </label>


                             </p></span>


                </div>
                <div class="box-body">

                    <table id="hist_table" class="table table-bordered table-hover">
                        <thead>
                            <tr>

                                <th><i class="fa fa-sort"></i> Product </th>
                                <th><i class="fa fa-sort"></i> Size </th>
                                <th><i class="fa fa-sort"></i> Qty.</th>
                                <th><i class="fa fa-sort"></i> Price</th>
                                <th><i class="fa fa-sort"></i> Total price</th>
                                <th><i class="fa fa-sort"></i> Action</th>
                            </tr>
                        </thead>
                        <tbody>



                        </tbody>

                    </table>
                    <br>
                    <button class="btn btn-sm btn-danger" onclick="saveStockData()">Save all data</button>


                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>

        {{--Customer-ads shortcut--}}
        <div class="modal fade" id="addSupplyer">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add new supplyer</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table">

                            <tbody>
                                <tr>
                                    <td></td>
                                    <td>Select type:</td>


                                    <td><select id="cat" required>
                                <option value="-1">Select...</option>

                                <option value="0">Export</option>
                                <option value="1">Local</option>

                            </select></td>

                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Name</td>


                                    <td><input type="text" id="name" required/></td>

                                </tr>


                                <tr>
                                    <td></td>
                                    <td>Address</td>


                                    <td><input type="text" id="address" /></td>

                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Contact</td>


                                    <td><input type="number" id="phone" /></td>

                                </tr>

                                <tr>
                                    <td></td>
                                    <td>Balance</td>


                                    <td><input type="number" id="balance" value="0" id="balance" /></td>

                                </tr>
                                <tr>
                                    <td></td>
                                    <td>Paid balance</td>


                                    <td><input type="number" id="paid" value="0" id="paid" /></td>

                                </tr>
                                <tr>
                                    <td></td>

                                    <td>
                                        <button type="submit" onclick="SavesupplyerDetails()" data-dismiss="modal" class="btn btn-success btn-sm">SAVE</button>
                                    </td>
                                    <td></td>

                                </tr>




                            </tbody>



                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <script>
            $(document).ready(function() {
                supplyerDetails();

                var navListItems = $('div.setup-panel div a'),
                    allWells = $('.setup-content'),
                    allNextBtn = $('.nextBtn');

                allWells.hide();

                navListItems.click(function(e) {
                    e.preventDefault();
                    var $target = $($(this).attr('href')),
                        $item = $(this);

                    if (!$item.hasClass('disabled')) {
                        navListItems.removeClass('btn-primary').addClass('btn-default');
                        $item.addClass('btn-primary');
                        allWells.hide();
                        $target.show();
                        $target.find('input:eq(0)').focus();
                    }
                });

                allNextBtn.click(function() {
                    var curStep = $(this).closest(".setup-content"),
                        curStepBtn = curStep.attr("id"),
                        nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
                        curInputs = curStep.find("input,input[type='url']"),
                        isValid = true;
                    // console.log(curStepBtn);

                    if (curStepBtn == "step-1") {
                        if (alert_supplier == -1) {
                            isValid = false;
                            alert("Supplyer not selected");
                        }
                    }


                    $("td").removeClass("has-error");
                    for (var i = 0; i < curInputs.length; i++) {
                        if (!curInputs[i].validity.valid) {
                            isValid = false;
                            $(curInputs[i]).closest("td").addClass("has-error");
                        }
                    }

                    if (isValid)
                        nextStepWizard.removeAttr('disabled').trigger('click');
                });

                $('div.setup-panel div a.btn-primary').trigger('click');
                $('.select2').select2();

            });

            function supplyerDetails() {

                $.ajax({
                    url: '/getAllSupplyer',
                    type: 'GET',
                    beforeSend: function(request) {
                        return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                    },
                    success: function(response) {
                        $('#supplyerId').empty().append($('<option>', {
                            value: -1,
                            text: "Select..."
                        }));

                        $.each(response, function(i, data) {
                            var type = "";
                            if (data.type == 1)
                                type = "Local";
                            else type = "Export";

                            $('#supplyerId').append($('<option>', {

                                value: data.id,
                                text: data.name + " (" + type + ")",
                            }));

                        });


                    }
                });
            }

            function SavesupplyerDetails() {
                var name = document.getElementById("name").value;
                var cat = document.getElementById("cat").value;

                if (name == "" || cat == -1) {
                    alert("Please select TYPE or Enter name atleast to add data.")
                } else {
                    $.ajax({
                        data: {
                            name: document.getElementById("name").value,
                            address: document.getElementById("address").value,
                            phone: document.getElementById("phone").value,
                            balance: document.getElementById("balance").value,
                            paid: document.getElementById("paid").value,
                            cat: document.getElementById("cat").value
                        },
                        url: '/save-supplyerAJAX',
                        type: 'POST',
                        beforeSend: function(request) {
                            return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                        },
                        success: function(response) {
                            supplyerDetails();
                            showSnakBar();


                        }
                    });
                }
            }
        </script>

        @endsection
