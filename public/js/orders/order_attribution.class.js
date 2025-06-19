class AttributionCart {
    constructor(serviceSelector) {
        this.serviceSelector = serviceSelector;
        this.responses = $(`#${this.serviceSelector}-cart-messages`);
        this.isAccommodation = this.serviceSelector === 'accommodation';
    }

    cart() {
        return $(`#${this.serviceSelector}-cart`);
    }

    services() {
        return this.cart().find('tr.order-' + this.serviceSelector + '-attribution-row').find(':checked');
    }


    add() {
        $(`#${this.serviceSelector}-distributor`).click(() => {
            this.responses.html('');
            let members = $('#members :checked'),
                services = this.services(),
                currentlyAffected = [],
                sql = [],
                translations = $('#attribution-messages');

            //console.log(members.length, 'Members');
            if (!members.length) {
                this.responses.html('<div class="alert alert-danger">' + translations.find('.at_least_one_member').text() + '</div>');
                return false;
            }

            if (!services.length) {
                this.responses.html('<div class="alert alert-danger">' + translations.find('.at_least_one_atttribution').text() + '</div>');
                return false;
            }

            services.each((index, service) => {
                let row = $($('#affected-' + this.serviceSelector).html()),
                    source = $(service).closest('.order-' + this.serviceSelector + '-attribution-row'),
                    service_id = source.find(':checkbox').val(),
                    remaining = produceNumberFromInput(source.find('.remaining').text()),
                    affectable_date = source.attr('data-date'),
                    affected_qty = produceNumberFromInput(source.find('.qty input').val());
                var max_quantity = produceNumberFromInput(source.find('.max-qty').data('max'));


                if (max_quantity > 0 && affected_qty > max_quantity) {
                    this.responses.html('<div class="alert alert-danger">' + translations.find('.groupmax').text() + max_quantity + '</div>');
                    return false;
                }

                if (affected_qty < 1) {
                    this.responses.html('<div class="alert alert-danger">' + translations.find('.minimal').text() + '</div>');
                    return false;
                }

                if (affected_qty > remaining) {
                    this.responses.html('<div class="alert alert-danger">' + translations.find('.overflow').text() + '</div>');
                    return false;
                }

                if (members.length > remaining) {
                    this.responses.html('<div class="alert alert-danger">' + translations.find('.unsufficient').text() + '</div>');
                    return false;
                }
                row.addClass(this.serviceSelector + '-' + service_id);
                row.find('.title').text($(service).parent().text());
                row.find('.service-date').text(source.find('.service-date').text());
                row.find('.qty').text(affected_qty);
                row.find('.affected-date').prepend(this.getFormattedDate());

				let hasError = false; // <--- nouvelle variable

				members.each((index, memberElement) => {
					let randomID = guid(),
						member_class = `.member-${$(memberElement).val()}`,
						member = $(member_class),
						service_class = member_class + '.affected-service.' + this.serviceSelector + '-' + service_id + (this.isAccommodation ? '-' + affectable_date : ''),
						affected_service = $(service_class),
						member_error = member.find('.error'),
						canAdd = true;

					member_error.addClass('d-none');

					if (affected_service.length) {
						if (this.isAccommodation) {
							member_error.removeClass('d-none').text(translations.find('.already_assigned').text());
							canAdd = false;
							hasError = true; // <--- erreur détectée
						}

						let previously_affected_qty = 0;
						affected_service.find('.qty').each(function () {
							previously_affected_qty += produceNumberFromInput($(this).text());
						});

						if (max_quantity > 0 && previously_affected_qty >= max_quantity) {
							member_error.removeClass('d-none').text(translations.find('.groupmax').text() + max_quantity);
							canAdd = false;
							hasError = true;
						}
					}

					if (this.isAccommodation) {
						let crossBookings = $(member_class + '.bookings').find('small'),
							crossAttributions = $(member_class + '.cross-attributions').find('small');

						if (crossBookings.length && crossBookings.filter('small[data-date="' + affectable_date + '"]').length) {
							canAdd = false;
							member_error.removeClass('d-none').text(translations.find('.already_booked_elsewhere').text());
							hasError = true;
						}

						if (crossAttributions.length && crossAttributions.filter('small[data-date=' + affectable_date + ']').length) {
							canAdd = false;
							member_error.removeClass('d-none').text(translations.find('.already_assigned_elsewhere').text());
							hasError = true;
						}
					}

					if (canAdd) {
						member.find('table tbody').append(row.clone());
						let lastRow = member.find('.affected-service').last();
						lastRow.addClass(randomID);
						lastRow.find('a').attr('data-identifier', randomID);

						currentlyAffected.push({'service_id': service_id, 'count': affected_qty});
						sql.push({
							'identifier': randomID,
							'service_id': service_id,
							'date': source.data('date'),
							'quantity': affected_qty,
							'event_contact_id': $(memberElement).val()
						});
					}
				});

				if (hasError) {
				  $('.messages').first().html(`
					<div class="alert alert-danger">
					  Certains membres n’ont pas pu être affectés au service sélectionné.
					</div>
				  `);
				} else {
				  $('.messages').first().empty();
				}
            });

            this.updateDistrubutionCounter(currentlyAffected);
            this.updateSqlAttrubution(sql, 'updateOrderServiceAttributions');

            services.prop('checked', false);
        });
    }

    manipulateRow(row) {
    }

    updateSqlAttrubution(sql, action) {
        let form = $('#attributions-form');
        if (sql.length) {
            ajax('action=update_' + this.serviceSelector + 'OrderAttributions' +
                '&order_id=' + form.data('order-id') +
                '&event_id=' + form.data('event-id') +
                '&group_id=' + form.data('group-id') +
                '&origin=' + form.data('origin') +
                '&' + this.toQueryString(sql), $('#' + this.serviceSelector + '-attribution-messages'));
        }
    }

    updateDistrubutionCounter(currentlyAffected) {

        if (currentlyAffected.length) {

            let countByServiceId = currentlyAffected.reduce((accumulator, item) => {
                if (accumulator[item.service_id]) {
                    accumulator[item.service_id] += item.count;
                } else {
                    accumulator[item.service_id] = item.count;
                }
                return accumulator;
            }, {});

            for (const [key, value] of Object.entries(countByServiceId)) {
                let row = $('.order-' + this.serviceSelector + '-attribution-row.' + this.serviceSelector + '-' + key),
                    distributed = row.find('.distributed'),
                    remaining = row.find('.remaining'),
                    updatedDistributed = produceNumberFromInput($.trim(distributed.text())) + produceNumberFromInput(value),
                    updatedRemaining = produceNumberFromInput($.trim(remaining.text())) - produceNumberFromInput(value);


                remaining.text(updatedRemaining);
                distributed.text(updatedDistributed);
            }
        }
    }

    toQueryString(arr) {
        return arr.map((obj, index) => {
            return Object.keys(obj).map(key => {
                return encodeURIComponent(`${key}[${index}]`) + '=' + encodeURIComponent(obj[key]);
            }).join('&');
        }).join('&');
    }

    init() {
        this.add();
        $('.order-' + this.serviceSelector + '-attribution-row').each((index, row) => {
            this.manipulateRow($(row));
        });
    }

    getFormattedDate() {

        let date = new Date();
        let day = String(date.getDate()).padStart(2, '0');
        let month = String(date.getMonth() + 1).padStart(2, '0');
        let year = date.getFullYear();

        return `${day}/${month}/${year}`;
    }
}


