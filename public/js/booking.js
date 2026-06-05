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

        recalculatePricing();
    } catch (err) {
        console.error('Error in calculateNights:', err);
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

// ── Promo Code ─────────────────────────────────────────────
function applyPromoCode() {
    try {
        var promoInput = document.getElementById('promo-input');
        var code = promoInput ? promoInput.value.trim().toUpperCase() : '';
        if (!code) {
            discountPercent = 0;
            recalculatePricing();
            return;
        }

        if (code === 'WELCOME10') {
            discountPercent = 10;
            alert('Promo Code Applied: 10% Welcome Discount!');
        } else if (code === 'BAGUS5') {
            discountPercent = 5;
            alert('Promo Code Applied: 5% Guesthouse Discount!');
        } else {
            discountPercent = 0;
            alert('Invalid Promo Code.');
        }

        recalculatePricing();
    } catch (err) {
        console.error('Error in applyPromoCode:', err);
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
            return;
        }

        subtotal = selectedRoom.price * nights;
        setElText('summary-subtotal', 'RP ' + subtotal.toLocaleString('id-ID'));

        if (discountPercent > 0) {
            discountAmount = Math.round(subtotal * (discountPercent / 100));
            setElText('summary-discount-label', 'Discount (' + discountPercent + '%):');
            setElText('summary-discount-amount', '-RP ' + discountAmount.toLocaleString('id-ID'));
            setElClass('summary-discount-row', 'remove', 'hidden');
        } else {
            discountAmount = 0;
            setElClass('summary-discount-row', 'add', 'hidden');
        }

        var taxableAmount = subtotal - discountAmount;
        taxAmount = Math.round(taxableAmount * 0.1);
        setElText('summary-tax', 'RP ' + taxAmount.toLocaleString('id-ID'));

        totalAmount = taxableAmount + taxAmount;
        setElText('summary-total', 'RP ' + totalAmount.toLocaleString('id-ID'));
    } catch (err) {
        console.error('Error in recalculatePricing:', err);
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
    goToStep('payment');
}

// ── Payment Method Selection ───────────────────────────────
function selectPaymentMethod(method) {
    selectedPaymentMethod = method;

    ['va', 'ewallet', 'cc'].forEach(function (m) {
        var radio = document.getElementById('pay-dot-' + m);
        if (!radio) return;
        var optionCard = radio.closest('.payment-option');
        if (m === method) {
            radio.classList.remove('hidden');
            if (optionCard) optionCard.classList.add('ring-2', 'ring-amber-700', 'border-amber-700/50', 'bg-amber-50/10');
        } else {
            radio.classList.add('hidden');
            if (optionCard) optionCard.classList.remove('ring-2', 'ring-amber-700', 'border-amber-700/50', 'bg-amber-50/10');
        }
    });

    var payBtn = document.getElementById('btn-pay-now');
    if (payBtn) {
        payBtn.removeAttribute('disabled');
        payBtn.className = 'w-full bg-amber-700 hover:bg-amber-800 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-lg shadow-amber-700/20 transition flex items-center justify-center gap-2 cursor-pointer select-none';
    }
}

// ── Process Payment (Simulated) ────────────────────────────
function processPayment() {
    if (!selectedPaymentMethod) return;

    goToStep('processing');

    setTimeout(function () {
        var name = document.getElementById('guest-name').value.trim();
        var checkIn = document.getElementById('check-in-input').value;
        var checkOut = document.getElementById('check-out-input').value;
        var adults = document.getElementById('adults-input').value;
        var children = document.getElementById('children-input').value;

        // Generate invoice details
        var randomId = Math.floor(1000 + Math.random() * 9000);
        var now = new Date();
        var invoiceNo = 'BGH-' + now.getFullYear() + String(now.getMonth() + 1).padStart(2, '0') + '-' + randomId;
        var today = now.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });

        // Map payment method code to text
        var methodText = 'Virtual Account';
        if (selectedPaymentMethod === 'ewallet') methodText = 'E-Wallet / QRIS';
        if (selectedPaymentMethod === 'cc') methodText = 'Credit Card';

        // Fill receipt
        document.getElementById('receipt-invoice-no').textContent = invoiceNo;
        document.getElementById('receipt-date').textContent = today;
        document.getElementById('receipt-guest-name').textContent = name;
        document.getElementById('receipt-room-name').textContent = selectedRoom.name;
        document.getElementById('receipt-check-in').textContent = formatDateDisplay(checkIn);
        document.getElementById('receipt-check-out').textContent = formatDateDisplay(checkOut);
        document.getElementById('receipt-nights').textContent = nights + ' night(s)';

        var guestsText = adults + ' adult(s)';
        if (parseInt(children) > 0) guestsText += ', ' + children + ' child(ren)';
        document.getElementById('receipt-guests').textContent = guestsText;
        document.getElementById('receipt-payment-method').textContent = methodText;

        // Pricing
        document.getElementById('receipt-subtotal').textContent = 'RP ' + subtotal.toLocaleString('id-ID');

        var recDiscountRow = document.getElementById('receipt-discount-row');
        if (discountPercent > 0) {
            document.getElementById('receipt-discount-label').textContent = 'Discount (' + discountPercent + '%):';
            document.getElementById('receipt-discount-amount').textContent = '-RP ' + discountAmount.toLocaleString('id-ID');
            recDiscountRow.classList.remove('hidden');
        } else {
            recDiscountRow.classList.add('hidden');
        }

        document.getElementById('receipt-tax').textContent = 'RP ' + taxAmount.toLocaleString('id-ID');
        document.getElementById('receipt-total').textContent = 'RP ' + totalAmount.toLocaleString('id-ID');

        goToStep('receipt');
    }, 1800);
}

