"use client";
import React from 'react';
import { Looking_good } from '@/components/Index'

export default function VendorLoginPage() {
  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    const formData = new FormData(e.target as HTMLFormElement);
    const data = Object.fromEntries(formData);
    console.log(data);
    try {
      const response = await fetch('/api/vendor-login', {
        method: 'POST', 
        headers: {
          'Content-Type': 'application/json', 
        },
        body: JSON.stringify(data),
      });
      const responseData = await response.json();
      if (response.ok) {
        console.log('Login Success:', responseData);
        window.location.href = '/vendor-dashboard';
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Something went wrong');
    }
  };
  return (<main>
    <div>
            <form action="" onSubmit={handleSubmit}  method="POST">

      <label htmlFor="driver_name">Name:</label>
        <input
          type="text"
          id="driver_name"
          name="driver_name"
          placeholder="Enter your full name"
          title="Full Name"
          required
        />
        <br />         
            <label htmlFor="vehicleNumber">Vehicle Number:</label>
            <input
              type="text" 
              title="Vehicle Number"
              id="vehicleNumber"
              name="vehicleNumber"
              placeholder="Enter vehicle number"
              required
            />
        
        <label htmlFor="phone">Mobile Number:</label>
        <input
          type="text"
          id="phone"
          name="phone"
          placeholder="Enter your mobile number"
          title="Mobile Number"
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
        <label htmlFor="companyName">Company Name:</label>
        <input
          type="text"
          id="companyName"
          name="company_name"
          placeholder="Enter your company name"
          title="Company Name"
          required
        />
        <br />    
        <button type="submit"   title="vendor-login" 
        >submit</button>
        </form>
    </div>

      <Looking_good />
      </main>
  )
}


