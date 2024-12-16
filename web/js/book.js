let selectedSeats = [];
const ticketPrice = document.getElementById('seat-selection').getAttribute('data-ticket-price');

function toggleSeatSelection(element) {
    const seatRow = element.getAttribute('data-row');
    const seatColumn = element.getAttribute('data-seat');
    const seatKey = seatRow + '-' + seatColumn;

    if (selectedSeats.includes(seatKey)) {
        selectedSeats = selectedSeats.filter(seat => seat !== seatKey);
        element.classList.remove('selected');
    } else {
        if (selectedSeats.length < 10) {
            selectedSeats.push(seatKey);
            element.classList.add('selected');
        } else {
            alert('You can only select up to 10 seats.');
        }
    }

    // Update the number of selected seats and the total cost
    document.getElementById('selected-seats-count').textContent = selectedSeats.length;
    document.getElementById('total-cost-value').textContent = (selectedSeats.length * ticketPrice) + ' €';
}

document.getElementById('booking-form').addEventListener('submit', function(event) {
    prepareSelectedSeats();
    console.log(document.getElementById('selected-seats-input').value); // Debugging step to verify selected seats value

    if (selectedSeats.length === 0) {
        event.preventDefault();
        alert("No seats selected. Please choose at least one seat.");
    }
});

function prepareSelectedSeats() {
    // Save selected seats to the hidden input before submitting the form
    document.getElementById('selected-seats-input').value = selectedSeats.join(',');
}