// ── Print Receipt ──────────────────────────────────────────
function printReceipt() {
    var printContent = document.getElementById('receipt-printable').innerHTML;
    var printWindow = window.open('', '_blank');
    var css = [
        'body { font-family: monospace; padding: 40px; color: #000; font-size: 13px; line-height: 1.5; }',
        '.text-center { text-align: center; }',
        '.border-b { border-bottom: 1px solid #ccc; }',
        '.border-b-dashed { border-bottom: 1px dashed #000; }',
        '.pb-3 { padding-bottom: 15px; }',
        '.pb-2 { padding-bottom: 10px; }',
        '.pt-3 { padding-top: 15px; }',
        '.pt-2 { padding-top: 10px; }',
        '.mb-2 { margin-bottom: 10px; }',
        '.space-y-1\\.5 > * { margin-bottom: 6px; }',
        '.flex { display: flex; }',
        '.justify-between { display: flex; justify-content: space-between; }',
        '.font-bold { font-weight: bold; }',
        '.uppercase { text-transform: uppercase; }',
        '.text-amber-700 { color: #b45309; }',
        '.relative { position: relative; }',
        '.absolute { position: absolute; }',
        '.right-0 { right: 0; }',
        '.top-0 { top: 0; }',
        '.border-2 { border: 2px solid #000; }',
        '.p-2 { padding: 8px; }'
    ].join('\n');

    var html = '<!DOCTYPE html><html><head>'
        + '<title>Invoice - Bagus Guest House<\/title>'
        + '<style>' + css + '<\/style>'
        + '</head><body>'
        + '<div style="max-width:420px;margin:0 auto;border:1px solid #ddd;padding:20px;border-radius:10px;">'
        + printContent
        + '</div>'
        + '<scr' + 'ipt>window.onload=function(){window.print();window.close();};<\/scr' + 'ipt>'
        + '</body></html>';

    printWindow.document.write(html);
    printWindow.document.close();
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
    document.getElementById('modal-total-price').textContent = 'RP ' + totalAmount.toLocaleString('id-ID');

    // Reset selected payment method state
    selectedPaymentMethod = null;
    ['va', 'ewallet', 'cc'].forEach(function (m) {
        var dot = document.getElementById('pay-dot-' + m);
        if (!dot) return;
        dot.classList.add('hidden');
        var card = dot.closest('.payment-option');
        if (card) card.classList.remove('ring-2', 'ring-amber-700', 'border-amber-700/50', 'bg-amber-50/10');
    });
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

        var promoBtn = document.getElementById('btn-apply-promo');
        if (promoBtn) promoBtn.addEventListener('click', applyPromoCode);

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
