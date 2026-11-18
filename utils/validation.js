// =============================================================
// utils/validation.js
// SERVER-SIDE validation. The browser also checks the form
// (public/js/validation.js), but the server checks again because
// browser checks can be switched off by the user.
// =============================================================

// NIC: old format = 9 digits + V or X (e.g. 991234567V)
//      new format = 12 digits          (e.g. 200156701234)
const NIC_REGEX = /^([0-9]{9}[VvXx]|[0-9]{12})$/;

// Contact: exactly 10 digits and must start with 0 (e.g. 0771234567)
const CONTACT_REGEX = /^0[0-9]{9}$/;

// Email: something@something.something
const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

// Make NIC upper-case so "991234567v" and "991234567V" are the same student
function cleanNic(nic) {
  return String(nic || '').trim().toUpperCase();
}

// Checks every student field. Returns a list of error messages
// (an empty list means the data is valid).
function validateStudent(data, courseIds) {
  const errors = [];
  const name = String(data.name || '').trim();
  const nic = cleanNic(data.nic);
  const address = String(data.address || '').trim();
  const contact = String(data.contact || '').trim();
  const email = String(data.email || '').trim();

  // 1. Required fields
  if (!name || !nic || !data.gender || !address || !contact || !email || !data.course_id) {
    errors.push('All fields are required.');
  }
  // 2. Format checks
  if (name && !/^[A-Za-z .]{3,100}$/.test(name)) {
    errors.push('Name must contain only letters, spaces and dots (3-100 characters).');
  }
  if (nic && !NIC_REGEX.test(nic)) {
    errors.push('NIC must be 9 digits followed by V/X, or 12 digits.');
  }
  if (data.gender && !['Male', 'Female'].includes(data.gender)) {
    errors.push('Please select a valid gender.');
  }
  if (contact && !CONTACT_REGEX.test(contact)) {
    errors.push('Contact number must be exactly 10 digits and start with 0.');
  }
  if (email && !EMAIL_REGEX.test(email)) {
    errors.push('Please enter a valid email address.');
  }
  if (data.course_id && !courseIds.includes(String(data.course_id))) {
    errors.push('Please select a valid course.');
  }
  return errors;
}

module.exports = { NIC_REGEX, cleanNic, validateStudent };
