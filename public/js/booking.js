/**
 * Booking Page – Reactive Interaction Logic
 * Extracted from booking.blade.php for maintainability and
 * to avoid HTML-parser / JS-string conflicts in inline scripts.
 */

// ── State ──────────────────────────────────────────────────
let selectedRoom = null;
let nights = 0;
let subtotal = 0;
let discountPercent = 0;
let discountAmount = 0;
let taxAmount = 0;
let totalAmount = 0;
let selectedPaymentMethod = null;

let includeBreakfast = false;
let includeExtraBed = false;
let lateCheckout = false;
let breakfastCost = 0;
let extraBedCost = 0;
let lateCheckoutCost = 0;

// ── Defensive DOM Helpers ──────────────────────────────────
function setElText(id, text) {
    var el = document.getElementById(id);
    if (el) el.textContent = text;
}

function setElClass(id, action, className) {
    var el = document.getElementById(id);
    if (el) {
        if (action === 'add') el.classList.add(className);
        else if (action === 'remove') el.classList.remove(className);
    }
}

// ── Date Utilities ─────────────────────────────────────────
function formatDateValue(date) {
    try {
        var y = date.getFullYear();
        var m = String(date.getMonth() + 1).padStart(2, '0');
        var d = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    } catch (err) {
        return '';
    }
}

function formatDateDisplay(dateStr) {
    if (!dateStr) return '-';
    var date = new Date(dateStr);
    if (isNaN(date.getTime())) return '-';
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
}

// ── Date Change Handler ────────────────────────────────────
function handleDateChange() {
    try {
        var checkInInput = document.getElementById('check-in-input');
        var checkOutInput = document.getElementById('check-out-input');
        if (!checkInInput || !checkOutInput) return;

        var checkInDate = new Date(checkInInput.value);
        var checkOutDate = new Date(checkOutInput.value);

        if (!isNaN(checkInDate.getTime()) && !isNaN(checkOutDate.getTime())) {
            if (checkOutDate <= checkInDate) {
                checkOutDate = new Date(checkInDate);
                checkOutDate.setDate(checkOutDate.getDate() + 1);
                checkOutInput.value = formatDateValue(checkOutDate);
            }

            var minCheckout = new Date(checkInDate);
            minCheckout.setDate(minCheckout.getDate() + 1);
            checkOutInput.min = formatDateValue(minCheckout);
        }

        calculateNights();
    } catch (err) {
        console.error('Error in handleDateChange:', err);
    }
}

