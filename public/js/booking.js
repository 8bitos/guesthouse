/**
 * Booking Page – Reactive Interaction Logic
 * Extracted from booking.blade.php for maintainability and
 * to avoid HTML-parser / JS-string conflicts in inline scripts.
 */

// ── Custom Alert Modal overrides ─────────────────────────────
window.showAlert = function(message, title = 'Sistem Reservasi') {
    var modal = document.getElementById('custom-alert-modal');
    var msgEl = document.getElementById('custom-alert-message');
    var titleEl = document.getElementById('custom-alert-title');
    if (modal && msgEl) {
        msgEl.textContent = message;
        if (titleEl) titleEl.textContent = title;
        modal.classList.remove('hidden');
    } else {
        // Fallback to native alert if DOM is not ready
        window._nativeAlert ? window._nativeAlert(message) : alert(message);
    }
};

window.closeCustomAlert = function() {
    var modal = document.getElementById('custom-alert-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
};

// Store original alert and override
if (!window._nativeAlert) {
    window._nativeAlert = window.alert;
    window.alert = function(message) {
        window.showAlert(message);
    };
}

// ── State ──────────────────────────────────────────────────
let selectedRooms = [];
let nights = 0;
let subtotal = 0;
let discountPercent = 0;
let discountAmount = 0;
let taxAmount = 0;
let totalAmount = 0;
let selectedPaymentMethod = 'Transfer Bank';
let lastBookingId = null;

let breakfastCost = 0;
let extraBedCost = 0;
let lateCheckoutCost = 0;
let otherAddonsCost = 0;

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
        var checkInSidebar = document.getElementById('check-in-sidebar');
        var checkOutSidebar = document.getElementById('check-out-sidebar');
        
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

        // Sync values to sidebar if they exist
        if (checkInSidebar && checkInSidebar.value !== checkInInput.value) {
            checkInSidebar.value = checkInInput.value;
        }
        if (checkInSidebar) {
            checkInSidebar.min = checkInInput.min;
        }
        if (checkOutSidebar && checkOutSidebar.value !== checkOutInput.value) {
            checkOutSidebar.value = checkOutInput.value;
        }
        if (checkOutSidebar) {
            checkOutSidebar.min = checkOutInput.min;
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

// ── Selected Rooms Renderer ────────────────────────────────
window.renderSelectedRooms = function() {
    try {
        var noRoomEl = document.getElementById('summary-no-room');
        var roomsListEl = document.getElementById('summary-rooms-list');
        if (!roomsListEl) return;

        if (selectedRooms.length === 0) {
            if (noRoomEl) noRoomEl.classList.remove('hidden');
            roomsListEl.classList.add('hidden');
            roomsListEl.innerHTML = '';
            return;
        }

        if (noRoomEl) noRoomEl.classList.add('hidden');
        roomsListEl.classList.remove('hidden');

        var html = '';
        selectedRooms.forEach(function(room) {
            var itemBgClass = room.is_available === false 
                ? 'bg-red-50/30 border-red-200 shadow-sm shadow-red-500/5' 
                : 'bg-amber-50/20 border-amber-100/50';
            var headerColorClass = room.is_available === false ? 'text-red-800' : 'text-amber-800';

            html += `
            <div class="${itemBgClass} border p-4 rounded-xl space-y-3 animate-fade-in-up" id="selected-room-item-${room.id}">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-[9px] font-bold ${headerColorClass} uppercase tracking-wider block">${room.type}</span>
                        <h4 class="text-xs font-bold text-gray-900">${room.name}</h4>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-black text-amber-700">RP ${room.price.toLocaleString('id-ID')}</span>
                        <button type="button" onclick="removeSelectedRoom(${room.id})" class="text-gray-400 hover:text-red-600 transition flex items-center justify-center p-1 cursor-pointer border-0 bg-transparent">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </div>
                </div>

                ${room.is_available === false ? `
                <div class="flex items-center gap-1.5 text-[10px] text-red-600 font-bold bg-red-50 border border-red-100 p-2 rounded-lg">
                    <span class="material-symbols-outlined text-xs leading-none">error</span>
                    <span>Sudah dibooking sampai: ${room.booked_until || '-'}</span>
                </div>
                ` : ''}
                
                <div class="grid grid-cols-1 gap-2 pt-2 border-t border-gray-100/50">
                    <!-- Addons -->
                    <div id="selected-room-addons-${room.id}">
                        ${(function() {
                            var addonsHtml = '';
                            if (room.addons_config && room.addons_config.length > 0) {
                                addonsHtml += `<div class="flex flex-wrap gap-1.5 mt-1">`;
                                room.addons_config.forEach(function(addon) {
                                    var isSelected = room.selected_addons && room.selected_addons.indexOf(addon.name) !== -1;
                                    var btnClass = isSelected 
                                        ? 'bg-amber-100 border-amber-300 text-amber-800' 
                                        : 'bg-white border-gray-200 text-gray-500 hover:bg-amber-50/10';
                                    
                                    var priceNum = parseInt(addon.price) || 0;
                                    var priceText = '';
                                    if (priceNum > 0) {
                                        if (priceNum >= 1000) {
                                            priceText = ' (+' + (priceNum / 1000) + 'k)';
                                        } else {
                                            priceText = ' (+' + priceNum + ')';
                                        }
                                    }
                                    
                                    var icon = 'add_circle';
                                    var lowerName = addon.name.toLowerCase();
                                    if (lowerName.indexOf('breakfast') !== -1 || lowerName.indexOf('sarapan') !== -1) icon = 'restaurant';
                                    else if (lowerName.indexOf('bed') !== -1 || lowerName.indexOf('kasur') !== -1) icon = 'bed';
                                    else if (lowerName.indexOf('checkout') !== -1 || lowerName.indexOf('check-out') !== -1 || lowerName.indexOf('late') !== -1) icon = 'schedule';
                                    else if (lowerName.indexOf('spa') !== -1 || lowerName.indexOf('massage') !== -1) icon = 'spa';

                                    addonsHtml += `
                                        <button type="button" onclick="toggleRoomCustomAddon(${room.id}, '${addon.name.replace(/'/g, "\\'")}')" 
                                                title="${addon.description || ''}"
                                                class="px-2 py-1 rounded text-[10px] font-bold transition flex items-center gap-1 border cursor-pointer select-none ${btnClass}">
                                            <span class="material-symbols-outlined text-xs leading-none">${icon}</span>
                                            <span>${addon.name}${priceText}</span>
                                        </button>
                                    `;
                                });
                                addonsHtml += `</div>`;
                            }
                            return addonsHtml;
                        })()}
                    </div>
                </div>
            </div>
            `;
        });
        roomsListEl.innerHTML = html;
    } catch (err) {
        console.error('Error in renderSelectedRooms:', err);
    }
};

// ── Room Selection ─────────────────────────────────────────
window.selectRoom = function(roomId) {
    try {
        var card = document.getElementById('room-card-' + roomId);
        if (!card) {
            console.warn('Room card not found for ID: ' + roomId);
            return;
        }

        var name = card.getAttribute('data-room-name') || 'Room';
        var rawPrice = card.getAttribute('data-room-price');
        var price = parseInt(rawPrice) || 0;
        var capacity = parseInt(card.getAttribute('data-room-capacity')) || 2;
        var type = card.getAttribute('data-room-type') || 'Room';

        var existingIndex = selectedRooms.findIndex(function(r) { return r.id === roomId; });
        if (existingIndex !== -1) {
            // Already selected, toggle it off
            selectedRooms.splice(existingIndex, 1);
        } else {
            // Select it, default guests to capacity
            var defaultGuests = capacity;
            var isBooked = bookedRoomIdsMap.hasOwnProperty(roomId);
            
            var rawAddons = card.getAttribute('data-room-addons') || '[]';
            var addonsConfig = [];
            try {
                addonsConfig = JSON.parse(rawAddons) || [];
            } catch(e) {
                console.error('Error parsing addons JSON:', e);
            }

            selectedRooms.push({
                id: roomId,
                name: name,
                price: price,
                capacity: capacity,
                type: type,
                guests: defaultGuests,
                addons_config: addonsConfig,
                selected_addons: [],
                is_available: !isBooked,
                booked_until: isBooked ? bookedRoomIdsMap[roomId].check_out_formatted : null
            });
        }

        updateRoomsAvailabilityUI();
        window.renderSelectedRooms();
        recalculatePricing();
    } catch (err) {
        console.error('Error in selectRoom:', err);
    }
};

window.removeSelectedRoom = function(roomId) {
    try {
        selectedRooms = selectedRooms.filter(function(r) { return r.id !== roomId; });
        updateRoomsAvailabilityUI();
        window.renderSelectedRooms();
        recalculatePricing();
    } catch (err) {
        console.error('Error in removeSelectedRoom:', err);
    }
};

window.updateRoomGuests = function(roomId, value) {
    try {
        var room = selectedRooms.find(function(r) { return r.id === roomId; });
        if (room) {
            var g = parseInt(value) || 1;
            g = Math.max(1, Math.min(room.capacity, g));
            room.guests = g;
            window.renderSelectedRooms();
            recalculatePricing();
        }
    } catch (err) {
        console.error('Error in updateRoomGuests:', err);
    }
};

window.toggleRoomAddon = function(roomId, addonKey) {
    try {
        var room = selectedRooms.find(function(r) { return r.id === roomId; });
        if (room) {
            var standardName = '';
            if (addonKey === 'breakfast') standardName = 'Breakfast';
            else if (addonKey === 'extra_bed') standardName = 'Extra Bed';
            else if (addonKey === 'late_checkout') standardName = 'Late Check-out';

            if (standardName) {
                window.toggleRoomCustomAddon(roomId, standardName);
            }
        }
    } catch (err) {
        console.error('Error in toggleRoomAddon:', err);
    }
};

window.toggleRoomCustomAddon = function(roomId, addonName) {
    try {
        var room = selectedRooms.find(function(r) { return r.id === roomId; });
        if (room) {
            if (!room.selected_addons) room.selected_addons = [];
            var idx = room.selected_addons.indexOf(addonName);
            if (idx !== -1) {
                room.selected_addons.splice(idx, 1);
            } else {
                room.selected_addons.push(addonName);
            }
            window.renderSelectedRooms();
            recalculatePricing();
        }
    } catch (err) {
        console.error('Error in toggleRoomCustomAddon:', err);
    }
};

window.toggleRoomCustomDates = function(roomId, checked) {
    try {
        var room = selectedRooms.find(function(r) { return r.id === roomId; });
        if (room) {
            room.has_custom_dates = checked;
            if (checked) {
                var globalCheckIn = document.getElementById('check-in-input').value;
                var globalCheckOut = document.getElementById('check-out-input').value;
                room.check_in = globalCheckIn;
                room.check_out = globalCheckOut;
                room.nights = nights;
            } else {
                delete room.check_in;
                delete room.check_out;
                delete room.nights;
            }
            window.checkRoomAvailabilityForDates(room);
        }
    } catch (err) {
        console.error('Error in toggleRoomCustomDates:', err);
    }
};

window.updateRoomCustomCheckIn = function(roomId, value) {
    try {
        var room = selectedRooms.find(function(r) { return r.id === roomId; });
        if (room && room.has_custom_dates) {
            room.check_in = value;
            
            var checkInDate = new Date(room.check_in);
            var checkOutDate = new Date(room.check_out);
            if (!isNaN(checkInDate.getTime()) && !isNaN(checkOutDate.getTime())) {
                if (checkOutDate <= checkInDate) {
                    checkOutDate = new Date(checkInDate);
                    checkOutDate.setDate(checkOutDate.getDate() + 1);
                    room.check_out = formatDateValue(checkOutDate);
                }
                room.nights = Math.ceil(Math.abs(checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
            } else {
                room.nights = 0;
            }
            window.checkRoomAvailabilityForDates(room);
        }
    } catch (err) {
        console.error('Error in updateRoomCustomCheckIn:', err);
    }
};

window.updateRoomCustomCheckOut = function(roomId, value) {
    try {
        var room = selectedRooms.find(function(r) { return r.id === roomId; });
        if (room && room.has_custom_dates) {
            room.check_out = value;
            
            var checkInDate = new Date(room.check_in);
            var checkOutDate = new Date(room.check_out);
            if (!isNaN(checkInDate.getTime()) && !isNaN(checkOutDate.getTime())) {
                if (checkOutDate <= checkInDate) {
                    checkOutDate = new Date(checkInDate);
                    checkOutDate.setDate(checkOutDate.getDate() + 1);
                    room.check_out = formatDateValue(checkOutDate);
                }
                room.nights = Math.ceil(Math.abs(checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
            } else {
                room.nights = 0;
            }
            window.checkRoomAvailabilityForDates(room);
        }
    } catch (err) {
        console.error('Error in updateRoomCustomCheckOut:', err);
    }
};

window.checkRoomAvailabilityForDates = function(room) {
    try {
        var checkIn = room.has_custom_dates ? room.check_in : document.getElementById('check-in-input').value;
        var checkOut = room.has_custom_dates ? room.check_out : document.getElementById('check-out-input').value;

        if (!checkIn || !checkOut) {
            room.is_available = true;
            delete room.booked_until;
            window.renderSelectedRooms();
            recalculatePricing();
            return;
        }

        var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch('/booking/check-availability?check_in=' + checkIn + '&check_out=' + checkOut, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(function(res) {
            if (!res.ok) throw new Error('Failed to check availability.');
            return res.json();
        })
        .then(function(data) {
            var isBooked = false;
            var bookedUntil = null;
            if (data.booked_rooms) {
                var match = data.booked_rooms.find(function(r) { return r.room_id === room.id; });
                if (match) {
                    isBooked = true;
                    bookedUntil = match.check_out_formatted;
                }
            }
            room.is_available = !isBooked;
            room.booked_until = bookedUntil;

            window.renderSelectedRooms();
            recalculatePricing();
        })
        .catch(function(err) {
            console.error('Error checking room availability:', err);
        });
    } catch (err) {
        console.error('Error in checkRoomAvailabilityForDates:', err);
    }
};

// ── Room Availability State & Functions ───────────────────
let bookedRoomIdsMap = {};

function deselectAllRooms() {
    try {
        selectedRooms = [];
        updateRoomsAvailabilityUI();
        window.renderSelectedRooms();
        recalculatePricing();
    } catch (err) {
        console.error('Error in deselectAllRooms:', err);
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

        var applyStayBtn = document.getElementById('btn-apply-stay');
        if (applyStayBtn) {
            applyStayBtn.disabled = true;
            applyStayBtn.innerHTML = `
                <svg class="animate-spin h-3.5 w-3.5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Applying...</span>
            `;
            applyStayBtn.classList.add('opacity-80', 'cursor-not-allowed');
        }

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

            if (applyStayBtn) {
                applyStayBtn.disabled = false;
                applyStayBtn.innerHTML = `
                    <span class="material-symbols-outlined text-xs leading-none">done</span>
                    <span>Apply Stay</span>
                `;
                applyStayBtn.classList.remove('opacity-80', 'cursor-not-allowed');
            }
        })
        .catch(function(err) {
            console.error('Error checking room availability:', err);
            if (applyStayBtn) {
                applyStayBtn.disabled = false;
                applyStayBtn.innerHTML = `
                    <span class="material-symbols-outlined text-xs leading-none">done</span>
                    <span>Apply Stay</span>
                `;
                applyStayBtn.classList.remove('opacity-80', 'cursor-not-allowed');
            }
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
                // Mark the selected room as unavailable instead of silently removing it
                var selectedRoom = selectedRooms.find(function(r) { return r.id === roomId; });
                if (selectedRoom && !selectedRoom.has_custom_dates) {
                    selectedRoom.is_available = false;
                    selectedRoom.booked_until = bookedRoomIdsMap[roomId].check_out_formatted;
                }

                // Add gray out styling & disable events
                card.classList.add('opacity-60', 'grayscale-[30%]', 'pointer-events-none');
                card.classList.remove('ring-2', 'ring-amber-700', 'border-amber-700/50');
                
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

                var selectedRoom = selectedRooms.find(function(r) { return r.id === roomId; });
                if (selectedRoom && !selectedRoom.has_custom_dates) {
                    selectedRoom.is_available = true;
                    delete selectedRoom.booked_until;
                }

                // If this room is currently selected, highlight it
                var isSelected = selectedRooms.some(function(r) { return r.id === roomId; });
                if (isSelected) {
                    card.classList.add('ring-2', 'ring-amber-700', 'border-amber-700/50');
                    if (selectBtn) {
                        selectBtn.textContent = 'Selected ✓';
                        selectBtn.className = 'btn-select-room bg-amber-700 text-white px-5 py-2 rounded-lg text-xs font-bold tracking-wide transition cursor-pointer select-none';
                    }
                } else {
                    card.classList.remove('ring-2', 'ring-amber-700', 'border-amber-700/50');
                    if (selectBtn) {
                        selectBtn.textContent = 'Select Room';
                        selectBtn.className = 'btn-select-room border border-amber-700 text-amber-700 hover:bg-amber-50 px-5 py-2 rounded-lg text-xs font-bold tracking-wide transition cursor-pointer select-none';
                    }
                }

                card.classList.remove('hidden');
            }
        });

        // Re-render rooms list to reflect availability warnings
        window.renderSelectedRooms();
        recalculatePricing();
    } catch (err) {
        console.error('Error in updateRoomsAvailabilityUI:', err);
    }
}

// ── Pricing Recalculation ──────────────────────────────────
function recalculatePricing() {
    try {
        if (selectedRooms.length === 0 || nights === 0) {
            setElText('summary-subtotal', 'RP 0');
            setElClass('summary-discount-row', 'add', 'hidden');
            setElText('summary-tax', 'RP 0');
            setElText('summary-total', 'RP 0');
            
            setElClass('summary-breakfast-row', 'add', 'hidden');
            setElClass('summary-extra-bed-row', 'add', 'hidden');
            setElClass('summary-late-checkout-row', 'add', 'hidden');
            
            var alertEl = document.getElementById('booking-availability-alert');
            if (alertEl) alertEl.classList.add('hidden');
            var submitBtn = document.getElementById('btn-submit-booking');
            if (submitBtn) {
                submitBtn.removeAttribute('disabled');
                submitBtn.className = 'w-full bg-amber-700 hover:bg-amber-800 text-white py-3 rounded-xl font-bold text-sm tracking-wide shadow-md shadow-amber-700/20 transition cursor-pointer select-none';
            }
            return;
        }

        subtotal = 0;
        breakfastCost = 0;
        extraBedCost = 0;
        lateCheckoutCost = 0;
        var otherAddonsCost = 0;

        selectedRooms.forEach(function(room) {
            var roomNights = room.has_custom_dates ? room.nights : nights;
            subtotal += room.price * roomNights;

            if (room.selected_addons && room.selected_addons.length > 0) {
                room.selected_addons.forEach(function(addonName) {
                    var addon = room.addons_config.find(function(a) { return a.name === addonName; });
                    if (addon) {
                        var price = parseInt(addon.price) || 0;
                        var type = addon.type || 'flat_fee';
                        var addonCost = 0;

                        if (type === 'per_guest_per_night') {
                            addonCost = price * room.guests * roomNights;
                        } else if (type === 'per_night') {
                            addonCost = price * roomNights;
                        } else {
                            addonCost = price;
                        }

                        var lowerName = addonName.toLowerCase();
                        if (lowerName.indexOf('breakfast') !== -1 || lowerName.indexOf('sarapan') !== -1) {
                            breakfastCost += addonCost;
                        } else if (lowerName.indexOf('extra bed') !== -1 || lowerName.indexOf('kasur') !== -1) {
                            extraBedCost += addonCost;
                        } else if (lowerName.indexOf('late check') !== -1 || lowerName.indexOf('late out') !== -1) {
                            lateCheckoutCost += addonCost;
                        } else {
                            otherAddonsCost += addonCost;
                        }
                    }
                });
            }
        });

        setElText('summary-subtotal', 'RP ' + subtotal.toLocaleString('id-ID'));

        // Breakfast
        if (breakfastCost > 0) {
            setElText('summary-breakfast-amount', 'RP ' + breakfastCost.toLocaleString('id-ID'));
            setElClass('summary-breakfast-row', 'remove', 'hidden');
        } else {
            setElClass('summary-breakfast-row', 'add', 'hidden');
        }

        // Extra Bed
        if (extraBedCost > 0) {
            setElText('summary-extra-bed-amount', 'RP ' + extraBedCost.toLocaleString('id-ID'));
            setElClass('summary-extra-bed-row', 'remove', 'hidden');
        } else {
            setElClass('summary-extra-bed-row', 'add', 'hidden');
        }

        // Late Check-out
        if (lateCheckoutCost > 0) {
            setElText('summary-late-checkout-amount', 'RP ' + lateCheckoutCost.toLocaleString('id-ID'));
            setElClass('summary-late-checkout-row', 'remove', 'hidden');
        } else {
            setElClass('summary-late-checkout-row', 'add', 'hidden');
        }

        // Other Extras
        if (otherAddonsCost > 0) {
            setElText('summary-other-addons-amount', 'RP ' + otherAddonsCost.toLocaleString('id-ID'));
            setElClass('summary-other-addons-row', 'remove', 'hidden');
        } else {
            setElClass('summary-other-addons-row', 'add', 'hidden');
        }

        var extrasTotal = breakfastCost + extraBedCost + lateCheckoutCost + otherAddonsCost;

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

        // Enable or disable submission button depending on availability
        var hasUnavailableRoom = selectedRooms.some(function(r) { return r.is_available === false; });
        var alertEl = document.getElementById('booking-availability-alert');
        var submitBtn = document.getElementById('btn-submit-booking');

        if (hasUnavailableRoom) {
            if (alertEl) alertEl.classList.remove('hidden');
            if (submitBtn) {
                submitBtn.setAttribute('disabled', true);
                submitBtn.className = 'w-full bg-gray-300 text-white py-3 rounded-xl font-bold text-sm tracking-wide transition cursor-not-allowed select-none pointer-events-none';
            }
        } else {
            if (alertEl) alertEl.classList.add('hidden');
            if (submitBtn) {
                submitBtn.removeAttribute('disabled');
                submitBtn.className = 'w-full bg-amber-700 hover:bg-amber-800 text-white py-3 rounded-xl font-bold text-sm tracking-wide shadow-md shadow-amber-700/20 transition cursor-pointer select-none';
            }
        }
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

    // Force default payment method
    selectPaymentMethod('Midtrans');

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
    if (window.userRole === 'admin') {
        alert('You cannot perform checkout because you are logged in as an admin.');
        return;
    }

    if (selectedPaymentMethod === 'Transfer Bank') {
        var fileInput = document.getElementById('payment-proof-input');
        if (!fileInput || !fileInput.files[0]) {
            alert('Please upload your payment transfer receipt first!');
            return;
        }
    }

    // Set loader text dynamically
    var loaderTitle = document.querySelector('#modal-step-processing h3');
    if (loaderTitle) {
        loaderTitle.textContent = selectedPaymentMethod === 'Midtrans' ? 'Initiating Online Payment...' : 'Uploading Payment Proof...';
    }

    goToStep('processing');

    var name = document.getElementById('guest-name').value.trim();
    var email = document.getElementById('guest-email').value.trim();
    var phone = document.getElementById('guest-phone').value.trim();
    var country = document.getElementById('guest-country').value.trim();
    var requests = document.getElementById('guest-requests').value.trim();
    var checkIn = document.getElementById('check-in-input').value;
    var checkOut = document.getElementById('check-out-input').value;

    var formData = new FormData();
    formData.append('guest_name', name);
    formData.append('guest_email', email);
    formData.append('guest_phone', phone);
    formData.append('guest_country', country);
    formData.append('special_requests', requests);
    formData.append('check_in', checkIn);
    formData.append('check_out', checkOut);
    formData.append('nights', nights);
    formData.append('payment_method', selectedPaymentMethod);
    if (selectedPaymentMethod === 'Transfer Bank') {
        formData.append('payment_proof', fileInput.files[0]);
    }

    // Append rooms array
    selectedRooms.forEach(function(room, index) {
        var roomNights = room.has_custom_dates ? room.nights : nights;
        var roomSubtotal = room.price * roomNights;
        var roomExtras = 0;
        var hasBreakfast = false;
        var hasExtraBed = false;
        var hasLateOut = false;

        if (room.selected_addons && room.selected_addons.length > 0) {
            room.selected_addons.forEach(function(addonName) {
                var addon = room.addons_config.find(function(a) { return a.name === addonName; });
                if (addon) {
                    var price = parseInt(addon.price) || 0;
                    var type = addon.type || 'flat_fee';
                    var addonCost = 0;

                    if (type === 'per_guest_per_night') {
                        addonCost = price * room.guests * roomNights;
                    } else if (type === 'per_night') {
                        addonCost = price * roomNights;
                    } else {
                        addonCost = price;
                    }

                    roomExtras += addonCost;

                    var lowerName = addonName.toLowerCase();
                    if (lowerName.indexOf('breakfast') !== -1 || lowerName.indexOf('sarapan') !== -1) {
                        hasBreakfast = true;
                    } else if (lowerName.indexOf('extra bed') !== -1 || lowerName.indexOf('kasur') !== -1) {
                        hasExtraBed = true;
                    } else if (lowerName.indexOf('late check') !== -1 || lowerName.indexOf('late out') !== -1) {
                        hasLateOut = true;
                    }
                }
            });
        }

        var roomDiscount = Math.round(roomSubtotal * (discountPercent / 100));
        var roomTaxable = roomSubtotal - roomDiscount + roomExtras;
        var roomTax = Math.round(roomTaxable * 0.1);
        var roomTotal = roomTaxable + roomTax;

        formData.append('rooms[' + index + '][room_id]', room.id);
        formData.append('rooms[' + index + '][guests]', room.guests);
        formData.append('rooms[' + index + '][include_breakfast]', hasBreakfast ? 1 : 0);
        formData.append('rooms[' + index + '][include_extra_bed]', hasExtraBed ? 1 : 0);
        formData.append('rooms[' + index + '][late_checkout]', hasLateOut ? 1 : 0);
        formData.append('rooms[' + index + '][addons]', JSON.stringify(room.selected_addons));
        formData.append('rooms[' + index + '][subtotal]', roomSubtotal);
        formData.append('rooms[' + index + '][discount]', roomDiscount);
        formData.append('rooms[' + index + '][tax]', roomTax);
        formData.append('rooms[' + index + '][total_price]', roomTotal);

        if (room.has_custom_dates) {
            formData.append('rooms[' + index + '][check_in]', room.check_in);
            formData.append('rooms[' + index + '][check_out]', room.check_out);
            formData.append('rooms[' + index + '][nights]', room.nights);
        }
    });

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
        var contentType = res.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            return res.json().then(function(data) {
                if (!res.ok) {
                    throw new Error(data.message || data.error || 'Something went wrong during submission.');
                }
                return data;
            });
        } else {
            return res.text().then(function(text) {
                if (res.status === 419) {
                    throw new Error('Your session has expired. Please refresh the page and try again.');
                }
                throw new Error('Server returned an unexpected response (Status ' + res.status + '). Please ensure database migrations are run: "php artisan migrate".');
            });
        }
    })
    .then(function(data) {
        var booking = data.booking;
        lastBookingId = booking.id;
        
        // Populate receipt fields
        fillReceiptDetails(booking);
        
        // Handle Midtrans Snap Payment Flow
        if (data.snap_token) {
            if (data.snap_token.startsWith('MOCK-SNAP-TOKEN-')) {
                openMockSnapModal(booking.id, booking.invoice_no, booking.total_price);
            } else {
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        confirmPaymentOnServer(booking.id, result.transaction_id || result.order_id);
                    },
                    onPending: function(result) {
                        updateReceiptToPending(booking);
                        goToStep('receipt');
                    },
                    onError: function(result) {
                        closeModal();
                    },
                    onClose: function() {
                        closeModal();
                    }
                });
            }
            return;
        }

        // Default Bank Transfer flow
        updateReceiptToPending(booking);
        goToStep('receipt');
    })
    .catch(function(err) {
        alert('Error: ' + err.message);
        goToStep('payment');
    });
}

