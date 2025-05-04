
// Set minimum date to today
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('appointmentDate').min = today;
  });
  
  function handleSubmit(event) {
    event.preventDefault();
    
    const formData = {
      username: document.getElementById('username').value,
      appointmentDate: document.getElementById('appointmentDate').value,
      appointmentTime: document.getElementById('appointmentTime').value,
      doctor: document.getElementById('doctor').value,
      symptoms: document.getElementById('symptoms').value
    };
  
    // Hide form and show success message
    document.getElementById('appointmentForm').classList.add('hidden');
    document.getElementById('successMessage').classList.remove('hidden');
  
    // Update success message content
    document.getElementById('thankYouMessage').textContent = `Thank you, ${formData.username}!`;
    document.getElementById('appointmentDetails').innerHTML = `
      <p>Your appointment is scheduled for:</p>
      <p><strong>${formData.appointmentDate} at ${formData.appointmentTime}</strong></p>
      <p><strong>${formData.doctor}</strong></p>
    `;
  }
  
  function resetForm() {
    // Reset form fields
    document.getElementById('bookingForm').reset();
    
    // Show form and hide success message
    document.getElementById('appointmentForm').classList.remove('hidden');
    document.getElementById('successMessage').classList.add('hidden');
  }
  