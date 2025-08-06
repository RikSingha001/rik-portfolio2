"use client";
import React from 'react';

export default function VendorRegistrationPage() 
   {
    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.target as HTMLFormElement);
    const data = Object.fromEntries(formData);
    
    try {
      const response = await fetch('/api/vendor-registration', { 
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
        window.location.href = '/vendorLebarRegister'; // ✅ Redirect if needed  
      } else {
        alert('Something went wrong!');
      }

    } catch (error) {
      console.error('Error:', error);
      alert('An error occurred while registering the vendor.');
    }
  };
  return(
<main>
     <div className="register-container" >

      <form  onSubmit={handleSubmit} method="POST">

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
        <button type="submit"   title="vendor registration"
        >Vendor Registration</button>
      </form>
    </div>
  </main>
  );
}
















  // try {
  //   const [existingVendor]: any = await db.execute(
  //     'SELECT id FROM vendors_registration WHERE company_email = ? OR company_licence = ?',
  //     [company_email, company_licence]
  //   );

    // if (existingVendor.length > 0) {
    //   return res.status(409).json({ message: 'Vendor already exists' });
    // }