/**
 * Handle payment option tabs select.
 */
function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    
    var optionBank = document.getElementById('payment-option-bank');
    var optionMidtrans = document.getElementById('payment-option-midtrans');
    var bankSection = document.getElementById('bank-transfer-details');
    var midtransSection = document.getElementById('midtrans-payment-details');
    var payBtn = document.getElementById('btn-pay-now');
    var payBtnText = payBtn ? payBtn.querySelector('span') : null;
    
    if (method === 'Transfer Bank') {
        if (optionBank) {
            optionBank.className = 'border-2 border-amber-600 bg-amber-50/10 rounded-xl p-3.5 cursor-pointer transition flex items-start gap-3 select-none';
        }
        if (optionMidtrans) {
            optionMidtrans.className = 'border-2 border-gray-200 bg-white rounded-xl p-3.5 cursor-pointer transition flex items-start gap-3 select-none';
        }
        
        if (bankSection) bankSection.classList.remove('hidden');
        if (midtransSection) midtransSection.classList.add('hidden');
        
        var fileInput = document.getElementById('payment-proof-input');
        if (fileInput && fileInput.files[0]) {
            if (payBtn) {
                payBtn.removeAttribute('disabled');
                payBtn.className = 'w-full bg-amber-700 hover:bg-amber-800 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-lg shadow-amber-700/20 transition flex items-center justify-center gap-2 cursor-pointer select-none';
            }
        } else {
            if (payBtn) {
                payBtn.setAttribute('disabled', true);
                payBtn.className = 'w-full bg-gray-300 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide transition flex items-center justify-center gap-2 cursor-not-allowed select-none';
            }
        }
        
        if (payBtnText) payBtnText.textContent = 'Submit Payment Proof';
        
    } else if (method === 'Midtrans') {
        if (optionBank) {
            optionBank.className = 'border-2 border-gray-200 bg-white rounded-xl p-3.5 cursor-pointer transition flex items-start gap-3 select-none';
        }
        if (optionMidtrans) {
            optionMidtrans.className = 'border-2 border-blue-600 bg-blue-50/10 rounded-xl p-3.5 cursor-pointer transition flex items-start gap-3 select-none';
        }
        
        if (bankSection) bankSection.classList.add('hidden');
        if (midtransSection) midtransSection.classList.remove('hidden');
        
        if (payBtn) {
            payBtn.removeAttribute('disabled');
            payBtn.className = 'w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-xs sm:text-sm tracking-wide shadow-lg shadow-blue-750/20 transition flex items-center justify-center gap-2 cursor-pointer select-none';
        }
        
        if (payBtnText) payBtnText.textContent = 'Pay with Midtrans Sandbox';
    }
}