// ── Nights Calculation ─────────────────────────────────────
function calculateNights() {
    try {
        var checkInInput = document.getElementById('check-in-input');
        var checkOutInput = document.getElementById('check-out-input');
        var checkInVal = checkInInput ? checkInInput.value : '';
        var checkOutVal = checkOutInput ? checkOutInput.value : '';

        if (checkInVal && checkOutVal) {
            var checkIn = new Date(checkInVal);
            var checkOut = new Date(checkOutVal);
            if (!isNaN(checkIn.getTime()) && !isNaN(checkOut.getTime())) {
                nights = Math.ceil(Math.abs(checkOut - checkIn) / (1000 * 60 * 60 * 24));
            } else {
                nights = 0;
            }
        } else {
            nights = 0;
        }

        setElText('summary-check-in', formatDateDisplay(checkInVal));
        setElText('summary-check-out', formatDateDisplay(checkOutVal));
        setElText('summary-nights', nights + ' night(s)');

        // Update stay price in room cards
        document.querySelectorAll('.room-card-duration-price').forEach(function(el) {
            var basePrice = parseInt(el.getAttribute('data-base-price')) || 0;
            if (nights > 0 && basePrice > 0) {
                var stayPrice = basePrice * nights;
                el.textContent = 'Price for ' + nights + ' night(s): RP ' + stayPrice.toLocaleString('id-ID');
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        });

        recalculatePricing();
        fetchRoomAvailability();
    } catch (err) {
        console.error('Error in calculateNights:', err);
    }
}

// ── Reset Add-on State Helper ──────────────────────────────
function resetAddonState(addonKey) {
    try {
        var checkbox = document.getElementById('addon-' + addonKey + '-checkbox');
        if (checkbox && checkbox.checked) {
            checkbox.checked = false;
            var card = document.getElementById('opt-' + addonKey);
            var indicator = document.getElementById('indicator-' + addonKey);
            if (card) {
                card.style.borderColor = '';
                card.style.backgroundColor = '';
            }
            if (indicator) {
                indicator.style.borderColor = '';
                indicator.style.backgroundColor = '';
                indicator.style.color = '';
                indicator.innerHTML = '<span class="material-symbols-outlined text-xs font-bold leading-none">close</span>';
            }
        }
    } catch (err) {
        console.error('Error in resetAddonState:', err);
    }
}

// ── Room Selection ─────────────────────────────────────────
function selectRoom(roomId) {
    try {
        var card = document.getElementById('room-card-' + roomId);
        if (!card) {
            console.warn('Room card not found for ID: ' + roomId);
            return;
        }

        var name = card.getAttribute('data-room-name') || 'Room';
        var rawPrice = card.getAttribute('data-room-price');
        var price = parseInt(rawPrice) || 0;

        selectedRoom = { id: roomId, name: name, price: price };

        // Reset all card states
        document.querySelectorAll('.room-card').forEach(function (c) {
            c.classList.remove('ring-2', 'ring-amber-700', 'border-amber-700/50');
            var btn = c.querySelector('.btn-select-room');
            if (btn) {
                btn.textContent = 'Select Room';
                btn.className = 'btn-select-room border border-amber-700 text-amber-700 hover:bg-amber-50 px-5 py-2 rounded-lg text-xs font-bold tracking-wide transition cursor-pointer select-none';
            }
        });

        // Highlight selected card
        card.classList.add('ring-2', 'ring-amber-700', 'border-amber-700/50');
        var selectBtn = card.querySelector('.btn-select-room');
        if (selectBtn) {
            selectBtn.textContent = 'Selected ✓';
            selectBtn.className = 'btn-select-room bg-amber-700 text-white px-5 py-2 rounded-lg text-xs font-bold tracking-wide transition cursor-pointer select-none';
        }

        // Update add-ons visibility
        var allowBreakfast = card.getAttribute('data-allow-breakfast') === '1';
        var allowExtraBed = card.getAttribute('data-allow-extra-bed') === '1';
        var allowLateCheckout = card.getAttribute('data-allow-late-checkout') === '1';

        if (allowBreakfast) {
            setElClass('opt-breakfast', 'remove', 'hidden');
        } else {
            setElClass('opt-breakfast', 'add', 'hidden');
            resetAddonState('breakfast');
        }

        if (allowExtraBed) {
            setElClass('opt-extra-bed', 'remove', 'hidden');
        } else {
            setElClass('opt-extra-bed', 'add', 'hidden');
            resetAddonState('extra-bed');
        }

        if (allowLateCheckout) {
            setElClass('opt-late-checkout', 'remove', 'hidden');
        } else {
            setElClass('opt-late-checkout', 'add', 'hidden');
            resetAddonState('late-checkout');
        }

        // Update summary card
        setElClass('summary-no-room', 'add', 'hidden');
        setElClass('summary-room-details', 'remove', 'hidden');
        setElText('summary-room-name', name);
        setElText('summary-room-rate', 'RP ' + price.toLocaleString('id-ID'));

        recalculatePricing();
    } catch (err) {
        console.error('Error in selectRoom:', err);
    }
}

// ── Room Availability State & Functions ───────────────────
let bookedRoomIdsMap = {};

function deselectCurrentRoom() {
    try {
        selectedRoom = null;
        
        // Reset all card states (remove rings, etc.)
        document.querySelectorAll('.room-card').forEach(function (c) {
            c.classList.remove('ring-2', 'ring-amber-700', 'border-amber-700/50');
            var btn = c.querySelector('.btn-select-room');
            if (btn && !c.classList.contains('opacity-60')) {
                btn.textContent = 'Select Room';
                btn.className = 'btn-select-room border border-amber-700 text-amber-700 hover:bg-amber-50 px-5 py-2 rounded-lg text-xs font-bold tracking-wide transition cursor-pointer select-none';
            }
        });

        // Update summary card to "no room" state
        setElClass('summary-no-room', 'remove', 'hidden');
        setElClass('summary-room-details', 'add', 'hidden');
        setElText('summary-room-name', '');
        setElText('summary-room-rate', '');

        recalculatePricing();
    } catch (err) {
        console.error('Error in deselectCurrentRoom:', err);
    }
}

function fetchRoomAvailability() {
    try {
        var checkInInput = document.getElementById('check-in-input');
        var checkOutInput = document.getElementById('check-out-input');
        if (!checkInInput || !checkOutInput) return;

        var checkInVal = checkInInput.value;
        var checkOutVal = checkOutInput.value;

        if (!checkInVal || !checkOutVal) return;

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/booking/check-availability?check_in=' + checkInVal + '&check_out=' + checkOutVal, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(function(res) {
            if (!res.ok) throw new Error('Failed to fetch availability.');
            return res.json();
        })
        .then(function(data) {
            bookedRoomIdsMap = {};
            if (data.booked_rooms) {
                data.booked_rooms.forEach(function(room) {
                    bookedRoomIdsMap[room.room_id] = room;
                });
            }
            updateRoomsAvailabilityUI();
        })
        .catch(function(err) {
            console.error('Error checking room availability:', err);
        });
    } catch (err) {
        console.error('Error in fetchRoomAvailability:', err);
    }
}

