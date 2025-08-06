'use client';
import React from 'react';
import './vendorRegister.css'; // ✅ Adjust path if needed

export default function VendorLebarRegister() {
  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
      e.preventDefault();
      const formData = new FormData(e.target as HTMLFormElement);
      const data = Object.fromEntries(formData);
      
      try {
        const response = await fetch('/api/vendorLabor', { 
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(data),
        });
  
        const responseData = await response.json();
  
        if (response.ok) {
          alert('Vendor registered successfully!');
          console.log('Success:', responseData);
          window.location.href = '/vendor-login'; // ✅ Redirect if needed  
        } else {
          alert('Something went wrong!');
        }
  
      } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while registering the vendor.');
      }
    };
  return (
  <main>
    <div>
      <form onSubmit={handleSubmit} method="POST">
      <h1> Vendor Register</h1>
      <label htmlFor="employeeID">employeeID:</label>
        <input
          type="text"
          id="employeeID"
          name="employeeID"
          placeholder="enter employee id"
          title="employeeID"
          required
        />
        <br />
        <label htmlFor="driver_name">Driver Name:</label>
        <input
          type="text"
          id="driver_name"
          name="driver_name"
          placeholder="Enter your full name"
          title="driver_name"
                required
        />
        <br />
        <label htmlFor="date_of_join">Date of join:</label>
        <input
          type="date"
          id="date_of_join"
          name="date_of_join"
          placeholder="Enter date of join"
          title="date_of_join"
          required
        />
        <br />
        

        <label htmlFor="password">Password:</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Enter a password"
          title="Password"
          required
        />
        <br />    
        <label htmlFor="vehicleType">Vehicle Type:</label>
        <input
          type="text"
          id="vehicleType"
          name="vehicleType"
          placeholder="nter your vehicle type"
          title="vehicleType"
          required
        />
        <br />
        <label htmlFor="vehicleNumber">vehicle  Number :</label>
        <input
          type="text"
          id="vehicleNumber"
          name="vehicleNumber"
          placeholder="nter your vehicle number"
          title="vehicleNumber" 
          required
        />
        <br />
        <label htmlFor="vehicleModel">model:</label>
        <input
          type="text"
          id="vehicleModel"
          name="vehicleModel"
          placeholder="enter the model"
          title="enter the model"
          required
        />
        <br />
        <label htmlFor="availability"> Availability:</label>
        <input
          type="text"
          id="availability"
          name="availability"
          placeholder="enter availability"
          title="enter availability"
          required
        />
        <br />
        <label htmlFor="condition_check_status"> condition check status:</label>
        <input
          type="text"
          id="condition_check_status"
          name="condition_check_status"
          placeholder="enter condition check status"
          title="enter condition check status"
          required
        />
        <br />
        <br />
        <label htmlFor="vehicleInsurance"> Vehicle Insurance:</label>
        <input
          type="text"
          id="vehicleInsurance"
          name="vehicleInsurance"
          placeholder="enter vehicle insurance"
          title="enter vehicle insurance"
          required
        />
        <br />
        <label htmlFor="panNumber">PAN Number:</label>
        <input
          type="text"
          id="panNumber"
          name="panNumber"
          placeholder="Enter your pan number"
          title="panNumber"
          required
        />
        <br />
        <label htmlFor="aadharNumber">Aadhar Number:</label>
        <input
          type="text"
          id="aadharNumber"
          name="aadharNumber"
          placeholder="Enter your aadhar number"
          title="aadharNumber"
          required
        />
        <br />
        <label htmlFor="licenseNumber">License Number:</label>
        <input
          type="text"
          id="licenseNumber"
          name="licenseNumber"
          placeholder="Enter your license number"
          title="licenseNumber"
          required
        />
        <br />
        <label htmlFor="phoneNumber">Phone Number:</label>
        <input
          type="number"
          id="phoneNumber"
          name="phoneNumber"
          placeholder="Enter your phone number"
          title="phoneNumber"
          required
        />
        <br />
        <label htmlFor="email">Email:</label>
        <input
          type="email"
          id="email"
          name="email"
          placeholder="Enter your email"
          title="email"
          required
        />
        <br />
        <label htmlFor="address">Address:</label>
        <input
          type="text"
          id="address"
          name="address"
          placeholder="Enter your address"
          title="address"
          required
        />
        <br />
        <label htmlFor="salary">Salary:</label>
        <input
          type="text"
          id="salary"
          name="salary"
          placeholder="Enter your salary"
          title="salary"
          required
        />
        <br />
        <label htmlFor="department">Department:</label>
        <input
          type="text"
          id="department"
          name="department"
          placeholder="Enter your department"
          title="department"
          required
        />
        <br />
        <label htmlFor="bankAccountNumber">Bank Account Number:</label>
        <input
          type="text"
          id="bankAccountNumber"
          name="bankAccountNumber"
          placeholder="Enter your bank account number"
          title="bankAccountNumber"
          required
        />
        <br />
        <label htmlFor="ifscCode">IFSC Code:</label>
        <input
          type="text"
          id="ifscCode"
          name="ifscCode"
          placeholder="Enter your ifsc code"
          title="ifscCode"
          required
        />
        <label htmlFor="owner_name">Owner Name:</label>
        <input
          type="text"
          id="owner_name"
          name="owner_name"
          placeholder="Enter your full name"
          title="Full Name"
          required
        />
        <br />

        <label htmlFor="company_email">company email:</label>
        <input
          type="email"
          id="company_email"
          name="company_email"
          placeholder="Enter your email"
          title="Email Address"
          required
        /><br />
        <label htmlFor="company_licence">enter company license number :</label>
        <input
          type="text"
          id="company_licence"
          name="company_licence"
          placeholder="Enter company license number"
          title="Enter company license number"
          required
        /><br />
        <label htmlFor="company_address">company address:</label>
        <input
          type="text"
          id="company_address"
          name="company_address"
          placeholder="enter company address "
          title="enter company address"
          required
        />
        <br />
        
        <label htmlFor="company_name">Company Name:</label>
            <input
              type="text"
              id="company_name"
              name="company_name"
              placeholder="Enter your company name"
              title="Company Name"
              required
              />
              <br />
              <label htmlFor="company_contact">company contact:</label>
        <input
          type="text"
          id="company_contact"
          name="company_contact"
          placeholder="enter company contact number"
          title="enter company contact number"
          required
        />
        <br />
        <button type="submit"   title="Register your account" 
        >Register</button>
        </form>
    </div>
    </main>
  );
}