/**
 * Populate all fields on the receipt printable.
 */
function fillReceiptDetails(booking) {
    document.getElementById('receipt-invoice-no').textContent = booking.invoice_no;
    document.getElementById('receipt-date').textContent = booking.date;
    document.getElementById('receipt-guest-name').textContent = booking.guest_name;
    document.getElementById('receipt-room-name').textContent = booking.room_name;
    document.getElementById('receipt-check-in').textContent = formatDateDisplay(booking.check_in);
    document.getElementById('receipt-check-out').textContent = formatDateDisplay(booking.check_out);
    document.getElementById('receipt-nights').textContent = booking.nights + ' night(s)';
    
    var guestsText = booking.guests + ' guest(s)';
    document.getElementById('receipt-guests').textContent = guestsText;
    document.getElementById('receipt-payment-method').textContent = booking.payment_method;

    document.getElementById('receipt-subtotal').textContent = 'RP ' + booking.subtotal.toLocaleString('id-ID');
    
    var recBreakfastRow = document.getElementById('receipt-breakfast-row');
    if (booking.include_breakfast && booking.include_breakfast != 0) {
        var bCost = booking.breakfast_cost !== undefined ? booking.breakfast_cost : 0;
        document.getElementById('receipt-breakfast-amount').textContent = 'RP ' + bCost.toLocaleString('id-ID');
        recBreakfastRow.style.display = '';
    } else {
        recBreakfastRow.style.display = 'none';
    }

    var recExtraBedRow = document.getElementById('receipt-extra-bed-row');
    if (booking.include_extra_bed && booking.include_extra_bed != 0) {
        var ebCost = booking.extra_bed_cost !== undefined ? booking.extra_bed_cost : 0;
        document.getElementById('receipt-extra-bed-amount').textContent = 'RP ' + ebCost.toLocaleString('id-ID');
        recExtraBedRow.style.display = '';
    } else {
        recExtraBedRow.style.display = 'none';
    }

    var recLateCheckoutRow = document.getElementById('receipt-late-checkout-row');
    if (booking.late_checkout && booking.late_checkout != 0) {
        var lcCost = booking.late_checkout_cost !== undefined ? booking.late_checkout_cost : 0;
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
}

/**
 * Update UI state of receipt block to Paid & Confirmed.
 */
function updateReceiptToConfirmed(booking) {
    var watermark = document.getElementById('receipt-watermark');
    if (watermark) {
        watermark.textContent = 'PAID & CONFIRMED';
        watermark.style.backgroundColor = '#10b981';
    }
    var statusBadge = document.getElementById('receipt-status-badge');
    if (statusBadge) {
        statusBadge.style.backgroundColor = '#d1fae5';
        statusBadge.style.color = '#065f46';
        statusBadge.style.borderColor = '#a7f3d0';
        statusBadge.innerHTML = '✅ Paid & Confirmed';
    }
    
    var bypassBtn = document.getElementById('btn-bypass-payment');
    if (bypassBtn) bypassBtn.classList.add('hidden');
}

/**
 * Update UI state of receipt block to Pending Verification.
 */
function updateReceiptToPending(booking) {
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
    
    var bypassBtn = document.getElementById('btn-bypass-payment');
    if (bypassBtn) bypassBtn.classList.remove('hidden');
}

/**
 * Open simulation mock snap modal.
 */
let currentMockBookingId = null;

function openMockSnapModal(bookingId, invoiceNo, amount) {
    currentMockBookingId = bookingId;
    
    document.getElementById('mock-snap-invoice').textContent = invoiceNo;
    document.getElementById('mock-snap-amount').textContent = 'RP ' + amount.toLocaleString('id-ID');
    
    var modal = document.getElementById('mock-snap-modal');
    if (modal) modal.classList.remove('hidden');
    
    goToStep('payment');
}

/**
 * Close simulation mock snap modal.
 */
function closeMockSnapModal() {
    var modal = document.getElementById('mock-snap-modal');
    if (modal) modal.classList.add('hidden');
    
    if (currentMockBookingId) {
        cancelBookingOnServer(currentMockBookingId);
    } else {
        closeModal();
    }
}

/**
 * Simulate payment confirmation inside mock snap modal.
 */
function simulateSuccessMockSnap() {
    if (!currentMockBookingId) return;
    
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var modal = document.getElementById('mock-snap-modal');
    if (modal) modal.classList.add('hidden');
    
    goToStep('processing');
    
    var loaderTitle = document.querySelector('#modal-step-processing h3');
    if (loaderTitle) {
        loaderTitle.textContent = 'Simulating payment confirmation...';
    }
    
    fetch('/booking/' + currentMockBookingId + '/bypass-payment', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(function(res) {
        return res.json().then(function(data) {
            if (!res.ok) throw new Error(data.error || 'Simulation failed');
            return data;
        });
    })
    .then(function(data) {
        var watermark = document.getElementById('receipt-watermark');
        if (watermark) {
            watermark.textContent = 'PAID & CONFIRMED';
            watermark.style.backgroundColor = '#10b981';
        }
        var statusBadge = document.getElementById('receipt-status-badge');
        if (statusBadge) {
            statusBadge.style.backgroundColor = '#d1fae5';
            statusBadge.style.color = '#065f46';
            statusBadge.style.borderColor = '#a7f3d0';
            statusBadge.innerHTML = '✅ Paid & Confirmed';
        }
        
        var bypassBtn = document.getElementById('btn-bypass-payment');
        if (bypassBtn) bypassBtn.classList.add('hidden');
        
        goToStep('receipt');
    })
    .catch(function(err) {
        alert('Error: ' + err.message);
        goToStep('receipt');
    });
}

/**
 * Directly bypass payment from receipt view.
 */
function bypassPaymentDirectly() {
    if (!lastBookingId) {
        alert('No booking ID available to verify.');
        return;
    }
    
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var bypassBtn = document.getElementById('btn-bypass-payment');
    var originalHtml = bypassBtn.innerHTML;
    bypassBtn.setAttribute('disabled', true);
    bypassBtn.innerHTML = '<span class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span> <span>Checking payment...</span>';
    
    fetch('/booking/' + lastBookingId + '/bypass-payment', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(function(res) {
        return res.json().then(function(data) {
            if (!res.ok) throw new Error(data.error || 'Verification failed');
            return data;
        });
    })
    .then(function(data) {
        alert('Payment status verified successfully!');
        
        var watermark = document.getElementById('receipt-watermark');
        if (watermark) {
            watermark.textContent = 'PAID & CONFIRMED';
            watermark.style.backgroundColor = '#10b981';
        }
        var statusBadge = document.getElementById('receipt-status-badge');
        if (statusBadge) {
            statusBadge.style.backgroundColor = '#d1fae5';
            statusBadge.style.color = '#065f46';
            statusBadge.style.borderColor = '#a7f3d0';
            statusBadge.innerHTML = '✅ Paid & Confirmed';
        }
        
        bypassBtn.classList.add('hidden');
    })
    .catch(function(err) {
        alert('Error: ' + err.message);
    })
    .finally(function() {
        bypassBtn.removeAttribute('disabled');
        bypassBtn.innerHTML = originalHtml;
    });
}

/**
 * Cancel/reject pending booking on server.
 */
function cancelBookingOnServer(bookingId) {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    goToStep('processing');
    var loaderTitle = document.querySelector('#modal-step-processing h3');
    if (loaderTitle) {
        loaderTitle.textContent = 'Cancelling reservation...';
    }
    
    fetch('/booking/' + bookingId + '/cancel', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(function(res) {
        return res.json().then(function(data) {
            if (!res.ok) throw new Error(data.error || 'Cancellation failed');
            return data;
        });
    })
    .then(function(data) {
        alert('Payment cancelled. Your booking has been cancelled.');
        closeModal();
    })
    .catch(function(err) {
        console.error('Error cancelling booking:', err);
        closeModal();
    });
}

/**
 * Confirm successful payment on server from client-side callback.
 */
function confirmPaymentOnServer(bookingId, transactionId) {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    goToStep('processing');
    var loaderTitle = document.querySelector('#modal-step-processing h3');
    if (loaderTitle) {
        loaderTitle.textContent = 'Verifying payment...';
    }
    
    fetch('/booking/' + bookingId + '/confirm-payment', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            transaction_id: transactionId
        })
    })
    .then(function(res) {
        return res.json().then(function(data) {
            if (!res.ok) throw new Error(data.error || 'Payment verification failed');
            return data;
        });
    })
    .then(function(data) {
        // Update UI receipt
        var watermark = document.getElementById('receipt-watermark');
        if (watermark) {
            watermark.textContent = 'PAID & CONFIRMED';
            watermark.style.backgroundColor = '#10b981';
        }
        var statusBadge = document.getElementById('receipt-status-badge');
        if (statusBadge) {
            statusBadge.style.backgroundColor = '#d1fae5';
            statusBadge.style.color = '#065f46';
            statusBadge.style.borderColor = '#a7f3d0';
            statusBadge.innerHTML = '✅ Paid & Confirmed';
        }
        
        var bypassBtn = document.getElementById('btn-bypass-payment');
        if (bypassBtn) bypassBtn.classList.add('hidden');
        
        goToStep('receipt');
    })
    .catch(function(err) {
        alert('Verification Error: ' + err.message);
        goToStep('receipt');
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

    if (window.userRole === 'admin') {
        alert('You cannot perform checkout because you are logged in as an admin.');
        return;
    }

    if (selectedRooms.length === 0) {
        alert('Please select at least one room before completing reservation!');
        return;
    }

    var hasUnavailableRoom = selectedRooms.some(function(r) { return r.is_available === false; });
    if (hasUnavailableRoom) {
        alert('Beberapa kamar yang Anda pilih tidak tersedia untuk tanggal tersebut. Silakan ubah tanggal atau hapus kamar yang sudah dibooking.');
        return;
    }

    var name = document.getElementById('guest-name').value.trim();
    var checkIn = document.getElementById('check-in-input').value;
    var checkOut = document.getElementById('check-out-input').value;
    var totalGuestsCount = selectedRooms.reduce(function(sum, r) { return sum + r.guests; }, 0);

    // Update Modal Step 1 Data
    document.getElementById('modal-guest-name').textContent = name;
    
    var roomNames = selectedRooms.map(function(r) { return r.name; }).join(', ');
    document.getElementById('modal-room-name').textContent = roomNames;
    document.getElementById('modal-stay-dates').textContent = formatDateDisplay(checkIn) + ' - ' + formatDateDisplay(checkOut);
    document.getElementById('modal-nights').textContent = nights + ' night(s)';

    var guestsText = totalGuestsCount + ' guest(s)';
    document.getElementById('modal-guests').textContent = guestsText;

    // Toggle Modal Extras Rows
    var mBreakfastRow = document.getElementById('modal-breakfast-row');
    if (breakfastCost > 0) {
        mBreakfastRow.classList.remove('hidden');
        document.getElementById('modal-breakfast-val').textContent = 'Yes (RP ' + breakfastCost.toLocaleString('id-ID') + ')';
    } else {
        mBreakfastRow.classList.add('hidden');
    }

    var mExtraBedRow = document.getElementById('modal-extra-bed-row');
    if (extraBedCost > 0) {
        mExtraBedRow.classList.remove('hidden');
        document.getElementById('modal-extra-bed-val').textContent = 'Yes (RP ' + extraBedCost.toLocaleString('id-ID') + ')';
    } else {
        mExtraBedRow.classList.add('hidden');
    }

    var mLateCheckoutRow = document.getElementById('modal-late-checkout-row');
    if (lateCheckoutCost > 0) {
        mLateCheckoutRow.classList.remove('hidden');
        document.getElementById('modal-late-checkout-val').textContent = 'Yes (RP ' + lateCheckoutCost.toLocaleString('id-ID') + ')';
    } else {
        mLateCheckoutRow.classList.add('hidden');
    }

    var mOtherAddonsRow = document.getElementById('modal-other-addons-row');
    if (mOtherAddonsRow) {
        if (otherAddonsCost > 0) {
            mOtherAddonsRow.classList.remove('hidden');
            document.getElementById('modal-other-addons-val').textContent = 'Yes (RP ' + otherAddonsCost.toLocaleString('id-ID') + ')';
        } else {
            mOtherAddonsRow.classList.add('hidden');
        }
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
        var checkInSidebar = document.getElementById('check-in-sidebar');
        var checkOutSidebar = document.getElementById('check-out-sidebar');

        if (checkInInput && checkOutInput) {
            var today = new Date();
            var tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);

            if (!checkInInput.value) checkInInput.value = formatDateValue(today);
            if (!checkOutInput.value) checkOutInput.value = formatDateValue(tomorrow);

            checkInInput.min = formatDateValue(today);
            checkOutInput.min = formatDateValue(tomorrow);

            if (checkInSidebar) {
                checkInSidebar.value = checkInInput.value;
                checkInSidebar.min = checkInInput.min;
            }
            if (checkOutSidebar) {
                checkOutSidebar.value = checkOutInput.value;
                checkOutSidebar.min = checkOutInput.min;
            }

            // Bind Apply Stay button
            var applyStayBtn = document.getElementById('btn-apply-stay');
            if (applyStayBtn) {
                applyStayBtn.addEventListener('click', handleDateChange);
            }

            if (checkInSidebar && checkOutSidebar) {
                checkInSidebar.addEventListener('change', function() {
                    checkInInput.value = checkInSidebar.value;
                    handleDateChange();
                });
                checkOutSidebar.addEventListener('change', function() {
                    checkOutInput.value = checkOutSidebar.value;
                    handleDateChange();
                });
            }
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
