
    <link rel="stylesheet" href="{{ asset('styleResource/bower_components/select2/dist/css/select2.min.css')}}">




    <script>

        var data_product = [];
        var alert_product = 0;
        var availableStock = 0;
        function detailsSet() {
            document.getElementById("invoiceID").innerHTML='Adding new products for invoice for ID: '+
                '<span class="text-danger">'+invoiceID+'</span>';
            getProductsAllPublishProducts();
        }
        function select_pro_gtter(pID,pName,AVS) {


            pro_id = pID;
            availableStock = AVS;
            pro_name= pName;

            if (pro_id < 10000)
                alert_product = 1;
            else
                alert_product = 0;
            array_data();
        }


        function stockCheck(r,value) {
            if(value=="")
                return true;

            if(data_product[r].availableStock>=parseInt(value))
                return true;
            else return false;

        }
        function multiply() {

            var textValue1 = document.getElementById('productQuantity').value;
            var textValue2 = document.getElementById('productPrice').value;
            if(stockCheck(0,textValue1)){
                var pID = 0;
                data_product[pID].quantity=textValue1;
                data_product[pID].price=textValue2;
                document.getElementById('productTotal').innerHTML = Math.ceil(textValue1 * textValue2);

            }
            else {
                document.getElementById('productQuantity').value=1;
                alert("Not available stock");
            }

        }

        function array_data() {

                var quantity = 1;
                var price = 0;
                var value = parseInt(quantity);
            data_product = [];
            document.getElementById('ProductName').innerHTML = "Product: "+ pro_name;


            if (availableStock == 0 || value > availableStock)
                    alert("Out of stock Product Please check");
                else {
                        var productInformation ={
                            invoiceID: invoiceID,
                            proName: pro_name,
                            proID:pro_id,
                            quantity:quantity,
                            price:price,
                            availableStock:availableStock
                        };

                        data_product.push(productInformation);
                    }
        }

        function array_data_save() {
            price_total_getter = Math.ceil(data_product[0].quantity*data_product[0].price);
                if (price_total_getter == 0) {

                    alert("Invoice can't be Updated at Zero Price");
                }
                else {
                    $.ajax({
                        data: {data: data_product},
                        url: '/update-invoice',
                        type: 'POST',
                        beforeSend: function (request) {
                            return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                        },
                        success: function (response) {
                            document.getElementById('productQuantity').value = "";
                            document.getElementById('productPrice').value = "";
                            data_product = [];
                            $('#Expenditure').modal('hide');


                            if(response==-1){
                                alert("Product already exists in the invoice");

                            }
                            else {
                                $('#invoiceAdd').modal('hide');
                                totalPriceInvoice = parseInt(price_total_getter) + parseInt(totalPriceInvoice);
                                getProducts(invoiceID, customer, totalPriceInvoice);
                                showSnakBar();
                            }
                        }
                    });
                }


        }
        function getProductsAllPublishProducts() {

            $.ajax({
                url: '/get-product-all',
                type: 'GET',
                beforeSend: function (request) {
                    return request.setRequestHeader('X-CSRF-Token', $("meta[name='csrf-token']").attr('content'));
                },
                success: function (response) {
                    var t = $('#invoiceTBL').DataTable();
                    t.clear().draw();
                    $.each(response, function (i, data) {
                        //console.log(data);

                        var pr =data.pName.replace(/'/g,"");

                    var span ='<label class=" btn btn-sm btn-primary" style="border-radius: 15px;">'+data.availableQty+'</label>';
                        var btn ="<button data-toggle='modal' data-target='#Expenditure' class='btn-danger btn btn-sm' onclick=\'select_pro_gtter(\""+data.ID+"\",\""+pr+"\",\""+data.availableQty+"\")\'> Add</button>";
                        t.row.add( [
                            data.pName+"("+data.size+")",
                            data.styles.name,
                            data.brand.name,
                            data.boxID,
                            span,
                            btn,


                        ] ).draw( true );


                    });


                }
            });

        }

    </script>

            <div class="box box-solid box-default">
                <div class="box-body">
                    <label id="invoiceID"></label>
                    <hr>
                    <table id="invoiceTBL" class="table table-condensed">
                        <thead>
                        <th>Product(Size)</th>
                        <th>Style</th>
                        <th>Brand</th>
                        <th>Belongs</th>
                        <th>Stocks</th>

                        <th>Action</th>

                        </thead>
                        <tbody>


                        </tbody>
                    </table>
                </div>
            </div>

        <div class="modal fade" id="Expenditure">
            <div class="modal-dialog modal-sm">

                <div class="modal-content">

                    <div class="modal-body">
                        <span id="ProductName" class="text text-danger"></span><br>
                        <table class="table table-striped">
                            <tbody>

                            <tr>
                                <td></td>
                                <td><span>Quantity</span></td>
                                <td><input type="number" id="productQuantity" value="" placeholder="0"
                                           onkeyup="multiply(this)"  onclick="multiply()"; min="0" class="span6 "/>
                                </td>

                            </tr>
                            <tr>
                                <td></td>
                                <td><span>Product Price</span></td>
                                <td><input type="number" id="productPrice" value="" placeholder="0" min="0"
                                           onkeyup="multiply(this)" onclick="multiply()"; class="span6 "/></td>

                            </tr>
                            <tr>
                                <td></td>
                                <td><span>Total</span></td>
                                <td id="productTotal"></td>
                                </td>

                            </tr>
                            </tbody>
                        </table>

                    </div>
                        <div class="modal-footer">
                            <a href="#" onclick="array_data_save()"  class="btn btn-primary btn-sm"> Save</a>
                        </div>
                </div>
            </div>
        </div>




        <!-- DataTables -->
        <script src="{{ asset('styleResource/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

        <script src="{{ asset('/styleResource/bower_components/datatables.net/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{ asset('/styleResource/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')}}"></script>

        <script>
            $(function () {
                $('.select2').select2();

                $('[data-toggle="popover"]').popover();


                $('#invoiceTBL').DataTable({
                    'paging'      : true,
                    'lengthChange': false,
                    'searching'   : true,
                    'ordering'    : false,
                    'info'        : true,
                    'autoWidth'   : true
                })
            });




        </script>