function updateRoomsAvailabilityUI() {
    try {
        var toggleAvailableOnly = document.getElementById('toggle-available-only');
        var hideBooked = toggleAvailableOnly ? toggleAvailableOnly.checked : false;

        document.querySelectorAll('.room-card').forEach(function(card) {
            var roomId = parseInt(card.getAttribute('data-room-id'));
            var isBooked = bookedRoomIdsMap.hasOwnProperty(roomId);

            var statusContainer = card.querySelector('.room-booked-status-container');
            var untilDateEl = card.querySelector('.room-booked-until-date');
            var selectBtn = card.querySelector('.btn-select-room');

            if (isBooked) {
                // If the selected room is booked, deselect it!
                if (selectedRoom && selectedRoom.id === roomId) {
                    deselectCurrentRoom();
                }

                // Add gray out styling & disable events
                card.classList.add('opacity-60', 'grayscale-[30%]', 'pointer-events-none');
                
                // Show notice
                if (statusContainer) statusContainer.classList.remove('hidden');
                if (untilDateEl) untilDateEl.textContent = bookedRoomIdsMap[roomId].check_out_formatted;

                // Disable select button
                if (selectBtn) {
                    selectBtn.textContent = 'Already Booked';
                    selectBtn.className = 'btn-select-room bg-gray-100 text-gray-400 border border-gray-200 px-5 py-2 rounded-lg text-xs font-bold tracking-wide transition cursor-not-allowed select-none pointer-events-none';
                }

                // Toggle visibility based on filter
                if (hideBooked) {
                    card.classList.add('hidden');
                } else {
                    card.classList.remove('hidden');
                }
            } else {
                // Restore normal state
                card.classList.remove('opacity-60', 'grayscale-[30%]', 'pointer-events-none');
                
                if (statusContainer) statusContainer.classList.add('hidden');

                // If this room is currently selected, make sure button says Selected ✓
                if (selectedRoom && selectedRoom.id === roomId) {
                    card.classList.add('ring-2', 'ring-amber-700', 'border-amber-700/50');
                    if (selectBtn) {
                        selectBtn.textContent = 'Selected ✓';
                        selectBtn.className = 'btn-select-room bg-amber-700 text-white px-5 py-2 rounded-lg text-xs font-bold tracking-wide transition cursor-pointer select-none';
                    }
                } else {
                    if (selectBtn) {
                        selectBtn.textContent = 'Select Room';
                        selectBtn.className = 'btn-select-room border border-amber-700 text-amber-700 hover:bg-amber-50 px-5 py-2 rounded-lg text-xs font-bold tracking-wide transition cursor-pointer select-none';
                    }
                }

                card.classList.remove('hidden');
            }
        });
    } catch (err) {
        console.error('Error in updateRoomsAvailabilityUI:', err);
    }
}

