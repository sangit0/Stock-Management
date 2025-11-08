@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-5">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Stock</h3>
                </div>
                <div class="box-body">
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (session('info'))
                        <div class="alert alert-danger">
                            {{ session('info') }}
                        </div>
                    @endif

                    <table id="purchaseInvoices" class="table table-hover">
                        <thead>
                            <tr>
                                <th><i class="fa fa-sort"></i> ID</th>
                                <th><i class="fa fa-sort"></i> Supplier</th>
                                <th><i class="fa fa-sort"></i> Date</th>
                                <th><i class="fa fa-cog"></i> Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                @php($firstProduct = $product->products->first())
                                <tr>
                                    <td>{{ $product->boxID }}</td>
                                    <td>{{ $product->supplyer->name }}</td>
                                    <td>
                                        {{ $firstProduct ? $firstProduct->created_at->format('d/m/Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        <button
                                            type="button"
                                            class="btn btn-primary btn-sm js-view-invoice"
                                            data-invoice-id="{{ $product->boxID }}"
                                            data-supplier-name="{{ $product->supplyer->name }}"
                                            data-total-price="{{ $product->price }}"
                                            data-total-quantity="{{ $product->availableStock }}"
                                        >
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        <a
                                            href="{{ URL::to('/makepdfpurchase/' . $product->boxID) }}"
                                            target="_blank"
                                            class="btn btn-danger btn-sm"
                                            title="Download invoice"
                                        >
                                            <i class="fa fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Stock details</h3>
                    <p class="text-muted">Particular products information</p>
                </div>
                <div class="box-body">
                    <div id="invoiceSummary" class="clearfix"></div>

                    <div class="table-responsive">
                        <table id="productsDetails" class="table table-hover">
                            <thead>
                                <tr>
                                    <th><i class="fa fa-sort"></i> Product (Qty.)</th>
                                    <th><i class="fa fa-sort"></i> Available</th>
                                    <th><i class="fa fa-sort"></i> Style</th>
                                    <th><i class="fa fa-sort"></i> Brand</th>
                                    <th><i class="fa fa-sort"></i> Price</th>
                                    <th><i class="fa fa-sort"></i> Size</th>
                                    <th><i class="fa fa-sort"></i> Color</th>
                                    <th><i class="fa fa-cog"></i> Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @component('components.modal')
        @slot('ID')
            invoiceAdd
        @endslot
        @slot('title')
            Add product to existing invoice
        @endslot
        @slot('body')
            @component('components.stockpurchase')
            @endcomponent
        @endslot
    @endcomponent

    <div class="modal fade" id="invoiceModal" tabindex="-1" role="dialog" aria-labelledby="invoiceModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="invoiceModalLabel">Update product details</h4>
                </div>
                <div class="modal-body">
                    <form id="updateProductForm">
                        <div class="form-group">
                            <label class="control-label">Invoice ID</label>
                            <p class="form-control-static" id="IDinvoice"></p>
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="productIN">Product</label>
                            <input type="text" class="form-control" id="productIN" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="qtyINp">Purchased quantity</label>
                            <input type="number" class="form-control" id="qtyINp" min="1" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="qtyIN">Available quantity</label>
                            <input type="number" class="form-control" id="qtyIN" min="0" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="priceIN">Price</label>
                            <input type="number" class="form-control" id="priceIN" min="0" step="0.01" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="sizeIN">Size</label>
                            <input type="text" class="form-control" id="sizeIN" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="colorIN">Color</label>
                            <input type="text" class="form-control" id="colorIN" />
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="styleID">Style</label>
                            <select class="form-control select2" id="styleID" style="width: 100%;"></select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveProductChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (function($, window, document) {
            'use strict';

            // Cached selectors for DOM nodes that are used repeatedly.
            const selectors = {
                invoicesTable: '#purchaseInvoices',
                productsTable: '#productsDetails',
                invoiceSummary: '#invoiceSummary',
                updateProductModal: '#invoiceModal',
                addProductModal: '#invoiceAdd',
                invoiceIdLabel: '#IDinvoice',
                productName: '#productIN',
                purchaseQuantity: '#qtyINp',
                availableQuantity: '#qtyIN',
                salePrice: '#priceIN',
                productSize: '#sizeIN',
                productColor: '#colorIN',
                productStyle: '#styleID',
                addProductInvoiceId: '#addProductInvoiceId',
                newProductBrand: '#newProductBrand',
                newProductStyle: '#newProductStyle',
                newProductName: '#newProductName',
                newProductSize: '#newProductSize',
                newProductColor: '#newProductColor',
                newProductQuantity: '#newProductQuantity',
                newProductPrice: '#newProductPrice',
                saveProductChanges: '#saveProductChanges',
                saveNewProduct: '#saveNewProduct'
            };

            // In-memory state for the currently selected invoice and its products.
            const state = {
                products: [],
                selectedProductIndex: null,
                activeInvoice: null
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            let invoiceDataTable;
            let productDataTable;

            // Attach the CSRF token to every AJAX request.
            const httpRequest = (options) => $.ajax($.extend(true, {
                headers: {
                    'X-CSRF-Token': csrfToken
                }
            }, options));

            // Format monetary values using the browser locale.
            const formatCurrency = (value) => {
                const number = Number(value) || 0;
                return '৳ ' + number.toLocaleString();
            };

            // Keep the invoice list button metadata in sync with the latest totals.
            const updateInvoiceRowMetadata = (invoice) => {
                const $button = $('.js-view-invoice[data-invoice-id="' + invoice.invoiceId + '"]');

                $button
                    .data('total-price', invoice.totalPrice)
                    .data('total-quantity', invoice.totalQuantity)
                    .attr('data-total-price', invoice.totalPrice)
                    .attr('data-total-quantity', invoice.totalQuantity);
            };

            const showInvoiceSummaryPlaceholder = () => {
                $(selectors.invoiceSummary).html('<div class="alert alert-info" role="alert"><i class="fa fa-spinner fa-spin"></i> Loading invoice details...</div>');
            };

            // Render a concise summary of the active invoice.
            const renderInvoiceSummary = (invoice) => {
                const summaryTemplate = [
                    '<div class="alert alert-info" role="alert">',
                    '<p><strong>Purchase ID:</strong> <span class="text-danger">' + invoice.invoiceId + '</span></p>',
                    '<p><strong>Supplier:</strong> ' + invoice.supplierName + '</p>',
                    '<p><strong>Total Price:</strong> ' + formatCurrency(invoice.totalPrice) + '</p>',
                    '<p><strong>Total Qty:</strong> ' + invoice.totalQuantity + '</p>',
                    '<button type="button" class="btn btn-danger btn-sm js-add-product" data-invoice-id="' + invoice.invoiceId + '">',
                    '<i class="fa fa-cart-plus"></i> Add product to invoice',
                    '</button>',
                    '</div>'
                ].join('');

                $(selectors.invoiceSummary).html(summaryTemplate);
                updateInvoiceRowMetadata(invoice);
            };

            // Derive the aggregate totals from the currently loaded products.
            const calculateInvoiceTotals = () => {
                if (!state.activeInvoice) {
                    return;
                }

                let totalQuantity = 0;
                let totalPrice = 0;

                state.products.forEach((product) => {
                    totalQuantity += product.purchaseQuantity;
                    totalPrice += Math.ceil(product.purchaseQuantity * product.price);
                });

                state.activeInvoice.totalQuantity = totalQuantity;
                state.activeInvoice.totalPrice = totalPrice;
            };

            // Normalise the product payload returned by the API into UI-friendly objects.
            const mapProduct = (product) => ({
                id: product.ID,
                invoiceId: state.activeInvoice.invoiceId,
                name: product.pName,
                price: Number(product.price) || 0,
                purchaseQuantity: Number(product.quantity) || 0,
                availableQuantity: Number(product.availableQty) || 0,
                size: product.size || '',
                color: product.color || '',
                styleId: product.styleID,
                styleName: product.styles ? product.styles.name : 'N/A',
                brandName: product.brand ? product.brand.name : 'N/A'
            });

            // Refresh the DataTable that lists the products attached to an invoice.
            const populateProductsTable = () => {
                productDataTable.clear();

                state.products.forEach((product, index) => {
                    const actionButton = [
                        '<button type="button" class="btn btn-sm btn-danger js-edit-product" data-product-index="' + index + '">',
                        '<i class="fa fa-pencil"></i>',
                        '</button>'
                    ].join('');

                    productDataTable.row.add([
                        product.name + ' ×' + product.purchaseQuantity,
                        product.availableQuantity,
                        product.styleName,
                        product.brandName,
                        product.price,
                        product.size,
                        product.color,
                        actionButton
                    ]);
                });

                productDataTable.draw();
            };

            // Retrieve invoice details from the server and update the UI.
            const loadInvoiceProducts = (invoice) => {
                state.activeInvoice = invoice;
                state.selectedProductIndex = null;

                showInvoiceSummaryPlaceholder();

                httpRequest({
                    url: '/view-stock-details/' + invoice.invoiceId,
                    method: 'GET'
                })
                    .done((response) => {
                        state.products = (response || []).map(mapProduct);
                        calculateInvoiceTotals();
                        renderInvoiceSummary(state.activeInvoice);
                        populateProductsTable();
                    })
                    .fail(() => {
                        window.alert('Unable to load invoice details. Please try again.');
                    });
            };

            // Populate the edit modal with the available style options.
            const initialiseStyleSelect = (styleId, styleName) => {
                const $styleSelect = $(selectors.productStyle);
                $styleSelect.empty();

                if (styleId) {
                    $styleSelect.append(new Option(styleName, styleId, true, true));
                }

                httpRequest({
                    url: '/get-style',
                    method: 'GET'
                })
                    .done((styles) => {
                        (styles || []).forEach((style) => {
                            if (Number(style.id) === Number(styleId)) {
                                return;
                            }

                            $styleSelect.append(new Option(style.name, style.id, false, false));
                        });

                        $styleSelect.trigger('change.select2');
                    })
                    .fail(() => {
                        window.alert('Unable to load styles. Please try again.');
                    });
            };

            // Open the edit modal and hydrate it with the selected product.
            const openEditModal = (index) => {
                const product = state.products[index];

                if (!product) {
                    return;
                }

                state.selectedProductIndex = index;

                $(selectors.invoiceIdLabel).text(product.invoiceId);
                $(selectors.productName).val(product.name);
                $(selectors.purchaseQuantity).val(product.purchaseQuantity);
                $(selectors.availableQuantity).val(product.availableQuantity);
                $(selectors.salePrice).val(product.price);
                $(selectors.productSize).val(product.size);
                $(selectors.productColor).val(product.color);

                initialiseStyleSelect(product.styleId, product.styleName);

                $(selectors.updateProductModal).modal('show');
            };

            // Ensure that the available quantity never exceeds the purchased amount.
            const validateAvailableQuantity = () => {
                if (state.selectedProductIndex === null) {
                    return;
                }

                const product = state.products[state.selectedProductIndex];
                const purchaseQuantity = Number($(selectors.purchaseQuantity).val());
                const availableQuantity = Number($(selectors.availableQuantity).val());

                if (availableQuantity > purchaseQuantity) {
                    window.alert("Available quantity can't be greater than purchase quantity.");
                    $(selectors.availableQuantity).val(product.availableQuantity);
                }
            };

            // Persist changes to an existing product on the invoice.
            const submitProductUpdate = () => {
                if (state.selectedProductIndex === null) {
                    return;
                }

                const product = state.products[state.selectedProductIndex];
                const name = $.trim($(selectors.productName).val());
                const purchaseQuantity = Number($(selectors.purchaseQuantity).val());
                const availableQuantity = Number($(selectors.availableQuantity).val());
                const price = Number($(selectors.salePrice).val());
                const size = $.trim($(selectors.productSize).val());
                const color = $.trim($(selectors.productColor).val());
                const styleId = $(selectors.productStyle).val();
                const styleName = $(selectors.productStyle).find('option:selected').text();

                if (!name || !styleId || Number.isNaN(purchaseQuantity) || Number.isNaN(availableQuantity) || Number.isNaN(price)) {
                    window.alert('Please complete the form before saving.');
                    return;
                }

                httpRequest({
                    url: '/update-product-details',
                    method: 'POST',
                    data: {
                        data: {
                            invoiceID: product.invoiceId,
                            pID: product.id,
                            pName: name,
                            Purchase: purchaseQuantity,
                            saleQuantity: availableQuantity,
                            salePrice: price,
                            size: size,
                            style: styleId,
                            color: color,
                            oldQty: product.purchaseQuantity,
                            oldPrice: product.price
                        }
                    }
                })
                    .done(() => {
                        product.name = name;
                        product.purchaseQuantity = purchaseQuantity;
                        product.availableQuantity = availableQuantity;
                        product.price = price;
                        product.size = size;
                        product.color = color;
                        product.styleId = Number(styleId);
                        product.styleName = styleName;

                        calculateInvoiceTotals();

                        populateProductsTable();
                        renderInvoiceSummary(state.activeInvoice);

                        $(selectors.updateProductModal).modal('hide');

                        if (typeof window.showSnackbar === 'function') {
                            window.showSnackbar('Product updated successfully.');
                        }
                    })
                    .fail(() => {
                        window.alert('Unable to update the product. Please try again.');
                    });
            };

            // Utility to populate <select> elements with server-provided options.
            const populateSelectOptions = ($select, items, { valueKey, textKey, placeholder, selectedValue }) => {
                $select.empty();

                if (placeholder) {
                    $select.append(new Option(placeholder, '', false, false));
                }

                (items || []).forEach((item) => {
                    const value = item[valueKey];
                    const text = item[textKey];
                    const isSelected = Number(value) === Number(selectedValue);

                    $select.append(new Option(text, value, isSelected, isSelected));
                });

                $select.trigger('change.select2');
            };

            // Retrieve the list of brands for the add-product modal.
            const loadBrandOptions = (selectedValue) => {
                const $brandSelect = $(selectors.newProductBrand);

                httpRequest({
                    url: '/get-brand',
                    method: 'GET'
                })
                    .done((brands) => {
                        populateSelectOptions($brandSelect, brands, {
                            valueKey: 'ID',
                            textKey: 'name',
                            placeholder: 'Select a brand',
                            selectedValue: selectedValue
                        });
                    })
                    .fail(() => {
                        window.alert('Unable to load brands. Please try again.');
                    });
            };

            // Retrieve the list of styles for the add-product modal.
            const loadStyleOptionsForNewProduct = (selectedValue) => {
                const $styleSelect = $(selectors.newProductStyle);

                httpRequest({
                    url: '/get-style',
                    method: 'GET'
                })
                    .done((styles) => {
                        populateSelectOptions($styleSelect, styles, {
                            valueKey: 'id',
                            textKey: 'name',
                            placeholder: 'Select a style',
                            selectedValue: selectedValue
                        });
                    })
                    .fail(() => {
                        window.alert('Unable to load styles. Please try again.');
                    });
            };

            // Clear the add-product form whenever the modal is closed.
            const resetNewProductForm = () => {
                $(selectors.newProductName).val('');
                $(selectors.newProductSize).val('');
                $(selectors.newProductColor).val('');
                $(selectors.newProductQuantity).val('');
                $(selectors.newProductPrice).val('');
                $(selectors.newProductBrand).val(null).trigger('change');
                $(selectors.newProductStyle).val(null).trigger('change');
            };

            // Launch the add-product modal for the active invoice.
            const openAddProductModal = () => {
                if (!state.activeInvoice) {
                    window.alert('Please select an invoice before adding products.');
                    return;
                }

                $(selectors.addProductInvoiceId).val(state.activeInvoice.invoiceId);

                loadBrandOptions();
                loadStyleOptionsForNewProduct();

                $(selectors.addProductModal).modal('show');
            };

            // Submit the add-product form and refresh the invoice.
            const submitNewProduct = () => {
                if (!state.activeInvoice) {
                    window.alert('Please select an invoice before adding products.');
                    return;
                }

                const name = $.trim($(selectors.newProductName).val());
                const size = $.trim($(selectors.newProductSize).val());
                const color = $.trim($(selectors.newProductColor).val());
                const quantity = Number($(selectors.newProductQuantity).val());
                const price = Number($(selectors.newProductPrice).val());
                const brandId = $(selectors.newProductBrand).val();
                const styleId = $(selectors.newProductStyle).val();

                if (!name || !brandId || !styleId || Number.isNaN(quantity) || quantity <= 0 || Number.isNaN(price) || price < 0) {
                    window.alert('Please provide valid product details before saving.');
                    return;
                }

                httpRequest({
                    url: '/save-purchase-old-invoice',
                    method: 'POST',
                    data: {
                        data1: [
                            {
                                product: name,
                                size: size,
                                color: color,
                                quantity: quantity,
                                price: price,
                                style: styleId,
                                Brand: brandId
                            },
                            {
                                boxID: state.activeInvoice.invoiceId
                            }
                        ]
                    }
                })
                    .done((response) => {
                        if (response && response.purchase) {
                            state.activeInvoice.totalPrice = response.purchase.price;
                            state.activeInvoice.totalQuantity = response.purchase.availableStock;
                        }

                        loadInvoiceProducts(state.activeInvoice);

                        $(selectors.addProductModal).modal('hide');
                        resetNewProductForm();

                        if (typeof window.showSnackbar === 'function') {
                            const message = response && response.message ? response.message : 'Product added successfully.';
                            window.showSnackbar(message);
                        }
                    })
                    .fail(() => {
                        window.alert('Unable to add the product. Please try again.');
                    });
            };

            // Initialise DataTables instances for the invoice and product listings.
            const initialiseDataTables = () => {
                if ($.fn.DataTable.isDataTable(selectors.invoicesTable)) {
                    invoiceDataTable = $(selectors.invoicesTable).DataTable();
                } else {
                    invoiceDataTable = $(selectors.invoicesTable).DataTable({
                        paging: true,
                        lengthChange: false,
                        searching: true,
                        ordering: false,
                        info: true,
                        autoWidth: false
                    });
                }

                if ($.fn.DataTable.isDataTable(selectors.productsTable)) {
                    productDataTable = $(selectors.productsTable).DataTable();
                } else {
                    productDataTable = $(selectors.productsTable).DataTable({
                        paging: true,
                        lengthChange: false,
                        searching: true,
                        ordering: false,
                        info: true,
                        autoWidth: false
                    });
                }
            };

            // Enable Select2 for the modal inputs.
            const initialiseSelects = () => {
                $(selectors.productStyle).select2({
                    dropdownParent: $(selectors.updateProductModal),
                    width: '100%'
                });

                $(selectors.newProductBrand).select2({
                    dropdownParent: $(selectors.addProductModal),
                    width: '100%'
                });

                $(selectors.newProductStyle).select2({
                    dropdownParent: $(selectors.addProductModal),
                    width: '100%'
                });
            };

            // Wire up DOM events for user interactions on the page.
            const bindEventListeners = () => {
                $(document).on('click', '.js-view-invoice', (event) => {
                    const $button = $(event.currentTarget);
                    const invoice = {
                        invoiceId: $button.data('invoice-id'),
                        supplierName: $button.data('supplier-name'),
                        totalPrice: Number($button.data('total-price')) || 0,
                        totalQuantity: Number($button.data('total-quantity')) || 0
                    };

                    loadInvoiceProducts(invoice);
                });

                $(document).on('click', '.js-edit-product', (event) => {
                    const index = Number($(event.currentTarget).data('product-index'));
                    openEditModal(index);
                });

                $(document).on('click', '.js-add-product', openAddProductModal);

                $(selectors.availableQuantity).on('input', validateAvailableQuantity);
                $(selectors.saveProductChanges).on('click', submitProductUpdate);
                $(selectors.saveNewProduct).on('click', submitNewProduct);

                $(selectors.addProductModal).on('hidden.bs.modal', resetNewProductForm);
            };

            $(document).ready(() => {
                initialiseDataTables();
                initialiseSelects();
                bindEventListeners();
            });

            // Public API exposed for future enhancements.
            window.StockPage = {
                reloadActiveInvoice: () => {
                    if (!state.activeInvoice) {
                        return;
                    }

                    loadInvoiceProducts(state.activeInvoice);
                }
            };
        })(window.jQuery, window, document);
    </script>
@endpush
