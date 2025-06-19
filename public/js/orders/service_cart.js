const service_cart = {
        selector: function () {
            return $('#service-selector');
        },
        cart: function () {
            return $('#service-cart');
        },
        orderDate: function () {
            return new Date($('#order_date').val());
        },
        guid: function () {
            return guid();
        },
        add: function () {
            $('#add-service-to-order').off().click(function () {

                service_cart.selector().find(':checked').each(function () {

                    $(this).prop('checked', false);

                    let item = $(this).closest('div.service-item'),
                        // Restriction attributes
                        service_group_id = item.closest('.service-grouped').data('service-group-id'),
                        stock = Number(item.data('stock')),
                        pec_enabled = Boolean(produceNumberFromInput(item.attr('data-pec-enabled')) && produceNumberFromInput($('#pec_enabled').val())),
                        pec_booked = produceNumberFromInput(item.attr('data-pec-booked')),
                        pec_max = produceNumberFromInput(item.attr('data-pec-max')),
                        unlimited_stock = Number(item.attr('data-unlimited')),
                        orderDate = document.querySelector('#order_date')._flatpickr.selectedDates[0],
                        hasPrice = false,
                        unbindedPrice = false,
                        priceTag = '-',
                        priceNet = 0,
                        priceVat = 0,
                        priceId = 0,
                        vat_id = 1,
                        restock = stock - 1,
                        pec_is_maxed = false,
                        pec_copies = false;


                    console.log(pec_booked, 'pec_booked');
                    if (pec_enabled) {
                        if (pec_max > 0 && pec_booked >= pec_max) {
                            pec_is_maxed = true;
                        }

                        if (!pec_is_maxed) {
                            let copies = service_cart.cart().find('.service-' + item.attr('data-id'));
                            if (copies.length) {
                                copies.each(function () {
                                    if ($(this).find('.pec-maxed').length || $(this).find('.pec-just-maxed')) {
                                        pec_is_maxed = true;
                                        pec_copies = true;
                                        return false;
                                    }
                                });
                                let totalPecAsked = 0;
                                copies.each(function () {
                                    totalPecAsked += produceNumberFromInput($(this).find('.qty').val());
                                });

                                if (totalPecAsked >= pec_max) {
                                    pec_is_maxed = true;
                                    pec_copies = true;
                                }
                            }
                        }
                    }

                    item.find('.error.text-danger, .alert').remove();

                    if (stock < 1 && unlimited_stock === 0) {
                        item.append('<span class="text-danger error">Aucun stock disponible</span>');
                        return false;
                    }

                    service_cart.downStock(item, restock);

                    let prices = item.find('.price');
                    for (let i = 0; i < prices.length; i++) {
                        let priceElement = $(prices[i]);
                        if (!hasPrice) {
                            let dataEnds = priceElement.data('ends');
                            if (dataEnds) {
                                if (orderDate <= new Date(dataEnds)) {
                                    hasPrice = true;
                                }
                            } else {
                                hasPrice = true;
                                unbindedPrice = true;
                            }
                        }
                        if (hasPrice) {
                            priceTag = priceElement.attr('data-price');
                            priceNet = priceElement.attr('data-net');
                            priceVat = priceElement.attr('data-vat');
                            priceId = priceElement.attr('data-price-id');
                            vat_id = priceElement.attr('data-vat_id');
                            break;
                        }
                    }

                    let priceTotal = priceTag,
                        row = $($('#service_cart_template').html()),
                        guid = service_cart.guid(),
                        maincell = row.find('td.label');
                    row.attr('data-identifier', guid).addClass('service-' + item.data('id') + ' date-' + $(this).data('date') + ' service-group-' + service_group_id);
                    if (unlimited_stock) {
                        row.addClass('unlimited_stock');
                    }
                    row.find('a').attr('data-identifier', guid);
                    maincell.find('.main').text(item.find('.main label').text());
                    maincell.find(':hidden').val(item.data('id'));
                    if (unbindedPrice) {
                        maincell.find('.text-danger').append('Prix sans date butoir.<br>');
                    }
                    if (priceTag === '-') {
                        maincell.find('.text-danger').append('Absence de prix.<br>Cette prestation ne sera pas ajoutée.');
                    }
                    if (isNaN(Number(priceTag))) {
                        $('<div class="alert alert-danger simplified">Absence de prix. Cette prestation ne sera pas ajoutée.</div>').insertAfter(item.find('>div:first-of-type'));

                    } else {

                        if (pec_enabled) {

                            row.find('.pec_label').removeClass('d-none');
                            row.find('.max_pec').html(item.find('.max_pec').html());
                            if (pec_is_maxed) {
                                row.find('.pec_label').append('<span class="d-block ' + (pec_copies ? 'pec-copies' : '') + ' pec-maxed text-danger">PEC déjà atteinte</span>');
                            }
                        }

                        let shouldBeZero = pec_enabled && !pec_is_maxed;

                        row.find('td.price').attr('data-unit-price', priceTag).attr('data-price-net', priceNet).attr('data-vat', priceVat).find('input').val(priceTag);
                        row.find('td.price_ht input').val(shouldBeZero ? 0 : priceNet)
                        row.find('td.vat input').val(shouldBeZero ? 0 : priceVat);
                        row.find('.pec_enabled').val(pec_copies ? 0 : Number(pec_enabled));
                        row.find('td.price_total input').val(shouldBeZero ? 0 : priceTotal);
                        row.find('td.quantity').attr('data-pec-maxed', Number(pec_is_maxed)).attr('data-pec-booked', pec_booked);
                        row.find('.pec_max').val(pec_max).end().find('.pec_booked').val(pec_booked).end().find('.vat_id').val(vat_id);
                        service_cart.cart().append(row);

                        service_cart.manipulateRow(row);
                        service_cart.calculateTotal();

                        if (unlimited_stock === 0) { // Not unlimited stock
                            console.log('updating unlimited_stock');
                            service_cart.updateStock(row, 'decreaseShoppableStock', 1);
                        }

                    }
                });
                manageOrderBtnStatus();
            });
        },

        manipulateRow: function (row) {
            let qty = $(row).find('input.qty');

            qty.off().change(function () {

                    console.log('triggered change in qty');
                    if (produceNumberFromInput($(this).val()) < 1) {
                        $(this).val(1);
                    }

                    let postvalue = produceNumberFromInput($(this).val()),
                        prevalue = produceNumberFromInput(qty.attr('data-qty'));

                    //console.log(postvalue, prevalue, 'postvalue, prevalue');

                    service_cart.calculateRow(row);

                    if (!$(row).hasClass('unlimited_stock') && postvalue !== prevalue) {
                        let action = postvalue > prevalue ? 'decreaseShoppableStock' : 'increaseShoppableStock';
                        console.log('ACTION STOCK', action);
                        service_cart.updateStock(row, action, prevalue);
                    }

                    qty.attr('data-qty', postvalue);
                },
            );

        },
        calculateRow: function (row) {
            calculateServiceRow(row);
        },
        calculateTotal() {
            calculateServiceCartTotals(this.cart(), this.totals);
        }
        ,
        totals: $('#service-total'),
        updateStock:
            function (row, action, prevalue) {

                let account_type = $('#client-type-selector :checked').val();

                ajax('action=' + action
                    + '&callback=resetServiceCartSelectablesStock'
                    + '&shoppable_model=' + this.cart().data('shoppable')
                    + '&shoppable_id=' + row.find('.service_id').val()
                    + '&pec_enabled=' + row.find('.pec_enabled').val()
                    + '&pec_max=' + row.find('.pec_max').val()
                    + '&pec_maxed=' + row.find('.quantity').attr('data-pec-maxed')
                    + '&pec_booked=' + row.find('.quantity').attr('data-pec-booked')
                    + '&shoppable_id=' + row.find('.service_id').val()
                    + '&prevalue=' + prevalue
                    + '&order_uuid=' + $('#order_uuid').val()
                    + '&identifier=' + row.attr('data-identifier')
                    + '&quantity=' + row.find('.qty').val()
                    + '&cart_id=' + row.data('cart-id')
                    + '&row_id=' + row.data('identifier')
                    + '&account_type=' + account_type
                    + '&account_id=' + $('#order_' + account_type + '_id').val()
                    + '&' + row.find('input').serialize(),
                    $('#service_cart_messages'), {'keepMessages': true});
            }

        ,
        downStock: function (item, stock) {
            item.attr('data-stock', stock).find('.stock-remaining').text(stock);
        }
        ,
        init: function () {

            if (Number($('#order_uuid').attr('data-has-errors')) === 1) {
                this.cart().find('tr').each(function () {
                    service_cart.calculateRow($(this));
                });
                this.calculateTotal();
            }
            this.add();
            this.cart().find('tr').each(function () {
                service_cart.manipulateRow($(this));
            });
        }
        ,
    }
;
service_cart.init();