// ── Pricing Recalculation ──────────────────────────────────
function recalculatePricing() {
    try {
        if (!selectedRoom || nights === 0) {
            setElText('summary-subtotal', 'RP 0');
            setElClass('summary-discount-row', 'add', 'hidden');
            setElText('summary-tax', 'RP 0');
            setElText('summary-total', 'RP 0');
            
            setElClass('summary-breakfast-row', 'add', 'hidden');
            setElClass('summary-extra-bed-row', 'add', 'hidden');
            setElClass('summary-late-checkout-row', 'add', 'hidden');
            return;
        }

        subtotal = selectedRoom.price * nights;
        setElText('summary-subtotal', 'RP ' + subtotal.toLocaleString('id-ID'));

        // Calculate Extras
        var adults = parseInt(document.getElementById('adults-input').value) || 2;
        var children = parseInt(document.getElementById('children-input').value) || 0;
        var totalGuests = adults + children;

        // Breakfast
        var breakfastCheckbox = document.getElementById('addon-breakfast-checkbox');
        includeBreakfast = breakfastCheckbox ? breakfastCheckbox.checked : false;
        if (includeBreakfast) {
            breakfastCost = 50000 * totalGuests * nights;
            setElText('summary-breakfast-amount', 'RP ' + breakfastCost.toLocaleString('id-ID'));
            setElClass('summary-breakfast-row', 'remove', 'hidden');
        } else {
            breakfastCost = 0;
            setElClass('summary-breakfast-row', 'add', 'hidden');
        }

        // Extra Bed
        var extraBedCheckbox = document.getElementById('addon-extra-bed-checkbox');
        includeExtraBed = extraBedCheckbox ? extraBedCheckbox.checked : false;
        if (includeExtraBed) {
            extraBedCost = 150000 * nights;
            setElText('summary-extra-bed-amount', 'RP ' + extraBedCost.toLocaleString('id-ID'));
            setElClass('summary-extra-bed-row', 'remove', 'hidden');
        } else {
            extraBedCost = 0;
            setElClass('summary-extra-bed-row', 'add', 'hidden');
        }

        // Late Check-out
        var lateCheckoutCheckbox = document.getElementById('addon-late-checkout-checkbox');
        lateCheckout = lateCheckoutCheckbox ? lateCheckoutCheckbox.checked : false;
        if (lateCheckout) {
            lateCheckoutCost = 100000;
            setElText('summary-late-checkout-amount', 'RP ' + lateCheckoutCost.toLocaleString('id-ID'));
            setElClass('summary-late-checkout-row', 'remove', 'hidden');
        } else {
            lateCheckoutCost = 0;
            setElClass('summary-late-checkout-row', 'add', 'hidden');
        }

        var extrasTotal = breakfastCost + extraBedCost + lateCheckoutCost;

        if (discountPercent > 0) {
            discountAmount = Math.round(subtotal * (discountPercent / 100));
            setElText('summary-discount-label', 'Discount (' + discountPercent + '%):');
            setElText('summary-discount-amount', '-RP ' + discountAmount.toLocaleString('id-ID'));
            setElClass('summary-discount-row', 'remove', 'hidden');
        } else {
            discountAmount = 0;
            setElClass('summary-discount-row', 'add', 'hidden');
        }

        var taxableAmount = subtotal - discountAmount + extrasTotal;
        taxAmount = Math.round(taxableAmount * 0.1);
        setElText('summary-tax', 'RP ' + taxAmount.toLocaleString('id-ID'));

        totalAmount = taxableAmount + taxAmount;
        setElText('summary-total', 'RP ' + totalAmount.toLocaleString('id-ID'));
    } catch (err) {
        console.error('Error in recalculatePricing:', err);
    }
}

