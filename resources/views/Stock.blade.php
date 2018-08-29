@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <script>
        var dataToPush = [];
        var row;
        var Supplier_name;

        function getProducts(r, supp, price, qty) {
            dataToPush = [];
            Supplier_name = supp;
            $.ajax({
                url: '/view-stock-details/' + r,
                type: 'GET',
                beforeSend: function(request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function(response) {
                    var t = $('#productsDetails').DataTable();
                    t.clear().draw();
                    var invoiceDetailsOption =
                        ' <p class="alert " style="color:black; border:1px solid black;"> Purchase ID: <label id="display_ID" class="label" style="font-size:13px; color:red;border:1px solid black; border-radious: 10/8px;">' + r +
                        '</label>\n' +
                        '\n' +
                        '                                      <br><br>\n' +
                        '                                      <label  style="font-size:13px;">Supplier: </label>\n' +
                        '                                      <label id="display_customer" class="label " style="font-size:13px; color:black; "> ' + supp + ' </label><br>\n' +
                        '                                      <label style="font-size:13px;">Total Price: </label>\n' +
                        '                                      <label id="display_price" class="label label-warning" style="font-size:13px; color:black; "> ৳ ' + price + ' </label><br>\n' +

                        '                                      <label style="font-size:13px;">Total Qty: </label>\n' +
                        '                                      <label id="display_price" class="label" style="font-size:13px; color:black; "> ' + qty + ' </label>\n' +
                        '                                  </p>\n' +
                        '\n' +
                        '\n' +
                        '                                 <button class="btn btn-danger" data-toggle="modal" data-target="#invoiceAdd" onclick="checkBoxID(' + r +
                        ')"><i class="fa fa-cart-plus"></i> Add new product to the invoice</button>';
                    document.getElementById("contentInvoice").innerHTML = invoiceDetailsOption;



                    $.each(response, function(i, data) {
                        //console.log(data);

                        var productInfo = {
                            invoiceID: r,
                            pID: data.ID,
                            pName: data.pName,
                            availableQty: data.availableQty,
                            Quantity: data.quantity,
                            salePrice: data.price,
                            size: data.size,
                            styleID: data.styleID,
                            styleName: data.styles.name,
                            brandName: data.brand.name,
                            color: data.color,
                        };
                        dataToPush.push(productInfo);

                        // Action Button
                        var btn = "<button data-toggle=\"modal\" data-target=\"#invoiceModal\" class='btn btn-sm btn-danger' onclick='getParticularSale(" +
                            dataToPush.length + ")'> <i class='fa fa-pencil'></i></button>";

                        t.row.add([
                            data.pName + " ×" + data.quantity,
                            data.availableQty,
                            data.styles.name,
                            data.brand.name,
                            data.price,
                            data.size,
                            data.color,
                            btn

                        ]).draw(true);


                    });


                }
            });

        }

        function getParticularSale(index) {
            //console.log(product);
            index = index - 1;
            row = index;
            document.getElementById("IDinvoice").innerHTML = dataToPush[index].invoiceID;
            document.getElementById("productIN").value = dataToPush[index].pName;
            document.getElementById("priceIN").value = dataToPush[index].salePrice;
            document.getElementById("qtyIN").value = dataToPush[index].availableQty;
            document.getElementById("qtyINp").value = dataToPush[index].Quantity;
            document.getElementById("colorIN").value = dataToPush[index].color;
            document.getElementById("sizeIN").value = dataToPush[index].size;
            getStyle(index);

        }

        function checkValidation() {
            var availableQty = document.getElementById("qtyIN").value;
            var Purchase = document.getElementById("qtyINp").value;
            if (parseInt(Purchase) < parseInt(availableQty)) {
                alert("Available quantity can't be greater than purchase quantity");
                document.getElementById("qtyIN").value = dataToPush[row].availableQty;
            }

        }

        function saveData() {
            var pName = document.getElementById("productIN").value;
            var salePrice = document.getElementById("priceIN").value;
            var availableQty = document.getElementById("qtyIN").value;
            var Purchase = document.getElementById("qtyINp").value;

            var color = document.getElementById("colorIN").value;
            var size = document.getElementById("sizeIN").value;
            var style = document.getElementById("styleID").value;

            var productInfo = {
                invoiceID: dataToPush[row].invoiceID,
                pID: dataToPush[row].pID,
                pName: pName,
                Purchase: Purchase,
                saleQuantity: availableQty,
                salePrice: salePrice,
                size: size,
                style: style,
                color: color,
                oldQty: dataToPush[row].Quantity,
                oldPrice: dataToPush[row].salePrice
            };
            //console.log(productInfo);
            $.ajax({
                data: {
                    data: productInfo
                },
                url: '/update-product-details',
                type: 'POST',
                beforeSend: function(request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function(response) {
                    window.location = "/stock-manage";

                }
            });
            //
        }
    </script>

    @if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif
    <?php
    echo Session::put('message', '');
    ?>
        @if (session('info'))
        <div class="alert alert-danger">
            {{ session('info') }}
        </div>
        @endif
        <?php
    echo Session::put('info', '');
    ?>
            <hr class="alert-info">
            <div class="row">
                <div class="col-md-5">
                    <div class="box  box-solid">
                        <div class="box-header with-border">
                            <h3 class="box-title">Stock</h3>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="productsTBL" class="table table-hover">
                                <thead>
                                    <tr>

                                        <th><i class="fa fa-sort"></i> ID </th>
                                        <th><i class="fa fa-sort"></i> Supplier</th>
                                        <th><i class="fa fa-sort"></i> Date</th>

                                        <th><i class=""></i> Action</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $product)
                                    <tr>
                                        <td>{{ $product->boxID}}</td>
                                        <td>{{ $product->supplyer->name}}</td>
                                        <td>{{ $product->products[0]->created_at->format('d/m/Y')}}</td>

                                        <td>
                                            <button class="btn btn-primary btn-sm" onclick='getProducts("{{$product->boxID}}","{{$product->supplyer->name}}","{{$product->price}}","{{$product->availableStock}}")'><i class="fa fa-eye"></i> View</button>
                                            <a href="{{URL::to('/makepdfpurchase/'.$product->boxID)}}" target="_blank"  class="btn btn-danger btn-sm"><i class="fa fa-print"></i></a>

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
                <div class="col-md-7">
                    @component('components.widget')
                    @slot('title') Stock details
                    @endslot
                    @slot('description') Particular products information
                    <br><br>
                    <span class="col-sm-7" id="contentInvoice">

                              </span><br>
                    @endslot
                    @slot('body')
                    @component('components.table')
                    @slot('tableID') productsDetails
                    @endslot
                    @slot('head')
                    <th><i class="fa fa-sort"></i> Product(Qty.)</th>
                    <th><i class="fa fa-sort"></i> Available </th>
                    <th><i class="fa fa-sort"></i> Style </th>
                    <th><i class="fa fa-sort"></i> Brand </th>

                    <th><i class="fa fa-sort"></i> Price </th>
                    <th><i class="fa fa-sort"></i> Size </th>
                    <th><i class="fa fa-sort"></i> Color</th>
                    <th><i class=""></i> Action</th>
                    @endslot
                    @endcomponent
                    @endslot
                    @endcomponent
                </div>
            </div>
            @component('components.modal')
            @slot('ID') invoiceAdd
            @endslot

            @slot('title') Add new purchase details details
            @endslot
            @slot('body')
            @component('components.stockpurchase')
            @endcomponent
            @endslot
            @endcomponent

            <div class="modal fade" id="invoiceModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">Update product details</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table">

                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td>Invoice ID</td>


                                        <td><label id="IDinvoice"></label></td>

                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Product</td>

                                        <td><input type="Text" id="productIN" /></td>



                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><label class="control-label">Purchased Quantity:</label></td>


                                        <td><input type="number" id="qtyINp" /></td>

                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><label class="control-label">Available Quantity:</label></td>


                                        <td><input onkeyup="checkValidation()" type="number" id="qtyIN" /></td>

                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td><label class="control-label">Price:</label></td>


                                        <td><input type="number" id="priceIN" /></td>


                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>Size</td>

                                        <td><input type="Text" id="sizeIN" /></td>


                                    </tr>


                                    <tr>
                                        <td></td>
                                        <td>Color</td>
                                        <td><input type="Text" id="colorIN" /></td>



                                    </tr>

                                    <tr>
                                        <td></td>
                                        <td>Style</td>
                                        <td>
                                            <select class="form-control select2" id="styleID" style="width: 80%;" ;>

                                </select>

                                        </td>



                                    </tr>


                                </tbody>

                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-sm btn-primary" onclick="saveData()" data-dismiss="modal">Save</button>

                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <script>
                $(function() {
                    $('.select2').select2();

                    $('#productsTBL').DataTable({
                        'paging': true,
                        'lengthChange': false,
                        'searching': true,
                        'ordering': false,
                        'info': true,
                        'autoWidth': true
                    })
                });

                function getStyle(ID) {
                    ajaxGet('/get-style', ID);
                    // alert("s");
                }

                function ajaxGet(url, ID) {
                    $.ajax({
                        url: url,
                        type: 'GET',

                        beforeSend: function(request) {
                            return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                        },
                        success: function(response) {
                            setData(response, styleID, ID);
                        }
                    });
                }

                function setData(getData, id, index) {

                    $(id).empty();
                    $(id).append($('<option>', {
                        value: dataToPush[index].styleID,
                        text: dataToPush[index].styleName,
                    }));
                    $.each(getData, function(i, data) {

                        $(id).append($('<option>', {
                            value: data.id,
                            text: data.name,
                        }));

                    });
                }
            </script>


            @endsection
