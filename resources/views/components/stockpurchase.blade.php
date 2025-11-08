<div class="alert alert-info" role="alert">
    Fill in the details below to add a new product to the selected invoice.
</div>

<form id="addProductForm">
    <div class="form-group">
        <label class="control-label" for="addProductInvoiceId">Purchase ID</label>
        <input type="number" id="addProductInvoiceId" class="form-control" readonly />
    </div>

    <div class="form-group">
        <label class="control-label" for="newProductBrand">Brand</label>
        <select class="form-control select2" id="newProductBrand" style="width: 100%;"></select>
    </div>

    <div class="form-group">
        <label class="control-label" for="newProductName">Product</label>
        <input type="text" id="newProductName" class="form-control" placeholder="Enter product name" />
    </div>

    <div class="form-group">
        <label class="control-label" for="newProductStyle">Style</label>
        <select class="form-control select2" id="newProductStyle" style="width: 100%;"></select>
    </div>

    <div class="form-group">
        <label class="control-label" for="newProductSize">Size</label>
        <input type="text" id="newProductSize" class="form-control" placeholder="Enter size" />
    </div>

    <div class="form-group">
        <label class="control-label" for="newProductColor">Color</label>
        <input type="text" id="newProductColor" class="form-control" placeholder="Enter color" />
    </div>

    <div class="form-group">
        <label class="control-label" for="newProductQuantity">Quantity</label>
        <input type="number" id="newProductQuantity" class="form-control" min="1" placeholder="Enter quantity" />
    </div>

    <div class="form-group">
        <label class="control-label" for="newProductPrice">Unit price</label>
        <input type="number" id="newProductPrice" class="form-control" min="0" step="0.01" placeholder="Enter price" />
    </div>

    <div class="form-group">
        <button type="button" class="btn btn-success" id="saveNewProduct">
            <i class="fa fa-plus-circle"></i> Save
        </button>
        <button type="button" class="btn btn-default" data-dismiss="modal">
            <i class="fa fa-times"></i> Cancel
        </button>
    </div>
</form>