// ── Toggle Extras Addon ────────────────────────────────────
function toggleAddon(addonKey) {
    try {
        var checkbox = document.getElementById('addon-' + addonKey + '-checkbox');
        var card = document.getElementById('opt-' + addonKey);
        var indicator = document.getElementById('indicator-' + addonKey);

        if (!checkbox || !card || !indicator) return;

        checkbox.checked = !checkbox.checked;

        if (checkbox.checked) {
            card.style.borderColor = '#b45309'; // amber-700
            card.style.backgroundColor = '#fffbeb'; // amber-50
            indicator.style.borderColor = '#10b981'; // emerald-500
            indicator.style.backgroundColor = '#dcfce7'; // emerald-100
            indicator.style.color = '#15803d'; // emerald-700
            indicator.innerHTML = '<span class="material-symbols-outlined text-xs font-bold leading-none">check</span>';
        } else {
            card.style.borderColor = '';
            card.style.backgroundColor = '';
            indicator.style.borderColor = '';
            indicator.style.backgroundColor = '';
            indicator.style.color = '';
            indicator.innerHTML = '<span class="material-symbols-outlined text-xs font-bold leading-none">close</span>';
        }

        recalculatePricing();
    } catch (err) {
        console.error('Error in toggleAddon:', err);
    }
}

// ── Modal Step Navigation ──────────────────────────────────
function goToStep(step) {
    var steps = ['details', 'payment', 'processing', 'receipt'];
    steps.forEach(function (s) {
        var el = document.getElementById('modal-step-' + s);
        if (el) el.classList.add('hidden');
    });

    var target = document.getElementById('modal-step-' + step);
    if (target) target.classList.remove('hidden');
}

function goToPaymentStep() {
    var amtEl = document.getElementById('transfer-amount-display');
    if (amtEl) amtEl.textContent = 'RP ' + totalAmount.toLocaleString('id-ID');
    
    // Reset file upload state
    var fileInput = document.getElementById('payment-proof-input');
    if (fileInput) fileInput.value = '';
    
    var placeholder = document.getElementById('upload-placeholder');
    var preview = document.getElementById('upload-success-preview');
    if (placeholder) placeholder.classList.remove('hidden');
    if (preview) preview.classList.add('hidden');
    
    var payBtn = document.getElementById('btn-pay-now');
    if (payBtn) {
        payBtn.setAttribute('disabled', true);
        payBtn.className = 'w-full bg-gray-300 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide transition flex items-center justify-center gap-2 cursor-not-allowed select-none';
    }

    goToStep('payment');
}

function handleFileSelect(e) {
    var file = e.target.files[0];
    var placeholder = document.getElementById('upload-placeholder');
    var preview = document.getElementById('upload-success-preview');
    var filenameEl = document.getElementById('upload-filename');
    var payBtn = document.getElementById('btn-pay-now');

    if (file) {
        if (placeholder) placeholder.classList.add('hidden');
        if (preview) preview.classList.remove('hidden');
        if (filenameEl) filenameEl.textContent = file.name;
        
        if (payBtn) {
            payBtn.removeAttribute('disabled');
            payBtn.className = 'w-full bg-amber-700 hover:bg-amber-800 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-lg shadow-amber-700/20 transition flex items-center justify-center gap-2 cursor-pointer select-none';
        }
    } else {
        if (placeholder) placeholder.classList.remove('hidden');
        if (preview) preview.classList.add('hidden');
        
        if (payBtn) {
            payBtn.setAttribute('disabled', true);
            payBtn.className = 'w-full bg-gray-300 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide transition flex items-center justify-center gap-2 cursor-not-allowed select-none';
        }
    }
}

