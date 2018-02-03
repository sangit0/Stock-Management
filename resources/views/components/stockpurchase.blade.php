
    <link rel="stylesheet" href="{{ asset('styleResource/bower_components/select2/dist/css/select2.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    <script>
        var boxID=0;
        var Style=-1;
        var dataProduct=[];

        //brand
        var BrandID=0;
        var alertBrand=-1;

        function checkBoxID(r) {
            document.getElementById('boxID').value =r;
            boxID =r;
            newInvoiceStock();
        }
        function stylecopyData(selTag) {
            Style= selTag.options[selTag.selectedIndex].value;
        }


        function brandCopyData(selTag) {
            BrandID= selTag.options[selTag.selectedIndex].value;


            if(BrandID<10000)
                alertBrand=1;
            else
                alertBrand=-1;

        }

        function addData() {
            var productName = document.getElementById('product').value;
            var size = document.getElementById('size').value;
            var quantity = document.getElementById('qty').value;
            var color = document.getElementById('color').value;

            var price = document.getElementById('price').value;

            if(alertBrand==-1)
                alert("Brand not selected");
            else if(product==""||size==""||quantity==""||price=="")
                alert("Please all the data in input box!");
            else if(Style==-1)
                alert("Select Style");
            else {
                var product = {
                    product: productName,
                    size: size,
                    color:color,
                    quantity: quantity,
                    price: price,
                    style:Style,
                    Brand: BrandID
                }
                //console.log(product);
                dataProduct.push(product);
                document.getElementById('product').value = "";
                document.getElementById('size').value = "";
                document.getElementById('qty').value = "";
                document.getElementById('price').value = "";
                document.getElementById('color').value = "";

            }
            saveStockData();



        }
        function saveStockData() {
            if(dataProduct.length==0)
                alert("Please add some product to save data.");
            else {
                var chk = confirm("Are you sure to save all the data in the under Invoice: "+boxID+" ?");

                if (chk) {
                    var dataImp = {
                        boxID: boxID,
                    }
                    dataProduct.push(dataImp);
                    //Dp.boxID
                    //console.log(dataProduct);

                    $.ajax({
                        data: {data1: dataProduct},
                        url: '/save-purchase-old-invoice',
                        type: 'POST',

                        beforeSend: function (request) {
                            return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                        },
                        success: function (response) {
                            // console.log(response);
                            showSnakBar();
                            getProducts(boxID,Supplier_name,response.price,response.availableStock);
                            dataProduct=[];
                             boxID=0;
                             Style=-1;
                             BrandID=0;
                             alertBrand=-1;
                        }
                    });
                }
            }

        }



    </script>

    @if (session('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif
    <?php
    echo Session::put('message','');

    ?>


            <span class="callout text-danger"> Fill up information to add new products.</span>

            <!-- /.box-header -->
            <div class="box-body">
                        <div class="">

                                <table class="table table-hover">
                                    <tr>

                                        <td><label class="control-label">Purchase ID:</label></td>
                                        <td>


                                            <input type="number"  id="boxID" required="required" class="form-control" maxlength="50px" placeholder="Enter Box number"  disabled/>
                                        </td>


                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Brand:</label></td>
                                        <td> <select class="form-control  select2" id="brandnew" onchange="brandCopyData(this)" style="width: 100%;";>

                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><label class="control-label">Product:</label></td>
                                        <td> <input id="product" type="text" required="required" class="form-control" maxlength="50px" placeholder="Enter Product" /></td>
                                    </tr>

                                    <tr>
                                        <td><label class="control-label">Style</label></td>
                                        <td> <select class="form-control select2" id="styleID" onchange="stylecopyData(this)" style="width: 100%;";>

                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Size:</label></td>
                                        <td> <input id="size" type="text" required="required" class="form-control" maxlength="50px" placeholder="Enter size"  /></td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Color:</label></td>
                                        <td> <input id="color" type="text" required="required" class="form-control" maxlength="50px" placeholder="Enter color"  /></td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Qty:</label></td>
                                        <td> <input id="qty" type="number" required="required" class="form-control" maxlength="50px" placeholder="Enter quantity"  /></td>
                                    </tr>
                                    <tr>
                                        <td><label class="control-label">Per price:</label></td>
                                        <td> <input id="price" type="number" required="required" class="form-control" maxlength="50px" placeholder="Enter price"  /></td>
                                    </tr>

                                    <br>

                                </table>
                                <br>
                                <button class="btn btn-success btn-md pull-left" onclick="addData();" data-dismiss="modal" type="button" ><i class="fa fa-plus-circle"></i> Save</button>
                            <button class="btn btn-danger btn-md pull-left" data-dismiss="modal" type="button" ><i class="fa fa-times"></i> Cancel</button>

                        </div>
                    </div>



    <script src="{{ asset('styleResource/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script>


        function newInvoiceStock() {
            ajaxGetSelect('/get-style',styleID,2);
            ajaxGetSelect('/get-brand',brandnew,3);


        }

        function ajaxGetSelect(url,id,option) {
            $.ajax({
                url: url,
                type: 'GET',

                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    setDataSelect(response,id,option);
                }
            });
        }
        function setDataSelect(getData,id,option) {
            $(id).empty();
            $(id).append($('<option>', {
                text: "Select..",
            }));
            if(option==2) {
                $.each(getData, function (i, data) {
                    $(id).append($('<option>', {
                        value: data.id,
                        text: data.name,
                    }));

                });
            }
            else if(option==3){
                $.each(getData, function (i, data) {
                    $(id).append($('<option>', {
                        value: data.ID,
                        text: data.name,
                    }));

                });
            }
        }
    </script>