function submitBooking() {
    var fileInput = document.getElementById('payment-proof-input');
    if (!fileInput || !fileInput.files[0]) {
        alert('Please upload your payment transfer receipt first!');
        return;
    }

    goToStep('processing');

    var name = document.getElementById('guest-name').value.trim();
    var email = document.getElementById('guest-email').value.trim();
    var phone = document.getElementById('guest-phone').value.trim();
    var country = document.getElementById('guest-country').value.trim();
    var requests = document.getElementById('guest-requests').value.trim();
    var checkIn = document.getElementById('check-in-input').value;
    var checkOut = document.getElementById('check-out-input').value;
    var adults = document.getElementById('adults-input').value;
    var children = document.getElementById('children-input').value;

    var formData = new FormData();
    formData.append('room_id', selectedRoom.id);
    formData.append('guest_name', name);
    formData.append('guest_email', email);
    formData.append('guest_phone', phone);
    formData.append('guest_country', country);
    formData.append('special_requests', requests);
    formData.append('include_breakfast', includeBreakfast ? 1 : 0);
    formData.append('include_extra_bed', includeExtraBed ? 1 : 0);
    formData.append('late_checkout', lateCheckout ? 1 : 0);
    formData.append('check_in', checkIn);
    formData.append('check_out', checkOut);
    formData.append('nights', nights);
    formData.append('adults', adults);
    formData.append('children', children);
    formData.append('subtotal', subtotal);
    formData.append('discount', discountAmount);
    formData.append('tax', taxAmount);
    formData.append('total_price', totalAmount);
    formData.append('payment_proof', fileInput.files[0]);

    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch('/booking', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(function(res) {
        return res.json().then(function(data) {
            if (!res.ok) {
                throw new Error(data.message || data.error || 'Something went wrong during submission.');
            }
            return data;
        });
    })
    .then(function(data) {
        var booking = data.booking;
        
        // Fill receipt data
        document.getElementById('receipt-invoice-no').textContent = booking.invoice_no;
        document.getElementById('receipt-date').textContent = booking.date;
        document.getElementById('receipt-guest-name').textContent = booking.guest_name;
        document.getElementById('receipt-room-name').textContent = booking.room_name;
        document.getElementById('receipt-check-in').textContent = formatDateDisplay(booking.check_in);
        document.getElementById('receipt-check-out').textContent = formatDateDisplay(booking.check_out);
        document.getElementById('receipt-nights').textContent = booking.nights + ' night(s)';
        
        var guestsText = booking.adults + ' adult(s)';
        if (parseInt(booking.children) > 0) guestsText += ', ' + booking.children + ' child(ren)';
        document.getElementById('receipt-guests').textContent = guestsText;
        document.getElementById('receipt-payment-method').textContent = booking.payment_method;

        // Pricing
        document.getElementById('receipt-subtotal').textContent = 'RP ' + booking.subtotal.toLocaleString('id-ID');
        
        // Dynamic extras in receipt
        var recBreakfastRow = document.getElementById('receipt-breakfast-row');
        if (booking.include_breakfast && booking.include_breakfast != 0) {
            var totalGuests = parseInt(booking.adults) + parseInt(booking.children);
            var bCost = 50000 * totalGuests * parseInt(booking.nights);
            document.getElementById('receipt-breakfast-amount').textContent = 'RP ' + bCost.toLocaleString('id-ID');
            recBreakfastRow.style.display = '';
        } else {
            recBreakfastRow.style.display = 'none';
        }

        var recExtraBedRow = document.getElementById('receipt-extra-bed-row');
        if (booking.include_extra_bed && booking.include_extra_bed != 0) {
            var ebCost = 150000 * parseInt(booking.nights);
            document.getElementById('receipt-extra-bed-amount').textContent = 'RP ' + ebCost.toLocaleString('id-ID');
            recExtraBedRow.style.display = '';
        } else {
            recExtraBedRow.style.display = 'none';
        }

        var recLateCheckoutRow = document.getElementById('receipt-late-checkout-row');
        if (booking.late_checkout && booking.late_checkout != 0) {
            var lcCost = 100000;
            document.getElementById('receipt-late-checkout-amount').textContent = 'RP ' + lcCost.toLocaleString('id-ID');
            recLateCheckoutRow.style.display = '';
        } else {
            recLateCheckoutRow.style.display = 'none';
        }

        var recDiscountRow = document.getElementById('receipt-discount-row');
        if (booking.discount > 0) {
            document.getElementById('receipt-discount-label').textContent = 'Discount:';
            document.getElementById('receipt-discount-amount').textContent = '-RP ' + booking.discount.toLocaleString('id-ID');
            recDiscountRow.style.display = '';
        } else {
            recDiscountRow.style.display = 'none';
        }
        
        document.getElementById('receipt-tax').textContent = 'RP ' + booking.tax.toLocaleString('id-ID');
        document.getElementById('receipt-total').textContent = 'RP ' + booking.total_price.toLocaleString('id-ID');

        // Force watermark and badge to PENDING
        var watermark = document.getElementById('receipt-watermark');
        if (watermark) {
            watermark.textContent = 'PENDING';
            watermark.style.backgroundColor = '#ca8a04';
        }
        var statusBadge = document.getElementById('receipt-status-badge');
        if (statusBadge) {
            statusBadge.style.backgroundColor = '#fef9c3';
            statusBadge.style.color = '#854d0e';
            statusBadge.style.borderColor = '#fde68a';
            statusBadge.innerHTML = '⏳ Pending Verification';
        }

        goToStep('receipt');
    })
    .catch(function(err) {
        alert('Error: ' + err.message);
        goToStep('payment');
    });
}

// ── Print Receipt ──────────────────────────────────────────
function printReceipt() {
    var receiptEl = document.getElementById('receipt-printable');
    if (!receiptEl) {
        alert('Receipt element not found.');
        return;
    }
    // html2canvas-pro may expose as html2canvas or html2canvas.default
    var h2c = (typeof html2canvas !== 'undefined') ? (html2canvas.default || html2canvas) : null;
    if (!h2c) {
        alert('html2canvas library is missing. Please check your internet connection and reload.');
        return;
    }
    h2c(receiptEl, { scale: 2, useCORS: true }).then(function (canvas) {
        var imgData = canvas.toDataURL('image/png');
        var link = document.createElement('a');
        link.href = imgData;
        var now = new Date();
        var timestamp = now.getFullYear() + ('0' + (now.getMonth() + 1)).slice(-2) + ('0' + now.getDate()).slice(-2) + '_' + ('0' + now.getHours()).slice(-2) + ('0' + now.getMinutes()).slice(-2);
        link.download = 'receipt_' + timestamp + '.png';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }).catch(function (err) {
        console.error('Error generating receipt image:', err);
        alert('Failed to generate receipt image. Error: ' + err.message);
    });
}

// ── Booking Form Submission ────────────────────────────────
function handleBookingSubmit(e) {
    e.preventDefault();

    if (!selectedRoom) {
        alert('Please select a room before completing reservation!');
        return;
    }

    var name = document.getElementById('guest-name').value.trim();
    var checkIn = document.getElementById('check-in-input').value;
    var checkOut = document.getElementById('check-out-input').value;
    var adults = document.getElementById('adults-input').value;
    var children = document.getElementById('children-input').value;

    // Update Modal Step 1 Data
    document.getElementById('modal-guest-name').textContent = name;
    document.getElementById('modal-room-name').textContent = selectedRoom.name;
    document.getElementById('modal-stay-dates').textContent = formatDateDisplay(checkIn) + ' - ' + formatDateDisplay(checkOut);
    document.getElementById('modal-nights').textContent = nights + ' night(s)';

    var guestsText = adults + ' adult(s)';
    if (parseInt(children) > 0) guestsText += ', ' + children + ' child(ren)';
    document.getElementById('modal-guests').textContent = guestsText;

    // Toggle Modal Extras Rows
    var mBreakfastRow = document.getElementById('modal-breakfast-row');
    if (includeBreakfast) {
        mBreakfastRow.classList.remove('hidden');
        document.getElementById('modal-breakfast-val').textContent = 'Yes (RP ' + breakfastCost.toLocaleString('id-ID') + ')';
    } else {
        mBreakfastRow.classList.add('hidden');
    }

    var mExtraBedRow = document.getElementById('modal-extra-bed-row');
    if (includeExtraBed) {
        mExtraBedRow.classList.remove('hidden');
        document.getElementById('modal-extra-bed-val').textContent = 'Yes (RP ' + extraBedCost.toLocaleString('id-ID') + ')';
    } else {
        mExtraBedRow.classList.add('hidden');
    }

    var mLateCheckoutRow = document.getElementById('modal-late-checkout-row');
    if (lateCheckout) {
        mLateCheckoutRow.classList.remove('hidden');
        document.getElementById('modal-late-checkout-val').textContent = 'Yes (RP ' + lateCheckoutCost.toLocaleString('id-ID') + ')';
    } else {
        mLateCheckoutRow.classList.add('hidden');
    }

    document.getElementById('modal-total-price').textContent = 'RP ' + totalAmount.toLocaleString('id-ID');

    // Reset file upload state
    var fileInput = document.getElementById('payment-proof-input');
    if (fileInput) fileInput.value = '';
    
    var placeholder = document.getElementById('upload-placeholder');
    var preview = document.getElementById('upload-success-preview');
    if (placeholder) placeholder.classList.remove('hidden');
    if (preview) preview.classList.add('hidden');
    
    var payBtn = document.getElementById('btn-pay-now');
    if (payBtn) {
        payBtn.setAttribute('disabled', true);
        payBtn.className = 'w-full bg-gray-300 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide transition flex items-center justify-center gap-2 cursor-not-allowed select-none';
    }

    // Show Step 1 Details
    goToStep('details');

    // Open Modal
    document.getElementById('success-modal').classList.remove('hidden');
}

// ── Close Modal ────────────────────────────────────────────
function closeModal() {
    document.getElementById('success-modal').classList.add('hidden');
}

// ── Initialization ─────────────────────────────────────────
function initBooking() {
    try {
        var checkInInput = document.getElementById('check-in-input');
        var checkOutInput = document.getElementById('check-out-input');

        if (checkInInput && checkOutInput) {
            var today = new Date();
            var tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);

            if (!checkInInput.value) checkInInput.value = formatDateValue(today);
            if (!checkOutInput.value) checkOutInput.value = formatDateValue(tomorrow);

            checkInInput.min = formatDateValue(today);
            checkOutInput.min = formatDateValue(tomorrow);

            checkInInput.addEventListener('change', handleDateChange);
            checkOutInput.addEventListener('change', handleDateChange);
        }

        var toggleAvailableOnly = document.getElementById('toggle-available-only');
        if (toggleAvailableOnly) {
            toggleAvailableOnly.addEventListener('change', updateRoomsAvailabilityUI);
        }

        var bookingForm = document.getElementById('booking-form');
        if (bookingForm) bookingForm.addEventListener('submit', handleBookingSubmit);

        // Initial calculations
        calculateNights();

        // Pre-select room from query parameter
        var urlParams = new URLSearchParams(window.location.search);
        var initialRoomId = urlParams.get('room_id');
        if (initialRoomId) selectRoom(parseInt(initialRoomId));
    } catch (err) {
        console.error('Error in initBooking:', err);
    }
}

// Run on DOM ready
if (document.readyState !== 'loading') {
    initBooking();
} else {
    document.addEventListener('DOMContentLoaded', initBooking);
}
